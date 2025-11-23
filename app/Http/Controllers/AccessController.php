<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FormService;
use App\Models\Form;
use App\Models\FormField;
use App\Models\LoanCase;
use App\Models\Roles;
use App\Models\UserAccessControl;
use App\Services\RolesService;
use Spatie\Permission\Models\Role;

class AccessController extends Controller
{
    public static function manageAccess()
    {
        $user_list = [];
        $case_list = [];
        $allowBranchList = 0;
        $standAlone = 0;
        $brancAccessList = []; 

        $branchInfo = BranchController::manageBranchAccess();
        $brancAccessList = $branchInfo['brancAccessList'];

        $current_user = auth()->user();

        if ($current_user->id == 103) {
            $user_list =  [103, 90];
        } else if (in_array($current_user->id, [5, 39, 137])) {
            $user_list =  [$current_user->id];
            $case_list =  [3632, 3633];
        } else if ($current_user->id == 63) {
            $user_list =  [63, 79];
        }else if ($current_user->id == 32) {
            $user_list =  [$current_user->id,143,127,119,118,90,110,112,113];
            $brancAccessList =  [5];
        } else if ($current_user->id == 106) {
            $user_list =  [90, 106];
        } else if ($current_user->id == 38) {
            $brancAccessList = [1, 4];
            $user_list =  [38];
            $case_list =  [3667];
        } else if ($current_user->id == 90) {
            $brancAccessList = [1, 5, 6];
            $user_list =  [90, 106];
        } else if (in_array($current_user->id, [112])) {
            $brancAccessList = [1, 5, 6];
            $user_list =  [11, 112, 90, 118];
        } else if ($current_user->id == 110) {
            $user_list =  [32, 90];
            $brancAccessList = [5];
        } else if (in_array($current_user->id, [127, 118])) {
            $user_list =  [$current_user->id,143,127,119,32,90,110,112,113];
            
        } else if ($current_user->id == 14) {
            //Moon can view all cases
        } else if ($current_user->id == 13) {
            $user_list =  [13, 141, 63, 79];
            // $brancAccessList = [2];
        } else if ($current_user->id == 80) {
            $user_list =  [80, 89, 116, 138];
        } else if ($current_user->id == 29) {
            $user_list =  [29];
            $brancAccessList = [1, 2, 4, 6];
            $case_list =  [1280, 1281, 2956];
        } else if ($current_user->id == 104) {
            $brancAccessList = [5, 6];
            $user_list = [];
        } else if (in_array($current_user->id, [102, 136])) {
            $brancAccessList = [3];
            $user_list = [80, 89, 116, 138];
        } else if ($current_user->id == 115) {
            $brancAccessList = [3];
            $user_list = [];
        } else if (in_array($current_user->id, [118, 120,143])) {
            $brancAccessList = [1, 5, 6];
            $user_list = [118, 143, 90, $current_user->id];
        } else if ($current_user->id == 111) {
            $brancAccessList = [3];
            $user_list = [89];
        } else if ($current_user->id == 119) {
            $brancAccessList = [1, 5, 6];
            $user_list = [90, 110, 106, 118, 32];
            $case_list =  [2670, 2743];
        } else if ($current_user->id == 121) {
            // $brancAccessList = [];
            $user_list = [13, 141];
            $case_list =  [2674];
        } else if ($current_user->id == 49) {
            $brancAccessList = [1, 2, 3, 5, 6];
            $user_list = [];
            // }else if (in_array($current_user->id,[112])) {
            //     $brancAccessList = [1,5,6];
            //     $user_list=[11,90];
        } else if (in_array($current_user->id, [93, 139])) {
            $brancAccessList = [1, 2, 3, 4, 5, 6];
            $user_list = [];
        } else if (in_array($current_user->id, [113])) {
            $brancAccessList = [1, 5, 6];
            $user_list = [103, 90];
        } else if (in_array($current_user->id, [49])) {
            $brancAccessList = [1, 2, 5, 6];
            $user_list = [];
        } else if (in_array($current_user->id, [125])) {
            $brancAccessList = [3];
            $user_list = [$current_user->id];
        } else if (in_array($current_user->id, [126])) {
            $brancAccessList = [3];
            $user_list = [$current_user->id, 125];
        } else if (in_array($current_user->id, [147])) {
            $brancAccessList = [3];
            $user_list = [$current_user->id, 125];
        } else if (in_array($current_user->id, [140])) {
            $brancAccessList = [3];
            $user_list = [$current_user->id, 126];
        }else if (in_array($current_user->id, [122])) {
            $user_list = [$current_user->id, 142];
        } else if (in_array($current_user->id, [142])) {
            $user_list = [$current_user->id, 122];
        }else if (in_array($current_user->id, [144])) {
            $user_list = [$current_user->id, 29];
        }  else {
            if (!in_array($current_user->menuroles, ['receptionist', 'maker'])) {
                $user_list =  [$current_user->id];
            }
        }

        if (in_array($current_user->menuroles, ['lawyer', 'clerk'])) {
            if (in_array($current_user->branch_id, [1, 4, 6])) {
                $brancAccessList = [1, 2, 3, 4, 6];
            }
        }

        return ['user_list' => $user_list, 'brancAccessList' =>  $brancAccessList, 'case_list' =>  $case_list];
    }

    public static function UserAccessController($code)
    {
        $current_user = auth()->user();
        $Role = Roles::where('name', $current_user->menuroles)->first();


        $UserAccessControl = UserAccessControl::where(function ($q) use ($current_user, $Role) {
            $q->where('user_id', $current_user->id)
                ->orWhere('branch_id', $current_user->branch_id)
                ->orWhere('role_id', $Role->id);
        })->where('code', $code)->first();

        if ($UserAccessControl) {
            return true;
        }

        return false;
    }

    public static function UserAccessPermissionController($code)
    {
        $current_user = auth()->user();


        $Role = Roles::where('name', $current_user->menuroles)->first();

        $UserAccessControl = UserAccessControl::where('code', $code)->first();
        // return $UserAccessControl;

        if ($UserAccessControl) {
            // $exclusive_branch_list = explode(',', $UserAccessControl->exclusive_branch_list);
            $exclusive_branch_list = json_decode($UserAccessControl->exclusive_branch_list);
            // $user_id_list = explode(',', $UserAccessControl->user_id_list);
            // $branch_id_list = explode(',', $UserAccessControl->branch_id_list);
            // $exclude_user_list = explode(',', $UserAccessControl->exclude_user_list);

            $user_id_list = json_decode($UserAccessControl->user_id_list);
            $branch_id_list = json_decode($UserAccessControl->branch_id_list);
            $exclude_user_list = json_decode($UserAccessControl->exclude_user_list);

            // Check exclusive_branch_list only if it's not empty (not '[]' or empty array)
            if ($UserAccessControl->exclusive_branch_list != '' && $UserAccessControl->exclusive_branch_list != '[]') {
                if (in_array($current_user->branch_id, $exclusive_branch_list)) {

                    if (is_array($exclude_user_list) && in_array($current_user->id, $exclude_user_list)) {
                        return false;
                    }
                    else {
                        return true;
                    }
                } else {
                    // return false;

                    if (is_array($user_id_list) && in_array($current_user->id, $user_id_list)) {
                        return true;
                    }else if (is_array($branch_id_list) && in_array($current_user->branch_id, $branch_id_list)) {
                        return true;
                    }  else {
                        return false;
                    }
                }
            }

            // Check exclude_branch_list only if it's not empty (not '[]' or empty array)
            if ($UserAccessControl->exclude_branch_list != '' && $UserAccessControl->exclude_branch_list != '[]') {
                // Exclude branch
                // $exclude_branch_list = explode(',', $UserAccessControl->exclude_branch_list);
                $exclude_branch_list = json_decode($UserAccessControl->exclude_branch_list);

                if (in_array($current_user->branch_id, $exclude_branch_list)) {
                    if (is_array($user_id_list) && in_array($current_user->id, $user_id_list)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    if (is_array($exclude_user_list) && in_array($current_user->id, $exclude_user_list)) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }

            // $role_id_list = explode(',', $UserAccessControl->role_id_list);

            $role_id_list = json_decode($UserAccessControl->role_id_list);

            if(is_array($role_id_list))
            {
                if (in_array($Role->id, $role_id_list)) {
                    return true;
                }
            }

            if(is_array($branch_id_list))
            {
                if (in_array($current_user->branch_id, $branch_id_list)) {
                    return true;
                }
            }

            if(is_array($user_id_list))
            {
                if (in_array($current_user->id, $user_id_list)) {
                    return true;
                }
            }

           
        }


        return false;
    }

    public static function UserAccessPermissionController2($code)
    {
        $current_user = auth()->user();

        $Role = Roles::where('name', $current_user->menuroles)->first();

        // return true;

        $UserAccessControl = UserAccessControl::where('code', $code)->first();

        if ($UserAccessControl) {
            // $exclusive_branch_list = explode(',', $UserAccessControl->exclusive_branch_list);
            $exclusive_branch_list = json_decode($UserAccessControl->exclusive_branch_list);
            // $user_id_list = explode(',', $UserAccessControl->user_id_list);
            // $branch_id_list = explode(',', $UserAccessControl->branch_id_list);
            // $exclude_user_list = explode(',', $UserAccessControl->exclude_user_list);

            $user_id_list = json_decode($UserAccessControl->user_id_list);
            $branch_id_list = json_decode($UserAccessControl->branch_id_list);
            $exclude_user_list = json_decode($UserAccessControl->exclude_user_list);
           
            if ($UserAccessControl->exclusive_branch_list != '') {
                
           
                if (in_array($current_user->branch_id, $exclusive_branch_list)) {

                    if (in_array($current_user->id, $exclude_user_list)) {
                        return false;
                    }
                    else {
                        return true;
                    }
                } else {
                    // return false;
                    
                    if (in_array($current_user->id, $user_id_list)) {
                        return true;
                    }else if (in_array($current_user->branch_id, $branch_id_list)) {
                        return true;
                    }
                      else {
                        return false;
                    }
                }
                
            }

            if ($UserAccessControl->exclude_branch_list != '') {
                // Exclude branch
                // $exclude_branch_list = explode(',', $UserAccessControl->exclude_branch_list);
                $exclude_branch_list = json_decode($UserAccessControl->exclude_branch_list);

                if (in_array($current_user->branch_id, $exclude_branch_list)) {
                    if (in_array($current_user->id, $user_id_list)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    if (in_array($current_user->id, $exclude_user_list)) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }

            // $role_id_list = explode(',', $UserAccessControl->role_id_list);

            $role_id_list = json_decode($UserAccessControl->role_id_list);

            if(is_array($role_id_list))
            {
                if (in_array($Role->id, $role_id_list)) {
                    return true;
                }
            }

            if(is_array($branch_id_list))
            {
                if (in_array($current_user->branch_id, $branch_id_list)) {
                    return true;
                }
            }

            if(is_array($user_id_list))
            {
                if (in_array($current_user->id, $user_id_list)) {
                    return true;
                }
            }

           
        }


        return false;
    }

    public static function UserAccessPermissionControllerBak($code)
    {
        $current_user = auth()->user();

        $Role = Roles::where('name', $current_user->menuroles)->first();

        $UserAccessControl = UserAccessControl::where('code', $code)->first();

        if ($UserAccessControl) {
            $exclusive_branch_list = explode(',', $UserAccessControl->exclusive_branch_list);
            $user_id_list = explode(',', $UserAccessControl->user_id_list);
            $branch_id_list = explode(',', $UserAccessControl->branch_id_list);
            $exclude_user_list = explode(',', $UserAccessControl->exclude_user_list);
            $exclude_user_list = explode(',', $UserAccessControl->exclude_user_list);

            if ($UserAccessControl->exclusive_branch_list != '') {
                if (in_array($current_user->branch_id, $exclusive_branch_list)) {

                    if (in_array($current_user->id, $exclude_user_list)) {
                        return false;
                    }
                    else {
                        return true;
                    }
                } else {
                    // return false;

                    if (in_array($current_user->id, $user_id_list)) {
                        return true;
                    }else if (in_array($current_user->branch_id, $branch_id_list)) {
                        return true;
                    }  else {
                        return false;
                    }
                }
            }

            if ($UserAccessControl->exclude_branch_list != '') {
                // Exclude branch
                $exclude_branch_list = explode(',', $UserAccessControl->exclude_branch_list);

                if (in_array($current_user->branch_id, $exclude_branch_list)) {
                    if (in_array($current_user->id, $user_id_list)) {
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    if (in_array($current_user->id, $exclude_user_list)) {
                        return false;
                    } else {
                        return true;
                    }
                }
            }





            $role_id_list = explode(',', $UserAccessControl->role_id_list);

            if (in_array($Role->id, $role_id_list)) {
                return true;
            }

            if (in_array($current_user->branch_id, $branch_id_list)) {
                return true;
            }



            if (in_array($current_user->id, $user_id_list)) {
                return true;
            }
        }


        return false;
    }
}
