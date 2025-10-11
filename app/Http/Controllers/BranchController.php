<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\FormService;
use App\Models\Form;
use App\Models\FormField;
use App\Services\RolesService;

class BranchController extends Controller
{
    public static function manageBranchAccess()
    {
        $branchList = [];
        $allowBranchList = 0;
        $standAlone = 0;
        $brancAccessList = [];
        $current_user = auth()->user();

        $userBranch = Branch::where('id', '=', $current_user->branch_id)->first();

        $roles_with_all_branch = array("admin", "management", "sales", "receptionist", "chambering", "account");
        $standalone = array("admin", "management", "sales", "receptionist", "chambering", "account");

        $userRoles = $current_user->menuroles;

        if (in_array($userRoles, ['admin','management', 'account']) || in_array($current_user->id, [14]) )
        {
            $branchList = Branch::where('status', '=', 1)->get();
        }
        else if (in_array($userRoles, ['sales','receptionist', 'chambering','lawyer']) &&  $current_user->id != 80)
        {
           
            if ($userBranch->branch_type == 'STANDALONE')
            {
                $branchList = Branch::where('status', '=', 1)->where('id', '=', $current_user->branch_id)->get();
            }
            else
            {
                if (in_array($current_user->id,[32]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 5])->get();
                }else if (in_array($current_user->id,[90]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 1,5,6])->get();
                }else if (in_array($current_user->id,[22]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 1])->get();
                }else if (in_array($current_user->id,[94]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 1])->get();
                }else if (in_array($current_user->id,[80]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 4,6,1])->get();
                }else if (in_array($current_user->id,[106,110,119]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [5,6])->get();
                }else if (in_array($current_user->id,[112,113,120]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,5,6])->get();
                }else if (in_array($current_user->id,[13]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [2])->get();
                }else if (in_array($current_user->id,[29,105]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,2,4,6])->get();
                }
                else if (in_array($current_user->id,[93,139]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,2,3,4,5,6])->get();
                }
                else if (in_array($current_user->id,[49]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,2,4,5,6])->get();
                }else if (in_array($current_user->id,[72]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,2])->get();
                }else if (in_array($current_user->id,[116]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id,6,1])->get();
                }
                else if (in_array($current_user->id,[64]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id,4,6])->get();
                }
                else if (in_array($current_user->id,[144]))
                {
                    $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id,4,6])->get();
                }
                else
                {
                    if($userBranch->hq == 1)
                    {
                        $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,2])->get();
                    }
                    else
                    {
                        $branchList = Branch::where('status', '=', 1)->where('id', '=', $userBranch->id)->get();
                    }
                    

                }
                
            }
        }
        else if ($current_user->id == 80)
        {
            $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 4,6,1])->get();
        }
        else if ($current_user->id == 138)
        {
            $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 1])->get();
        }
        else if ($current_user->id == 188)
        {
            $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id])->get();
        }
        else
        { 

            if (in_array($current_user->id,[38,21,22,94,25])  )
            {
                $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 1])->get();
            }
            else if (in_array($current_user->id,[90,103]) )
            {
                $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id, 1,2,6])->get();
            }
            else if (in_array($current_user->id,[104,119,118,121,143]) )
            {
                $branchList = Branch::where('status', '=', 1)->whereIn('id', [$current_user->branch_id,6])->get();
            }
            else if (in_array($current_user->id,[110,112,113,120]) )
            {
                $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,5,6])->get();
            }
            else if (in_array($current_user->id,[72]) )
            {
                $branchList = Branch::where('status', '=', 1)->whereIn('id', [1,2])->get();
            }
            else
            {
                $branchList = Branch::where('status', '=', 1)->where('id', '=', $current_user->branch_id)->get();
            }
        }

        if ($userBranch->branch_type == 'STANDALONE')
        {
            $standAlone = 1;
        }

        if (in_array($current_user->menuroles, ['lawyer', 'clerk'])) 
        {
            if (in_array($current_user->branch_id, [1,4,6]))
            {
                // $brancAccessList = [1,3,4,6];
                $branchList = Branch::where('status', '=', 1)->whereIn('id',  [1,2,3,4,6])->get();
            }
        }

        // if (in_array($current_user->menuroles, ['maker'])) 
        // {
        //     if (in_array($current_user->id, [102]))
        //     {
        //         $branchList = Branch::where('status', '=', 1)->whereIn('id',  [1,2,3,4,6])->get();
        //     }
        // }

        

        for ($i = 0; $i < count($branchList); $i++) {
            array_push($brancAccessList, $branchList[$i]->id);
        }

        return ['standAlone' => $standAlone, 'branch' =>  $branchList, 'brancAccessList' =>  $brancAccessList];
    }
}


