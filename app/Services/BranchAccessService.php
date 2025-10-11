<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class BranchAccessService
{
    /**
     * Get accessible branch IDs for a user
     */
    public static function getAccessibleBranchIds(User $user): array
    {
        $cacheKey = "user_branch_access_{$user->id}";
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            $branchIds = [];
            
            switch ($user->menuroles) {
                case 'admin':
                case 'account':
                    // Admin and account can access all branches
                    $branchIds = self::getAllActiveBranchIds();
                    break;
                    
                case 'maker':
                    $branchIds = self::getMakerBranchAccess($user);
                    break;
                    
                case 'lawyer':
                    $branchIds = [$user->branch_id];
                    break;
                    
                case 'sales':
                    $branchIds = self::getSalesBranchAccess($user);
                    break;
                    
                default:
                    // Default to user's own branch
                    $branchIds = [$user->branch_id];
            }
            
            return array_unique($branchIds);
        });
    }
    
    /**
     * Get maker-specific branch access
     */
    private static function getMakerBranchAccess(User $user): array
    {
        $branchConfig = config('branch.access_rules.maker', []);
        
        // Check for special branch combinations
        if (in_array($user->branch_id, $branchConfig['special_combinations'] ?? [])) {
            return $branchConfig['special_combinations'][$user->branch_id] ?? [$user->branch_id];
        }
        
        // Check for specific branch rules
        if (isset($branchConfig['specific_branches'][$user->branch_id])) {
            return $branchConfig['specific_branches'][$user->branch_id];
        }
        
        // Default to user's own branch
        return [$user->branch_id];
    }
    
    /**
     * Get sales-specific branch access
     */
    private static function getSalesBranchAccess(User $user): array
    {
        $branchConfig = config('branch.access_rules.sales', []);
        
        // Check for special user IDs
        if (in_array($user->id, $branchConfig['special_users'] ?? [])) {
            return $branchConfig['special_users'][$user->id] ?? [$user->branch_id];
        }
        
        // Default to user's own branch
        return [$user->branch_id];
    }
    
    /**
     * Get all active branch IDs
     */
    private static function getAllActiveBranchIds(): array
    {
        return Cache::remember('all_active_branch_ids', 3600, function () {
            return \App\Models\Branch::where('status', 1)->pluck('id')->toArray();
        });
    }
    
    /**
     * Apply branch filtering to a query
     */
    public static function applyBranchFilter($query, User $user, string $branchColumn = 'branch_id'): void
    {
        $accessibleBranches = self::getAccessibleBranchIds($user);
        
        if (count($accessibleBranches) === 1) {
            $query->where($branchColumn, '=', $accessibleBranches[0]);
        } else {
            $query->whereIn($branchColumn, $accessibleBranches);
        }
    }
    
    /**
     * Check if user has access to a specific branch
     */
    public static function hasBranchAccess(User $user, int $branchId): bool
    {
        $accessibleBranches = self::getAccessibleBranchIds($user);
        return in_array($branchId, $accessibleBranches);
    }
    
    /**
     * Clear user's branch access cache
     */
    public static function clearUserCache(User $user): void
    {
        Cache::forget("user_branch_access_{$user->id}");
    }
    
    /**
     * Clear all branch access caches
     */
    public static function clearAllCaches(): void
    {
        Cache::forget('all_active_branch_ids');
        // Note: Individual user caches will be cleared when they next access the system
    }
}
