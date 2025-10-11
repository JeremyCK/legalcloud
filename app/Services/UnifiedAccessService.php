<?php

namespace App\Services;

use App\Models\User;
use App\Models\Branch;
use App\Models\UserAccessControl;
use App\Models\Roles;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UnifiedAccessService
{
    /**
     * Get comprehensive access information for a user
     */
    public static function getUserAccessInfo(User $user): array
    {
        $cacheKey = "user_access_info_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            return [
                'branch_access' => self::getBranchAccess($user),
                'permission_access' => self::getPermissionAccess($user),
                'user_access' => self::getUserAccess($user),
                'case_access' => self::getCaseAccess($user),
            ];
        });
    }

    /**
     * Get branch access information
     */
    public static function getBranchAccess(User $user): array
    {
        $branchConfig = config('access.branch_rules', []);
        $userBranch = Branch::where('id', $user->branch_id)->first();
        
        $accessibleBranches = [];
        $standalone = false;
        
        // Check role-based access
        if (in_array($user->menuroles, ['admin', 'management', 'account'])) {
            $accessibleBranches = Branch::where('status', 1)->pluck('id')->toArray();
        } else {
            // Check user-specific rules first
            $userRules = $branchConfig['user_specific'][$user->id] ?? null;
            if ($userRules) {
                $accessibleBranches = $userRules['branches'] ?? [$user->branch_id];
                $standalone = $userRules['standalone'] ?? false;
            } else {
                // Check role-based rules
                $roleRules = $branchConfig['role_based'][$user->menuroles] ?? null;
                if ($roleRules) {
                    $accessibleBranches = self::applyRoleBranchRules($user, $roleRules, $userBranch);
                    $standalone = $roleRules['standalone'] ?? false;
                } else {
                    // Default behavior
                    $accessibleBranches = [$user->branch_id];
                    $standalone = $userBranch->branch_type === 'STANDALONE';
                }
            }
        }
        
        return [
            'accessible_branches' => array_unique($accessibleBranches),
            'standalone' => $standalone,
            'user_branch' => $userBranch,
        ];
    }

    /**
     * Get permission access information
     */
    public static function getPermissionAccess(User $user): array
    {
        $permissions = [];
        $role = Roles::where('name', $user->menuroles)->first();
        
        if ($role) {
            $accessControls = UserAccessControl::where('status', 1)
                ->where(function ($query) use ($user, $role) {
                    $query->where('role_id', $role->id)
                          ->orWhere('user_id', $user->id)
                          ->orWhere('branch_id', $user->branch_id);
                })
                ->get();
            
            foreach ($accessControls as $control) {
                if (self::evaluatePermissionControl($user, $control)) {
                    $permissions[] = $control->code;
                }
            }
        }
        
        return $permissions;
    }

    /**
     * Get user access information (which users can be accessed)
     */
    public static function getUserAccess(User $user): array
    {
        $userConfig = config('access.user_rules', []);
        $accessibleUsers = [];
        
        // Check user-specific rules
        $userRules = $userConfig['user_specific'][$user->id] ?? null;
        if ($userRules) {
            $accessibleUsers = $userRules['accessible_users'] ?? [$user->id];
        } else {
            // Check role-based rules
            $roleRules = $userConfig['role_based'][$user->menuroles] ?? null;
            if ($roleRules) {
                $accessibleUsers = self::applyRoleUserRules($user, $roleRules);
            } else {
                // Default behavior
                $accessibleUsers = [$user->id];
            }
        }
        
        return array_unique($accessibleUsers);
    }

    /**
     * Get case access information
     */
    public static function getCaseAccess(User $user): array
    {
        $caseConfig = config('access.case_rules', []);
        $accessibleCases = [];
        
        // Check user-specific case access
        $userRules = $caseConfig['user_specific'][$user->id] ?? null;
        if ($userRules) {
            $accessibleCases = $userRules['accessible_cases'] ?? [];
        }
        
        return $accessibleCases;
    }

    /**
     * Apply branch filtering to a query
     */
    public static function applyBranchFilter($query, User $user, string $branchColumn = 'branch_id'): void
    {
        $branchAccess = self::getBranchAccess($user);
        $accessibleBranches = $branchAccess['accessible_branches'];
        
        if (count($accessibleBranches) === 1) {
            $query->where($branchColumn, '=', $accessibleBranches[0]);
        } else {
            $query->whereIn($branchColumn, $accessibleBranches);
        }
    }

    /**
     * Check if user has permission
     */
    public static function hasPermission(User $user, string $permissionCode): bool
    {
        $permissions = self::getPermissionAccess($user);
        return in_array($permissionCode, $permissions);
    }

    /**
     * Check if user has access to specific branch
     */
    public static function hasBranchAccess(User $user, int $branchId): bool
    {
        $branchAccess = self::getBranchAccess($user);
        return in_array($branchId, $branchAccess['accessible_branches']);
    }

    /**
     * Check if user has access to specific user
     */
    public static function hasUserAccess(User $user, int $targetUserId): bool
    {
        $userAccess = self::getUserAccess($user);
        return in_array($targetUserId, $userAccess);
    }

    /**
     * Clear user's access cache
     */
    public static function clearUserCache(User $user): void
    {
        Cache::forget("user_access_info_{$user->id}");
    }

    /**
     * Clear all access caches
     */
    public static function clearAllCaches(): void
    {
        // Clear all user access caches
        $users = User::all();
        foreach ($users as $user) {
            self::clearUserCache($user);
        }
    }

    /**
     * Apply role-based branch rules
     */
    private static function applyRoleBranchRules(User $user, array $roleRules, $userBranch): array
    {
        $accessibleBranches = [];
        
        // Check branch type rules
        if ($userBranch->branch_type === 'STANDALONE') {
            $accessibleBranches = [$user->branch_id];
        } else {
            // Check special branch combinations
            $specialCombinations = $roleRules['special_combinations'] ?? [];
            if (isset($specialCombinations[$user->branch_id])) {
                $accessibleBranches = $specialCombinations[$user->branch_id];
            } else {
                // Check HQ rules
                if ($userBranch->hq == 1) {
                    $accessibleBranches = $roleRules['hq_branches'] ?? [1, 2];
                } else {
                    $accessibleBranches = [$user->branch_id];
                }
            }
        }
        
        return $accessibleBranches;
    }

    /**
     * Apply role-based user rules
     */
    private static function applyRoleUserRules(User $user, array $roleRules): array
    {
        $accessibleUsers = [];
        
        // Check special user combinations
        $specialUsers = $roleRules['special_users'] ?? [];
        if (isset($specialUsers[$user->id])) {
            $accessibleUsers = $specialUsers[$user->id];
        } else {
            // Default to user's own access
            $accessibleUsers = [$user->id];
        }
        
        return $accessibleUsers;
    }

    /**
     * Evaluate permission control
     */
    private static function evaluatePermissionControl(User $user, UserAccessControl $control): bool
    {
        // Parse JSON fields
        $exclusiveBranches = json_decode($control->exclusive_branch_list, true) ?? [];
        $userList = json_decode($control->user_id_list, true) ?? [];
        $branchList = json_decode($control->branch_id_list, true) ?? [];
        $roleList = json_decode($control->role_id_list, true) ?? [];
        $excludeBranches = json_decode($control->exclude_branch_list, true) ?? [];
        $excludeUsers = json_decode($control->exclude_user_list, true) ?? [];
        
        $role = Roles::where('name', $user->menuroles)->first();
        
        // Check exclusive branch logic
        if (!empty($exclusiveBranches)) {
            if (in_array($user->branch_id, $exclusiveBranches)) {
                return !in_array($user->id, $excludeUsers);
            } else {
                return in_array($user->id, $userList) || in_array($user->branch_id, $branchList);
            }
        }
        
        // Check exclude branch logic
        if (!empty($excludeBranches)) {
            if (in_array($user->branch_id, $excludeBranches)) {
                return in_array($user->id, $userList);
            } else {
                return !in_array($user->id, $excludeUsers);
            }
        }
        
        // Check role, branch, and user lists
        if ($role && in_array($role->id, $roleList)) {
            return true;
        }
        
        if (in_array($user->branch_id, $branchList)) {
            return true;
        }
        
        if (in_array($user->id, $userList)) {
            return true;
        }
        
        return false;
    }
}
