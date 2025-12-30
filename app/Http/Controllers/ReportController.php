<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountCategory;
use App\Models\AccountItem;
use App\Models\AccountLog;
use App\Models\AccountTemplateDetails;
use App\Models\AccountTemplateMain;
use App\Models\BankReconRecord;
use App\Models\Branch;
use App\Models\CaseAccountTransaction;
use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\EmailTemplateMain;
use App\Models\DocumentTemplateMain;
use App\Models\DocumentTemplateDetails;
use App\Models\DocumentTemplatePages;
use App\Models\caseTemplate;
use App\Models\Roles;
use App\Models\caseTemplateDetails;
use App\Models\EmailTemplateDetails;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Models\Users;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\LoanCase;
use App\Models\LoanCaseAccount;
use App\Models\LoanCaseBillMain;
use App\Models\LoanCaseBillReferrals;
use App\Models\LoanCaseInvoiceMain;
use App\Models\OfficeBankAccount;
use App\Models\Portfolio;
use App\Models\QuotationTemplateDetails;
use App\Models\QuotationTemplateMain;
use App\Models\TransferFeeDetails;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
// use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls as WriterXls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{

    public static function getCaseReportAccess()
    {
        return 'CaseReport';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $quotations = DB::table('quotation_template_main AS a')
            ->select('a.*')
            ->orderBy('id', 'ASC')
            ->paginate(10);

        $quotations = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no')
            ->where('a.status', '<>', '99')
            ->orderBy('a.id', 'ASC')
            ->get();


        $total_Pfee1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('pfee1_recv');
        $total_Pfee2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('pfee2_recv');
        $total_Pfee = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('pfee_recv');
        $total_disb = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('disb_recv');
        $total_sst = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('sst_recv');
        $total_referral_a1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a1');
        $total_referral_a2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a2');
        $total_referral_a3 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a3');
        $total_referral_a4 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('referral_a4');
        $total_marketing = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('marketing');
        $total_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('total_amt');
        $total_collected_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('collected_amt');
        $total_uncollected = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('uncollected');


        return view('dashboard.summary-report.index', [
            'quotations' => $quotations,
            'total_Pfee' => $total_Pfee,
            'total_Pfee1' => $total_Pfee1,
            'total_Pfee2' => $total_Pfee2,
            'total_disb' => $total_disb,
            'total_sst' => $total_sst,
            'total_amt' => $total_amt,
            'total_uncollected' => $total_uncollected,
            'total_collected_amt' => $total_collected_amt,
            'total_referral_a1' => $total_referral_a1,
            'total_referral_a2' => $total_referral_a2,
            'total_referral_a3' => $total_referral_a3,
            'total_referral_a4' => $total_referral_a4,
            'total_marketing' => $total_marketing
        ]);
    }

    public function reportInvoice()
    {

        $current_user = auth()->user();

        $branchInfo = BranchController::manageBranchAccess();


        if (in_array($current_user->menuroles, ['sales'])) {
            if (!in_array($current_user->id, [51, 32, 127])) {
                return redirect()->route('case.index');
            }
        }

        // $quotations = DB::table('loan_case_bill_main AS a')
        //     ->join('loan_case as c', 'c.id', '=', 'a.case_id')
        //     ->leftJoin('transfer_fee_details as t', 't.loan_case_main_bill_id', '=', 'a.id')
        //     ->select('a.*', 'c.case_ref_no', 't.transfer_amount', 't.sst_amount', 't.is_recon')
        //     ->where('a.status', '<>', '99')
        //     ->where('a.bln_sst', '=', '0')
        //     ->where('a.bln_invoice', '=', '1')
        //     ->where('a.invoice_no', '<>', '')
        //     ->orderBy('a.invoice_no', 'ASC');

        // if (in_array($current_user->menuroles, ['maker'])) {
        //     $quotations = $quotations->where('c.branch_id', '=', $current_user->branch_id);
        // }

        // $quotations = $quotations->get();

        // for ($i = count($quotations) - 1; $i >= 0; $i--) {
        //     $payment_date = '';
        //     $voucher_main = VoucherMain::where('case_bill_main_id', '=', $quotations[$i]->id)->where('status', '=', 4)->get();

        //     for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
        //         $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
        //     }

        //     $quotations[$i]->paydate = $payment_date;
        // }

        // $quotations = DB::table('loan_case_invoice_details AS i')
        // ->join('loan_case_bill_details as d', 'd.id', '=', 'i.loan_case_main_bill_id')
        // ->join('loan_case_bill_main as b', 'b.id', '=', 'i.loan_case_main_bill_id')
        // ->join('loan_case as c', 'c.id', '=', 'b.case_id')
        // ->select('b.*', 'c.case_ref_no')
        // ->where('i.status', '<>', '99')
        // ->orderBy('i.id', 'ASC')
        // ->get();
        // for ($i = 0; $i < count($quotations); $i++)
        // {
        //     $this->updateBillSummary($quotations[$i]->id); 
        // }


        // $total_Pfee1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('pfee1_inv');
        // $total_Pfee2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('pfee2_inv');
        // $total_Pfee = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('pfee1_inv');
        // $total_disb = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('disb_inv');
        // $total_sst = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('sst_inv');
        // $total_referral_a1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a1');
        // $total_referral_a2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a2');
        // $total_referral_a3 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a3');
        // $total_referral_a4 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a4');
        // $total_marketing = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('marketing');
        // $total_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('total_amt_inv');
        // $total_collected_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('collected_amt');
        // $total_uncollected = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('uncollected');


        return view('dashboard.report-invoice.index', [
            'quotations' => [],
            // 'total_Pfee' => $total_Pfee,
            // 'total_Pfee1' => $total_Pfee1,
            // 'total_Pfee2' => $total_Pfee2,
            // 'total_disb' => $total_disb,
            // 'total_sst' => $total_sst,
            // 'total_amt' => $total_amt,
            'current_user' => $current_user,
            'branches' => $branchInfo['branch'],
            // 'total_uncollected' => $total_uncollected,
            // 'total_collected_amt' => $total_collected_amt,
            // 'total_referral_a1' => $total_referral_a1,
            // 'total_referral_a2' => $total_referral_a2,
            // 'total_referral_a3' => $total_referral_a3,
            // 'total_referral_a4' => $total_referral_a4,
            // 'total_marketing' => $total_marketing
        ]);
    }

    public function reportSST()
    {

        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();

        $quotations = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no')
            ->where('a.status', '<>', '99')
            ->where('a.bln_sst', '=', '1')
            ->where('a.invoice_no', '<>', '')
            ->orderBy('a.invoice_no', 'ASC');

        if (in_array($current_user->menuroles, ['maker'])) {
            $quotations = $quotations->where('c.branch_id', '=', $current_user->branch_id);
        }

        $quotations = $quotations->get();



        for ($i = count($quotations) - 1; $i >= 0; $i--) {
            $payment_date = '';
            $voucher_main = VoucherMain::where('case_bill_main_id', '=', $quotations[$i]->id)->where('status', '=', 4)->get();

            for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
                $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
            }

            $quotations[$i]->paydate = $payment_date;
        }


        $total_Pfee1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('pfee1_inv');
        $total_Pfee2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('pfee2_inv');
        $total_Pfee = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('pfee1_inv');
        $total_disb = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('disb_inv');
        $total_sst = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('sst_inv');
        $total_referral_a1 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a1');
        $total_referral_a2 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a2');
        $total_referral_a3 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a3');
        $total_referral_a4 = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('referral_a4');
        $total_marketing = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->sum('marketing');
        $total_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('total_amt_inv');
        $total_collected_amt = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('collected_amt');
        $total_uncollected = DB::table('loan_case_bill_main AS a')->where('status', '<>', '99')->where('invoice_no', '<>', null)->sum('uncollected');


        return view('dashboard.report-sst.index', [
            'quotations' => $quotations,
            'total_Pfee' => $total_Pfee,
            'total_Pfee1' => $total_Pfee1,
            'total_Pfee2' => $total_Pfee2,
            'total_disb' => $total_disb,
            'total_sst' => $total_sst,
            'total_amt' => $total_amt,
            'branchInfo' => $branchInfo['branch'],
            'total_uncollected' => $total_uncollected,
            'total_collected_amt' => $total_collected_amt,
            'total_referral_a1' => $total_referral_a1,
            'total_referral_a2' => $total_referral_a2,
            'total_referral_a3' => $total_referral_a3,
            'total_referral_a4' => $total_referral_a4,
            'total_marketing' => $total_marketing
        ]);
    }

    public function getInvoiceReportV2(Request $request)
    {
        if ($request->ajax()) {
            $current_user = auth()->user();

            $accessInfo = AccessController::manageAccess();

            $rows = DB::table('loan_case_bill_main as b')
                ->leftJoin('loan_case_invoice_main as i', 'i.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
                ->leftJoin('transfer_fee_details as t', 't.loan_case_main_bill_id', '=', 'b.id')
                ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
                ->select('b.*', 'i.*', 'i.id as invoice_id', 'l.case_ref_no', 'c.name as client_name', 't.transfer_amount', 't.sst_amount', 't.is_recon')
                ->where('b.status', '<>',  99)
                // ->where('b.bln_sst', '=', '0')
                ->where('b.invoice_no', '<>', '')
                ->where('b.bln_invoice', '=',  1);

            if ($request->input('branch')) {
                $rows = $rows->where('b.invoice_branch_id', '=', $request->input("branch"));
            }

            if ($request->input("year") <> 0) {
                $rows = $rows->whereYear('b.invoice_date', $request->input("year"));
            }

            if ($request->input("month") <> 0) {
                $rows = $rows->whereMonth('b.invoice_date', $request->input("month"));
            }

            if (in_array($current_user->menuroles, ['maker'])) {
                if (in_array($current_user->branch_id, [5, 6])) {
                    $rows = $rows->whereIn('b.invoice_branch_id', [5, 6]);
                } else {
                    $rows = $rows->whereIn('b.invoice_branch_id', [$current_user->branch_id]);
                }
            } else if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [51, 32, 127])) {
                    $rows = $rows->where('l.sales_user_id', '=',  32);
                }
            } else if (in_array($current_user->menuroles, ['lawyer'])) {
                
                if (in_array($current_user->id, [13]))
                {
                    $rows = $rows->whereIn('l.branch_id', [$current_user->branch_id])->where('l.id', '>=', 2342);
                }
                else
                {
                    $rows = $rows->where('l.sales_user_id', '=',  $current_user->id);
                }
            }

            $rows = $rows->orderBy('b.invoice_no', 'ASC')->get();

            // for ($i = count($rows) - 1; $i >= 0; $i--) { 
            //     $payment_date = '';
            //     $voucher_main = VoucherMain::where('case_bill_main_id', '=', $rows[$i]->id)->where('status', '=', 4)->get();

            //     for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
            //         $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
            //     }

            //     $rows[$i]->paydate = $payment_date;
            // }

            return DataTables::of($rows)
                ->addIndexColumn()
                ->addColumn('action', function ($data) use ($request) {

                    $disabled = '';

                    if ($data->bln_sst == 1) {
                        $disabled = 'disabled';
                    }

                    // Use invoice_id if available, otherwise fall back to bill id
                    $checkbox_value = isset($data->invoice_id) ? $data->invoice_id : $data->id;
                    $checkbox_id = isset($data->invoice_id) ? 'chk_inv_' . $data->invoice_id : 'chk_' . $data->id;
                    
                    $actionBtn = '
                    <div class="checkbox">
                        <input type="checkbox" name="invoice" value="' . $checkbox_value . '" id="' . $checkbox_id . '" ' . $disabled . '>
                        <label for="' . $checkbox_id . '">' . $data->invoice_no . '</label>
                    </div>
                    ';

                    return $actionBtn;
                })
                ->editColumn('transfer_amount', function ($data) {
                    return $data->transfer_amount ?: 0;
                })
                ->editColumn('sst_amount', function ($data) {
                    return $data->sst_amount ?: 0;
                })
                ->editColumn('case_ref_no', function ($data) {

                    return $data->client_name . '<br/><b>Ref No: </b><a href="/case/' . $data->case_id . '">' . $data->case_ref_no . '</a> ';
                })
                ->addColumn('bal_to_transfer', function ($data) {
                    $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv + $data->sst_inv;
                    return $bal_to_transfer;
                })
                ->addColumn('sst_to_transfer', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;


                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    return $sst_to_transfer;
                })

                ->addColumn('bal_to_transfer_v2', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $pf2 = number_format((float)$data->pfee2_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transferred_pfee_amt, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)  - (float)($data->transferred_pfee_amt);

                    $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }
                    // $bal_to_transfer =(float)($data->pfee2_inv);
                    $sst_to_transfer = $data->sst_inv  - $data->transferred_sst_amt;
                    $actionBtn = '' . $bal_to_transfer . '<input class="bal_to_transfer" onchange="balUpdate()" type="hidden" id="ban_to_transfer' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                   
                    <input type="hidden" id="ban_to_transfer_limt_' . $data->id  . '" value = "' . $bal_to_transfer . '" />
                    <input type="hidden" id="sst_to_transfer_' . $data->id  . '" value = "' . $sst_to_transfer . '" />
                    ';
                    return $actionBtn;
                })

                ->addColumn('bal_to_transfer_v3', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $pf2 = number_format((float)$data->pfee2_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)   - (float)($data->transferred_pfee_amt);

                    $bal_to_transfer = (float)($pf1) + (float)($pf2)  - (float)($pftf);

                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }

                    return $bal_to_transfer;
                })
                ->addColumn('cal_pfee_bal', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    // $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $sst_inv = number_format((float)$data->sst_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');


                    // $bal_to_transfer = (float)($data->pfee1_inv) + (float)($data->pfee2_inv)   - (float)($data->transferred_pfee_amt);

                    if ($data->sst_amount == 0) {
                        $bal_to_transfer = (float)($pftf) - (float)($sst_inv);
                    } else {
                        $bal_to_transfer = (float)($pftf);
                    }



                    if ($bal_to_transfer < 0) {
                        $bal_to_transfer = 0.00;
                    }

                    return $bal_to_transfer;
                })
                ->addColumn('cal_sst_bal', function ($data) {
                    // $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transfer_fee_bal;
                    // return $bal_to_transfer;

                    // $pf1 = number_format((float)$data->pfee1_inv, 2, '.', '');
                    $sst_inv = number_format((float)$data->sst_inv, 2, '.', '');
                    $pftf = number_format((float)$data->transfer_amount, 2, '.', '');

                    if ($data->sst_amount == 0) {
                        return $data->sst_inv;
                    } else {
                        return $data->sst_amount;
                    }
                })
                ->addColumn('paid', function ($data) {



                    if ($data->bln_sst == 1)
                        return '<span class="label bg-success">Paid</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->addColumn('pfee_sum', function ($data) {
                    $pfee_sum = $data->pfee1_inv + $data->pfee2_inv;
                    return $pfee_sum;
                })
                // ->addColumn('bal_to_transfer_v2', function ($data) {
                //     $bal_to_transfer = $data->pfee1_inv + $data->pfee2_inv - $data->transferred_pfee_amt;
                //     return $bal_to_transfer;
                // })
                ->addColumn('transferred_to_office_bank', function ($data) {
 

                    if ($data->transferred_to_office_bank == 1)
                        return '<span class="label bg-success">Yes</span>';
                    else
                        return '<span class="label bg-warning">No</span>';
                })
                ->editColumn('invoice_date', function ($data) {
                    $formatedDate = Carbon::createFromFormat('Y-m-d H:i:s', $data->invoice_date)->format('d-m-Y');
                    return $formatedDate;
                })
                ->rawColumns(['action', 'paid', 'bal_to_transfer', 'voucher_type', 'cal_sst_bal', 'cal_pfee_bal', 'transaction_type', 'transferred_to_office_bank', 'case_ref_no', 'bal_to_transfer_v2', 'bal_to_transfer_v3', 'pfee_sum', 'sst_to_transfer'])
                ->make(true);
        }
    }

    public function getInvoiceReport(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $quotations = DB::table('loan_case_bill_main AS a')
                ->join('loan_case as c', 'c.id', '=', 'a.case_id')
                ->select('a.*', 'c.case_ref_no')
                ->where('a.status', '<>', '99')
                // ->where('a.bln_sst', '=', '0')
                ->where('a.bln_invoice', '=', '1')
                // ->where('a.invoice_no', '<>', '')
                ->orderBy('a.invoice_no', 'ASC')
                ->get();

            for ($i = count($quotations) - 1; $i >= 0; $i--) {
                $payment_date = '';
                $voucher_main = VoucherMain::where('case_bill_main_id', '=', $quotations[$i]->id)->where('status', '=', 4)->get();

                for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
                    $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
                }

                $quotations[$i]->paydate = $payment_date;
            }


            return DataTables::of($quotations)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $blnDisable = '';
                    if ($row->id == 1) {
                        $blnDisable = 'disabled';
                    }
                    $actionBtn = '
                    <div class="checkbox">
                        <input type="checkbox" name="invoice" value="' . $row->id . '" id="chk_' . $row->id . '" ' . $blnDisable . '>
                        <label for="chk_' . $row->id . '">' . $row->bill_no . '</label>
                    </div>
                    ';

                    return $actionBtn;
                })
                // ->editColumn('status', function ($data) {
                //     if ($data->status === '0')
                //         return '<span class="label bg-warning">Sending</span>';
                //     elseif ($data->status === '1')
                //         return '<span class="label bg-success">Completed</span>';
                //     else
                //         return '<span class="label bg-info">Dispatch</span>';
                // })
                // ->editColumn('file', function ($data) {
                //     if ($data->file_new_name <> '') {
                //         $actionBtn = ' <a target="_blank" href="app/documents/dispatch/' . $data->file_new_name . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-paperclip"></i></a>';
                //     } else {
                //         $actionBtn = '';
                //     }
                //     return $actionBtn;
                // })
                // ->editColumn('dispatch_type', function ($data) {
                //     if ($data->dispatch_type === '1')
                //         return '<span class="label bg-warning">Outgoing</span>';
                //     elseif ($data->dispatch_type === '2')
                //         return '<span class="label bg-success">Incoming</span>';
                // })
                // ->editColumn('case_ref_no', function ($row) {
                //     if ($row->case_id != 0) {
                //         $actionBtn = ' <a href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' >> </a>';
                //     } else {
                //         $actionBtn = $row->case_ref;
                //     }

                //     return $actionBtn; 
                // })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function getReferralReport(Request $request)
    {
        if ($request->ajax()) {
            $bill_id = [];

            // $LoanCaseBillReferrals = LoanCaseBillReferrals::where('status', 1)->distinct()->get();

            // for ($j = 0; $j < count($LoanCaseBillReferrals); $j++) 
            // {
            //     array_push($bill_id, $LoanCaseBillReferrals[$j]->main_id);
            // }



            $current_user = auth()->user();
            $quotations = DB::table('loan_case_bill_main AS a')
                ->leftJoin('loan_case as c', 'c.id', '=', 'a.case_id')
                ->leftJoin('users as u', 'u.id', '=', 'c.sales_user_id')
                ->leftJoin('client as cl', 'cl.id', '=', 'c.customer_id')
                ->select('a.*', 'c.case_ref_no', 'u.name as marketing_name', 'cl.name as client_name')
                ->where('a.status', '<>', '99');

            if (in_array($current_user->menuroles, ['maker'])) {
                if ($current_user->branch_id == 2) {
                    $quotations = $quotations->where('c.sales_user_id', '=', 13);
                } else  if (in_array($current_user->branch_id, [5, 6])) {
                    $quotations = $quotations->whereIn('c.branch_id', [5, 6]);
                } else {
                    $quotations = $quotations->where('c.branch_id', '=', $current_user->branch_id);
                }
            }

            


            if ($request->input('trx_id') <> 0) {
                $quotations = $quotations->where(function ($q) use ($request) {
                    $q->where('a.referral_a1_trx_id', 'like', '%' . $request->input("trx_id") . '%')
                        ->orWhere('a.referral_a2_trx_id', 'like', '%' . $request->input("trx_id") . '%')
                        ->orWhere('a.referral_a3_trx_id', 'like', '%' . $request->input("trx_id") . '%')
                        ->orWhere('a.referral_a4_trx_id', 'like', '%' . $request->input("trx_id") . '%')
                        ->orWhere('a.disb_trx_id', 'like', '%' . $request->input("trx_id") . '%')
                        ->orWhere('a.marketing_trx_id', 'like', '%' . $request->input("trx_id") . '%')
                        ->orWhere('a.other_trx_id', 'like', '%' . $request->input("trx_id") . '%');
                });
            } else {
                if ($request->input('referral_input') == '1') {
                    $quotations = $quotations->where(function ($q) use ($current_user) {
                        $q->where('a.referral_a1', '<>', 0)
                            ->orWhere('a.referral_a2', '<>', 0)
                            ->orWhere('a.referral_a3', '<>', 0)
                            ->orWhere('a.referral_a4', '<>', 0);

                        if (in_array($current_user->menuroles, ['maker'])) {
                            $q = $q->where('c.branch_id', '=', $current_user->branch_id);
                        }
                    });
                } elseif ($request->input('referral_input') == '0') {
                    $quotations = $quotations->where(function ($q) use ($current_user) {
                        $q->where('a.referral_a1', '=', 0)
                            ->where('a.referral_a2', '=', 0)
                            ->where('a.referral_a3', '=', 0)
                            ->where('a.referral_a4', '=', 0);

                        if (in_array($current_user->menuroles, ['maker'])) {
                            $q = $q->where('c.branch_id', '=', $current_user->branch_id);
                        }
                    });
                }

                if ($request->input('paid') == '0') {

                    $quotations = $quotations->where(function ($q) use ($current_user) {
                        $q->where('a.referral_a1_payment_date', '=', null)
                            ->orWhereNull('referral_a2_payment_date')
                            ->orWhereNull('referral_a3_payment_date')
                            ->orWhereNull('referral_a4_payment_date')
                            ->orWhereNull('marketing_payment_date');

                        if (in_array($current_user->menuroles, ['maker'])) {
                            $q = $q->where('c.branch_id', '=', $current_user->branch_id);
                        }
                    });
                } elseif ($request->input('paid') == '1') {
                    $quotations = $quotations->where(function ($q) use ($current_user) {
                        $q->where('a.referral_a1_payment_date', '<>', null)
                            ->orWhereNotNull('referral_a2_payment_date')
                            ->orWhereNotNull('referral_a3_payment_date')
                            ->orWhereNotNull('referral_a4_payment_date')
                            ->orWhereNotNull('marketing_payment_date');

                        if (in_array($current_user->menuroles, ['maker'])) {
                            $q = $q->where('c.branch_id', '=', $current_user->branch_id);
                        }
                    });
                }

                if ($request->input("branch")) {
                    $quotations = $quotations->where('c.branch_id', $request->input("branch"));
                }


                if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
                    $quotations = $quotations->where(function ($q) use ($request, $current_user) {
                        $q->whereBetween('a.referral_a1_payment_date', [$request->input("date_from"), $request->input("date_to")])
                            ->orWhereBetween('a.referral_a2_payment_date', [$request->input("date_from"), $request->input("date_to")])
                            ->orWhereBetween('a.referral_a3_payment_date', [$request->input("date_from"), $request->input("date_to")])
                            ->orWhereBetween('a.referral_a4_payment_date', [$request->input("date_from"), $request->input("date_to")])
                            ->orWhereBetween('a.marketing_payment_date', [$request->input("date_from"), $request->input("date_to")]);

                        if (in_array($current_user->menuroles, ['maker'])) {
                            $q = $q->where('c.branch_id', '=', $current_user->branch_id);
                        }
                    });
                } else {
                    if ($request->input("date_from") <> null) {

                        $quotations = $quotations->where(function ($q) use ($request, $current_user) {
                            $q->where('a.referral_a1_payment_date', '>=', $request->input("date_from"))
                                ->orWhere('a.referral_a2_payment_date', '>=', $request->input("date_from"))
                                ->orWhere('a.referral_a3_payment_date', '>=', $request->input("date_from"))
                                ->orWhere('a.referral_a4_payment_date', '>=', $request->input("date_from"))
                                ->orWhere('a.marketing_payment_date', '>=', $request->input("date_from"));

                            if (in_array($current_user->menuroles, ['maker'])) {
                                $q = $q->where('c.branch_id', '=', $current_user->branch_id);
                            }
                        });
                    }

                    if ($request->input("date_to") <> null) {
                        // $quotations = $quotations->where('a.created_at', '<=', $request->input("date_to"));

                        $quotations = $quotations->where(function ($q) use ($request, $current_user) {
                            $q->where('a.referral_a1_payment_date', '<=', $request->input("date_from"))
                                ->orWhere('a.referral_a2_payment_date', '<=', $request->input("date_from"))
                                ->orWhere('a.referral_a3_payment_date', '<=', $request->input("date_from"))
                                ->orWhere('a.referral_a4_payment_date', '<=', $request->input("date_from")
                                    ->orWhere('a.marketing_payment_date', '<=', $request->input("date_from")));

                            if (in_array($current_user->menuroles, ['maker'])) {
                                $q = $q->where('c.branch_id', '=', $current_user->branch_id);
                            }
                        });
                    }
                }
            }

            if (in_array($current_user->menuroles, ['sales'])) {
                if (in_array($current_user->id, [51, 32, 127])) {
                    $quotations = $quotations->whereIN('c.branch_id', [5, 6]);
                }
            } else  if (in_array($current_user->menuroles, ['lawyer'])) {
                // $quotations = $quotations->whereIN('c.branch_id', [$current_user->branch_id]);
                $quotations = $quotations->whereIN('c.sales_user_id', [$current_user->id]);
            }



            // if (in_array($current_user->menuroles, ['maker'])) {
            //     $quotations = $quotations->where('c.branch_id', '=', $current_user->branch_id);
            // }


            $quotations = $quotations->orderBy('a.invoice_no', 'desc')->get();

            // for ($i = count($quotations) - 1; $i >= 0; $i--) {
            //     $payment_date = '';
            //     $voucher_main = VoucherMain::where('case_bill_main_id', '=', $quotations[$i]->id)->where('status', '=', 4)->get();

            //     for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
            //         $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
            //     }

            //     $quotations[$i]->paydate = $payment_date;
            // }


            return DataTables::of($quotations)
                ->addIndexColumn()
                // ->addColumn('action', function ($row) {
                //     $actionBtn = ' <a href="/dispatch/' . $row->id . '/edit" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-pencil"></i></a>';

                //     $actionBtn = '<div class="btn-group">
                //     <button type="button" class="btn btn-info btn-flat">Action</button>
                //     <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                //       <span class="caret"></span>
                //       <span class="sr-only">Toggle Dropdown</span>
                //     </button>
                //     <div class="dropdown-menu">
                //       <a class="dropdown-item" href="/dispatch/' . $row->id . '/edit" ><i class="cil-pencil"></i>Edit</a>
                //       <div class="dropdown-divider"></div>
                //       <a class="dropdown-item" href="javascript:void(0)" onclick="deleteDispatch(' . $row->id . ')" ><i class="cil-x"></i>Delete</a>
                //     </div>
                //   </div>
                //     ';

                //     return $actionBtn;
                // })
                // ->editColumn('status', function ($data) {
                //     if ($data->status === '0')
                //         return '<span class="label bg-warning">Sending</span>';
                //     elseif ($data->status === '1')
                //         return '<span class="label bg-success">Completed</span>';
                //     else
                //         return '<span class="label bg-info">Dispatch</span>';
                // })
                // ->editColumn('file', function ($data) {
                //     if ($data->file_new_name <> '') {
                //         $actionBtn = ' <a target="_blank" href="app/documents/dispatch/' . $data->file_new_name . '" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Edit"><i class="cil-paperclip"></i></a>';
                //     } else {
                //         $actionBtn = '';
                //     }
                //     return $actionBtn;
                // })
                // ->editColumn('dispatch_type', function ($data) {
                //     if ($data->dispatch_type === '1')
                //         return '<span class="label bg-warning">Outgoing</span>';
                //     elseif ($data->dispatch_type === '2') 
                //         return '<span class="label bg-success">Incoming</span>';
                // })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . '>> </a><br/>';
                        $actionBtn .= '<b>Client: </b>' . $row->client_name . '<br/>';
                        $actionBtn .= '<b>Bill No: </b>' . $row->bill_no . '<br/>';
                        $actionBtn .= '<b>Invoice No: </b>' . $row->invoice_no;
                    } else {
                        $actionBtn = $row->case_ref;
                    }

                    return $actionBtn;
                })
                ->addColumn('referral_a1_data', function ($data) {

                    $name = '-';
                    if ($data->referral_a1_id != '0' && $data->referral_a1_id != '') {
                        $name = $data->referral_a1_id;
                    }

                    $returnVal = '';

                    $returnVal .= '<b>Name: </b>' . $name . '<br/>';
                    $returnVal .= '<b>TRX ID: </b>' . $data->referral_a1_trx_id . '<br/>';
                    $returnVal .= '<b>Payment Date: </b>' . $data->referral_a1_payment_date . '<br/>';

                    if ($data->referral_a1_payment_date == '') {
                        if ($data->referral_a1_id != '0' && $data->referral_a1_id != '') {
                            $returnVal .= '<span class="label bg-warning">Unpaid</span>';
                        }
                    } else {

                        $returnVal .= '<span class="label bg-success">Paid</span>';
                    }

                    return $returnVal;
                })
                ->addColumn('referral_a2_data', function ($data) {

                    $name = '-';
                    if ($data->referral_a2_id != '0' && $data->referral_a2_id != '') {
                        $name = $data->referral_a2_id;
                    }

                    $returnVal = '';

                    $returnVal .= '<b>Name: </b>' . $name . '<br/>';
                    $returnVal .= '<b>TRX ID: </b>' . $data->referral_a2_trx_id . '<br/>';
                    $returnVal .= '<b>Payment Date: </b>' . $data->referral_a2_payment_date . '<br/>';

                    if ($data->referral_a2_payment_date == '') {
                        if ($data->referral_a2_id != '0' && $data->referral_a2_id != '') {
                            $returnVal .= '<span class="label bg-warning">Unpaid</span>';
                        }
                    } else {

                        $returnVal .= '<span class="label bg-success">Paid</span>';
                    }

                    return $returnVal;
                })
                ->addColumn('referral_a3_data', function ($data) {

                    $name = '-';
                    if ($data->referral_a3_id != '0') {
                        $name = $data->referral_a3_id;
                    }

                    $returnVal = '';

                    $returnVal .= '<b>Name: </b>' . $name . '<br/>';
                    $returnVal .= '<b>TRX ID: </b>' . $data->referral_a3_trx_id . '<br/>';
                    $returnVal .= '<b>Payment Date: </b>' . $data->referral_a3_payment_date . '<br/>';

                    if ($data->referral_a3_payment_date == '') {
                        if ($data->referral_a3_id != '0' && $data->referral_a3_id != '') {
                            $returnVal .= '<span class="label bg-warning">Unpaid</span>';
                        }
                    } else {

                        $returnVal .= '<span class="label bg-success">Paid</span>';
                    }

                    return $returnVal;
                })
                ->addColumn('referral_a4_data', function ($data) {

                    $name = '-';
                    if ($data->referral_a1_id != '0') {
                        $name = $data->referral_a4_id;
                    }

                    $returnVal = '';

                    $returnVal .= '<b>Name: </b>' . $name . '<br/>';
                    $returnVal .= '<b>TRX ID: </b>' . $data->referral_a4_trx_id . '<br/>';
                    $returnVal .= '<b>Payment Date: </b>' . $data->referral_a4_payment_date . '<br/>';

                    if ($data->referral_a4_payment_date == '') {
                        if ($data->referral_a4_id != '0' && $data->referral_a4_id != '') {
                            $returnVal .= '<span class="label bg-warning">Unpaid</span>';
                        }
                    } else {

                        $returnVal .= '<span class="label bg-success">Paid</span>';
                    }

                    return $returnVal;
                })

                ->addColumn('marketing_data', function ($data) {

                    $returnVal = '';

                    $returnVal .= '<b>Name: </b>' . $data->marketing_name . '<br/>';
                    $returnVal .= '<b>TRX ID: </b>' . $data->marketing_trx_id . '<br/>';
                    $returnVal .= '<b>Payment Date: </b>' . $data->marketing_payment_date . '<br/>';

                    if ($data->marketing_payment_date == '') {
                        $returnVal .= '<span class="label bg-warning">Unpaid</span>';
                    } else {

                        $returnVal .= '<span class="label bg-success">Paid</span>';
                    }

                    return $returnVal;
                })

                ->addColumn('disb_data', function ($data) {

                    $returnVal = '';

                    $returnVal .= '<b>Name: </b>' . $data->disb_name . '<br/>';
                    $returnVal .= '<b>TRX ID: </b>' . $data->disb_trx_id . '<br/>';
                    $returnVal .= '<b>Payment Date: </b>' . $data->disb_payment_date . '<br/>';

                    if ($data->disb_payment_date == '') {
                        $returnVal .= '<span class="label bg-warning">Unpaid</span>';
                    } else {

                        $returnVal .= '<span class="label bg-success">Paid</span>';
                    }

                    return $returnVal;
                })

                ->addColumn('other_data', function ($data) {

                    $returnVal = '';

                    $returnVal .= '<b>Name: </b>' . $data->other_name . '<br/>';
                    $returnVal .= '<b>TRX ID: </b>' . $data->other_trx_id . '<br/>';
                    $returnVal .= '<b>Payment Date: </b>' . $data->other_payment_date . '<br/>';

                    if ($data->other_payment_date == '') {
                        $returnVal .= '<span class="label bg-warning">Unpaid</span>';
                    } else {

                        $returnVal .= '<span class="label bg-success">Paid</span>';
                    }

                    return $returnVal;
                })
                ->editColumn('referral_a1_id', function ($data) {
                    if ($data->referral_a1_id == '0') {
                        return '-';
                    } else {
                        return $data->referral_a1_id;
                    }
                })
                ->editColumn('referral_a2_id', function ($data) {
                    if ($data->referral_a2_id == '0') {
                        return '-';
                    } else {
                        return $data->referral_a2_id;
                    }
                })
                ->editColumn('referral_a3_id', function ($data) {
                    if ($data->referral_a3_id == '0') {
                        return '-';
                    } else {
                        return $data->referral_a3_id;
                    }
                })
                ->editColumn('referral_a4_id', function ($data) {
                    if ($data->referral_a4_id == '0') {
                        return '-';
                    } else {
                        return $data->referral_a4_id;
                    }
                })
                ->editColumn('referral_a1_payment_status', function ($data) {
                    if ($data->referral_a1_payment_date == '') {
                        return '-';
                    } else {
                        return '<span class="label bg-success">Paid</span>';
                    }
                })
                ->editColumn('referral_a2_payment_status', function ($data) {
                    if ($data->referral_a2_payment_date == '') {
                        return '-';
                    } else {
                        return '<span class="label bg-success">Paid</span>';
                    }
                })
                ->editColumn('referral_a3_payment_status', function ($data) {
                    if ($data->referral_a3_payment_date == '') {
                        return '-';
                    } else {
                        return '<span class="label bg-success">Paid</span>';
                    }
                })
                ->editColumn('referral_a4_payment_status', function ($data) {
                    if ($data->referral_a4_payment_date == '') {
                        return '-';
                    } else {
                        return '<span class="label bg-success">Paid</span>';
                    }
                })
                // ->editColumn('marketing_payment_status', function ($data) {
                //     if ($data->marketing_payment_date == '') {
                //         return '-';
                //     } else {
                //         return '<span class="label bg-success">Paid</span>';
                //     }
                // })
                // ->editColumn('marketing_payment_status', function ($data) {
                //     if ($data->marketing_payment_status == '') {
                //         return '-';
                //     } else {
                //         return '<span class="label bg-success">Paid</span>';
                //     }
                // })
                ->rawColumns([
                    'action', 'case_ref_no', 'referral_a1_id', 'referral_a2_id', 'referral_a3_id',
                    'referral_a4_id', 'referral_a1_payment_status', 'referral_a2_payment_status', 'referral_a3_payment_status',
                    'referral_a4_payment_status0', 'referral_a1_data', 'referral_a2_data', 'referral_a3_data', 'referral_a4_data', 'marketing_data', 'disb_data','other_data'
                ])
                ->make(true);
        }
    }


    public function downloadReferral(Request $request)
    {
        $quotations = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.marketing_id')
            ->select('a.*', 'c.case_ref_no', 'u.name as marketing_name')
            ->where('a.status', '<>', '99');


        if ($request->input('referral_input') == '1') {
            $quotations = $quotations->where(function ($q) {
                $q->where('a.referral_a1', '<>', 0)
                    ->orWhere('a.referral_a2', '<>', 0)
                    ->orWhere('a.referral_a3', '<>', 0)
                    ->orWhere('a.referral_a4', '<>', 0);
            });
        } elseif ($request->input('referral_input') == '0') {
            $quotations = $quotations->where(function ($q) {
                $q->where('a.referral_a1', '=', 0)
                    ->where('a.referral_a2', '=', 0)
                    ->where('a.referral_a3', '=', 0)
                    ->where('a.referral_a4', '=', 0);
            });
        }

        if ($request->input('paid') == '0') {
            $quotations = $quotations->where('a.referral_a1_payment_date', '=', null)->where('a.referral_a2_payment_date', '=', null)
                ->where('a.referral_a3_payment_date', '=', null)
                ->where('a.referral_a4_payment_date', '=', null);
        } elseif ($request->input('paid') == '1') {
            $quotations = $quotations->where(function ($q) {
                $q->where('a.referral_a1_payment_date', '<>', null)
                    ->orWhere('a.referral_a2_payment_date', '<>', null)
                    ->orWhere('a.referral_a3_payment_date', '<>', null)
                    ->orWhere('a.referral_a4_payment_date', '<>', null);
            });
        }


        if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            $quotations = $quotations->whereBetween('a.created_at', [$request->input("date_from"), $request->input("date_to")]);
        } else {
            if ($request->input("date_from") <> null) {
                $quotations = $quotations->where('a.created_at', '>=', $request->input("date_from"));
            }

            if ($request->input("date_to") <> null) {
                $quotations = $quotations->where('a.created_at', '<=', $request->input("date_to"));
            }
        }

        if ($request->input("branch")) {
            $quotations = $quotations->where('c.branch_id', $request->input("branch"));
        }


        $quotations = $quotations->orderBy('a.invoice_no', 'ASC')->get();

        for ($i = count($quotations) - 1; $i >= 0; $i--) {
            $payment_date = '';
            $voucher_main = VoucherMain::where('case_bill_main_id', '=', $quotations[$i]->id)->where('status', '=', 4)->get();

            for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
                $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
            }

            $quotations[$i]->paydate = $payment_date;
        }

        // $data_array[] = Schema::getColumnListing('loan_case_bill_main');

        $data_array[] = array("Bill No", "Ref No", "Referral 1", "", "", "", "Referral 2", "", "", "", "Referral 3", "", "", "", "Referral 4", "", "", "", "Marketing", "", "");
        $data_array[] = array("", "", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date");

        foreach ($quotations as $data_item) {
            $referral_a1_payment_status = '-';
            $referral_a2_payment_status = '-';
            $referral_a3_payment_status = '-';
            $referral_a4_payment_status = '-';

            if ($data_item->referral_a1_payment_date <> null) {
                $referral_a1_payment_status = 'Paid';
            }
            if ($data_item->referral_a2_payment_date <> null) {
                $referral_a2_payment_status = 'Paid';
            }
            if ($data_item->referral_a3_payment_date <> null) {
                $referral_a3_payment_status = 'Paid';
            }
            if ($data_item->referral_a4_payment_date <> null) {
                $referral_a4_payment_status = 'Paid';
            }
            $data_array[] = array(
                $data_item->bill_no,
                $data_item->case_ref_no,
                $data_item->referral_a1,
                $data_item->referral_a1_id,
                $data_item->referral_a1_payment_date,
                $referral_a1_payment_status,
                $data_item->referral_a2,
                $data_item->referral_a2_id,
                $data_item->referral_a2_payment_date,
                $referral_a2_payment_status,
                $data_item->referral_a3,
                $data_item->referral_a3_id,
                $data_item->referral_a3_payment_date,
                $referral_a3_payment_status,
                $data_item->referral_a4,
                $data_item->referral_a4_id,
                $data_item->referral_a4_payment_date,
                $referral_a4_payment_status,
                $data_item->marketing,
                $data_item->marketing_name,
                $data_item->marketing_payment_date,
            );
        }

        // return FacadesExcel::download(new $quotations, 'users.xlsx');

        // Excel::create('Programma_Data', function($excel) use ($quotations)
        // {
        // 	$excel->setTitle('Apotelesmata');
        // 	$excel->sheet('Programma_Data', function($sheet) use ($quotations)
        // 	{
        // 		$sheet->fromArray($quotations, null, 'A1', false, false);
        // 	});
        // }) -> download('xlsx');

        $spreadSheet = new Spreadsheet();

        $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
        $spreadSheet->getActiveSheet()->fromArray($data_array);

        $spreadSheet->getActiveSheet()->mergeCells('A1:A2');
        $spreadSheet->getActiveSheet()->mergeCells('B1:B2');
        $spreadSheet->getActiveSheet()->mergeCells('C1:F1');
        $spreadSheet->getActiveSheet()->mergeCells('G1:J1');
        $spreadSheet->getActiveSheet()->mergeCells('K1:N1');
        $spreadSheet->getActiveSheet()->mergeCells('O1:R1');
        $spreadSheet->getActiveSheet()->mergeCells('S1:U1');

        $spreadSheet
            ->getActiveSheet()
            ->getStyle('A1:U1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);

        $spreadSheet
            ->getActiveSheet()
            ->getStyle('A2:U2')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);

        $spreadSheet->getActiveSheet()->getStyle('A1:U1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $spreadSheet->getActiveSheet()->getStyle('A1:U1')->getAlignment()->setHorizontal('center');
        $spreadSheet->getActiveSheet()->getStyle('A2:U2')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $spreadSheet->getActiveSheet()->getStyle('A2:U2')->getAlignment()->setHorizontal('center');

        $spreadSheet->getActiveSheet()->getStyle('A1:Z999')->getAlignment()->setWrapText(true);


        $Excel_writer = new WriterXls($spreadSheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Customer_ExportedData.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $Excel_writer->save('php://output');
        exit();


        return Excel::download(new User(), 'users.xlsx');
    }

    public function downloadSummary(Request $request)
    {

        $CaseBill = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no')
            ->where('a.status', '<>', '99')
            ->where('a.bln_invoice', '=', 1);


        // $CaseBill = DB::table('loan_case_bill_main AS a')
        //     ->join('loan_case as c', 'c.id', '=', 'a.case_id')
        //     ->select('a.*', 'c.case_ref_no')
        //     ->where('a.status', '<>', '99')
        // ->where('a.bln_invoice', '=', 1);


        if ($request->input("user") <> 0) {
            $users = User::where('id', '=', $request->input("user"))->first();

            if ($users) {
                if ($users->menuroles == 'lawyer') {
                    $CaseBill = $CaseBill->where('c.lawyer_id', '=', $request->input("user"));
                } else if ($users->menuroles == 'clerk') {
                    $CaseBill = $CaseBill->where('c.clerk_id', '=', $request->input("user"));
                }
            }
        }

        if ($request->input("branch") <> 0) {
            $CaseBill = $CaseBill->where('a.invoice_branch_id', '=', $request->input("branch"));
        }


        $CaseBill = $CaseBill->orderBy('a.invoice_no', 'ASC')->get();

        for ($i = count($CaseBill) - 1; $i >= 0; $i--) {
            $payment_date = '';
            $voucher_main = VoucherMain::where('case_bill_main_id', '=', $CaseBill[$i]->id)->where('status', '=', 4)->get();

            for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
                $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
            }

            $CaseBill[$i]->paydate = $payment_date;
        }

        // $data_array[] = Schema::getColumnListing('loan_case_bill_main');

        // $data_array[] = array("Ref No", "P1", "P1 Receipt Date", "P2 Receipt TRX ID", "P2", "P2 Receipt Date", "P2 Receipt TRX ID", 
        // "SST", "SST PYMT Date", "SST TRX ID", "R1", "R1 (Agent)", "R1 PYMT DATE", "R1 PYMT TRX ID", "R2", "R2 (Agent)", "R2 PYMT DATE", "R2 PYMT TRX ID", 
        // "R3", "R3 (Agent)", "R3 PYMT DATE", "R3 PYMT TRX ID", "R4", "R4 (Agent)", "R4 PYMT DATE", "R4 PYMT TRX ID", 
        // "Uncollected", "Finaced Fees (RM)", "Finaced Sum (RM)", "Prof Balance", "Staff Bonus(2%)", 
        // "Staff Bonus(2%) Claim status", "Staff Bonus(3%)", "Staff Bonus(3%) Claim status", "Staff Bonus(2%) P1", "Staff Bonus(3%) P1", 
        // "Lawyer Bonus(2%)", "Lawyer Bonus(2%) Claim status", "Lawyer Bonus(3%)", "Lawyer Bonus(3%) Claim status", "Lawyer Bonus(2%) P1", 
        // "Lawyer Bonus(3%) P1", "Disbursement", "Disbursement Used", "Balance Disbursement", "Actual Balance", "Advance");

        $data_array[] = array(
            "Ref No", "Invoice No", "P1",  "P2",
            "SST",  "R1", "R1 (Agent)", "R1 PYMT DATE", "R1 PYMT TRX ID", "R2", "R2 (Agent)", "R2 PYMT DATE", "R2 PYMT TRX ID",
            "R3", "R3 (Agent)", "R3 PYMT DATE", "R3 PYMT TRX ID", "R4", "R4 (Agent)", "R4 PYMT DATE", "R4 PYMT TRX ID",
            "Staff Bonus(2%)", "Staff Bonus(3%)",
            "Uncollected", "Finaced Fees (RM)", "Finaced Sum (RM)", "Prof Balance",  "Disbursement", "Disbursement Used", "Balance Disbursement", "Actual Balance",
        );


        // $data_array[] = array("", "", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date", "Status", "Amount", "Name", "Payment Date");

        foreach ($CaseBill as $data_item) {
            $staff_bonuss_2_per_paid = '-';
            $staff_bonuss_3_per_paid = '-';
            $lawyer_bonuss_2_per_paid = '-';
            $lawyer_bonuss_3_per_paid = '-';

            if ($data_item->staff_bonuss_2_per_paid <> 0) {
                $staff_bonuss_2_per_paid = 'Paid';
            }
            if ($data_item->staff_bonuss_3_per_paid <> 0) {
                $staff_bonuss_3_per_paid = 'Paid';
            }
            if ($data_item->lawyer_bonuss_2_per_paid <> 0) {
                $lawyer_bonuss_2_per_paid = 'Paid';
            }
            if ($data_item->lawyer_bonuss_3_per_paid <> 0) {
                $lawyer_bonuss_3_per_paid = 'Paid';
            }


            $prof_bal = $data_item->pfee1 + $data_item->pfee2 - $data_item->referral_a1 - $data_item->referral_a2 - $data_item->referral_a3 - $data_item->referral_a4 - $data_item->marketing - $data_item->uncollected;

            $disb_bal = $data_item->disb - $data_item->used_amt;

            $actual_bal = $prof_bal + $disb_bal;
            $actual_bal_deduct_bonus = $actual_bal - ($data_item->staff_bonus_2_per + $data_item->staff_bonus_3_per + $data_item->lawyer_bonus_2_per + $data_item->lawyer_bonus_3_per);
            $actual_bal_deduct_p1_bonus = $actual_bal - ($data_item->staff_bonus_2_per_p1 + $data_item->staff_bonus_3_per_p1 + $data_item->lawyer_bonus_2_per_p1 + $data_item->lawyer_bonus_3_per_p1);


            $data_array[] = array(
                $data_item->case_ref_no,
                $data_item->invoice_no,
                $data_item->pfee1,
                // $data_item->pfee1_receipt_date,
                // $data_item->pfee1_receipt_trx_id,
                $data_item->pfee2,
                // $data_item->pfee2_receipt_date,
                // $data_item->pfee2_receipt_trx_id,
                $data_item->sst,
                // $data_item->sst_payment_date,
                // $data_item->sst_trx_id,
                $data_item->referral_a1,
                $data_item->referral_a1_id,
                $data_item->referral_a1_payment_date,
                $data_item->referral_a1_trx_id,
                $data_item->referral_a2,
                $data_item->referral_a2_id,
                $data_item->referral_a2_payment_date,
                $data_item->referral_a2_trx_id,
                $data_item->referral_a3,
                $data_item->referral_a3_id,
                $data_item->referral_a3_payment_date,
                $data_item->referral_a3_trx_id,
                $data_item->referral_a4,
                $data_item->referral_a4_id,
                $data_item->referral_a4_payment_date,
                $data_item->referral_a4_trx_id,
                $data_item->staff_bonus_2_per,
                $data_item->staff_bonus_3_per,
                // $data_item->staff_bonus_2_per_p1,
                // $data_item->staff_bonus_3_per_p1,
                // $data_item->lawyer_bonus_2_per,
                // $data_item->lawyer_bonus_3_per,
                // $data_item->lawyer_bonus_2_per_p1,
                // $data_item->lawyer_bonus_3_per_p1,
                $data_item->uncollected,
                $data_item->financed_fee,
                $data_item->financed_sum,
                $prof_bal,
                // $data_item->staff_bonus_2_per,
                // $staff_bonuss_2_per_paid,
                // $data_item->staff_bonus_2_per,
                // $staff_bonuss_3_per_paid,
                // $data_item->staff_bonus_2_per_p1,
                // $data_item->staff_bonus_3_per_p1,
                // $data_item->lawyer_bonus_2_per,
                // $lawyer_bonuss_2_per_paid,
                // $data_item->lawyer_bonus_2_per,
                // $lawyer_bonuss_3_per_paid,
                // $data_item->lawyer_bonus_2_per_p1,
                // $data_item->lawyer_bonus_3_per_p1,
                $data_item->disb,
                $data_item->used_amt,
                $disb_bal,
                $prof_bal,
                // $actual_bal_deduct_bonus,
                // $actual_bal_deduct_p1_bonus,
                // $data_item->adv,
            );
        }


        $spreadSheet = new Spreadsheet();

        $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
        $spreadSheet->getActiveSheet()->fromArray($data_array);

        $spreadSheet
            ->getActiveSheet()
            ->getStyle('A1:AI1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);


        $spreadSheet->getActiveSheet()->getStyle('A1:AI1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $spreadSheet->getActiveSheet()->getStyle('A1:AI1')->getAlignment()->setHorizontal('center');


        $spreadSheet->getActiveSheet()->getStyle('A1:Z999')->getAlignment()->setWrapText(true);


        $Excel_writer = new WriterXls($spreadSheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="summary_report' . date('Y-m-d H:i:s') . '.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $Excel_writer->save('php://output');
        exit();


        return Excel::download(new User(), 'users.xlsx');
    }

    public function filterInvoiceReport(Request $request)
    {

        $current_user = auth()->user();
        $quotations = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no')
            ->where('a.status', '<>', '99')
            ->where('a.bln_sst', '=', 0)
            ->where('a.bln_invoice', '=', 1)
            ->where('a.invoice_no', '<>', '');

        if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            $quotations = $quotations->whereBetween('payment_receipt_date', [$request->input("date_from"), $request->input("date_to")]);
        } else {
            if ($request->input("date_from") <> null) {
                $quotations = $quotations->where('payment_receipt_date', '>=', $request->input("date_from"));
            }

            if ($request->input("date_to") <> null) {
                $quotations = $quotations->where('payment_receipt_date', '<=', $request->input("date_to"));
            }
        }

        if ($request->input("ref_no") <> '') {
            $quotations = $quotations->where('c.case_ref_no', 'like', '%' . $request->input("ref_no") . '%');
        }

        if (in_array($current_user->menuroles, ['maker'])) {
            $quotations =  $quotations->where('c.branch_id', '=', $current_user->branch_id);
        } else {
            if ($request->input("branch") <> 0) {
                $quotations = $quotations->where('c.branch_id', '=', $request->input("branch"));
            }
        }

        $quotations = $quotations->orderBy('a.invoice_no', 'ASC')->get();

        for ($i = count($quotations) - 1; $i >= 0; $i--) {
            $payment_date = '';
            $voucher_main = VoucherMain::where('case_bill_main_id', '=', $quotations[$i]->id)->where('status', '=', 4)->get();

            if ($voucher_main <> null) {
                for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
                    $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
                }
            }



            $quotations[$i]->paydate = $payment_date;
        }

        return response()->json([
            'view' => view('dashboard.report-invoice.tbl-invoice-report', compact('quotations'))->render(),
        ]);
    }

    public function filterSSTReport(Request $request)
    {

        $current_user = auth()->user();
        $quotations = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no')
            ->where('a.status', '<>', '99')
            ->where('a.bln_sst', '=', '1')
            ->where('a.invoice_no', '<>', '');

        if ($request->input("date_from") <> null && $request->input("date_to") <> null) {
            $quotations = $quotations->whereBetween('payment_receipt_date', [$request->input("date_from"), $request->input("date_to")]);
        } else {
            if ($request->input("date_from") <> null) {
                $quotations = $quotations->where('payment_receipt_date', '>=', $request->input("date_from"));
            }

            if ($request->input("date_to") <> null) {
                $quotations = $quotations->where('payment_receipt_date', '<=', $request->input("date_to"));
            }
        }

        if ($request->input("branch") <> 0) {
            $quotations->where('a.invoice_branch_id', '=', $request->input("branch"));
        }

        $quotations = $quotations->orderBy('a.invoice_no', 'ASC')->get();

        for ($i = count($quotations) - 1; $i >= 0; $i--) {
            $payment_date = '';
            $voucher_main = VoucherMain::where('case_bill_main_id', '=', $quotations[$i]->id)->where('status', '=', 4)->get();



            // $voucher_main->whereBetween('payment_receipt_date',[$from, $to])->get();

            if ($voucher_main <> null) {
                for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
                    $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
                }
            }



            $quotations[$i]->paydate = $payment_date;
        }

        return response()->json([
            'view' => view('dashboard.report-invoice.tbl-invoice-report', compact('quotations'))->render(),
        ]);
    }

    public function reportReferral()
    {
        $current_user = auth()->user();

        if (in_array($current_user->menuroles, ['sales'])) {
            if (!in_array($current_user->id, [51, 32, 127])) {
                return redirect()->route('case.index');
            }
        }
 
        $branchInfo = BranchController::manageBranchAccess(); 

        return view('dashboard.report-referral.index', [
            'branchs' => $branchInfo['branch'],
        ]);
    }

    public function updatePaidStatus(Request $request)
    {
        if ($request->input('invoice_id') != null) {
            $invoice_id = json_decode($request->input('invoice_id'), true);
        }

        if (count($invoice_id) > 0) {


            for ($i = 0; $i < count($invoice_id); $i++) {
                // invoice_id is the ID from loan_case_invoice_main
                $invoice_main_id = $invoice_id[$i]['invoice_id'];

                // Find the invoice record first
                $LoanCaseInvoiceMain = LoanCaseInvoiceMain::where('id', '=', $invoice_main_id)->first();

                if ($LoanCaseInvoiceMain) {
                    // Update bln_sst in loan_case_invoice_main
                    $LoanCaseInvoiceMain->bln_sst = 1;
                    $LoanCaseInvoiceMain->updated_at = date('Y-m-d H:i:s');
                    $LoanCaseInvoiceMain->save();

                    // Also update bln_sst in loan_case_bill_main using loan_case_main_bill_id
                    if ($LoanCaseInvoiceMain->loan_case_main_bill_id) {
                        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $LoanCaseInvoiceMain->loan_case_main_bill_id)->first();
                        if ($LoanCaseBillMain) {
                            $LoanCaseBillMain->bln_sst = 1;
                            $LoanCaseBillMain->updated_at = date('Y-m-d H:i:s');
                            $LoanCaseBillMain->save();
                        }
                    }

                    $current_user = auth()->user();
                    $AccountLog = new AccountLog();
                    $AccountLog->user_id = $current_user->id;
                    if (isset($LoanCaseBillMain)) {
                        $AccountLog->case_id = $LoanCaseBillMain->case_id;
                        $AccountLog->bill_id = $LoanCaseInvoiceMain->loan_case_main_bill_id;
                    }
                    $AccountLog->ori_amt = 0;
                    $AccountLog->new_amt = 0;
                    $AccountLog->action = 'Paid';
                    $AccountLog->desc = $current_user->name . ' set invoice(' . $LoanCaseInvoiceMain->invoice_no . ') to Paid ';
                    $AccountLog->status = 1;
                    $AccountLog->created_at = date('Y-m-d H:i:s');
                    $AccountLog->save();
                }
            }
        }

        return response()->json(['status' => 1, 'message' => 'Status updated']);
    }

    public function ReportBankRecon()
    {
        $current_user = auth()->user();
        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1);

        if (in_array($current_user->menuroles, ['sales'])) {
            if (!in_array($current_user->id, [51, 32, 127])) {
                return redirect()->route('case.index');
            }
        }

        if (in_array($current_user->menuroles, ['maker'])) {
            if (in_array($current_user->branch_id, [5, 6])) {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [5, 6]);
            } else {
                $OfficeBankAccount = $OfficeBankAccount->where('branch_id', $current_user->branch_id);
            }
        } else  if (in_array($current_user->menuroles, ['sales'])) {
            if (in_array($current_user->id, [32, 51, 127])) {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [5, 6]);
            }
        } else if (in_array($current_user->menuroles, ['lawyer'])) {
            if (in_array($current_user->id, [13])) {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [$current_user->branch_id]);
            } else {
                $OfficeBankAccount = $OfficeBankAccount->whereIn('branch_id', [99]);
            }
        }

        $OfficeBankAccount = $OfficeBankAccount->get();

        $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
            ->groupBy('recon_date')
            ->get();

        $recon_date = VoucherMain::where('recon_date', '<>', null)->select('recon_date')
            ->groupBy('recon_date')
            ->orderBy('recon_date', 'asc')
            ->get();

        return view('dashboard.reports.bank-recon.index', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'recon_date' => $recon_date
        ]);
    }

    public function getBankReconReport(Request $request)
    {
        $current_user = auth()->user();

        $OfficeBank = OfficeBankAccount::where('id', '=', $request->input("bank_id"))->first();

        $totalAddCLRDeposit = 0;
        $totalLessCLRDeposit = 0;
        $totalLastReconBalance = 0;

        $d = date_parse_from_format("Y-m-d", $request->input("recon_date"));


        // // $bank_recon = DB::table('voucher_main as m')
        // //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
        // //     ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date')
        // //     ->where('m.status', '<>', 99)
        // //     ->where('m.account_approval', '=', 1)
        // //     ->where('d.is_recon', '=', 1)
        // //     ->whereMonth('d.recon_date', $d["month"])
        // //     ->whereYear('d.recon_date', $d["year"])
        // //     ->whereIn('m.voucher_type', [3, 4])
        // //     ->where('m.office_account_id', '=', $request->input("bank_id"))->get();

        $bank_recon = DB::table('voucher_main as m')
            ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date')
            ->where('m.status', '<>', 99)
            ->where('m.account_approval', '=', 1)
            ->where('m.is_recon', '=', 1)
            ->whereMonth('m.recon_date', $d["month"])
            ->whereYear('m.recon_date', $d["year"])
            ->whereIn('m.voucher_type', [3, 4])
            ->where('m.office_account_id', '=', $request->input("bank_id"))->get();

        // $bank_recon = DB::table('ledger_entries as m')
        // ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
        // ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date')
        // ->where('m.status', '<>', 99)
        // ->where('m.account_approval', '=', 1)
        // ->where('m.is_recon', '=', 1)
        // ->whereMonth('m.recon_date', $d["month"])
        // ->whereYear('m.recon_date', $d["year"])
        // ->whereIn('m.voucher_type', [3, 4])
        // ->where('m.office_account_id', '=', $request->input("bank_id"))->get();

        // $bank_recon = DB::table('ledger_entries as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     // ->whereIn('m.type', ['RECONADD', 'JOURNALIN', 'TRANSFERIN', 'SSTIN'])
        //     ->whereIn('m.type', ['RECONADD', 'JOURNALINRECON', 'TRANSFERINRECON', 'SSTINRECON', 'CLOSEFILEIN'])
        //     // ->whereIn('m.type', ['RECONADD'])
        //     ->where('m.bank_id', '=', $request->input("bank_id"))->get();


        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->whereYear('recon_date', '<', $d["year"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');
        // $BankReconRecordOut = BankReconRecord::whereYear('recon_date', '<=', $d["year"])->whereMonth('recon_date', '<=', $d["month"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('out_amt');
        $BankReconRecordOut = BankReconRecord::where('recon_date', '<',  $request->input("recon_date"))->where('bank_account_id', '=', $request->input("bank_id"))->sum('out_amt');

        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->whereYear('recon_date', '<', $d["year"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');
        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->whereYear('recon_date', '<=', $d["year"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');
        $BankReconRecordIn = BankReconRecord::where('recon_date', '<',  $request->input("recon_date"))->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');

        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');

        // return $request->input("bank_id");

        $OfficeBankOpeningBalanace = 0;
        $OfficeBankAccount = OfficeBankAccount::whereMonth('opening_bal_date', '=', '10')->where('id', '=', $request->input("bank_id"))->first();

        if ($OfficeBankAccount) {
            $OfficeBankOpeningBalanace = $OfficeBankAccount->opening_balance;
        }

        // return  $BankReconRecordIn;

        $totalLastReconBalance = $BankReconRecordIn - $BankReconRecordOut;

        $AddCLRDeposit = view('dashboard.reports.bank-recon.tbl-bank-recon-list', compact('bank_recon'))->render();

        if (Count($bank_recon) > 0) {
            for ($i = 0; $i < count($bank_recon); $i++) {
                $totalAddCLRDeposit += $bank_recon[$i]->amount;
            }
        }

        $bank_recon = DB::table('voucher_main as m')
            ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date')
            ->where('m.status', '<>', 99)
            ->where('m.account_approval', '=', 1)
            ->where('m.is_recon', '=', 1)
            ->whereMonth('m.recon_date', $d["month"])
            ->whereYear('m.recon_date', $d["year"])
            ->whereIn('m.voucher_type', [1, 2])
            ->where('m.office_account_id', '=', $request->input("bank_id"));


        $bank_recon = DB::table('ledger_entries as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereMonth('m.date', $d["month"])
            ->whereYear('m.date', $d["year"])
            // ->whereIn('m.type',  ['RECONLESS', 'TRANSFEROUT', 'SSTOUT','JOURNALOUT'])
            ->whereIn('m.type',  ['RECONLESS', 'TRANSFEROUTRECON', 'SSTOUTRECON', 'CLOSEFILEOUT'])
            ->where('m.bank_id', '=', $request->input("bank_id"));



        // if (in_array($current_user->menuroles, ['maker'])) {
        //     $bank_recon = $bank_recon->where('l.branch_id', '=', $current_user->branch_id);
        // }

        $bank_recon = $bank_recon->get();

        $LessCLRDeposit = view('dashboard.reports.bank-recon.tbl-bank-recon-list', compact('bank_recon'))->render();

        $bank_details = view('dashboard.reports.bank-recon.bank-details', compact('OfficeBank'))->render();

        if (Count($bank_recon) > 0) {
            for ($i = 0; $i < count($bank_recon); $i++) {
                $totalLessCLRDeposit += $bank_recon[$i]->amount;
            }
        }

        return [
            'status' => 1,
            'AddCLRDeposit' => $AddCLRDeposit,
            'LessCLRDeposit' => $LessCLRDeposit,
            'totalAddCLRDeposit' => $totalAddCLRDeposit,
            'totalLessCLRDeposit' => $totalLessCLRDeposit,
            'totalLastReconBalance' => $totalLastReconBalance,
            'bank_details' => $bank_details,
            'OfficeBankOpeningBalanace' => $OfficeBankOpeningBalanace
        ];
    }


    public function getBankReconReportV2(Request $request)
    { 
        $current_user = auth()->user();
        $firm_name = 'L H YEO & CO';

        $OfficeBank = OfficeBankAccount::where('id', '=', $request->input("bank_id"))->first();

        if(!in_array($OfficeBank->branch_id, [0]))
        {
            $branch = Branch::where('id', '=', $OfficeBank->branch_id)->first();
            $firm_name = $branch->office_name;
        }

        $totalAddCLRDeposit = 0;
        $totalLessCLRDeposit = 0;
        $totalLastReconBalance = 0;

        $d = date_parse_from_format("Y-m-d", $request->input("recon_date"));

        $bank_recon = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereMonth('m.recon_date', $d["month"])
            ->whereYear('m.recon_date', $d["year"])
            ->whereIn('m.type', ['RECONADD', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'BILL_RECV', 'TRUST_RECV', 'REIMB_IN', 'REIMB_SST_IN'])
            ->where('m.is_recon', 1)
            ->where('m.bank_id', '=', $request->input("bank_id"))->get();



        // $bank_recon = DB::table('ledger_entries as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     // ->whereIn('m.type', ['RECONADD', 'JOURNALIN', 'TRANSFERIN', 'SSTIN'])
        //     ->whereIn('m.type', ['JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'CLOSEFILE_IN', 'CLOSEFILEIN'])
        //     // ->whereIn('m.type', ['RECONADD'])
        //     ->where('m.bank_id', '=', $request->input("bank_id"))->get();


        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->whereYear('recon_date', '<', $d["year"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');
        // $BankReconRecordOut = BankReconRecord::whereYear('recon_date', '<=', $d["year"])->whereMonth('recon_date', '<=', $d["month"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('out_amt');
        $BankReconRecordOut = BankReconRecord::where('recon_date', '<',  $request->input("recon_date"))->where('bank_account_id', '=', $request->input("bank_id"))->sum('out_amt');

        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->whereYear('recon_date', '<', $d["year"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');
        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->whereYear('recon_date', '<=', $d["year"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');
        $BankReconRecordIn = BankReconRecord::where('recon_date', '<',  $request->input("recon_date"))->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');

        // $BankReconRecordIn = BankReconRecord::whereMonth('recon_date', '<', $d["month"])->where('bank_account_id', '=', $request->input("bank_id"))->sum('in_amt');

        // return $request->input("bank_id");

        $OfficeBankOpeningBalanace = 0;
        $OfficeBankAccount = OfficeBankAccount::whereMonth('opening_bal_date', '=', '10')->where('id', '=', $request->input("bank_id"))->first();

        if ($OfficeBankAccount) {
            $OfficeBankOpeningBalanace = $OfficeBankAccount->opening_balance;
        }

        // return  $BankReconRecordIn;

        $totalLastReconBalance = $BankReconRecordIn - $BankReconRecordOut;

        $AddCLRDeposit = view('dashboard.reports.bank-recon.tbl-bank-recon-list', compact('bank_recon'))->render();

        if (Count($bank_recon) > 0) {
            for ($i = 0; $i < count($bank_recon); $i++) {
                $totalAddCLRDeposit += $bank_recon[$i]->amount;
            }
        }


        $totalAddUncreditDeposit = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->where(function ($q) use ($request) {
                $q->where('m.recon_date', '>', $request->input("recon_date"));
            })
            ->where('m.date', '<=', $request->input("recon_date"))
            ->whereIn('m.type', ['RECONADD', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'BILL_RECV', 'TRUST_RECV', 'REIMB_IN', 'REIMB_SST_IN'])
            ->where('m.bank_id', '=', $request->input("bank_id"))->sum('amount');

        $totalAddUncreditDepositNull = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereNull('m.recon_date')
            ->where('m.date', '<=', $request->input("recon_date"))
            ->whereIn('m.type', ['RECONADD', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'BILL_RECV', 'TRUST_RECV', 'REIMB_IN', 'REIMB_SST_IN'])
            ->where('m.bank_id', '=', $request->input("bank_id"))->sum('amount');

        $totalAddUncreditDeposit = $totalAddUncreditDeposit + $totalAddUncreditDepositNull;

        $UncreditDeposit = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->where(function ($q) use ($request) {
                $q->where('m.recon_date', '>', $request->input("recon_date"));
            })
            ->where('m.date', '<=', $request->input("recon_date"))
            ->whereIn('m.type', ['RECONADD', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'BILL_RECV', 'TRUST_RECV', 'REIMB_IN', 'REIMB_SST_IN'])
            ->where('m.bank_id', '=', $request->input("bank_id"));

        $UncreditDepositNull = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereNull('m.recon_date')
            ->where('m.date', '<=', $request->input("recon_date"))
            ->whereIn('m.type', ['RECONADD', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'BILL_RECV', 'TRUST_RECV', 'REIMB_IN', 'REIMB_SST_IN'])
            ->where('m.bank_id', '=', $request->input("bank_id"));

        $merged = $UncreditDeposit->get()->merge($UncreditDepositNull->get());
        $bank_recon = $merged->all();

        $UncreditDeposit = view('dashboard.reports.bank-recon.tbl-bank-recon-list', compact('bank_recon'))->render();

        $totalAddUncreditDeposit = $merged->sum('amount');




        // $merged = $UncreditDeposit->merge($UncreditDepositNull);
        // $bank_recon = $merged->all();

        // $UncreditDeposit = view('dashboard.reports.bank-recon.tbl-bank-recon-list', compact('bank_recon'))->render();

        // $totalAddUncreditDeposit = DB::table('ledger_entries_v2 as m')
        // ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        // ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        // ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
        // ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        // ->where('m.status', '<>', 99)
        // ->where('m.recon_date','>', $request->input("recon_date"))
        // ->where('m.date','<=', $request->input("recon_date"))
        // ->whereIn('m.type', ['RECONADD', 'JOURNAL_IN', 'TRANSFER_IN', 'SST_IN', 'BILL_RECV', 'TRUST_RECV'])
        // ->where('m.is_recon', 0)
        // ->where('m.bank_id', '=', $request->input("bank_id"))->get();

        // return $request->input("recon_date") ;




        // $bank_recon = DB::table('voucher_main as m')
        //     ->leftJoin('voucher_details as d', 'd.voucher_main_id', '=', 'm.id')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->select('m.*', 'd.amount as detail_amt', 'd.id as details_id', 'd.is_recon as d_is_recon', 'd.recon_date as d_recon_date')
        //     ->where('m.status', '<>', 99)
        //     ->where('m.account_approval', '=', 1)
        //     ->where('m.is_recon', '=', 1)
        //     ->whereMonth('m.recon_date', $d["month"])
        //     ->whereYear('m.recon_date', $d["year"])
        //     ->whereIn('m.voucher_type', [1, 2])
        //     ->where('m.office_account_id', '=', $request->input("bank_id"));


        // $bank_recon = DB::table('ledger_entries as m')
        //     ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
        //     ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
        //     ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
        //     ->where('m.status', '<>', 99)
        //     ->whereMonth('m.date', $d["month"])
        //     ->whereYear('m.date', $d["year"])
        //     // ->whereIn('m.type',  ['RECONLESS', 'TRANSFEROUT', 'SSTOUT','JOURNALOUT'])
        //     ->whereIn('m.type',  ['RECONLESS', 'TRANSFEROUTRECON', 'SSTOUTRECON', 'CLOSEFILEOUT'])
        //     ->where('m.bank_id', '=', $request->input("bank_id"));


        //Less Cleared Cheques
        $bank_recon = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereMonth('m.recon_date', $d["month"])
            ->whereYear('m.recon_date', $d["year"])
            // ->whereIn('m.type',  ['JOURNAL_OUT', 'TRANSFER_OUT', 'SST_OUT', 'CLOSEFILE_OUT', 'BILL_DISB', 'TRUST_DISB'])
            ->whereIn('m.type',  ['JOURNAL_OUT', 'TRANSFER_OUT', 'SST_OUT', 'CLOSEFILE_OUT', 'BILL_DISB', 'TRUST_DISB', 'REIMB_OUT', 'REIMB_SST_OUT'])
            ->where('m.is_recon', 1)
            ->where('m.bank_id', '=', $request->input("bank_id"));

        $bank_recon = $bank_recon->get();

        $LessCLRDeposit = view('dashboard.reports.bank-recon.tbl-bank-recon-list', compact('bank_recon'))->render();

        if (Count($bank_recon) > 0) {
            for ($i = 0; $i < count($bank_recon); $i++) {
                $totalLessCLRDeposit += $bank_recon[$i]->amount;
            }
        }


        //Unpresent
        $totalLessPresentedCheuque = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->where('m.recon_date', '>', $request->input("recon_date"))
            ->where('m.date', '<=', $request->input("recon_date"))
            ->whereIn('m.type',  ['JOURNAL_OUT', 'TRANSFER_OUT', 'SST_OUT', 'CLOSEFILE_OUT', 'BILL_DISB', 'TRUST_DISB', 'REIMB_OUT', 'REIMB_SST_OUT'])
            ->where('m.bank_id', '=', $request->input("bank_id"));

        $totalLessPresentedCheuquetNull = DB::table('ledger_entries_v2 as m')
            ->leftJoin('loan_case as l', 'l.id', '=', 'm.case_id')
            ->leftJoin('loan_case_bill_main as lm', 'lm.id', '=', 'm.loan_case_main_bill_id')
            ->leftJoin('voucher_main as vm', 'vm.id', '=', 'm.key_id')
            ->select('m.*', 'l.case_ref_no', 'lm.invoice_no')
            ->where('m.status', '<>', 99)
            ->whereNull('m.recon_date')
            ->where('m.date', '<=', $request->input("recon_date"))
            ->whereIn('m.type',  ['JOURNAL_OUT', 'TRANSFER_OUT', 'SST_OUT', 'CLOSEFILE_OUT', 'BILL_DISB', 'TRUST_DISB', 'REIMB_OUT', 'REIMB_SST_OUT'])
            ->where('m.bank_id', '=', $request->input("bank_id"));

        $merged = $totalLessPresentedCheuque->get()->merge($totalLessPresentedCheuquetNull->get());
        $bank_recon = $merged->all();

        $LessPresented = view('dashboard.reports.bank-recon.tbl-bank-recon-list', compact('bank_recon'))->render();

        $totalLessPresentedCheuque = $merged->sum('amount');


        $bank_details = view('dashboard.reports.bank-recon.bank-details', compact('OfficeBank'))->render();



        return [
            'status' => 1,
            'AddCLRDeposit' => $AddCLRDeposit,
            'firm_name' => $firm_name,
            'LessCLRDeposit' => $LessCLRDeposit,
            'totalAddCLRDeposit' => $totalAddCLRDeposit,
            'totalLessCLRDeposit' => $totalLessCLRDeposit,
            'totalLastReconBalance' => $totalLastReconBalance,
            'LessPresented' => $LessPresented,
            'UncreditDeposit' => $UncreditDeposit,
            'bank_details' => $bank_details,
            'totalAddUncreditDeposit' => $totalAddUncreditDeposit,
            'totalLessPresentedCheuque' => $totalLessPresentedCheuque,
            'OfficeBankOpeningBalanace' => $OfficeBankOpeningBalanace
        ];
    }

    public function ReportSummary()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;
        if (in_array($current_user->menuroles, ['admin', 'management']) || $current_user->id == 37) {
        } else {
            return redirect()->route('dashboard.index');
        }

        $bill_disb = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  1)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $users = User::whereIn('menuroles', ['clerk', 'lawyer'])->orderBy('name', 'ASC')->get();


        $CaseBill = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no', 'c.status as case_status')
            ->where('a.status', '<>', '99')
            ->where('c.status', '=', '99')
            ->where('a.bill_recv', '>', '0')
            ->orderBy('c.id', 'ASC')
            ->get();

        $trust_disb = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  2)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $bill_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  4)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $trust_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  3)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        $Branch = Branch::where('status', '=', 1)->get();
        $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
            ->groupBy('recon_date')
            ->get();

        $CaseBill = [];

        return view('dashboard.reports.summary-report.index', [
            'bill_disb' => $bill_disb,
            'trust_disb' => $trust_disb,
            'bill_receive' => $bill_receive,
            'trust_receive' => $trust_receive,
            'users' => $users,
            'branches' => $Branch,
            'CaseBill' => $CaseBill,
            'OfficeBankAccount' => $OfficeBankAccount,
            'recon_date' => $recon_date
        ]);

        // return view('dashboard.report-summary.index', [
        //     'OfficeBankAccount' => $OfficeBankAccount,
        //     'recon_date' => $recon_date
        // ]); 
    }

    public function ReportQuotation()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;
        if (in_array($current_user->menuroles, ['admin', 'management']) || $current_user->id == 37) {
        } else {
            return redirect()->route('dashboard.index');
        }



        $users = User::whereIn('menuroles', ['clerk', 'lawyer'])->orderBy('name', 'ASC')->get();



        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        $Branch = Branch::where('status', '=', 1)->get();
        $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
            ->groupBy('recon_date')
            ->get();

        $CaseBill = [];

        return view('dashboard.reports.quotation.index', [
            'users' => $users,
            'branches' => $Branch,
            'CaseBill' => $CaseBill,
            'OfficeBankAccount' => $OfficeBankAccount,
            'recon_date' => $recon_date
        ]);

        // return view('dashboard.report-summary.index', [
        //     'OfficeBankAccount' => $OfficeBankAccount,
        //     'recon_date' => $recon_date
        // ]); 
    }

    public function ReportAdvance()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;
        if (in_array($current_user->menuroles, ['admin', 'management']) || $current_user->id == 37) {
        } else {
            return redirect()->route('dashboard.index');
        }

        $bill_disb = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  1)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $users = User::whereIn('menuroles', ['clerk', 'lawyer'])->orderBy('name', 'ASC')->get();


        $CaseBill = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no', 'c.status as case_status')
            ->where('a.status', '<>', '99')
            ->where('c.status', '=', '99')
            ->where('a.bill_recv', '>', '0')
            ->orderBy('c.id', 'ASC')
            ->get();

        $trust_disb = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  2)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $bill_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  4)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $trust_receive = DB::table('voucher_details AS vd')
            ->leftJoin('voucher_main AS vm', 'vm.id', '=', 'vd.voucher_main_id')
            ->where('vm.voucher_type', '=',  3)
            ->where('vm.status', '<>',  99)
            ->sum('amount');

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();
        $Branch = Branch::where('status', '=', 1)->get();
        $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
            ->groupBy('recon_date')
            ->get();

        $CaseBill = [];

        return view('dashboard.reports.advance-report.index', [
            'bill_disb' => $bill_disb,
            'trust_disb' => $trust_disb,
            'bill_receive' => $bill_receive,
            'trust_receive' => $trust_receive,
            'users' => $users,
            'branches' => $Branch,
            'CaseBill' => $CaseBill,
            'OfficeBankAccount' => $OfficeBankAccount,
            'recon_date' => $recon_date
        ]);

        // return view('dashboard.report-summary.index', [
        //     'OfficeBankAccount' => $OfficeBankAccount,
        //     'recon_date' => $recon_date
        // ]); 
    }

    public function getAdvanceReport(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $CaseBill = DB::table('loan_case_bill_main AS a')
                ->join('loan_case as c', 'c.id', '=', 'a.case_id')
                ->leftJoin('users as u', 'u.id', '=', 'a.marketing_id')
                ->leftJoin('loan_case_trust_main as t', 't.case_id', '=', 'a.case_id')
                ->select('a.*', 'c.case_ref_no', 'u.name as sales_name', 't.total_received as total_trust_receive', 't.total_used as total_trust_used')
                ->where('a.status', '<>', '99')
                ->where('a.bln_invoice', '=', 0);


            if ($request->input("user") <> 0) {
                $users = User::where('id', '=', $request->input("user"))->first();

                if ($users) {
                    if ($users->menuroles == 'lawyer') {
                        $CaseBill = $CaseBill->where('c.lawyer_id', '=', $request->input("user"));
                    } else if ($users->menuroles == 'clerk') {
                        $CaseBill = $CaseBill->where('c.clerk_id', '=', $request->input("user"));
                    }
                }
            }

            if ($request->input("branch") <> 0) {
                $CaseBill = $CaseBill->where('c.branch_id', '=', $request->input("branch"));
            }

            // if ($request->input("date_option") <> 99) {
            //     if ($request->input("date_option") == 1) {
            //         if ($request->input("year") <> 0) {
            //             $CaseBill = $CaseBill->whereYear('a.created_at', $request->input("year"));
            //         }
            //     } else if ($request->input("date_option") == 2) {
            //         if ($request->input("month") <> 0) {
            //             $CaseBill = $CaseBill->whereMonth('a.created_at', $request->input("month"));
            //         }
            //     } else if ($request->input("date_option") == 3) {
            //         $CaseBill = $CaseBill->whereDay('a.created_at', $request->input("day"));
            //     }
            // }

            if ($request->input("year") <> 0) {
                $CaseBill = $CaseBill->whereYear('a.created_at', $request->input("year"));
            }

            if ($request->input("month") <> 0) {
                $CaseBill = $CaseBill->whereMonth('a.created_at', $request->input("month"));
            }

            $CaseBill = $CaseBill->orderBy('a.bill_no', 'ASC')->get();



            return response()->json([
                'view' => view('dashboard.reports.advance-report.tbl-advance-report', compact('CaseBill'))->render(),
            ]);
        }
    }

    public function downloadAdvance(Request $request)
    {


        $CaseBill = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->leftJoin('users as u', 'u.id', '=', 'a.marketing_id')
            ->leftJoin('loan_case_trust_main as t', 't.case_id', '=', 'a.case_id')
            ->select('a.*', 'c.case_ref_no', 'u.name as sales_name', 't.total_received as total_trust_receive', 't.total_used as total_trust_used')
            ->where('a.status', '<>', '99')
            ->where('a.bln_invoice', '=', 0);


        // $CaseBill = DB::table('loan_case_bill_main AS a')
        //     ->join('loan_case as c', 'c.id', '=', 'a.case_id')
        //     ->select('a.*', 'c.case_ref_no')
        //     ->where('a.status', '<>', '99')
        // ->where('a.bln_invoice', '=', 1);


        if ($request->input("user") <> 0) {
            $users = User::where('id', '=', $request->input("user"))->first();

            if ($users) {
                if ($users->menuroles == 'lawyer') {
                    $CaseBill = $CaseBill->where('c.lawyer_id', '=', $request->input("user"));
                } else if ($users->menuroles == 'clerk') {
                    $CaseBill = $CaseBill->where('c.clerk_id', '=', $request->input("user"));
                }
            }
        }

        if ($request->input("branch") <> 0) {
            $CaseBill = $CaseBill->where('l.branch_id', '=', $request->input("branch"));
        }


        $CaseBill = $CaseBill->orderBy('a.bill_no', 'ASC')->get();

        for ($i = count($CaseBill) - 1; $i >= 0; $i--) {
            $payment_date = '';
            $voucher_main = VoucherMain::where('case_bill_main_id', '=', $CaseBill[$i]->id)->where('status', '=', 4)->get();

            for ($j = count($voucher_main) - 1; $j >= 0; $j--) {
                $payment_date = $payment_date . $voucher_main[$j]->payment_date . '<br/>';
            }

            $CaseBill[$i]->paydate = $payment_date;
        }

        $data_array[] = array(
            "Ref No", "Bill No", "Disbursement", "Trust",
            "R1", "R1 (Agent)", "R1 PYMT DATE", "R1 PYMT TRX ID", "R2", "R2 (Agent)", "R2 PYMT DATE", "R2 PYMT TRX ID",
            "R3", "R3 (Agent)", "R3 PYMT DATE", "R3 PYMT TRX ID", "R4", "R4 (Agent)", "R4 PYMT DATE", "R4 PYMT TRX ID",
            "Marketing amt", "Marketing", "Marketing PYMT DATE", "Marketing PYMT TRX ID",
            "Uncollected", "Advance",
        );

        foreach ($CaseBill as $data_item) {
            $staff_bonuss_2_per_paid = '-';
            $staff_bonuss_3_per_paid = '-';
            $lawyer_bonuss_2_per_paid = '-';
            $lawyer_bonuss_3_per_paid = '-';

            if ($data_item->staff_bonuss_2_per_paid <> 0) {
                $staff_bonuss_2_per_paid = 'Paid';
            }
            if ($data_item->staff_bonuss_3_per_paid <> 0) {
                $staff_bonuss_3_per_paid = 'Paid';
            }
            if ($data_item->lawyer_bonuss_2_per_paid <> 0) {
                $lawyer_bonuss_2_per_paid = 'Paid';
            }
            if ($data_item->lawyer_bonuss_3_per_paid <> 0) {
                $lawyer_bonuss_3_per_paid = 'Paid';
            }

            $adv = $data_item->total_trust_used + $data_item->used_amt;

            $data_array[] = array(
                $data_item->case_ref_no,
                $data_item->bill_no,
                $data_item->disb,
                $data_item->total_trust_receive,
                $data_item->referral_a1,
                $data_item->referral_a1_id,
                $data_item->referral_a1_payment_date,
                $data_item->referral_a1_trx_id,
                $data_item->referral_a2,
                $data_item->referral_a2_id,
                $data_item->referral_a2_payment_date,
                $data_item->referral_a2_trx_id,
                $data_item->referral_a3,
                $data_item->referral_a3_id,
                $data_item->referral_a3_payment_date,
                $data_item->referral_a3_trx_id,
                $data_item->referral_a4,
                $data_item->referral_a4_id,
                $data_item->referral_a4_payment_date,
                $data_item->referral_a4_trx_id,
                $data_item->marketing,
                $data_item->sales_name,
                $data_item->marketing_payment_date,
                $data_item->marketing_trx_id,
                $data_item->uncollected,
                $adv,
            );
        }


        $spreadSheet = new Spreadsheet();

        $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(30);
        $spreadSheet->getActiveSheet()->fromArray($data_array);

        $spreadSheet
            ->getActiveSheet()
            ->getStyle('A1:AI1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK);


        $spreadSheet->getActiveSheet()->getStyle('A1:AI1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
        $spreadSheet->getActiveSheet()->getStyle('A1:AI1')->getAlignment()->setHorizontal('center');


        $spreadSheet->getActiveSheet()->getStyle('A1:Z999')->getAlignment()->setWrapText(true);


        $Excel_writer = new WriterXls($spreadSheet);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="advance_report' . date('Y-m-d H:i:s') . '.xls"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $Excel_writer->save('php://output');
        exit();


        return Excel::download(new User(), 'users.xlsx');
    }

    public function ReportStaffCases()
    {

        if (AccessController::UserAccessPermissionController(PermissionController::StaffCaseReportPermission()) == false) {
            return redirect()->route('dashboard.index');
        }


        $current_user = auth()->user();

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

            $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();

            $branchInfo = BranchController::manageBranchAccess();

            $staffCaseCount = Users::whereIn('menuroles', ['clerk', 'lawyer'])->where('status', '<>', 99)->orderBy('menuroles', 'asc')->orderBy('name', 'asc')->get();

            for ($i = 0; $i < count($staffCaseCount); $i++) {
                $cases_count = [];
                $cases_count2022 = [];
                $cases_count2024 = [];

                for ($j = 1; $j <= 12; $j++) {
                    if ($staffCaseCount[$i]->menuroles == 'lawyer') {
                        $LoanCaseCount2024 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2024)->count();
                        $LoanCaseCount2022 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2022)->count();
                        $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2023)->count();
                    } elseif ($staffCaseCount[$i]->menuroles == 'clerk') {
                        $LoanCaseCount2024 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2024)->count();
                        $LoanCaseCount2022 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2022)->count();
                        $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2023)->count();
                    }
                    $cases_count[$j] = $LoanCaseCount;
                    $cases_count2022[$j] = $LoanCaseCount2022;
                    $cases_count2024[$j] = $LoanCaseCount2024;
                }

                if ($staffCaseCount[$i]->menuroles == 'lawyer') {
                    $LoanCaseCountTotal = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2023)->count();
                    $LoanCaseCountTotal2022 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2022)->count();
                    $LoanCaseCountTotal2024 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2024)->count();
                } elseif ($staffCaseCount[$i]->menuroles == 'clerk') {
                    $LoanCaseCountTotal = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2023)->count();
                    $LoanCaseCountTotal2022 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2022)->count();
                    $LoanCaseCountTotal2024 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2024)->count();
                }


                $staffCaseCount[$i]->cases_count = $cases_count;
                $staffCaseCount[$i]->cases_count_2022 = $cases_count2022;
                $staffCaseCount[$i]->cases_count_2024 = $cases_count2024;
                $staffCaseCount[$i]->total_cases_count = $LoanCaseCountTotal;
                $staffCaseCount[$i]->total_cases_count_2022 = $LoanCaseCountTotal2022;
                $staffCaseCount[$i]->total_cases_count_2024 = $LoanCaseCountTotal2024;
            }

            $month = [];

            for ($m = 1; $m <= 12; $m++) {
                $month[] = date('M', mktime(0, 0, 0, $m, 1, date('Y')));
            }

            $fiscal_year = SettingsController::getFiscalYear();

            return view('dashboard.reports.staff.index', [
                'OfficeBankAccount' => $OfficeBankAccount,
                'staffs' => $staff,
                'month' => $month,
                'fiscal_year' => $fiscal_year,
                'branchs' => $branchInfo['branch'],
                'staffCaseCount' => $staffCaseCount,
            ]);
    }

    public function ReportStaffDetails()
    {

        if (AccessController::UserAccessPermissionController(PermissionController::StaffCaseReportPermission()) == false) {
            return redirect()->route('dashboard.index');
        }


        $current_user = auth()->user();

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

            $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();

            $branchInfo = BranchController::manageBranchAccess();
            $staffCaseCount = Users::whereIn('menuroles', ['clerk', 'lawyer'])->where('status', '<>', 99)->orderBy('menuroles', 'asc')->orderBy('name', 'asc')->get();
            

            for ($i = 0; $i < count($staffCaseCount); $i++) {
                $cases_count = [];
                $cases_count2022 = [];
                $cases_count2024 = [];

                for ($j = 1; $j <= 12; $j++) {
                    if ($staffCaseCount[$i]->menuroles == 'lawyer') {
                        $LoanCaseCount2024 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2024)->count();
                        $LoanCaseCount2022 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2022)->count();
                        $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2023)->count();
                    } elseif ($staffCaseCount[$i]->menuroles == 'clerk') {
                        $LoanCaseCount2024 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2024)->count();
                        $LoanCaseCount2022 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2022)->count();
                        $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2023)->count();
                    }
                    $cases_count[$j] = $LoanCaseCount;
                    $cases_count2022[$j] = $LoanCaseCount2022;
                    $cases_count2024[$j] = $LoanCaseCount2024;
                }

                if ($staffCaseCount[$i]->menuroles == 'lawyer') {
                    $LoanCaseCountTotal = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2023)->count();
                    $LoanCaseCountTotal2022 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2022)->count();
                    $LoanCaseCountTotal2024 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2024)->count();
                } elseif ($staffCaseCount[$i]->menuroles == 'clerk') {
                    $LoanCaseCountTotal = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2023)->count();
                    $LoanCaseCountTotal2022 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2022)->count();
                    $LoanCaseCountTotal2024 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2024)->count();
                }


                $staffCaseCount[$i]->cases_count = $cases_count;
                $staffCaseCount[$i]->cases_count_2022 = $cases_count2022;
                $staffCaseCount[$i]->cases_count_2024 = $cases_count2024;
                $staffCaseCount[$i]->total_cases_count = $LoanCaseCountTotal;
                $staffCaseCount[$i]->total_cases_count_2022 = $LoanCaseCountTotal2022;
                $staffCaseCount[$i]->total_cases_count_2024 = $LoanCaseCountTotal2024;
            }

            $month = [];

            for ($m = 1; $m <= 12; $m++) {
                $month[] = date('M', mktime(0, 0, 0, $m, 1, date('Y')));
            }

            $fiscal_year = SettingsController::getFiscalYear();

            return view('dashboard.reports.staff-details.index', [
                'OfficeBankAccount' => $OfficeBankAccount,
                'staffs' => $staff,
                'month' => $month,
                'fiscal_year' => $fiscal_year,
                'branchs' => $branchInfo['branch'],
                'staffCaseCount' => $staffCaseCount,
            ]);
    }

    public function getStaffDetailsReport(Request $request)
    {

        $staff = User::where('id', $request->input("staff"))->first();

        $accessInfo = AccessController::manageAccess();
        
        $CaseCountActive = LoanCase::where(function ($query) use($staff) {
            $query->where('lawyer_id', $staff->id)
                  ->orWhere('clerk_id', $staff->id);
        })->whereIn('status', [1,2,3])->whereYear('created_at', $request->input("year"))->get();

        
        $CaseCountPendingClose = LoanCase::where(function ($query) use($staff) {
            $query->where('lawyer_id', $staff->id)
                  ->orWhere('clerk_id', $staff->id);
        })->whereIn('status', [4])->whereYear('created_at', $request->input("year"))->get();

        
        $CaseCountReviewing = LoanCase::where(function ($query) use($staff) {
            $query->where('lawyer_id', $staff->id)
                  ->orWhere('clerk_id', $staff->id);
        })->whereIn('status', [7])->whereYear('created_at', $request->input("year"))->get();

        
        $CaseCountClose = LoanCase::where(function ($query) use($staff) {
            $query->where('lawyer_id', $staff->id)
                  ->orWhere('clerk_id', $staff->id);
        })->whereIn('status', [0])->whereYear('created_at', $request->input("year"))->get();

        // $CaseCountPendingClose = LoanCase::where('lawyer_id', $staff->id)->orWhere('clerk_id', $staff->id)->where('status', 4)->whereYear('created_at', $request->input("year"))->count();
        // $CaseCountReviewing = LoanCase::where('lawyer_id', $staff->id)->orWhere('clerk_id', $staff->id)->where('status', 7)->whereYear('created_at', $request->input("year"))->count();
        // $CaseCountClose = LoanCase::where('lawyer_id', $staff->id)->orWhere('clerk_id', $staff->id)->where('status', 0)->whereYear('created_at', $request->input("year"))->count();

        $case_label = [];
        $case_count = [];
        $cases_count = [];
        $cases_Mon = [];

        $totalCount = 0;

        for ($j = 1; $j <= 12; $j++) {

            
        $LoanCaseCount = LoanCase::where(function ($query) use($staff) {
            $query->where('lawyer_id', $staff->id)
                  ->orWhere('clerk_id', $staff->id);
        })->where('status',  '<>', 99)->whereYear('created_at', $request->input("year"))->whereMonth('created_at', $j)->count();
            
            // $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staff->id)->orWhere('clerk_id', $staff->id)->whereYear('created_at', $request->input("year"))
            // ->whereMonth('created_at', $j)->count();

            // $totalCount += $LoanCaseCount;
            $cases_count[] = $LoanCaseCount;
            $cases_Mon[] = $j;
        }

        

        $Chart_data = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'p.id', '=', 'l.bank_id')
            ->select('bank_id', 'p.name', DB::raw('count(*) as total'))
            ->groupBy('bank_id', 'p.name')
            ->where('l.status', '<>', 99)
            ->where('l.bank_id', '<>', 0)
            ->where('l.lawyer_id', $staff->id)
            ->orWhere('clerk_id', $staff->id)
            ->orderBy('p.name', 'asc');

        $Chart_data = $Chart_data->whereIn('branch_id', $accessInfo['brancAccessList']);

        if ($request->input("year") <> 0) {
            $Chart_data = $Chart_data->whereYear('l.created_at', $request->input("year"));
        }


        $Chart_data = $Chart_data->get();

        

        foreach ($Chart_data as $data) {
            $case_label[] = $data->name;
            $case_count[] = $data->total;
            $totalCount += $data->total;
        }


        
        return [
            // 'view_lawyer' => view('dashboard.reports.staff.tbl-lawyer-report', compact('lawyer_cases'))->render(),
            // 'view_clerk' => view('dashboard.reports.staff.tbl-clerk-report', compact('clerk_cases'))->render(),
            // 'status' => 1,
            'CaseCountActive' => $CaseCountActive,
            'CaseCountPendingClose' => $CaseCountPendingClose,
            'CaseCountReviewing' => $CaseCountReviewing,
            'CaseCountClose' => $CaseCountClose,
            'cases_count' => $cases_count,
            'cases_Mon' => $cases_Mon,
            'case_label' => $case_label,
            'case_count' => $case_count,
            'divCaseSummary' => view('dashboard.reports.staff-details.div-case-summary', compact('CaseCountActive', 'CaseCountPendingClose', 'CaseCountReviewing', 'CaseCountClose'))->render(),
            'tblCaseActive' => view('dashboard.reports.staff-details.tbl-case', ['caseCount' => $CaseCountActive])->render(),
            'tblCaseReviewing' => view('dashboard.reports.staff-details.tbl-case', ['caseCount' => $CaseCountReviewing])->render(),
            'tblCasePendingClose' => view('dashboard.reports.staff-details.tbl-case', ['caseCount' => $CaseCountPendingClose])->render(),
            'tblCaseClose' => view('dashboard.reports.staff-details.tbl-case', ['caseCount' => $CaseCountClose])->render(),
        ];
    }

    public function getStaffCaseReport(Request $request)
    {
        $current_user = auth()->user();

        $staffList = [];
        $lawyerList = [];
        $clerkList = [];
        $count = [];
        $lawyercount = [];
        $clerkcount = [];
        $staff = [];
        $lawyer = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer'])->orderBy('name', 'asc')->get();
        $accessInfo = AccessController::manageAccess();
        // $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();
        // $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();

        $lawyer_cases = DB::table('loan_case as l')
        ->leftJoin('users as p', 'p.id', '=', 'l.lawyer_id')
        ->select('lawyer_id', 'p.name', DB::raw('count(*) as total'))
        ->groupBy('lawyer_id', 'p.name')
        ->where('l.status', '<>', 99)
        ->where('lawyer_id', '<>', 0)
        ->where('l.sales_user_id', '<>', 1)
        ->orderBy('p.name', 'asc');

        $clerk_cases = DB::table('loan_case as l')
        ->leftJoin('users as p', 'p.id', '=', 'l.clerk_id')
        ->select('clerk_id', 'p.name', DB::raw('count(*) as total'))
        ->groupBy('clerk_id', 'p.name')
        ->where('l.status', '<>', 99)
        ->where('clerk_id', '<>', 0)
        ->where('l.sales_user_id', '<>', 1)
        ->orderBy('p.name', 'asc');

        if ($request->input("branch") <> 0) {
            $lawyer_cases = $lawyer_cases->where('l.branch_id', $request->input("branch"));
            $clerk_cases = $clerk_cases->where('l.branch_id', $request->input("branch"));
        }

        if ($request->input("year") <> 0) {
            $lawyer_cases = $lawyer_cases->whereYear('l.created_at', $request->input("year"));
            $clerk_cases = $clerk_cases->whereYear('l.created_at', $request->input("year"));
        }

        if ($request->input("month") <> 0) {
            $lawyer_cases = $lawyer_cases->whereMonth('l.created_at', $request->input("month"));
            $clerk_cases = $clerk_cases->whereMonth('l.created_at', $request->input("month"));
        }

        $lawyer_cases = $lawyer_cases->whereIn('l.branch_id', $accessInfo['brancAccessList']);
        $clerk_cases = $clerk_cases->whereIn('l.branch_id', $accessInfo['brancAccessList']);

        $lawyer_cases = $lawyer_cases->get();
        $clerk_cases = $clerk_cases->get();

        
        



        if ($request->input("role") == 'lawyer') {
            $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'chambering'])->orderBy('name', 'asc');
        } else {
            $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['clerk'])->orderBy('name', 'asc');
        }

        if ($request->input("branch")) {
            $staff = $staff->where('branch_id', $request->input("branch"));
        }


        $staff = $staff->get();


        $lawyer = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'chambering'])->orderBy('name', 'asc');
        $clerk = Users::where('status', '<>', 99)->whereIn('menuroles', ['clerk'])->orderBy('name', 'asc');

        if ($request->input("branch")) {
            $lawyer = $lawyer->where('branch_id', $request->input("branch"));
            $clerk = $clerk->where('branch_id', $request->input("branch"));
        }

        $lawyer = $lawyer->get();
        $clerk = $clerk->get();


        for ($i = 0; $i < count($lawyer); $i++) {
            array_push($lawyerList,  $lawyer[$i]->name);

            $caseCount = LoanCase::whereMonth('created_at', $request->input("month"))
                ->where('status', '<>', 99)
                ->whereYear('created_at', $request->input("year"))
                ->where(function ($q) use ($lawyer, $i) {
                    $q->where('lawyer_id', $lawyer[$i]->id)
                        ->orWhere('clerk_id', $lawyer[$i]->id);
                })->count();


            array_push($lawyercount,  $caseCount);
        }

        for ($i = 0; $i < count($clerk); $i++) {
            array_push($clerkList,  $clerk[$i]->name);

            $caseCount = LoanCase::whereMonth('created_at', $request->input("month"))
                ->where('status', '<>', 99)
                ->whereYear('created_at', $request->input("year"))
                ->where(function ($q) use ($clerk, $i) {
                    $q->where('clerk_id', $clerk[$i]->id);
                })->count();

            array_push($clerkcount,  $caseCount);
        }


        for ($i = 0; $i < count($staff); $i++) {
            array_push($staffList,  $staff[$i]->name);

            // $caseCount = LoanCase::where('lawyer_id', $staff[$i]->id)->orWhere('clerk_id', $staff[$i]->id)
            // ->whereMonth('created_at', $request->input("month"))->count();
            $caseCount = LoanCase::where('lawyer_id', $staff[$i]->id)
                ->whereMonth('created_at', $request->input("month"))
                ->whereYear('created_at', $request->input("year"))
                ->where(function ($q) use ($staff, $i) {
                    $q->where('lawyer_id', $staff[$i]->id)
                        ->orWhere('clerk_id', $staff[$i]->id);
                })->count();

            array_push($count,  $caseCount);
        }

        // for ($i = 0; $i < count($lawyer); $i++) {
        //     array_push($lawyerList,  $lawyer[$i]->name);
        //     array_push($count,  1);
        // }

        foreach ($lawyer_cases as $data) {
            $lawyer_label[] = $data->name;
            $lawyer_case_count[] = $data->total;
        }

        foreach ($clerk_cases as $data) {
            $clerk_label[] = $data->name;
            $clerk_case_count[] = $data->total;
        }

        return [
            'view_lawyer' => view('dashboard.reports.staff.tbl-lawyer-report', compact('lawyer_cases'))->render(),
            'view_clerk' => view('dashboard.reports.staff.tbl-clerk-report', compact('clerk_cases'))->render(),
            'status' => 1,
            'staffList' => $staffList,
            'clerkList' => $clerkList,
            'lawyerList' => $lawyerList,
            'count' => $count,
            'lawyercount' => $lawyercount,
            'clerkcount' => $clerkcount,
            'lawyer_label' => $lawyer_label,
            'lawyer_case_count' => $lawyer_case_count,
            'clerk_label' => $clerk_label,
            'clerk_case_count' => $clerk_case_count,
            'lawyer_cases' => $lawyer_cases,
            'clerk_cases' => $clerk_cases,
        ];
    }

    public function ReportCases()
    {

        if (AccessController::UserAccessPermissionController(PermissionController::CaseReportPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $current_user = auth()->user();

        $staffCaseCount = Users::whereIn('menuroles', ['clerk', 'lawyer'])->where('status', '<>', 99)->orderBy('menuroles', 'asc')->orderBy('name', 'asc')->get();

        $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

        $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();

        $branchInfo = BranchController::manageBranchAccess();

        $staffCaseCount = Users::whereIn('menuroles', ['clerk', 'lawyer'])->where('status', '<>', 99)->orderBy('menuroles', 'asc')->orderBy('name', 'asc')->get();

        for ($i = 0; $i < count($staffCaseCount); $i++) {
            $cases_count = [];
            $cases_count2022 = [];

            for ($j = 1; $j <= 12; $j++) {
                if ($staffCaseCount[$i]->menuroles == 'lawyer') {
                    $LoanCaseCount2022 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2022)->count();
                    $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2023)->count();
                } elseif ($staffCaseCount[$i]->menuroles == 'clerk') {
                    $LoanCaseCount2022 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2022)->count();
                    $LoanCaseCount = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereMonth('created_at', $j)->whereYear('created_at', 2023)->count();
                }
                $cases_count[$j] = $LoanCaseCount;
                $cases_count2022[$j] = $LoanCaseCount2022;
            }

            if ($staffCaseCount[$i]->menuroles == 'lawyer') {
                $LoanCaseCountTotal = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2023)->count();
                $LoanCaseCountTotal2022 = LoanCase::where('status', '<>', 99)->where('lawyer_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2022)->count();
            } elseif ($staffCaseCount[$i]->menuroles == 'clerk') {
                $LoanCaseCountTotal = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2023)->count();
                $LoanCaseCountTotal2022 = LoanCase::where('status', '<>', 99)->where('clerk_id', $staffCaseCount[$i]->id)->whereYear('created_at', 2022)->count();
            }


            $staffCaseCount[$i]->cases_count = $cases_count;
            $staffCaseCount[$i]->cases_count_2022 = $cases_count2022;
            $staffCaseCount[$i]->total_cases_count = $LoanCaseCountTotal;
            $staffCaseCount[$i]->total_cases_count_2022 = $LoanCaseCountTotal2022;
        }

        $month = [];

        for ($m = 1; $m <= 12; $m++) {
            $month[] = date('M', mktime(0, 0, 0, $m, 1, date('Y')));
        }

        $Portfolio = Portfolio::where('status', 1)->orderBy('name', 'asc')->get();


        $fiscal_year = SettingsController::getFiscalYear();


        return view('dashboard.reports.cases.index', [
            'OfficeBankAccount' => $OfficeBankAccount,
            'staffs' => $staff,
            'month' => $month,
            'fiscal_year' => $fiscal_year,
            'Portfolio' => $Portfolio,
            'branchs' => $branchInfo['branch'],
            'staffCaseCount' => $staffCaseCount,
        ]);
    }

    public function generateChartDataCasesByType(Request $request)
    {
        $data = DB::table('loan_case as c')
            ->leftJoin('portfolio as p', 'p.id', '=', 'c.id')
            ->select('bank_id', 'p.name', DB::raw('count(*) as total'))
            ->groupBy('bank_id', 'p.name')
            ->get();

        if ($request->input("type") <> 0) {
            $data = $data->where('l.bank_id', $request->input("type"));
        }

        if ($request->input("branch") <> 0) {
            $data = $data->where('l.branch_id', $request->input("branch"));
        }

        if ($request->input("year") <> 0) {
            $data = $data->whereYear('l.created_at', $request->input("year"));
        }

        if ($request->input("month") <> 0) {
            $data = $data->whereMonth('l.created_at', $request->input("month"));
        }

        $data = $data->get();
    }

    public function getCaseReport(Request $request)
    {
        $current_user = auth()->user();

        $case_label = [];
        $case_count = [];
        $case_type = 'All';
        $Branch_name = 'All';
        $accessInfo = AccessController::manageAccess();


        $LoanCase = DB::table('loan_case as l')
            ->leftJoin('client as c', 'l.customer_id', '=', 'c.id')
            ->leftJoin('portfolio as p', 'p.id', '=', 'l.bank_id')
            ->select('l.*', 'p.name as portfolio_name', 'c.name as customer_name')
            ->where('l.bank_id', '<>', 0)
            ->where('l.status', '<>', 99)->orderBy('id', 'asc');

        $Chart_data = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'p.id', '=', 'l.bank_id')
            ->select('bank_id', 'p.name', DB::raw('count(*) as total'))
            ->groupBy('bank_id', 'p.name')
            ->where('l.status', '<>', 99)
            ->where('l.bank_id', '<>', 0)
            ->orderBy('p.name', 'asc');

        $LoanCase = $LoanCase->whereIn('branch_id', $accessInfo['brancAccessList']);
        $Chart_data = $Chart_data->whereIn('branch_id', $accessInfo['brancAccessList']);

        if ($request->input("type") <> 0) {
            $LoanCase = $LoanCase->where('l.bank_id', $request->input("type"));
            // $Chart_data = $Chart_data->where('l.bank_id', $request->input("type"));

            $Portfolio = Portfolio::where('id', $request->input("type"))->first();
            $case_type = $Portfolio->name;
        }

        if ($request->input("branch") <> 0) {
            $LoanCase = $LoanCase->where('l.branch_id', $request->input("branch"));
            $Chart_data = $Chart_data->where('l.branch_id', $request->input("branch"));

            $branch = Branch::where('id',  $request->input("branch"))->first();
            $Branch_name = $branch->name;
        }

        if ($request->input("year") <> 0) {
            $LoanCase = $LoanCase->whereYear('l.created_at', $request->input("year"));
            $Chart_data = $Chart_data->whereYear('l.created_at', $request->input("year"));
        }

        if ($request->input("month") <> 0) {
            $LoanCase = $LoanCase->whereMonth('l.created_at', $request->input("month"));
            $Chart_data = $Chart_data->whereMonth('l.created_at', $request->input("month"));
        }

        $Chart_data = $Chart_data->get();
        $LoanCase = $LoanCase->get();


        foreach ($Chart_data as $data) {
            $case_label[] = $data->name;
            $case_count[] = $data->total;
        }


        return response()->json([
            'view' => view('dashboard.reports.cases.tbl-case-report', compact('LoanCase', 'case_type', 'Branch_name'))->render(),
            'case_label' => $case_label,
            'case_count' => $case_count
        ]);
    }

    public function ReportBank()
    {
        if (AccessController::UserAccessPermissionController(PermissionController::BankReportPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        $current_user = auth()->user();
        $branchInfo = BranchController::manageBranchAccess();
        $fiscal_year = SettingsController::getFiscalYear();
        
        // Get all active banks (Portfolio)
        $Portfolio = Portfolio::where('status', 1)->orderBy('name', 'asc')->get();

        return view('dashboard.reports.bank.index', [
            'Portfolio' => $Portfolio,
            'branchs' => $branchInfo['branch'],
            'fiscal_year' => $fiscal_year,
        ]);
    }

    public function getBankReport(Request $request)
    {
        if (AccessController::UserAccessPermissionController(PermissionController::BankReportPermission()) == false) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $current_user = auth()->user();
        $accessInfo = AccessController::manageAccess();
        
        // Decode JSON string from frontend
        $banks_input = $request->input('banks', '[]');
        $bank_ids = is_string($banks_input) ? json_decode($banks_input, true) : $banks_input;
        if (!is_array($bank_ids)) {
            $bank_ids = [];
        }
        
        $year = $request->input('year', 0);
        $month = $request->input('month', 0);
        
        $bank_label = [];
        $bank_count = [];
        $bank_names = [];
        
        // Get loan cases filtered by selected banks, year, and month
        $LoanCase = DB::table('loan_case as l')
            ->leftJoin('client as c', 'l.customer_id', '=', 'c.id')
            ->leftJoin('portfolio as p', 'p.id', '=', 'l.bank_id')
            ->select('l.*', 'p.name as portfolio_name', 'c.name as customer_name')
            ->where('l.bank_id', '<>', 0)
            ->where('l.status', '<>', 99)
            ->orderBy('l.id', 'asc');
        
        // Chart data - count cases by bank
        $Chart_data = DB::table('loan_case as l')
            ->leftJoin('portfolio as p', 'p.id', '=', 'l.bank_id')
            ->select('l.bank_id', 'p.name', DB::raw('count(*) as total'))
            ->where('l.status', '<>', 99)
            ->where('l.bank_id', '<>', 0)
            ->groupBy('l.bank_id', 'p.name')
            ->orderBy('p.name', 'asc');
        
        // Apply branch access
        $LoanCase = $LoanCase->whereIn('l.branch_id', $accessInfo['brancAccessList']);
        $Chart_data = $Chart_data->whereIn('l.branch_id', $accessInfo['brancAccessList']);
        
        // Filter by selected banks - MUST filter if banks are selected
        if (!empty($bank_ids) && is_array($bank_ids) && count($bank_ids) > 0) {
            // Convert string IDs to integers if needed
            $bank_ids = array_map('intval', $bank_ids);
            $LoanCase = $LoanCase->whereIn('l.bank_id', $bank_ids);
            $Chart_data = $Chart_data->whereIn('l.bank_id', $bank_ids);
        } else {
            // If no banks selected, return empty result
            return response()->json([
                'view' => view('dashboard.reports.bank.tbl-bank-report', compact('LoanCase', 'bank_names'))->render(),
                'bank_label' => [],
                'bank_count' => []
            ]);
        }
        
        // Filter by year
        if ($year != 0) {
            $LoanCase = $LoanCase->whereYear('l.created_at', $year);
            $Chart_data = $Chart_data->whereYear('l.created_at', $year);
        }
        
        // Filter by month
        if ($month != 0) {
            $LoanCase = $LoanCase->whereMonth('l.created_at', $month);
            $Chart_data = $Chart_data->whereMonth('l.created_at', $month);
        }
        
        $Chart_data = $Chart_data->get();
        $LoanCase = $LoanCase->get();
        
        // Build chart data
        foreach ($Chart_data as $data) {
            $bank_label[] = $data->name;
            $bank_count[] = $data->total;
            $bank_names[$data->bank_id] = $data->name;
        }
        
        return response()->json([
            'view' => view('dashboard.reports.bank.tbl-bank-report', compact('LoanCase', 'bank_names'))->render(),
            'bank_label' => $bank_label,
            'bank_count' => $bank_count
        ]);
    }

    public function exportBankReportPDF(Request $request)
    {
        if (AccessController::UserAccessPermissionController(PermissionController::BankReportPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        try {
            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();
            
            $bank_ids = json_decode($request->input('banks', '[]'), true);
            $year = $request->input('year', 0);
            $month = $request->input('month', 0);
            
            // Get loan cases filtered by selected banks, year, and month
            $LoanCase = DB::table('loan_case as l')
                ->leftJoin('client as c', 'l.customer_id', '=', 'c.id')
                ->leftJoin('portfolio as p', 'p.id', '=', 'l.bank_id')
                ->select('l.*', 'p.name as portfolio_name', 'c.name as customer_name')
                ->where('l.bank_id', '<>', 0)
                ->where('l.status', '<>', 99)
                ->orderBy('l.id', 'asc');
            
            // Apply branch access
            $LoanCase = $LoanCase->whereIn('l.branch_id', $accessInfo['brancAccessList']);
            
            // Filter by selected banks
            if (!empty($bank_ids) && is_array($bank_ids)) {
                $LoanCase = $LoanCase->whereIn('l.bank_id', $bank_ids);
            }
            
            // Filter by year
            if ($year != 0) {
                $LoanCase = $LoanCase->whereYear('l.created_at', $year);
            }
            
            // Filter by month (0 means all months)
            if ($month != 0) {
                $LoanCase = $LoanCase->whereMonth('l.created_at', $month);
            }
            
            $LoanCase = $LoanCase->get();
            
            // Get bank names
            $bank_names = [];
            if (!empty($bank_ids)) {
                $banks = Portfolio::whereIn('id', $bank_ids)->get();
                foreach ($banks as $bank) {
                    $bank_names[$bank->id] = $bank->name;
                }
            }
            
            // Generate filename
            $filename = 'Bank_Report_' . date('Y-m-d') . '.pdf';
            
            // Generate PDF
            $pdf = Pdf::loadView('dashboard.reports.bank.export-pdf', [
                'LoanCase' => $LoanCase,
                'bank_names' => $bank_names,
                'year' => $year,
                'month' => $month
            ]);
            
            $pdf->setPaper('A4', 'landscape');
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            Log::error('Bank Report PDF Export Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error generating PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportBankReportExcel(Request $request)
    {
        if (AccessController::UserAccessPermissionController(PermissionController::BankReportPermission()) == false) {
            return redirect()->route('dashboard.index');
        }

        try {
            $current_user = auth()->user();
            $accessInfo = AccessController::manageAccess();
            
            $bank_ids = json_decode($request->input('banks', '[]'), true);
            $year = $request->input('year', 0);
            $month = $request->input('month', 0);
            
            // Get loan cases filtered by selected banks, year, and month
            $LoanCase = DB::table('loan_case as l')
                ->leftJoin('client as c', 'l.customer_id', '=', 'c.id')
                ->leftJoin('portfolio as p', 'p.id', '=', 'l.bank_id')
                ->select('l.*', 'p.name as portfolio_name', 'c.name as customer_name')
                ->where('l.bank_id', '<>', 0)
                ->where('l.status', '<>', 99)
                ->orderBy('l.id', 'asc');
            
            // Apply branch access
            $LoanCase = $LoanCase->whereIn('l.branch_id', $accessInfo['brancAccessList']);
            
            // Filter by selected banks
            if (!empty($bank_ids) && is_array($bank_ids) && count($bank_ids) > 0) {
                $bank_ids = array_map('intval', $bank_ids);
                $LoanCase = $LoanCase->whereIn('l.bank_id', $bank_ids);
            } else {
                return response()->json([
                    'status' => 0,
                    'message' => 'Please select at least one bank.'
                ], 400);
            }
            
            // Filter by year
            if ($year != 0) {
                $LoanCase = $LoanCase->whereYear('l.created_at', $year);
            }
            
            // Filter by month
            if ($month != 0) {
                $LoanCase = $LoanCase->whereMonth('l.created_at', $month);
            }
            
            $LoanCase = $LoanCase->get();
            
            // Create new Spreadsheet
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            
            // Set title
            $sheet->setCellValue('A1', 'Bank Report');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            
            // Set total count
            $sheet->setCellValue('A2', 'Total case count: ' . count($LoanCase));
            $sheet->mergeCells('A2:H2');
            $sheet->getStyle('A2')->getFont()->setBold(true);
            
            // Set headers
            $headers = ['Create Date', 'Ref No', 'Bank', 'Property Address', 'Customer Name', 'Purchase Price', 'Loan Sum', 'Status'];
            $col = 'A';
            $row = 3;
            
            foreach ($headers as $header) {
                $sheet->setCellValue($col . $row, $header);
                $sheet->getStyle($col . $row)->getFont()->setBold(true);
                $sheet->getStyle($col . $row)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E0E0E0');
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $col++;
            }
            
            // Add data
            $row = 4;
            foreach ($LoanCase as $record) {
                $sheet->setCellValue('A' . $row, date('d-m-Y', strtotime($record->created_at)));
                $sheet->setCellValue('B' . $row, $record->case_ref_no);
                $sheet->setCellValue('C' . $row, $record->portfolio_name ?? 'N/A');
                $sheet->setCellValue('D' . $row, $record->property_address ?? '-');
                $sheet->setCellValue('E' . $row, $record->customer_name ?? '-');
                
                // Format numeric columns
                $sheet->setCellValue('F' . $row, (float)($record->purchase_price ?? 0));
                $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
                $sheet->setCellValue('G' . $row, (float)($record->loan_sum ?? 0));
                $sheet->getStyle('G' . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
                // Status
                $statusText = 'Unknown';
                if ($record->status == 0) $statusText = 'Closed';
                elseif ($record->status == 1) $statusText = 'In progress';
                elseif ($record->status == 2) $statusText = 'Open';
                elseif ($record->status == 3) $statusText = 'KIV';
                elseif ($record->status == 4) $statusText = 'Pending Close';
                elseif ($record->status == 7) $statusText = 'Reviewing';
                elseif ($record->status == 99) $statusText = 'Aborted';
                
                $sheet->setCellValue('H' . $row, $statusText);
                $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                
                $row++;
            }
            
            // Auto-size columns
            foreach (range('A', 'H') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }
            
            // Add borders to data
            $dataRange = 'A3:H' . ($row - 1);
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
            // Generate filename
            $filename = 'bank_report_' . date('Y-m-d_His') . '.xlsx';
            
            // Set response headers
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            
            // Write file
            $writer = new Xlsx($spreadsheet);
            ob_end_clean();
            $writer->save('php://output');
            exit();
            
        } catch (\Exception $e) {
            Log::error('Bank Report Excel Export Error: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Error generating Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ReportBonus()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        if (in_array($current_user->menuroles, ['admin', 'management']) || $current_user->id == 37) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

            $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();
            $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
                ->groupBy('recon_date')
                ->get();

            $recon_date = VoucherMain::where('recon_date', '<>', null)->select('recon_date')
                ->groupBy('recon_date')
                ->orderBy('recon_date', 'asc')
                ->get();

            for ($i = 0; $i < count($staff); $i++) {
                $bonus_total_sum_2 = DB::table('bonus_request_records as l')
                    ->where('l.status', '=',  1)
                    ->where('l.percentage', '=',  2)
                    ->where('l.user_id', '=',  $staff[$i]->id)
                    ->whereYear('created_at', '2022')->sum('amount');

                $bonus_total_sum_3 = DB::table('bonus_request_records as l')
                    ->where('l.status', '=',  1)
                    ->where('l.percentage', '=',  3)
                    ->where('l.user_id', '=',  $staff[$i]->id)
                    ->whereYear('created_at', '2022')->sum('amount');

                $staff[$i]->bonus_total_sum_2 = $bonus_total_sum_2;
                $staff[$i]->bonus_total_sum_3 = $bonus_total_sum_3;
            }

            $bonus_total_sum_2 = DB::table('bonus_request_records as l')
                ->where('l.status', '=',  1)
                ->where('l.percentage', '=',  2)
                ->whereYear('created_at', '2022')->sum('amount');

            $bonus_total_sum_3 = DB::table('bonus_request_records as l')
                ->where('l.status', '=',  1)
                ->where('l.percentage', '=',  3)
                ->whereYear('created_at', '2022')->sum('amount');

            return view('dashboard.reports.bonus.index', [
                'OfficeBankAccount' => $OfficeBankAccount,
                'staffs' => $staff,
                'bonus_total_sum_2' => $bonus_total_sum_2,
                'bonus_total_sum_3' => $bonus_total_sum_3,
                'recon_date' => $recon_date
            ]);
        } else {

            return redirect()->route('dashboard.index');
        }
    }

    public function ReportBonusEstimate()
    {
        $current_user = auth()->user();
        $role = $current_user->menuroles;

        if (in_array($current_user->menuroles, ['admin', 'management']) || $current_user->id == 37) {
            $OfficeBankAccount = OfficeBankAccount::where('status', '=', 1)->get();

            $staff = Users::where('status', '<>', 99)->whereIn('menuroles', ['lawyer', 'clerk', 'chambering'])->orderBy('name', 'asc')->get();
            $recon_date = VoucherDetails::where('recon_date', '<>', null)->select('recon_date')
                ->groupBy('recon_date')
                ->get();

            $recon_date = VoucherMain::where('recon_date', '<>', null)->select('recon_date')
                ->groupBy('recon_date')
                ->orderBy('recon_date', 'asc')
                ->get();

            for ($i = 0; $i < count($staff); $i++) {
                $bonus_total_sum_2 = DB::table('bonus_request_records as l')
                    ->where('l.status', '=',  1)
                    ->where('l.percentage', '=',  2)
                    ->where('l.user_id', '=',  $staff[$i]->id)
                    ->whereYear('created_at', '2022')->sum('amount');

                $bonus_total_sum_3 = DB::table('bonus_request_records as l')
                    ->where('l.status', '=',  1)
                    ->where('l.percentage', '=',  3)
                    ->where('l.user_id', '=',  $staff[$i]->id)
                    ->whereYear('created_at', '2022')->sum('amount');

                $staff[$i]->bonus_total_sum_2 = $bonus_total_sum_2;
                $staff[$i]->bonus_total_sum_3 = $bonus_total_sum_3;
            }

            $bonus_total_sum_2 = DB::table('bonus_request_records as l')
                ->where('l.status', '=',  1)
                ->where('l.percentage', '=',  2)
                ->whereYear('created_at', '2022')->sum('amount');

            $bonus_total_sum_3 = DB::table('bonus_request_records as l')
                ->where('l.status', '=',  1)
                ->where('l.percentage', '=',  3)
                ->whereYear('created_at', '2022')->sum('amount');

            return view('dashboard.reports.bonus.index-est', [
                'OfficeBankAccount' => $OfficeBankAccount,
                'staffs' => $staff,
                'bonus_total_sum_2' => $bonus_total_sum_2,
                'bonus_total_sum_3' => $bonus_total_sum_3,
                'recon_date' => $recon_date
            ]);
        } else {

            return redirect()->route('dashboard.index');
        }
    }

    public function getStaffBonusReport(Request $request, $id)
    {
        // $rows = DB::table('loan_case as l')
        //     ->join('loan_case_bill_main as b', 'l.id', '=', 'b.case_id')
        //     ->select('b.*', 'l.case_ref_no', 'l.status as case_status')
        //     ->where('l.status', '<>',  99)
        //     ->whereNotIn('b.status', [0,99])
        //     ->where(function ($q) use($id) {
        //         $q->where('l.lawyer_id', '=', $id)
        //             ->orWhere('l.clerk_id', '=', $id);
        //     })->get();

        // $rows = DB::table('bonus as l')
        // ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
        // ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
        // ->select('l.*', 'c.case_ref_no', 'c.status as case_status','b.name','b.bill_no','b.pfee1','b.pfee2')
        // ->where('c.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where('b.marketing', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->whereNotIn('b.status', [0,99])
        // ->where(function ($q) use($id) {
        //     $q->where('l.lawyer_id', '=', $id)
        //         ->orWhere('c.clerk_id', '=', $id);
        // })->get();

        // $rows = DB::table('bonus as l')
        // ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
        // ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
        // ->select('l.*', 'c.case_ref_no', 'c.status as case_status','b.name','b.bill_no','b.pfee1','b.pfee2')
        // ->where('c.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where('b.marketing', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->where('l.user_id', '=',  $id)
        // ->whereNotIn('b.status', [0,99])->get();

        $current_user = auth()->user();

        $rows = DB::table('bonus_estimate as l')
            ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
            ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
            ->select('l.*', 'c.case_ref_no', 'c.status as case_status', 'b.name', 'b.bill_no', 'b.pfee1', 'b.pfee2', 'b.referral_a1', 'b.referral_a2',  'b.referral_a3', 'b.referral_a4',   'b.marketing', 'b.uncollected', 'b.id as bill_id', 'c.agreed_fee', 'c.targeted_collect_amount')
            ->where('c.status', '<>',  99)
            // ->where('b.marketing', '>',  0)
            // ->where('b.uncollected', '>',  0)
            ->where('l.user_id', '=',  $id)
            ->where('l.status', '=',  1)
            ->whereNotIn('b.status', [0, 99]);

        if ($request->input("claimed")) {
            $rows = $rows->where("claimed", "=", $request->input("claimed"));
        }

        $rows = $rows->orderBy('case_id', 'asc')->get();




        // $bonus = DB::table('loan_case as l')
        // ->join('loan_case_bill_main as b', 'l.id', '=', 'b.case_id')
        // ->select(
        //     DB::raw("SUM(staff_bonus_2_per) as staff_bonus_2_per"),
        //     DB::raw("SUM(staff_bonus_3_per) as staff_bonus_3_per"),
        //     DB::raw("SUM(staff_bonus_2_per_p1) as staff_bonus_2_per_p1"),
        //     DB::raw("SUM(staff_bonus_3_per_p1) as staff_bonus_3_per_p1"),
        // )
        // ->where('l.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where(function ($q) use($id) {
        //     $q->where('l.lawyer_id', '=', $id)
        //         ->orWhere('l.clerk_id', '=', $id);
        // })->first();

        // $bonus = DB::table('bonus as l')
        // ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
        // ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
        // ->select(
        //     DB::raw("SUM(bonus_2_percent) as staff_bonus_2_per"),
        //     DB::raw("SUM(bonus_3_percent) as staff_bonus_3_per"),
        //     DB::raw("SUM(p1_bonus_2_percent) as staff_bonus_2_per_p1"),
        //     DB::raw("SUM(p1_bonus_3_percent) as staff_bonus_3_per_p1"),
        // )
        // ->where('c.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where('b.marketing', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->whereNotIn('b.status', [0,99])
        // ->where('l.user_id', '=',  $id)->first();

        $bonus_total_sum = DB::table('bonus_request_records as l')
            ->where('l.status', '=',  1)
            ->whereYear('created_at', '2021')
            ->where('l.user_id', '=',  $id)->sum('amount');

        // return $bonus_total_sum;

        $bonus = DB::table('bonus_estimate as l')
            ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
            ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
            ->select(
                DB::raw("SUM(bonus_2_percent) as staff_bonus_2_per"),
                DB::raw("SUM(bonus_3_percent) as staff_bonus_3_per"),
            )
            ->where('c.status', '<>',  99)
            ->where('b.status', '<>',  99)
            ->whereNotIn('b.status', [0, 99])
            ->where('l.status', '=',  1)
            ->where('l.user_id', '=',  $id)->first();

        if (count($rows) > 0) {
            for ($i = 0; $i < count($rows); $i++) {
                $pfee1 = $rows[$i]->pfee1;
                $pfee2 = $rows[$i]->pfee2;

                $referral_a1 = $rows[$i]->referral_a1;
                $referral_a2 = $rows[$i]->referral_a2;
                $referral_a3 = $rows[$i]->referral_a3;
                $referral_a4 = $rows[$i]->referral_a4;
                $marketing = $rows[$i]->marketing;
                $uncollected = $rows[$i]->uncollected;

                $prof_bal = 0;
                $prof_bal = $pfee1 + $pfee2 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;

                $rows[$i]->bonus_25_per = $prof_bal * 0.2;
                $rows[$i]->bonus_50_per = $prof_bal * 0.5;
                $rows[$i]->bonus_2_per_new = $prof_bal * 0.02;
                $rows[$i]->bonus_3_per_new  = $prof_bal * 0.03;
            }
        }



        $staff = Users::where('status', '<>', 99)->where('id', $id)->first();

        $bonusList = view('dashboard.reports.bonus.tbl-bonus-list', compact('rows', 'staff', 'bonus', 'current_user'))->render();

        return [
            'status' => 1,
            'bonusList' => $bonusList,
            'bonus' => $bonus,
        ];
    }

    public function getBonusReportEstimate(Request $request, $id)
    {
        // $rows = DB::table('loan_case as l')
        //     ->join('loan_case_bill_main as b', 'l.id', '=', 'b.case_id')
        //     ->select('b.*', 'l.case_ref_no', 'l.status as case_status')
        //     ->where('l.status', '<>',  99)
        //     ->whereNotIn('b.status', [0,99])
        //     ->where(function ($q) use($id) {
        //         $q->where('l.lawyer_id', '=', $id)
        //             ->orWhere('l.clerk_id', '=', $id);
        //     })->get();

        // $rows = DB::table('bonus as l')
        // ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
        // ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
        // ->select('l.*', 'c.case_ref_no', 'c.status as case_status','b.name','b.bill_no','b.pfee1','b.pfee2')
        // ->where('c.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where('b.marketing', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->whereNotIn('b.status', [0,99])
        // ->where(function ($q) use($id) {
        //     $q->where('l.lawyer_id', '=', $id)
        //         ->orWhere('c.clerk_id', '=', $id);
        // })->get();

        // $rows = DB::table('bonus as l')
        // ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
        // ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
        // ->select('l.*', 'c.case_ref_no', 'c.status as case_status','b.name','b.bill_no','b.pfee1','b.pfee2')
        // ->where('c.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where('b.marketing', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->where('l.user_id', '=',  $id)
        // ->whereNotIn('b.status', [0,99])->get();

        $current_user = auth()->user();

        $rows = DB::table('bonus_estimate as l')
            ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
            ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
            ->select('l.*', 'c.case_ref_no', 'c.status as case_status', 'b.name', 'b.bill_no', 'b.pfee1', 'b.pfee2', 'b.referral_a1', 'b.referral_a2',  'b.referral_a3', 'b.referral_a4',   'b.marketing', 'b.uncollected', 'b.id as bill_id', 'c.agreed_fee', 'c.targeted_collect_amount')
            ->where('c.status', '<>',  99)
            // ->where('b.marketing', '>',  0)
            // ->where('b.uncollected', '>',  0)
            ->where('l.user_id', '=',  $id)
            ->where('l.status', '=',  1)
            ->whereNotIn('b.status', [0, 99]);

        if ($request->input("claimed")) {
            $rows = $rows->where("claimed", "=", $request->input("claimed"));
        }

        $rows = $rows->orderBy('case_id', 'asc')->get();




        // $bonus = DB::table('loan_case as l')
        // ->join('loan_case_bill_main as b', 'l.id', '=', 'b.case_id')
        // ->select(
        //     DB::raw("SUM(staff_bonus_2_per) as staff_bonus_2_per"),
        //     DB::raw("SUM(staff_bonus_3_per) as staff_bonus_3_per"),
        //     DB::raw("SUM(staff_bonus_2_per_p1) as staff_bonus_2_per_p1"),
        //     DB::raw("SUM(staff_bonus_3_per_p1) as staff_bonus_3_per_p1"),
        // )
        // ->where('l.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where(function ($q) use($id) {
        //     $q->where('l.lawyer_id', '=', $id)
        //         ->orWhere('l.clerk_id', '=', $id);
        // })->first();

        // $bonus = DB::table('bonus as l')
        // ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
        // ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
        // ->select(
        //     DB::raw("SUM(bonus_2_percent) as staff_bonus_2_per"),
        //     DB::raw("SUM(bonus_3_percent) as staff_bonus_3_per"),
        //     DB::raw("SUM(p1_bonus_2_percent) as staff_bonus_2_per_p1"),
        //     DB::raw("SUM(p1_bonus_3_percent) as staff_bonus_3_per_p1"),
        // )
        // ->where('c.status', '<>',  99)
        // ->where('b.status', '<>',  99)
        // ->where('b.marketing', '>',  0)
        // ->where('b.uncollected', '>',  0)
        // ->whereNotIn('b.status', [0,99])
        // ->where('l.user_id', '=',  $id)->first();

        $bonus = DB::table('bonus_estimate as l')
            ->leftJoin('loan_case_bill_main as b', 'l.bill_id', '=', 'b.id')
            ->leftJoin('loan_case as c', 'c.id', '=', 'l.case_id')
            ->select(
                DB::raw("SUM(bonus_2_percent) as staff_bonus_2_per"),
                DB::raw("SUM(bonus_3_percent) as staff_bonus_3_per"),
            )
            ->where('c.status', '<>',  99)
            ->where('b.status', '<>',  99)
            ->whereNotIn('b.status', [0, 99])
            ->where('l.status', '=',  1)
            ->where('l.user_id', '=',  $id)->first();

        if (count($rows) > 0) {
            for ($i = 0; $i < count($rows); $i++) {
                $pfee1 = $rows[$i]->pfee1;
                $pfee2 = $rows[$i]->pfee2;

                $referral_a1 = $rows[$i]->referral_a1;
                $referral_a2 = $rows[$i]->referral_a2;
                $referral_a3 = $rows[$i]->referral_a3;
                $referral_a4 = $rows[$i]->referral_a4;
                $marketing = $rows[$i]->marketing;
                $uncollected = $rows[$i]->uncollected;

                $prof_bal = 0;
                $prof_bal = $pfee1 + $pfee2 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;

                $rows[$i]->bonus_25_per = $prof_bal * 0.2;
                $rows[$i]->bonus_50_per = $prof_bal * 0.5;
                $rows[$i]->bonus_2_per_new = $prof_bal * 0.02;
                $rows[$i]->bonus_3_per_new  = $prof_bal * 0.03;
            }
        }



        $staff = Users::where('status', '<>', 99)->where('id', $id)->first();

        $bonusList = view('dashboard.reports.bonus.tbl-bonus-list-est', compact('rows', 'staff', 'bonus', 'current_user'))->render();

        return [
            'status' => 1,
            'bonusList' => $bonusList,
            'bonus' => $bonus,
        ];
    }

    public function getStaffBonusReportBak(Request $request, $id)
    {
        $rows = DB::table('loan_case as l')
            ->join('loan_case_bill_main as b', 'l.id', '=', 'b.case_id')
            ->select('b.*', 'l.case_ref_no', 'l.status as case_status')
            ->where('l.status', '<>',  99)
            ->whereNotIn('b.status', [0, 99])
            ->where(function ($q) use ($id) {
                $q->where('l.lawyer_id', '=', $id)
                    ->orWhere('l.clerk_id', '=', $id);
            })->get();

        $bonus = DB::table('loan_case as l')
            ->join('loan_case_bill_main as b', 'l.id', '=', 'b.case_id')
            ->select(
                DB::raw("SUM(staff_bonus_2_per) as staff_bonus_2_per"),
                DB::raw("SUM(staff_bonus_3_per) as staff_bonus_3_per"),
                DB::raw("SUM(staff_bonus_2_per_p1) as staff_bonus_2_per_p1"),
                DB::raw("SUM(staff_bonus_3_per_p1) as staff_bonus_3_per_p1"),
            )
            ->where('l.status', '<>',  99)
            ->where('b.status', '<>',  99)
            ->where(function ($q) use ($id) {
                $q->where('l.lawyer_id', '=', $id)
                    ->orWhere('l.clerk_id', '=', $id);
            })->first();



        $staff = Users::where('status', '<>', 99)->where('id', $id)->first();

        $bonusList = view('dashboard.reports.bonus.tbl-bonus-list', compact('rows', 'staff', 'bonus'))->render();

        return [
            'status' => 1,
            'bonusList' => $bonusList,
            'bonus' => $bonus,
        ];
    }

    public function loadCaseQuotation($id)
    {
        $category = AccountCategory::where('status', '=', 1)->get();

        $quotation = array();
        $item_id = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('loan_case_bill_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id', 'a.pfee1_item')
                ->where('qd.loan_case_main_bill_id', '=',  $id)
                ->where('qd.status', '=',  1)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->get();

            array_push($quotation,  array('category' => $category[$i], 'account_details' => $QuotationTemplateDetails));

            for ($j = 0; $j < count($QuotationTemplateDetails); $j++) {
                array_push($item_id,  $QuotationTemplateDetails[$j]->account_item_id);
            }
        }

        return response()->json([
            'view' => view('dashboard.reports.bonus.tbl-quotation-list', compact('quotation'))->render(),
        ]);
    }

    public function loadBonusDetails($id)
    {

        $rows = DB::table('loan_case as l')
            ->join('loan_case_bill_main as b', 'l.id', '=', 'b.case_id')
            ->leftJoin('referral as r1', 'r1.id', '=', 'b.referral_a1_ref_id')
            ->leftJoin('referral as r2', 'r2.id', '=', 'b.referral_a2_ref_id')
            ->leftJoin('referral as r3', 'r3.id', '=', 'b.referral_a3_ref_id')
            ->leftJoin('referral as r4', 'r4.id', '=', 'b.referral_a4_ref_id')
            ->leftJoin('users as u', 'u.id', '=', 'b.marketing_id')
            ->select('b.*', 'l.case_ref_no', 'r1.name as referral_name_1', 'r2.name as referral_name_2', 'r3.name as referral_name_3', 'r4.name as referral_name_4', 'u.name as marketing_name')
            ->where('l.status', '<>',  99)
            ->where('b.id', '=',  $id)
            ->whereNotIn('b.status', [0, 99])->first();


        return response()->json([
            'view' => view('dashboard.reports.bonus.bonus-details', compact('rows'))->render(),
        ]);
    }

    public function updateSummarySum(Request $request)
    {

        $staff_bonus_2_per = DB::table('loan_case_bill_main AS a')
            ->join('loan_case as c', 'c.id', '=', 'a.case_id')
            ->select(
                DB::raw("SUM(staff_bonus_2_per) as staff_bonus_2_per"),
                DB::raw("SUM(staff_bonus_3_per) as staff_bonus_3_per"),
                DB::raw("SUM(disb) as disb"),
                DB::raw("SUM(bill_dis) as bill_dis"),
                DB::raw("SUM(disb_balance) as disb_balance"),
                DB::raw("SUM(bill_recv) as bill_recv"),
                DB::raw("SUM(trust_recv) as trust_recv")
            )
            ->where('a.status', '<>', '99');

        if ($request->input("user") <> 0) {
            $users = User::where('id', '=', $request->input("user"))->first();

            if ($users) {
                if ($users->menuroles == 'lawyer') {
                    $staff_bonus_2_per = $staff_bonus_2_per->where('c.lawyer_id', '=', $request->input("user"));
                } else if ($users->menuroles == 'clerk') {
                    $staff_bonus_2_per = $staff_bonus_2_per->where('c.clerk_id', '=', $request->input("user"));
                }
            }
        }

        $staff_bonus_2_per = $staff_bonus_2_per->get();

        return $staff_bonus_2_per;
    }

    public function getSummaryReportList(Request $request)
    {
        if ($request->ajax()) {
            //Default branch
            $branch_id = 1;

            // if (!empty($request->input('branch_id'))) {
            //     $branch_id = $request->input('branch_id');
            // }

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $CaseBill = DB::table('loan_case_bill_main AS a')
                ->join('loan_case as c', 'c.id', '=', 'a.case_id')
                ->select('a.*', 'c.case_ref_no')
                ->where('a.status', '<>', '99')
                ->where('a.bln_invoice', '=', 1);


            if ($request->input("user") <> 0) {
                $users = User::where('id', '=', $request->input("user"))->first();

                if ($users) {
                    if ($users->menuroles == 'lawyer') {
                        $CaseBill = $CaseBill->where('c.lawyer_id', '=', $request->input("user"));
                    } else if ($users->menuroles == 'clerk') {
                        $CaseBill = $CaseBill->where('c.clerk_id', '=', $request->input("user"));
                    }
                }
            }

            if ($request->input("branch") <> 0) {
                $CaseBill = $CaseBill->where('a.invoice_branch_id', '=', $request->input("branch"));
            }



            $CaseBill = $CaseBill->orderBy('a.invoice_no', 'ASC')->get();






            return DataTables::of($CaseBill, $request)
                ->addIndexColumn()
                ->editColumn('status', function ($data) {
                    if ($data->status == '2')
                        return '<span class="label bg-info">Open</span>';
                    elseif ($data->status == '0')
                        return '<span class="label bg-success">Closed</span>';
                    elseif ($data->status == '1')
                        return '<span class="label bg-purple">Running</span>';
                    elseif ($data->status == '3')
                        return '<span class="label bg-warning">KIV</span>';
                    elseif ($data->status == '99')
                        return '<span class="label bg-danger">Aborted</span>';
                    else
                        return '<span class="label bg-danger">Overdue</span>';
                })
                ->editColumn('staff_bonuss_2_per_paid', function ($data) {
                    if ($data->staff_bonuss_2_per_paid == 0)
                        return '<span class="label bg-warning">No</span>';
                    elseif ($data->staff_bonuss_2_per_paid == 1)
                        return '<span class="label bg-success">Yes</span>';
                })
                ->editColumn('staff_bonuss_3_per_paid', function ($data) {
                    if ($data->staff_bonuss_3_per_paid == 0)
                        return '<span class="label bg-warning">No</span>';
                    elseif ($data->staff_bonuss_3_per_paid == 1)
                        return '<span class="label bg-success">Yes</span>';
                })
                ->addColumn('prof_bal', function ($data) {
                    // $pfee1 = 


                    $prof_bal = $data->pfee1 + $data->pfee2 - $data->referral_a1 - $data->referral_a2 - $data->referral_a3 - $data->referral_a4 - $data->marketing - $data->uncollected;

                    return $prof_bal;
                })
                ->addColumn('disb_bal', function ($data) {
                    $disb_bal = $data->disb - $data->used_amt;

                    return $disb_bal;
                })
                ->addColumn('actual_bal', function ($data) {
                    $actual_bal = ($data->pfee1 + $data->pfee2 - $data->referral_a1 - $data->referral_a2 - $data->referral_a3 - $data->referral_a4 - $data->marketing - $data->uncollected) + ($data->disb - $data->used_amt);

                    return $actual_bal;
                })
                ->addColumn('actual_bal_deduct_bonus', function ($data) {
                    $actual_bal_deduct_bonus = ($data->pfee1 + $data->pfee2 - $data->referral_a1 - $data->referral_a2 - $data->referral_a3 - $data->referral_a4 - $data->marketing - $data->uncollected) + ($data->disb - $data->used_amt);
                    $actual_bal_deduct_bonus = $actual_bal_deduct_bonus - ($data->staff_bonus_2_per + $data->staff_bonus_3_per + $data->lawyer_bonus_2_per + $data->lawyer_bonus_3_per);
                    return $actual_bal_deduct_bonus;
                })
                ->addColumn('actual_bal_deduct_p1_bonus', function ($data) {
                    $actual_bal_deduct_p1_bonus = ($data->pfee1 + $data->pfee2 - $data->referral_a1 - $data->referral_a2 - $data->referral_a3 - $data->referral_a4 - $data->marketing - $data->uncollected) + ($data->disb - $data->used_amt);
                    $actual_bal_deduct_p1_bonus = $actual_bal_deduct_p1_bonus - ($data->staff_bonus_2_per_p1 + $data->staff_bonus_3_per_p1 + $data->lawyer_bonus_2_per_p1 + $data->lawyer_bonus_3_per_p1);
                    return $actual_bal_deduct_p1_bonus;
                })
                ->addColumn('r1_info', function ($data) {
                    $agent = '-';

                    if ($data->referral_a1_id != 0) {
                        $agent = $data->referral_a1_id;
                    }

                    return '<b>Agent: </b>' . $agent . '<br/><b>Payment Date: </b>' . $data->referral_a1_payment_date . '<br/><b>TRX ID: </b>' . $data->referral_a1_trx_id;
                })
                ->addColumn('r2_info', function ($data) {

                    $agent = '-';

                    if ($data->referral_a2_id != 0) {
                        $agent = $data->referral_a2_id;
                    }

                    return '<b>Agent: </b>' . $agent . '<br/><b>Payment Date: </b>' . $data->referral_a2_payment_date . '<br/><b>TRX ID: </b>' . $data->referral_a2_trx_id;
                })
                ->addColumn('r3_info', function ($data) {

                    $agent = '-';

                    if ($data->referral_a3_id != 0) {
                        $agent = $data->referral_a3_id;
                    }

                    return '<b>Agent: </b>' . $agent . '<br/><b>Payment Date: </b>' . $data->referral_a3_payment_date . '<br/><b>TRX ID: </b>' . $data->referral_a3_trx_id;
                })
                ->addColumn('r4_info', function ($data) {

                    $agent = '-';

                    if ($data->referral_a4_id != 0) {
                        $agent = $data->referral_a4_id;
                    }
                    return '<b>Agent: </b>' . $agent . '<br/><b>Payment Date: </b>' . $data->referral_a4_payment_date . '<br/><b>TRX ID: </b>' . $data->referral_a4_trx_id;
                })
                ->editColumn('lawyer_bonuss_2_per_paid', function ($data) {
                    if ($data->lawyer_bonuss_2_per_paid == 0)
                        return '<span class="label bg-warning">No</span>';
                    elseif ($data->lawyer_bonuss_2_per_paid == 1)
                        return '<span class="label bg-success">Yes</span>';
                })
                ->editColumn('lawyer_bonuss_3_per_paid', function ($data) {
                    if ($data->lawyer_bonuss_3_per_paid == 0)
                        return '<span class="label bg-warning">No</span>';
                    elseif ($data->lawyer_bonuss_3_per_paid == 1)
                        return '<span class="label bg-success">Yes</span>';
                })
                ->editColumn('case_ref_no', function ($row) {
                    if ($row->case_id != 0) {
                        $actionBtn = ' <a target="_blank" href="/case/' . $row->case_id . '" class="  " >' . $row->case_ref_no . ' </a> [' . $row->invoice_no . ']<br/>' . $row->name;
                    } else {
                        $actionBtn = $row->case_ref;
                    }

                    return $actionBtn;
                })
                ->editColumn('prof_balance', function ($row) {

                    $pfee = 0;
                    $pfee1 = $row->pfee1;
                    $pfee2 = $row->pfee2;
                    $referral_a1 = $row->referral_a1;
                    $referral_a2 = $row->referral_a2;
                    $referral_a3 = $row->referral_a3;
                    $referral_a4 = $row->referral_a4;
                    $marketing = $row->marketing;
                    $uncollected = $row->uncollected;

                    $prof_bal = $pfee1 + $pfee2 - $referral_a1 - $referral_a2 - $referral_a3 - $referral_a4 - $marketing - $uncollected;

                    return $prof_bal;
                })
                ->rawColumns([
                    'status', 'case_ref_no', 'prof_balance', 'staff_bonuss_2_per_paid', 'staff_bonuss_3_per_paid', 'lawyer_bonuss_2_per_paid',
                    'lawyer_bonuss_3_per_paid', 'prof_bal', 'disb_bal', 'actual_bal', 'r1_info', 'r2_info', 'r3_info', 'r4_info', 'actual_bal_deduct_bonus', 'actual_bal_deduct_p1_bonus'
                ])
                ->make(true);
        }
    }

    public function getSummaryReport(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $CaseBill = DB::table('loan_case_bill_main AS a')
                ->join('loan_case as c', 'c.id', '=', 'a.case_id')
                ->select('a.*', 'c.case_ref_no')
                ->where('a.status', '<>', '99')
                ->where('a.bln_invoice', '=', 1);

            if ($request->input("user") <> 0) {
                $users = User::where('id', '=', $request->input("user"))->first();

                if ($users) {
                    if ($users->menuroles == 'lawyer') {
                        $CaseBill = $CaseBill->where('c.lawyer_id', '=', $request->input("user"));
                    } else if ($users->menuroles == 'clerk') {
                        $CaseBill = $CaseBill->where('c.clerk_id', '=', $request->input("user"));
                    }
                }
            }

            if ($request->input("branch") <> 0) {
                $CaseBill = $CaseBill->where('a.invoice_branch_id', '=', $request->input("branch"));
            }

            if ($request->input("year") <> 0) {
                $CaseBill = $CaseBill->whereYear('a.invoice_date', $request->input("year"));
            }

            if ($request->input("month") <> 0) {
                $CaseBill = $CaseBill->whereMonth('a.invoice_date', $request->input("month"));
            }

            // if ($request->input("date_option") <> 99) {
            //     if ($request->input("date_option") == 1) {
            //         if ($request->input("year") <> 0) {
            //             $CaseBill = $CaseBill->whereYear('a.created_at', $request->input("year"));
            //         }
            //     } else if ($request->input("date_option") == 2) {
            //         if ($request->input("month") <> 0) {
            //             $CaseBill = $CaseBill->whereMonth('a.created_at', $request->input("month"));
            //         }
            //     } else if ($request->input("date_option") == 3) {
            //         $CaseBill = $CaseBill->whereDay('a.created_at', $request->input("day"));
            //     }
            // }

            $CaseBill = $CaseBill->orderBy('a.invoice_no', 'ASC')->get();

            if (count($CaseBill) > 0) {
                for ($i = 0; $i < count($CaseBill); $i++) {
                    $sumBonus = DB::table('bonus_request_list AS a')
                        ->join('bonus_request_records as b', 'b.bonus_request_list_id', '=', 'a.id')
                        ->select('a.*', 'c.case_ref_no')
                        ->where('a.status', 2)
                        ->where('b.percentage', 3)
                        ->whereNotNull('a.selected_bill_id')
                        ->where('a.selected_bill_id', $CaseBill[$i]->id)
                        ->sum('b.amount');

                    $CaseBill[$i]->bonus_3 = $sumBonus;


                    $sumBonus = DB::table('bonus_request_list AS a')
                        ->join('bonus_request_records as b', 'b.bonus_request_list_id', '=', 'a.id')
                        ->select('a.*', 'c.case_ref_no')
                        ->where('a.status', 2)
                        ->where('b.percentage', 5)
                        ->whereNotNull('a.selected_bill_id')
                        ->where('a.selected_bill_id', $CaseBill[$i]->id)
                        ->sum('b.amount');

                    $CaseBill[$i]->bonus_5 = $sumBonus;
                }
            }

            return response()->json([
                'view' => view('dashboard.reports.summary-report.tbl-summary-report', compact('CaseBill'))->render(),
            ]);
        }
    }

    public function getQuotationReport(Request $request)
    {
        if ($request->ajax()) {

            $current_user = auth()->user();
            $userRoles = $current_user->menuroles;

            $CaseBill = DB::table('loan_case_bill_main AS a')
                ->join('loan_case as c', 'c.id', '=', 'a.case_id')
                ->select('a.*', 'c.case_ref_no')
                ->where('a.status', '<>', '99')
                ->where('a.bln_invoice', '=', 0);

            if ($request->input("user") <> 0) {
                $users = User::where('id', '=', $request->input("user"))->first();

                if ($users) {
                    if ($users->menuroles == 'lawyer') {
                        $CaseBill = $CaseBill->where('c.lawyer_id', '=', $request->input("user"));
                    } else if ($users->menuroles == 'clerk') {
                        $CaseBill = $CaseBill->where('c.clerk_id', '=', $request->input("user"));
                    }
                }
            }

            if ($request->input("branch") <> 0) {
                $CaseBill = $CaseBill->where('c.branch_id', '=', $request->input("branch"));
            }

            if ($request->input("year") <> 0) {
                $CaseBill = $CaseBill->whereYear('a.created_at', $request->input("year"));
            }

            if ($request->input("month") <> 0) {
                $CaseBill = $CaseBill->whereMonth('a.created_at', $request->input("month"));
            }

            $CaseBill = $CaseBill->orderBy('a.invoice_no', 'ASC')->get();

            if (count($CaseBill) > 0) {
                for ($i = 0; $i < count($CaseBill); $i++) {
                    $sumBonus = DB::table('bonus_request_list AS a')
                        ->join('bonus_request_records as b', 'b.bonus_request_list_id', '=', 'a.id')
                        ->select('a.*', 'c.case_ref_no')
                        ->where('a.status', 2)
                        ->where('b.percentage', 3)
                        ->whereNotNull('a.selected_bill_id')
                        ->where('a.selected_bill_id', $CaseBill[$i]->id)
                        ->sum('b.amount');

                    $CaseBill[$i]->bonus_3 = $sumBonus;


                    $sumBonus = DB::table('bonus_request_list AS a')
                        ->join('bonus_request_records as b', 'b.bonus_request_list_id', '=', 'a.id')
                        ->select('a.*', 'c.case_ref_no')
                        ->where('a.status', 2)
                        ->where('b.percentage', 5)
                        ->whereNotNull('a.selected_bill_id')
                        ->where('a.selected_bill_id', $CaseBill[$i]->id)
                        ->sum('b.amount');

                    $CaseBill[$i]->bonus_5 = $sumBonus;
                }
            }

            return response()->json([
                'view' => view('dashboard.reports.quotation.tbl-report', compact('CaseBill'))->render(),
            ]);
        }
    }

    function view($id)
    {
        $account_template_cat = DB::table('voucher')
            ->join('loan_case', 'loan_case.id', '=', 'voucher.case_id')
            ->join('loan_case_account', 'loan_case_account.id', '=', 'voucher.account_details_id')
            ->join('users', 'users.id', '=', 'voucher.user_id')
            ->select('voucher.*', 'loan_case.case_ref_no', 'loan_case_account.item_name', 'users.name')
            ->where('voucher.id', '=', $id)
            ->first();

        return response()->json(['status' => 1, 'data' => $account_template_cat]);
    }

    public function create()
    {
        $account_category = AccountCategory::all();

        return view('dashboard.quotation.create', [
            'account_category' => $account_category
        ]);
    }

    public function store(Request $request)
    {

        $quotationTemplateMain  = new QuotationTemplateMain();

        $quotationTemplateMain->name = $request->input('name');
        $quotationTemplateMain->remark = $request->input('remark');
        $quotationTemplateMain->status = $request->input('status');
        $quotationTemplateMain->created_at = date('Y-m-d H:i:s');
        $quotationTemplateMain->save();

        $quotationTemplateMain->save();

        $request->session()->flash('message', 'Successfully created new quotation');
        return redirect()->route('quotation.index');
    }

    public function edit($id)
    {
        $quotation = QuotationTemplateMain::where('id', '=', $id)->first();
        $account_category = AccountCategory::where('status', '=', 1)->get();

        $accounts = DB::table('account_item AS a')
            ->leftJoin('account_category AS ac', 'ac.id', '=', 'a.account_cat_id')
            ->select('a.*', 'ac.category')
            ->orderBy('name', 'ASC')
            ->get();

        $category = AccountCategory::where('status', '=', 1)->get();
        $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->get();


        $quotation_details = array();

        for ($i = 0; $i < count($category); $i++) {

            // $QuotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)
            //     ->where('account_cat_id', '=', $category[$i]->id)
            //     ->get();

            $QuotationTemplateDetails = DB::table('quotation_template_details AS qd')
                ->leftJoin('account_item AS a', 'a.id', '=', 'qd.account_item_id')
                ->select('qd.*', 'a.name as account_name', 'a.formula as account_formula', 'a.min as account_min', 'a.id as account_item_id')
                ->where('qd.acc_main_template_id', '=',  $id)
                ->where('a.account_cat_id', '=',  $category[$i]->id)
                ->orderBy('order_no', 'ASC')
                // ->orderBy('id', 'ASC')
                ->get();

            array_push($quotation_details,  array(
                'category' => $category[$i],
                'account_details' => $QuotationTemplateDetails
            ));
        }

        // return $quotation_details;



        // $account_template = AccountTemplateMain::where('id', '=', 1)->get();
        // $account_template_details = AccountTemplateDetails::where('acc_main_template_id', '=', $id)->get();

        // $account_template_cat = DB::table('loan_case_account')
        //     ->join('account_category', 'loan_case_account.account_cat_id', '=', 'account_category.id')
        //     ->select('account_category.id', 'account_category.category', 'taxable', 'percentage')
        //     ->distinct()
        //     ->groupBy('loan_case_account.id')
        //     ->where('loan_case_account.case_id', '=', 1)
        //     ->get();

        // $joinData = array();

        // for ($i = 0; $i < count($account_template_cat); $i++) {

        //     $account_template_details_by_cat = LoanCaseAccount::where('case_id', '=', $id)
        //         ->where('account_cat_id', '=', $account_template_cat[$i]->id)
        //         ->get();
        //     array_push($joinData,  array('category' => $account_template_cat[$i], 'account_details' => $account_template_details_by_cat));
        // }

        return view('dashboard.quotation.edit', [
            'quotation' => $quotation,
            'quotation_details' => $quotation_details,
            'accounts' => $accounts
            // 'account_template_with_cat' => $joinData
        ]);
    }

    public function updateBillSummary($id)
    {
        $pfee = 0;
        $disb = 0;
        $sst = 0;

        $referral_a1 = 0;
        $referral_a2 = 0;
        $marketing = 0;

        $LoanCaseBillMain = LoanCaseBillMain::where('id', '=', $id)->first();

        $loanBillDetails = DB::table('loan_case_bill_details AS bd')
            ->leftJoin('account_item AS a', 'a.id', '=', 'bd.account_item_id')
            ->select('bd.*', 'a.account_cat_id')
            ->where('bd.loan_case_main_bill_id', '=',  $id)
            ->get();

        for ($i = 0; $i < count($loanBillDetails); $i++) {

            if ($loanBillDetails[$i]->account_cat_id == 1) {
                $pfee += $loanBillDetails[$i]->quo_amount;
            }

            if ($loanBillDetails[$i]->account_cat_id == 3) {
                $disb += $loanBillDetails[$i]->quo_amount;
            }
        }

        $sst = $pfee * 0.06;
        $sst = number_format((float)$sst, 2, '.', '');

        $LoanCaseBillMain->pfee = $pfee;
        $LoanCaseBillMain->disb = $disb;
        $LoanCaseBillMain->sst = $sst;

        $referral_a1 = $LoanCaseBillMain->referral_a1;
        $referral_a2 = $LoanCaseBillMain->referral_a2;
        $marketing = $LoanCaseBillMain->marketing;

        $collected_amt = $LoanCaseBillMain->collected_amt;
        $collected_amt_sum = $collected_amt;

        // 

        if ($collected_amt >= 0) {

            if (($collected_amt - $pfee) >= 0) {
                $collected_amt = $collected_amt - $pfee;
                $LoanCaseBillMain->pfee_recv = $pfee;

                $sst = $pfee * 0.06;
                $sst = number_format((float)$sst, 2, '.', '');

                $LoanCaseBillMain->sst_recv = $sst;
            } else {
                $LoanCaseBillMain->pfee_recv = $collected_amt;

                $sst = $collected_amt * 0.06;
                $sst = number_format((float)$sst, 2, '.', '');

                $LoanCaseBillMain->sst_recv = $sst;
                $collected_amt = 0;
            }
        }

        if ($collected_amt >= 0) {
            if (($collected_amt - $disb) >= 0) {
                $collected_amt = $collected_amt - $disb;
                $LoanCaseBillMain->disb_recv = $disb;
            } else {
                $LoanCaseBillMain->disb_recv = $collected_amt;
                $collected_amt = 0;
            }
        }

        if ($collected_amt >= 0) {
            $collected_amt = $collected_amt - $referral_a1;
            $collected_amt = $collected_amt - $referral_a2;
            $collected_amt = $collected_amt - $marketing;

            $LoanCaseBillMain->uncollected = $collected_amt;
        }

        $LoanCaseBillMain->save();

        return response()->json(['status' => 1, 'data' => 'Updated bill details']);
    }

    function addAccountIntoQuotation(Request $request, $id)
    {

        $status = 1;
        $message = 'Added account item into quotation template';
        $current_user = auth()->user();
        $accountItem = AccountItem::where('id', '=', $id)->first();

        if ($accountItem) {
            $quotationTemplateDetails  = new QuotationTemplateDetails();

            $quotationTemplateDetails->acc_main_template_id = $id;
            $quotationTemplateDetails->account_item_id = $request->input('selected_account_id');
            $quotationTemplateDetails->max =  $accountItem->max;
            $quotationTemplateDetails->min =  $accountItem->max;
            $quotationTemplateDetails->amount =  $accountItem->max;
            $quotationTemplateDetails->formula =  $accountItem->formula;
            $quotationTemplateDetails->created_by =  $current_user->id;
            $quotationTemplateDetails->status = 1;
            $quotationTemplateDetails->created_at = date('Y-m-d H:i:s');
            $quotationTemplateDetails->save();
        }
        return response()->json(['status' => $status, 'data' => $message]);
    }

    function deleteAccountIntoQuotation(Request $request, $id)
    {
        $status = 1;
        $data = '';

        $quotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $id)->first();

        $quotationTemplateDetails->delete();

        return response()->json(['status' => $status, 'message' => 'Deleted account item']);
    }


    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        // $caseTemplateDetail = CaseTemplateDetails::all();

        $docTemplateDetailSelected = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->where('status', '=', 1)->get();
        $caseMasterListCategory = CaseMasterListCategory::all();
        $caseMasterListField = CaseMasterListField::all();

        $docTemplatePages = DB::table('document_template_pages')
            ->leftJoin('users', 'users.id', '=', 'document_template_pages.is_locked')
            ->select('document_template_pages.*', 'users.name')
            ->get();

        $current_user = auth()->user();

        // $docTemplatePage = DocumentTemplatePages::where('document_template_details_id', '=', $docTemplateDetailSelected[0]->id)->get();
        $docTemplateDetail = DocumentTemplateDetails::where('document_template_main_id', '=', $id)->get();
        $docTemplateMain = DocumentTemplateMain::where('id', '=', $id)->get();
        return view('dashboard.documentTemplate.show', [
            'template' => DocumentTemplateMain::where('id', '=', $id)->first(),
            'docTemplatePages' => $docTemplatePages,
            'docTemplateDetail' => $docTemplateDetail,
            'docTemplateMain' => $docTemplateMain,
            'caseMasterListField' => $caseMasterListField,
            'caseMasterListCategory' => $caseMasterListCategory
        ]);
    }

    public function updateQuotationBill(Request $request, $id)
    {
        $status = 1;
        $need_approval = 0;
        $totalAmount = 0;
        $message = 'Voucher requested';
        $billList = [];

        if ($request->input('bill_list') != null) {
            $billList = json_decode($request->input('bill_list'), true);
        }

        $current_user = auth()->user();

        if (count($billList) > 0) {

            for ($i = 0; $i < count($billList); $i++) {

                $quotationTemplateDetails = QuotationTemplateDetails::where('id', '=', $billList[$i]['id'])->first();

                $quotationTemplateDetails->min = $billList[$i]['min'];
                $quotationTemplateDetails->max = $billList[$i]['max'];
                $quotationTemplateDetails->order_no = $billList[$i]['order_no'];
                $quotationTemplateDetails->amount = $billList[$i]['amount'];
                $quotationTemplateDetails->updated_at = date('Y-m-d H:i:s');
                $quotationTemplateDetails->save();
            }
        }

        return response()->json(['status' => $status, 'data' => $message]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $validatedData = $request->validate([
        //     'name'             => 'required|min:1|max:64',
        //     'shortName'        => 'required|min:1|max:64',
        //     'is_default'       => 'required|in:true,false'
        // ]);
        $status = 1;
        $message = '';
        $current_user = auth()->user();


        try {
            $account = QuotationTemplateMain::where('id', '=', $id)->first();

            $account->name = $request->input('name');
            $account->remark = $request->input('remark');
            $account->status = $request->input('status');
            $account->updated_at = date('Y-m-d H:i:s');
            $account->save();
            $message = 'Quotation template information updated';
        } catch (\Throwable $e) {
            $status = 0;
            $message = $e;
        }

        $request->session()->flash('message', $message);
        return redirect()->route('quotation.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuLangList  $menuLangList
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, Request $request)
    {
    }
}
