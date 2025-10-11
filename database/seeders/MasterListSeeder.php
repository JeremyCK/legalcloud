<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* Folders  */
        $MainCat = array();

        array_push($MainCat, array('name' => 'Purchaser', 'type' => '4', 'no' => '1'));
        array_push($MainCat, array('name' => 'Purchaser', 'type' => '4', 'no' => '2'));
        array_push($MainCat, array('name' => 'Purchaser', 'type' => '4', 'no' => '3'));
        array_push($MainCat, array('name' => 'Purchaser', 'type' => '4', 'no' => '4'));
        array_push($MainCat, array('name' => 'Vendor', 'type' => '4', 'no' => '1'));
        array_push($MainCat, array('name' => 'Vendor', 'type' => '4', 'no' => '2'));
        array_push($MainCat, array('name' => 'Vendor', 'type' => '4', 'no' => '3'));
        array_push($MainCat, array('name' => 'Vendor', 'type' => '4', 'no' => '4'));

        array_push($MainCat, array('name' => 'Purchaser Company', 'type' => '5'));
        array_push($MainCat, array('name' => 'Vendor Company', 'type' => '5'));

        array_push($MainCat, array('name' => 'Property', 'type' => '6'));
        array_push($MainCat, array('name' => 'Purchase Price', 'type' => '7'));
        array_push($MainCat, array('name' => 'Loan Sum', 'type' => '8'));
        array_push($MainCat, array('name' => 'Date', 'type' => '9'));

        array_push($MainCat, array('name' => 'Purchaser Financier', 'type' => '10'));
        array_push($MainCat, array('name' => 'Developer', 'type' => '10'));
        array_push($MainCat, array('name' => 'Proprietor', 'type' => '10'));
        array_push($MainCat, array('name' => 'Liquidator', 'type' => '10'));
        array_push($MainCat, array('name' => 'Maintenance office', 'type' => '10'));
        array_push($MainCat, array('name' => 'Valuer', 'type' => '10'));
        array_push($MainCat, array('name' => 'Land Office', 'type' => '10'));

        array_push($MainCat, array('name' => 'Vendor Solicitors', 'type' => '11'));
        array_push($MainCat, array('name' => 'Purchaser Solicitors', 'type' => '11'));
        array_push($MainCat, array('name' => 'Purchaser Financier Solicitors', 'type' => '11'));
        array_push($MainCat, array('name' => 'POT Solicitors', 'type' => '11'));
        array_push($MainCat, array('name' => 'POC Solicitors', 'type' => '11'));

        
        array_push($MainCat, array('name' => 'Agent', 'type' => '12'));
        array_push($MainCat, array('name' => 'Vendor Financier', 'type' => '13'));

        // array_push($MainCat, array('name' => 'Date', 'type' => '3'));
        // array_push($MainCat, array('name' => 'Property', 'type' =>'2'));
        // array_push($MainCat, array('name' => 'Vendor Financier', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Purchaser Financier', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Vendor Solicitors', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Purchaser Solicitors', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Agent', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Banker', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Purchaser Financier Solicitors', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Our lawyer', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Our marketing', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Our clerk', 'type' => '1'));
        // array_push($MainCat, array('name' => 'LHDN branch', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Developer', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Proprietor', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Liquidator', 'type' => '1'));
        // array_push($MainCat, array('name' => 'maintenance office', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Valuer', 'type' => '1'));
        // array_push($MainCat, array('name' => 'POT solicitors', 'type' => '1'));
        // array_push($MainCat, array('name' => 'POC solicitors', 'type' => '1'));
        // array_push($MainCat, array('name' => 'Land Office', 'type' => '1'));

        $field1 = array();

        array_push($field1, array('name' => 'Name', 'type' => 'text'));
        array_push($field1, array('name' => 'NRIC', 'type' => 'text'));
        array_push($field1, array('name' => 'Address', 'type' => 'area'));
        array_push($field1, array('name' => 'HP', 'type' => 'text'));
        array_push($field1, array('name' => 'Tel', 'type' => 'text'));
        array_push($field1, array('name' => 'Fax', 'type' => 'text'));
        array_push($field1, array('name' => 'Email', 'type' => 'email'));

        $field2 = array();

        array_push($field2, array('name' => 'Title', 'type' => 'text'));
        array_push($field2, array('name' => 'Address', 'type' => 'area'));
        array_push($field2, array('name' => 'Loan Sum', 'type' => 'number'));
        array_push($field2, array('name' => 'Purchase Price', 'type' => 'number'));
        array_push($field2, array('name' => '10 per deposit', 'type' => 'number'));
        array_push($field2, array('name' => 'Ddr Pekan Mukim', 'type' => 'text'));
        array_push($field2, array('name' => 'Lot Pt', 'type' => 'text'));
        array_push($field2, array('name' => 'No Bangunan', 'type' => 'text'));
        array_push($field2, array('name' => 'No Tingkat', 'type' => 'text'));
        array_push($field2, array('name' => 'No Petak', 'type' => 'text'));
        array_push($field2, array('name' => 'Petak Aksesori', 'type' => 'text'));
        array_push($field2, array('name' => 'Hakmilik', 'type' => 'text'));
        array_push($field2, array('name' => 'Charge Presentation No', 'type' => 'text'));
        array_push($field2, array('name' => 'Charge Prsn Date', 'type' => 'date'));

        $field3 = array();

        array_push($field3, array('name' => 'SMP Date', 'type' => 'date'));
        array_push($field3, array('name' => 'Completion Date', 'type' => 'date'));


        $field4 = array();

        array_push($field4, array('name' => 'Name', 'type' => 'text'));
        array_push($field4, array('name' => 'NRIC', 'type' => 'text'));
        array_push($field4, array('name' => 'Address', 'type' => 'area'));
        array_push($field4, array('name' => 'HP', 'type' => 'text'));
        array_push($field4, array('name' => 'Tel', 'type' => 'text'));
        array_push($field4, array('name' => 'Fax', 'type' => 'text'));
        array_push($field4, array('name' => 'Email', 'type' => 'email'));
        array_push($field4, array('name' => 'Income Tax No', 'type' => 'text'));
        array_push($field4, array('name' => 'Income Tax Branch', 'type' => 'text'));
        array_push($field4, array('name' => 'Income Tax Branch Address', 'type' => 'text'));


        $field5 = array();

        array_push($field5, array('name' => 'Name', 'type' => 'text'));
        array_push($field5, array('name' => 'Company No', 'type' => 'text'));
        array_push($field5, array('name' => 'Address', 'type' => 'area'));
        array_push($field5, array('name' => 'HP', 'type' => 'text'));
        array_push($field5, array('name' => 'Tel', 'type' => 'text'));
        array_push($field5, array('name' => 'Fax', 'type' => 'text'));
        array_push($field5, array('name' => 'Email', 'type' => 'email'));
        array_push($field5, array('name' => 'Income Tax No', 'type' => 'text'));
        array_push($field5, array('name' => 'Income Tax Branch', 'type' => 'text'));
        array_push($field5, array('name' => 'Income Tax Branch Address', 'type' => 'text'));
        array_push($field5, array('name' => 'File Ref', 'type' => 'text'));
        array_push($field5, array('name' => 'Purchaser Director 1 Name', 'type' => 'text'));
        array_push($field5, array('name' => 'Purchaser Director 1 NRIC', 'type' => 'text'));
        array_push($field5, array('name' => 'Purchaser Director 1 Address', 'type' => 'text'));
        array_push($field5, array('name' => 'Purchaser Director / Secretary Name', 'type' => 'text'));
        array_push($field5, array('name' => 'Purchaser Director / Secretary NRIC', 'type' => 'text'));
        array_push($field5, array('name' => 'Purchaser Director / Secretary Address', 'type' => 'text'));

        $field6 = array();

        array_push($field6, array('name' => 'Property title', 'type' => 'text', 'code' => 'property_details'));
        array_push($field6, array('name' => 'Property details', 'type' => 'area', 'code' => 'property_title'));
        array_push($field6, array('name' => 'Property address', 'type' => 'area', 'code' => 'property_address'));
        array_push($field6, array('name' => 'Restriction of interest', 'type' => 'text', 'code' => 'restriction_of_interest'));
        array_push($field6, array('name' => 'Charge presentation no', 'type' => 'text', 'code' => 'charge_presentation_no'));
        array_push($field6, array('name' => 'Charge presentation date', 'type' => 'text', 'code' => 'charge_prsn_date'));
        array_push($field6, array('name' => 'Bandar/Pekan/Mukim', 'type' => 'text', 'code' => 'bdr_pekan_mukim'));
        array_push($field6, array('name' => 'No. & Lot/Petak/P.T.', 'type' => 'text', 'code' => 'lot_pt'));
        array_push($field6, array('name' => 'Jenis dan No. Hakmilik', 'type' => 'text', 'code' => 'hakmilik'));
        array_push($field6, array('name' => 'No. bangunan', 'type' => 'text', 'code' => 'bangunan'));
        array_push($field6, array('name' => 'No. tingkat', 'type' => 'text', 'code' => 'tingkat'));
        array_push($field6, array('name' => 'No. petak', 'type' => 'text', 'code' => 'petak'));
        array_push($field6, array('name' => 'No. petak aksesori', 'type' => 'text', 'code' => 'petak_aksesori'));


        $field7 = array();

        array_push($field7, array('name' => 'Property purchase price in word', 'type' => 'number', 'code' => 'property purchase price in word'));
        array_push($field7, array('name' => 'Property purchase price', 'type' => 'number', 'code' => 'property_purchase_price'));
        array_push($field7, array('name' => 'RPGT in word', 'type' => 'text', 'code' => 'rpgt_word'));
        array_push($field7, array('name' => 'RPGT', 'type' => 'number', 'code' => 'rpgt'));
        array_push($field7, array('name' => 'Earnest deposit in word', 'type' => 'text', 'code' => 'ed_word'));
        array_push($field7, array('name' => 'Earnest deposit', 'type' => 'number', 'code' => 'ed'));
        array_push($field7, array('name' => 'Balance deposit in word', 'type' => 'text', 'code' => 'bd_word'));
        array_push($field7, array('name' => 'Balance deposit', 'type' => 'number', 'code' => 'bd'));
        array_push($field7, array('name' => 'Balance purchase price in word', 'type' => 'text', 'code' => 'bpp_word'));
        array_push($field7, array('name' => 'Balance purchase price', 'type' => 'number', 'code' => 'bpp'));
        array_push($field7, array('name' => '10% deposit in word', 'type' => 'text', 'code' => '10per_deposit_word'));
        array_push($field7, array('name' => '10% deposit', 'type' => 'number', 'code' => '10per_deposit'));
        array_push($field7, array('name' => 'Differential Sum', 'type' => 'number', 'code' => 'differential_sum'));


        $field8 = array();

        array_push($field8, array('name' => 'Full Loan Sum', 'type' => 'number', 'code' => 'full_loan_sum'));
        array_push($field8, array('name' => 'Insurance', 'type' => 'text', 'code' => 'insurance'));
        array_push($field8, array('name' => 'Legal Fees', 'type' => 'number', 'code' => 'legal_fees'));
        array_push($field8, array('name' => 'Valuer Fees', 'type' => 'number', 'code' => 'valuer_fees'));
        array_push($field8, array('name' => 'Misc Fees', 'type' => 'number', 'code' => 'valuer_fees'));
        array_push($field8, array('name' => 'Actual Loan Sum', 'type' => 'number', 'code' => 'actual_loan_sum'));


        $field9 = array();

        array_push($field9, array('name' => 'S&P Date', 'type' => 'number', 'code' => 'snp_date'));
        array_push($field9, array('name' => 'Completion Date', 'type' => 'text', 'code' => 'completion_date'));
        array_push($field9, array('name' => 'Extended Completion Date 1', 'type' => 'number', 'code' => 'extended_completion_date_1'));
        array_push($field9, array('name' => 'Extended Completion Date 2', 'type' => 'number', 'code' => 'extended_completion_date_2'));
        array_push($field9, array('name' => 'Extended Completion Date 3', 'type' => 'number', 'code' => 'extended_completion_date_3'));

        $field10 = array();

        array_push($field10, array('name' => 'Name', 'type' => 'text'));
        array_push($field10, array('name' => 'Company No', 'type' => 'text'));
        array_push($field10, array('name' => 'HQ Address', 'type' => 'area'));
        array_push($field10, array('name' => 'Branch Address', 'type' => 'area'));
        array_push($field10, array('name' => 'HP', 'type' => 'text'));
        array_push($field10, array('name' => 'Tel', 'type' => 'text'));
        array_push($field10, array('name' => 'Fax', 'type' => 'text'));
        array_push($field10, array('name' => 'Email', 'type' => 'email'));
        array_push($field10, array('name' => 'File Ref', 'type' => 'text'));

        $field11 = array();

        array_push($field11, array('name' => 'Name', 'type' => 'text'));
        array_push($field11, array('name' => 'Address', 'type' => 'area'));
        array_push($field11, array('name' => 'HP', 'type' => 'text'));
        array_push($field11, array('name' => 'Tel', 'type' => 'text'));
        array_push($field11, array('name' => 'Fax', 'type' => 'text'));
        array_push($field11, array('name' => 'Email', 'type' => 'email'));
        array_push($field11, array('name' => 'File Ref', 'type' => 'text'));
        array_push($field11, array('name' => 'Lawyer', 'type' => 'text'));
        array_push($field11, array('name' => 'Clerk', 'type' => 'text'));

        $field12 = array();

        array_push($field12, array('name' => 'Name', 'type' => 'text'));
        array_push($field12, array('name' => 'NRIC', 'type' => 'text'));
        array_push($field12, array('name' => 'Address', 'type' => 'area'));
        array_push($field12, array('name' => 'Agency Name', 'type' => 'text'));
        array_push($field12, array('name' => 'Agency Address', 'type' => 'area'));
        array_push($field12, array('name' => 'HP', 'type' => 'text'));
        array_push($field12, array('name' => 'Tel', 'type' => 'text'));
        array_push($field12, array('name' => 'Fax', 'type' => 'text'));
        array_push($field12, array('name' => 'Email', 'type' => 'email'));
        array_push($field12, array('name' => 'File Ref', 'type' => 'text'));
        array_push($field12, array('name' => 'Booking Form No', 'type' => 'text'));


        $field13 = array();

        array_push($field13, array('name' => 'Name', 'type' => 'text'));
        array_push($field13, array('name' => 'Company No', 'type' => 'text'));
        array_push($field13, array('name' => 'Address', 'type' => 'area'));
        array_push($field13, array('name' => 'HP', 'type' => 'text'));
        array_push($field13, array('name' => 'Tel', 'type' => 'text'));
        array_push($field13, array('name' => 'Fax', 'type' => 'text'));
        array_push($field13, array('name' => 'Email', 'type' => 'email'));
        array_push($field13, array('name' => 'File Ref', 'type' => 'text'));
        array_push($field13, array('name' => 'Borrower', 'type' => 'text'));

        for ($j = 0; $j < count($MainCat); $j++) {

            $Catcode = strtolower($MainCat[$j]['name']);
            $Catcode = str_replace(" ", "_", $Catcode);

            if (array_key_exists("no", $MainCat[$j])) {
                $MainCat[$j]['name'] =  $MainCat[$j]['name'] . ' ' . $MainCat[$j]['no'];
            }

            DB::table('case_masterlist_field_category')->insert([
                'code' => $Catcode,
                'name' => $MainCat[$j]['name'],
                'order' => $j + 1,
                'status' => 1
            ]);

            $lastId = DB::getPdo()->lastInsertId();

            $cateType = $field1;

            if ($MainCat[$j]['type'] == '2') {
                $cateType = $field2;
            } else if ($MainCat[$j]['type'] == '3') {
                $cateType = $field3;
            } else if ($MainCat[$j]['type'] == '4') {
                $cateType = $field4;
            } else if ($MainCat[$j]['type'] == '5') {
                $cateType = $field5;
            } else if ($MainCat[$j]['type'] == '6') {
                $cateType = $field6;
            } else if ($MainCat[$j]['type'] == '7') {
                $cateType = $field7;
            } else if ($MainCat[$j]['type'] == '8') {
                $cateType = $field8;
            } else if ($MainCat[$j]['type'] == '9') {
                $cateType = $field9;
            }else if ($MainCat[$j]['type'] == '10') {
                $cateType = $field10;
            }else if ($MainCat[$j]['type'] == '11') {
                $cateType = $field10;
            }else if ($MainCat[$j]['type'] == '12') {
                $cateType = $field10;
            }else if ($MainCat[$j]['type'] == '13') {
                $cateType = $field10;
            }

            for ($i = 0; $i < count($cateType); $i++) {

                $code = strtolower($cateType[$i]['name']);
                $code = str_replace(" ", "_", $code);
                $code = str_replace("/", "", $code);
                $code = str_replace("__", "_", $code);

                if (strpos($code, 'income_tax_branch_address') !== false) {
                    $code = str_replace("income_tax_branch_address", "LHDN_address", $code);
                }

                if (strpos($code, 'tax_branch') !== false) {
                    $code = str_replace("income_tax_branch", "LHDN_branch", $code);
                }


                if (array_key_exists("no", $MainCat[$j])) {
                    $code = $code . '_' . $MainCat[$j]['no'];
                    // $cateType[$i]['name'] =  $cateType[$i]['name'] . ' ' . $MainCat[$j]['no'];
                }

                $code = $Catcode . '_' . $code;

                if (array_key_exists("code", $cateType[$i])) {
                    $code = $cateType[$i]['code'];
                }

                // if($cateType[$i]['name'] == 'Purchaser')
                // {
                //     $code = $code.'_'.$cateType[$i]['no'];
                // }


                DB::table('case_masterlist_field')->insert([
                    'case_field_id' => $lastId,
                    'code' => $code,
                    'name' => $cateType[$i]['name'],
                    'type' =>  $cateType[$i]['type'],
                    'status' => 1
                ]);
            }
        }


        // DB::table('case_masterlist_field_category')->insert([ 
        //     'code' => $Catcode,
        //     'name' => $MainCat[$j]['name'],
        //     'status' => 1
        // ]);

    }
}
