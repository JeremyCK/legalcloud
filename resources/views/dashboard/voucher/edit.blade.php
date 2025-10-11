@extends('dashboard.base')
<style type="text/css">
    @media print {
        /* body * {
            background-color: white;
        } */

        /* .div2 * {
            visibility: visible;
        }

        .div2 {
            position: absolute;
            top: 40px;
            left: 30px;
        } */
    }
</style>
@section('content')

    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-12">

                    <div id="dVoucherInvoice" class="div2 invoice printableArea d_operation " style="margin-bottom:20px;">

                        <div class="row no-print" style="margin-bottom:20px">
                            <div class="col-12">
                                <a href="/voucher" class="btn btn-danger pull-left"><i class="ion-reply"></i> Back
                                </a>

                                {{-- @if ($voucherMain->lawyer_approval == 1 || $voucherMain->account_approval == 1) --}}
                                <button type="button" class="btn btn-warning pull-right" onclick="printlo()"
                                    style="margin-right: 5px;">
                                    <span><i class="fa fa-print"></i> Print</span>
                                </button>
                                {{-- @endif --}}
                            </div>

                            <div class="col-12">
                                <br />
                                <span>* Printing section</span><br />
                                <hr />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h2 class="page-header">
                                    <!-- Payment Voucher -->
                                    Voucher [{{ $voucherMain->voucher_no }}] -
                                    @if ($voucherMain->voucher_type == 1)
                                        Bill
                                    @else
                                        Trust
                                    @endif
                                    {{-- <small class="pull-right">Date: <?php echo date('d/m/Y'); ?> </small> --}}
                                    <small class="pull-right">Date:
                                        {{ date('d-m-Y', strtotime($voucherMain->created_at)) }} </small>
                                </h2>
                            </div>
                            <!-- /.col -->
                        </div>
                        <div class="row invoice-info">
                            <div class="col-sm-6 invoice-col">
                                <b>From</b>
                                <address class="print-formal">
                                    <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>
                                    Advocates & Solicitors<br>
                                    {!! $Branch->address !!}<br>
                                    <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
                                    <b>Email</b>: {{ $Branch->email }}
                                </address>
                                {{-- @if ($case->branch_id == '1')
                                    <address>
                                        <strong style="color: #2d659d">L H YEO & CO.</strong><br>
                                        Advocates & Solicitors<br>
                                        No, 62B, 2nd Floor, Jalan SS21/62, Damansara Utama<br>
                                        47400 Petaling Jaya, Selangor Darul Ehsan<br>
                                        <b>Phone</b>: +603-7727 1818 <b>Fax</b>: +603-7732 8818<br>
                                        <b>Email</b>: legal@lhyeo.com
                                    </address> 
                                @elseif($case->branch_id == '2')
                                    <address>
                                        <strong style="color: #2d659d">L H YEO & CO.</strong><br>
                                        Advocates & Solicitors<br>
                                        No, 13-2, 2nd Floor, Jalan Puteri 1/4, Bandar Puteri<br>
                                        47100 Puchong,Selangor <br>
                                        <b>Phone</b>: 03-7727 1818 <b>Fax</b>: 03-7732 8818<br>
                                        <b>Email</b>: legal@lhyeo.com
                                    </address>
                                @elseif($case->branch_id == '3')
                                    <address>
                                        <strong style="color: #2d659d">L H YEO & CO.</strong><br>
                                        Advocates & Solicitors<br>
                                        No. A-2-11, Plaza Arkadia, Jalan Resident, Desa ParkCity, 52200 Kuala Lumpur,
                                        Malaysia <br>
                                        <b>Phone</b>: +603-2732 7897 <b>Fax</b>: +603-6261 7896<br>
                                        <b>Email</b>: lhyeodp@lhyeo.com
                                    </address>
                                @elseif($case->branch_id == '4')
                                    <address>
                                        <strong class="text-red">RAMAKRISHNAN & CO.</strong><br>
                                        Advocates & Solicitors<br>
                                        NO. 11-1, JALAN PUTERI 1/4, BANDAR PUTERI PUCHONG, 47100 PUCHONG, SELANGOR,
                                        Malaysia <br>
                                        <b>Phone</b>: +603-8051 5556 <b>Fax</b>: +603-8051 9556<br>
                                        <b>Email</b>: rkco@rknan.com
                                    </address>
                                @elseif($case->branch_id == '5')
                                    <address>
                                        <strong class="text-red">L H YEO & CO. (Dataran Prima)</strong><br>
                                        Advocates & Solicitors<br>
                                        13-7, Block C1, Dataran prima, Jalan PJU 1/41, 47301 petaling Jaya, Selangor,
                                        Malaysia <br>
                                        <b>Phone</b>: +603-7887 0808 <b>Fax</b>: +603-7887 8838<br>
                                        <b>Email</b>: info@lhyeo-pj.com
                                    </address>
                                @else
                                    <address>
                                        <strong style="color: #2d659d">L H YEO & CO.</strong><br>
                                        Advocates & Solicitors<br>
                                        No, 62B, 2nd Floor, Jalan SS21/62, Damansara Utama<br>
                                        47400 Petaling Jaya, Selangor Darul Ehsan<br>
                                        <b>Phone</b>: +603-7727 1818 <b>Fax</b>: +603-7732 8818<br>
                                        <b>Email</b>: legal@lhyeo.com
                                    </address>
                                @endif --}}


                                <div class=""><b>Approved By (Lawyer): </b>{{ $lawyerName }}</div>
                                <div class=""><b>Approved Date : </b>
                                    @if ($voucherMain->lawyer_approval_date)
                                        {{-- {{ date('d-m-Y ', strtotime($voucherMain->lawyer_approval_date)) }} --}}
                                        {{ $voucherMain->lawyer_approval_date }}
                                    @else
                                        -
                                    @endif

                                </div>
                                <div class=""><b>Approved By (Account): </b>{{ $ApprovalName }}</div>
                                <div class=""><b>Request By: </b>{{ $requestName }}</div>
                            </div>
                            <div class="col-sm-6 invoice-col ">

                                <div class="row">


                                    <div class="col-4">
                                    </div>
                                    <div class="col-8 ">
                                        <b class="pull-right">To</b><br />
                                        <address>
                                            <strong class="text-blue pull-right"> {{ $voucherMain->payee }}</strong><br>
                                        </address>
                                        <table class="table-bordered" style="width:100%;padding:10px">
                                            <tr>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px"><b
                                                        class="pull-left">Payment
                                                        Type</b></td>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px">
                                                    @if ($voucherMain->payment_type == '1')
                                                        Cash
                                                    @elseif($voucherMain->payment_type == '2')
                                                        Cheque
                                                    @elseif($voucherMain->payment_type == '3')
                                                        Bank transfer
                                                    @elseif($voucherMain->payment_type == '4')
                                                        Credit Card
                                                    @elseif($voucherMain->payment_type == '5')
                                                        FPX
                                                    @elseif($voucherMain->payment_type == '6')
                                                        Contra
                                                    @elseif($voucherMain->payment_type == '7')
                                                        Jompay
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px"><b
                                                        class="pull-left">Payment Date</b></td>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px">
                                                    {{ $voucherMain->payment_date }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px"><b
                                                        class="pull-left">Payee Email</b></td>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px">
                                                    {{ $voucherMain->email }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px"><b
                                                        class="pull-left">Payee
                                                        Bank</b></td>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px">
                                                    {{ isset($bank->name) ? $bank->name : '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px"><b
                                                        class="pull-left">Payee Account
                                                        No</b></td>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px">
                                                    {{ isset($voucherMain->bank_account) ? $voucherMain->bank_account : '-' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px"><b
                                                        class="pull-left">Adjudication/Smartbox No/ID Pemfailan</b></td>
                                                <td style="border: 1px solid black !important;padding:5px;font-size:12px">
                                                    {{ isset($voucherMain->adjudication_no) ? $voucherMain->adjudication_no : '-' }}
                                                </td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>

                            </div>

                            <div class="col-sm-12 invoice-col" style="margin-top:20px ">

                                <div class="invoice-details row no-margin">
                                    <div class="col-md-6 col-lg-6"><b>Case Ref No: </b><a target="_blank"
                                            href="/case/{{ $case->id }}">{{ $case->case_ref_no }}</a> <br />
                                        <b>Client Name: </b>{{ $customer->name }}
                                    </div>
                                    <div class="col-md-6 col-lg-6 pull-right">
                                        <span class="pull-right"><b>Transaction ID:</b>
                                            {{ isset($voucherMain->transaction_id) ? $voucherMain->transaction_id : '-' }}</span>
                                    </div>
                                </div>


                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->

                        <div class="row">
                            <?php $total_amt = 0; ?>
                            <div class="col-12 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Description</th>
                                            <th class="text-right  no-print">Balance</th>

                                            @if ($voucherMain->transaction_id == null)
                                                @if (in_array($current_user->menuroles, ['admin', 'account', 'clerk', 'lawyer', 'management', 'maker']))
                                                    <th class="text-right no-print">Edit Value</th>
                                                @endif
                                            @endif
                                            <th class="text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl-submit-voucher">
                                        @if (count($voucherDetails))
                                            @foreach ($voucherDetails as $index => $details)
                                                <?php $total_amt += $details->amount; ?>
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if ($voucherMain->transaction_id == null)
                                                            @if ($voucherMain->voucher_type == 1)
                                                                @if (in_array($current_user->menuroles, ['admin', 'account', 'clerk', 'lawyer', 'management', 'maker']) ||
                                                                        in_array($current_user->id, [51]))
                                                                    <a href="javascript:void(0)" data-backdrop="static"
                                                                        data-keyboard="false"
                                                                        onclick="editAccountItemModal('{{ $details->id }}','{{ $details->account_cat_id }}','{{ $details->bill_detail_id }}','{{ $details->acc_item_id }}')"
                                                                        data-toggle="modal" data-target="#accountItemModal"
                                                                        class="btn  btn-primary no-print"><i
                                                                            class="cil-transfer"></i></a>
                                                                @endif
                                                            @endif
                                                        @endif

                                                        {{ $details->account_item_name }}
                                                    </td>
                                                    <td class="text-right  no-print">
                                                        @if ($voucherMain->voucher_type == 1)
                                                            @if (isset($details->detail_bal))
                                                                {{ number_format((float) $details->detail_bal, 2, '.', ',') }}
                                                            @else-
                                                            @endif
                                                        @else
                                                        @endif

                                                    </td>
                                                    @if ($voucherMain->transaction_id == null)
                                                        @if (in_array($current_user->menuroles, ['admin', 'account', 'clerk', 'lawyer', 'management', 'maker']) ||
                                                                in_array($current_user->id, [51]))
                                                            <td class="text-right td_item_price no-print">
                                                                <input class="text-right" id="{{ $details->id }}"
                                                                    name="voucher_value" type="number"
                                                                    value="{{ $details->amount }}" />
                                                            </td>
                                                        @endif
                                                    @endif

                                                    <td class="text-right td_item_price">
                                                        {{ number_format((float) $details->amount, 2, '.', ',') }} </td>
                                                </tr>
                                            @endforeach
                                        @endif


                                    </tbody>
                                </table>


                            </div>
                            <!-- /.col -->
                        </div>


                        <div class="row">
                            <div class="col-12 col-sm-6 text-left">
                                <div class=""><b>Payment description: </b>
                                    <textarea class="form-control" id="remark_display" name="remark_display" rows="3" readonly>{{ isset($voucherMain->remark) ? $voucherMain->remark : '' }}</textarea>
                                </div>

                            </div>
                            <div class="col-12 col-sm-6 text-right">
                                <div class="total-payment">
                                    <h3><b>Total :</b><span id="span_total_amount" class="">RM
                                            {{ number_format($total_amt, 2, '.', ',') }}</span> </h3>
                                </div>
                                @if ($voucherMain->transaction_id == null)
                                    @if (in_array($current_user->menuroles, ['admin', 'account', 'clerk', 'lawyer', 'management', 'maker']) ||
                                            in_array($current_user->id, [51]))
                                        <button type="button" onclick="updateVoucherValue()"
                                            class="btn btn-success no-print"><i class="fa fa-credit-card"></i> Update
                                            Voucher
                                            amount
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>



                    </div>



                    <div id="dFile" class="card d_operation" style="display:none">

                        <div class="card-header">
                            <h4>Upload file</h4>
                        </div>
                        <div class="card-body">
                            <form id="form_file" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                                        <!-- <input class="form-control" type="hidden" id="selected_id" name="selected_id" value=""> -->
                                        <input class="form-control" type="hidden" id="case_id" name="case_id"
                                            value="">
                                        <div id="field_file" class="form-group row">
                                            <div class="col">
                                                <label>File</label>
                                                <input class="form-control" type="file" id="inp_file"
                                                    name="inp_file">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Remark</label>
                                                <textarea class="form-control" id="file_remark" name="file_remark" rows="3"></textarea>
                                            </div>
                                        </div>

                                        <button class="btn btn-success float-right" onclick="uploadFile()"
                                            type="button">
                                            <span id="span_upload">Upload</span>
                                            <div class="overlay" style="display:none">
                                                <i class="fa fa-refresh fa-spin"></i>
                                            </div>
                                        </button>
                                        <a href="javascript:void(0);" onclick="viewMode()"
                                            class="btn btn-danger">Cancel</a>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>

                    <div id="accountItemModal" class="modal fade" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <form id="form_add">
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Account Item</label>
                                                <select id="ddlAccountItem" class="form-control" name="accountItem">
                                                    @if (count($AccountItem))
                                                        @foreach ($AccountItem as $index => $item)
                                                            <option id="acc_{{ $item->acc_item_id }}"
                                                                value="{{ $item->id }}"
                                                                class="cat_id_all cat_id_{{ $item->account_cat_id }}">
                                                                {{ $item->account_item_name }} (Balance:
                                                                {{ $item->bal }})</option>
                                                        @endforeach
                                                    @endif
                                                    AccountItem
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Request Value</label>
                                                <input class="form-control" type="number" name="request_val"
                                                    id="request_val" />
                                                <input class="form-control" type="hidden" name="current_id"
                                                    id="current_id" />
                                                <input class="form-control" type="hidden" name="bill_detail_id"
                                                    id="bill_detail_id" />
                                            </div>
                                        </div>

                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" id="btnClose2" class="btn btn-default"
                                        data-dismiss="modal">Close</button>
                                    <button type="button" class="btn btn-success float-right"
                                        onclick="updateVoucherAccountItem()">Save
                                        <div class="overlay" style="display:none">
                                            <i class="fa fa-refresh fa-spin"></i>
                                        </div>
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <div class="col-12">
                    <div id="d-listing" class="card">
                        <div class="card-header">

                            <div class="row">

                                <div class="col-sm-6">
                                    <h4><i class="fa fa-briefcase  "></i> Files</h4>
                                </div>

                                <div class="col-sm-6">
                                    <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)"
                                        onclick="fileMode()">
                                        <i class="cil-cloud-upload"> </i>Upload new file
                                    </a>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">

                            <div class="row">
                                <div class="col-sm-12">
                                    <span style="color: red"> * If the "Download File" button not working, try click on the
                                        "Download Old file" button</span>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <hr />
                                </div>

                            </div>

                            <table class="table table-striped table-bordered datatable">
                                <thead>
                                    <tr class="text-center">
                                        <th>No</th>
                                        <th>File Name</th>
                                        {{-- <th>Date</th>
                                        <th>Remarks</th> --}}
                                        <th>Upload By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if (count($LoanCaseAccountFiles))
                                        @foreach ($LoanCaseAccountFiles as $index => $file)
                                            <tr>
                                                <td class="text-center">{{ $index + 1 }}</td>
                                                <td>{{ $file->ori_name }} <br />
                                                    <b>Remark: </b> <br />{{ $file->remarks }}
                                                </td>
                                                <td>
                                                    {{ $file->upload_by }} <br /><span
                                                        style="font-size: 10px;color:gray">{{ $file->created_at }}</span>
                                                </td>
                                                <td class="text-center">

                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-info btn-flat">Action</button>
                                                        <button type="button"
                                                            class="btn btn-info btn-flat dropdown-toggle"
                                                            data-toggle="dropdown">
                                                            <span class="caret"></span>
                                                            <span class="sr-only">Toggle Dropdown</span>
                                                        </button>
                                                        <div class="dropdown-menu" style="padding:0">

                                                            @if ($file->s3_file_name)
                                                                {{-- <a href="javascript:void(0)"
                                                                    onclick="openFileFromS3('{{ $file->s3_file_name }}')"
                                                                    class="mailbox-attachment-name" data-toggle="tooltip"
                                                                    data-placement="top"
                                                                    title="Download">{{ $file->file_name }}</a> --}}

                                                                <a class="dropdown-item btn-success"
                                                                    href="javascript:void(0)"
                                                                    onclick="openFileFromS3('{{ $file->s3_file_name }}')"
                                                                    style="color:white;margin:0"><i
                                                                        style="margin-right: 10px;"
                                                                        class="cil-cloud-download"></i>Download</a>
                                                            @else
                                                                <a class="dropdown-item btn-success" target="_blank"
                                                                    href="/{{ $template_path . $file->file_name }}"
                                                                    style="color:white;margin:0"><i
                                                                        style="margin-right: 10px;"
                                                                        class="cil-cloud-download"></i>Download</a>
                                                                <div class="dropdown-divider" style="margin:0"></div>
                                                                <a class="dropdown-item btn-warning" target="_blank"
                                                                    href="/{{ $template_path_old . $file->file_name }}"
                                                                    style="color:white;margin:0"><i
                                                                        style="margin-right: 10px;"
                                                                        class="cil-cloud-download"></i>Download Old
                                                                    File</a>
                                                            @endif

                                                            @if ($voucherMain->account_approval == 0 || in_array($current_user->menuroles, ['admin']))
                                                                <div class="dropdown-divider" style="margin:0"></div>
                                                                <a class="dropdown-item btn-danger"
                                                                    href="javascript:void(0)"
                                                                    onclick="deleteVoucherAttachment('{{ $file->id }}')"
                                                                    style="color:white;margin:0"><i
                                                                        style="margin-right: 10px;"
                                                                        class="fa fa-close"></i>Delete</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    {{-- <a target="_blank" href="/{{ $template_path . $file->file_name }}"
                                                        class="btn btn-info shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Download"><i
                                                            class="cil-cloud-download"></i></a><br />

                                                    <a target="_blank"
                                                        href="/{{ $template_path_old . $file->file_name }}"
                                                        class="btn btn-warning shadow sharp mr-1" data-toggle="tooltip"
                                                        data-placement="top" title="Download">Old File</a><br />

                                                    @if ($voucherMain->account_approval == 0 || in_array($current_user->menuroles, ['admin']))
                                                        @if ($current_user->id == $file->created_by || in_array($current_user->menuroles, ['admin', 'management', 'account']))
                                                            <a href="javascript:void(0)"
                                                                onclick="deleteVoucherAttachment('{{ $file->id }}')"
                                                                class="btn btn-danger"><i class="cil-x"></i></a>
                                                        @endif
                                                    @endif --}}





                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center" colspan="7">No data</td>
                                        </tr>
                                    @endif

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card ">
                        <div class="card-header">
                            <div class="row">

                                <div class="col-sm-6">
                                    <h4 style="margin-bottom: 20px;"><i class="fa fa-book"></i> Update Voucher Details
                                    </h4>
                                </div>

                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-12">

                                <form id="form_trust_main" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                            <div class="form-group row dPersonal">
                                                <div class="col">
                                                    <label>Payee name</label>
                                                    <input class="form-control" id="payee" name="payee"
                                                        type="text"
                                                        value="{{ isset($voucherMain->payee) ? $voucherMain->payee : '' }}" />
                                                </div>
                                            </div>

                                            <!-- <div class="form-group row dPersonal">
                                                                                                                                                    <div class="col">
                                                                                                                                                        <label>Amount</label>
                                                                                                                                                        <input class="form-control" id="amount" name="amount" type="number" value="{{ isset($voucherMain->payee) ? $voucherMain->payee : '' }}" />
                                                                                                                                                    </div>
                                                                                                                                                </div> -->

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Payment Description</label>
                                                    <textarea class="form-control" id="remark" name="remark" rows="3">{{ isset($voucherMain->remark) ? $voucherMain->remark : '' }}</textarea>
                                                </div>
                                            </div>

                                            @if (
                                                $current_user->menuroles == 'admin' ||
                                                    $current_user->menuroles == 'account' ||
                                                    $current_user->menuroles == 'maker' ||
                                                    $current_user->menuroles == 'management' ||
                                                    $current_user->id == 51)
                                                <div class="form-group row dPersonal">
                                                    <div class="col">
                                                        <label>Transaction ID</label>
                                                        <input class="form-control" id="transaction_id"
                                                            name="transaction_id" type="text"
                                                            value="{{ isset($voucherMain->transaction_id) ? $voucherMain->transaction_id : '' }}" />
                                                    </div>
                                                </div>

                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label>Payment Date</label>
                                                        <input class="form-control" type="date" id="payment_date"
                                                            name="payment_date"
                                                            value="{{ isset($voucherMain->payment_date) ? $voucherMain->payment_date : '' }}">
                                                    </div>
                                                </div>
                                            @endif



                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Transaction Type</label>
                                                    <select class="form-control" id="payment_type" name="payment_type"
                                                        required>
                                                        <option value="">-- Please select the payment type --
                                                        </option>
                                                        @foreach ($parameters as $index => $parameter)
                                                            <option value="{{ $parameter->parameter_value_3 }}"
                                                                @if ((isset($voucherMain->payment_type) ? $voucherMain->payment_type : '') == $parameter->parameter_value_3) selected @endif>
                                                                {{ $parameter->parameter_value_2 }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                            @if (in_array($current_user->menuroles, ['admin', 'account', 'maker']) || in_array($current_user->id, [51]))
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label>Office bank account</label>
                                                        <select class="form-control" id="office_account_id"
                                                            name="office_account_id" required>
                                                            <option value="0">-- Please select a bank -- </option>
                                                            @foreach ($OfficeBankAccount as $index => $bank)
                                                                <option value="{{ $bank->id }}"
                                                                    @if ((isset($voucherMain->office_account_id) ? $voucherMain->office_account_id : '') == $bank->id) selected @endif>
                                                                    {{ $bank->name }} ({{ $bank->account_no }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Payee Email (Max 2 emails)</label>
                                                    <input class="form-control" id="email" name="email"
                                                        type="text"
                                                        value="{{ isset($voucherMain->email) ? $voucherMain->email : '' }}" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Payee Bank Account</label>
                                                    <input class="form-control" id="bank_account" name="bank_account"
                                                        type="text"
                                                        value="{{ isset($voucherMain->bank_account) ? $voucherMain->bank_account : '' }}" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Payee Bank</label>
                                                    <select id="payee_bank" class="form-control" name="payee_bank">
                                                        <option value="0">-- Select bank --</option>
                                                        @foreach ($bank_list as $index => $bank)
                                                            <option value="{{ $bank->id }}"
                                                                @if ((isset($voucherMain->bank_id) ? $voucherMain->bank_id : '') == $bank->id) selected @endif>
                                                                {{ $bank->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Credit Card No</label>
                                                    <input class="form-control" id="credit_card_no" name="credit_card_no"
                                                        type="text"
                                                        value="{{ isset($voucherMain->credit_card_no) ? $voucherMain->credit_card_no : '' }}" />
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Adjudication/Smartbox No/ID Pemfailan</label>
                                                    <input class="form-control" id="adjudication_no"
                                                        name="adjudication_no" type="text"
                                                        value="{{ isset($voucherMain->adjudication_no) ? $voucherMain->adjudication_no : '' }}" />
                                                </div>
                                            </div>

                                        </div>

                                        @if ($current_user->menuroles == 'admin' || $current_user->menuroles == 'lawyer')
                                            <div class="col-6 ">
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label>Lawyer Reject reason </label>
                                                        <textarea class="form-control" id="lawyer_reject_reason" name="lawyer_reject_reason" rows="3">{{ isset($voucherMain->lawyer_reject_reason) ? $voucherMain->lawyer_reject_reason : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if ($current_user->menuroles == 'clerk' || $current_user->menuroles == 'lawyer')
                                            <div class="col-6 ">
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label>Account Reject reason </label>
                                                        <textarea class="form-control" rows="3" disabled>{{ isset($voucherMain->account_reject_reason) ? $voucherMain->account_reject_reason : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        @if (in_array($current_user->menuroles, ['admin', 'account', 'maker']))
                                            <div class="col-6 ">
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label>Lawyer Reject reason </label>
                                                        <textarea class="form-control" rows="3" disabled>{{ isset($voucherMain->lawyer_reject_reason) ? $voucherMain->lawyer_reject_reason : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-6 ">
                                                <div class="form-group row">
                                                    <div class="col">
                                                        <label>Account Reject reason </label>
                                                        <textarea class="form-control" id="account_reject_reason" name="account_reject_reason" rows="3">{{ isset($voucherMain->account_reject_reason) ? $voucherMain->account_reject_reason : '' }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="col-6 ">
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Current Status</label>

                                                    @switch($voucherMain->account_approval)
                                                        @case(0)
                                                            <span class="label bg-warning">Pending</span>
                                                        @break

                                                        @case(1)
                                                            <span class="label bg-success">Approved</span>
                                                        @break

                                                        @case(2)
                                                            <span class="label bg-danger">Rejected</span>
                                                        @break

                                                        @case(5)
                                                            <span class="label bg-info">Resubmit</span>
                                                        @break

                                                        @case(6)
                                                            <span class="label bg-warning">In Progress</span>
                                                        @break
                                                    @endswitch

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </form>



                                {{-- @if ($voucherMain->lawyer_approval == 1 || $voucherMain->account_approval == 1)
                                    <button type="button" class="btn btn-warning pull-left" onclick="printlo()"
                                        style="margin-right: 5px;">
                                        <span><i class="fa fa-print"></i> Print</span>
                                    </button>
                                @endif --}}

                                {{-- new section============================================================== --}}

                                @if (in_array($userRoles, ['lawyer', 'admin', 'management', 'account', 'maker']))
                                @endif
                                {{-- new section============================================================== --}}

                                @if (in_array($userRoles, ['lawyer', 'admin', 'management']))
                                    @if ($voucherMain->lawyer_approval == 0)
                                        {{-- <button type="button" class="btn btn-info pull-left"
                                            onclick="updateVoucherStatus('{{ $voucherMain->id }}',2)"
                                            style="margin-right: 5px;">
                                            <span><i class="fa  fa-check"></i> Approve</span>
                                        </button> --}}

                                        <button type="button" class="btn btn-info pull-left"
                                            onclick="approveVoucher('{{ $voucherMain->id }}')"
                                            style="margin-right: 5px;">
                                            <span><i class="fa  fa-check"></i> Approve</span>
                                        </button>

                                        <button type="button" class="btn btn-danger pull-left"
                                            onclick="rejectVoucher('{{ $voucherMain->id }}',3, 'lawyer')"
                                            style="margin-right: 5px;">
                                            <span><i class="fa fa-close"></i> Reject</span>
                                        </button>
                                    @endif

                                    @if ($voucherMain->account_approval == 2 && $voucherMain->status != 5)
                                        <button type="button" class="btn btn-info pull-left"
                                            onclick="resubmitVoucher('{{ $voucherMain->id }}')"
                                            style="margin-right: 5px;">
                                            <span><i class="fa  fa-check"></i> Resubmit</span>
                                        </button>
                                    @endif
                                @endif

                                @if ($userRoles == 'clerk')
                                    @if ($voucherMain->account_approval == 2 && $voucherMain->status != 5)
                                        <button type="button" class="btn btn-info pull-left"
                                            onclick="resubmitVoucher('{{ $voucherMain->id }}')"
                                            style="margin-right: 5px;">
                                            <span><i class="fa  fa-check"></i> Resubmit</span>
                                        </button>
                                    @endif
                                @endif

                                {{-- @if ($userRoles == 'account') --}}
                                @if (in_array($current_user->menuroles, ['account', 'maker']) || in_array($current_user->id, [51]))
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-info btn-flat">Action</button>
                                        <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                            data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" style="padding:0">
                                            @if (in_array($voucherMain->account_approval, [0, 5, 6]))
                                                {{-- <a class="dropdown-item btn-success" href="javascript:void(0)"
                                                    style="color:white;margin:0"
                                                    onclick="updateVoucherStatus('{{ $voucherMain->id }}',4)"><i
                                                        style="margin-right: 10px;" class="fa  fa-check"></i>Approve</a> --}}

                                                <a class="dropdown-item btn-success" href="javascript:void(0)"
                                                    style="color:white;margin:0"
                                                    onclick="approveVoucher('{{ $voucherMain->id }}')"><i
                                                        style="margin-right: 10px;" class="fa  fa-check"></i>Approve</a>
                                                <div class="dropdown-divider" style="margin:0"></div>
                                                <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                                    style="color:white;margin:0"
                                                    onclick="rejectVoucher('{{ $voucherMain->id }}',3, 'account')"><i
                                                        style="margin-right: 10px;" class="fa fa-close"></i>Reject</a>
                                                <div class="dropdown-divider" style="margin:0"></div>
                                                @if ($voucherMain->account_approval != 6)
                                                    <a class="dropdown-item btn-warning" href="javascript:void(0)"
                                                        style="color:white;margin:0"
                                                        onclick="UpdateVoucherStatusV2('{{ $voucherMain->id }}','INPROGRESS')"><i
                                                            style="margin-right: 10px;" class="fa fa-repeat"></i>In
                                                        Progress</a>
                                                @endif
                                                @if ($voucherMain->account_approval == 6)
                                                    <a class="dropdown-item btn-warning" href="javascript:void(0)"
                                                        style="color:white;margin:0"
                                                        onclick="UpdateVoucherStatusV2('{{ $voucherMain->id }}','PENDING')"><i
                                                            style="margin-right: 10px;"
                                                            class="fa fa-repeat"></i>Pending</a>
                                                @endif
                                            @elseif ($voucherMain->account_approval == 2 && $voucherMain->status != 5)
                                                <a class="dropdown-item btn-info" href="javascript:void(0)"
                                                    style="color:white;margin:0"
                                                    onclick="resubmitVoucher('{{ $voucherMain->id }}')"><i
                                                        style="margin-right: 10px;" class="fa fa-check"></i>Resubmit</a>
                                            @elseif ($voucherMain->account_approval == 1)
                                                <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                                    style="color:white;margin:0"
                                                    onclick="unapproveVoucher('{{ $voucherMain->id }}',3, 'account')"><i
                                                        style="margin-right: 10px;" class="fa fa-close"></i>Unapprove</a>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                {{-- @if ($userRoles != 'sales') --}}
                                @if (in_array($current_user->menuroles, ['admin', 'account', 'maker']))
                                    @if ($voucherMain->account_approval != 1)
                                        <button type="button" onclick="updateVoucherStatus('{{ $voucherMain->id }}',1)"
                                            class="btn btn-success pull-right"><i class="fa fa-credit-card"></i>
                                            Update</button>
                                    @endif
                                @else
                                    @if ($voucherMain->transaction_id == null)
                                        @if ($voucherMain->account_approval != 1)
                                            <button type="button"
                                                onclick="updateVoucherStatus('{{ $voucherMain->id }}',1)"
                                                class="btn btn-success pull-right"><i class="fa fa-credit-card"></i>
                                                Update</button>
                                        @endif
                                    @endif
                                @endif



                                {{-- @endif --}}

                            </div>
                        </div>
                    </div>
                </div>






            </div>
        </div>
    </div>

@endsection

@section('javascript')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/PrintArea.min.css" rel="stylesheet">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
    <script src="{{ asset('js/jquery.print.js') }}"></script>
    <script>
        function openFileFromS3(filename) {
            var form_data = new FormData();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            form_data.append("filename", filename);
            // form_data.append("filename", '9gRrec82ztUG8so4UF2HtkZPb2ZH9Z9f2jD5E9oE.pdf');

            $.ajax({
                type: 'POST',
                url: '/getFileFromS3',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator
                            .userAgent)) {
                        window.location.href = data;
                    } else {
                        window.open(data, "_blank");
                    }
                }
            });
        }

        function fileMode() {
            $("#d-listing").hide();
            $("#dFile").show();
        }

        function viewMode() {
            $("#d-listing").show();
            $("#dFile").hide();
        }

        function editAccountItemModal(itemId, accountCatId, bill_detail_id, accItemId) {
            // $(".cat_id_all").hide();
            // $(".cat_id_" + accountCatId).show();
            $("#acc_" + accItemId).hide();
            $("#current_id").val(itemId);
            $("#bill_detail_id").val(bill_detail_id);
            // alert(itemId);
            // $("#ddlAccountItem").val(accItemId);

        }

        function updateVoucherAccountItem() {
            var voucher = [];
            var item = [];


            $.ajax({
                type: 'POST',
                url: '/updateVoucherAccountItem',
                data: $('#form_add').serialize(),

                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        location.reload();

                    } else {
                        Swal.fire(
                            'Notice!',
                            data.message,
                            'warning'
                        )
                    }
                }
            });

            console.log(voucher);
        }

        function updateVoucherValue() {
            var voucher = [];
            var item = [];

            $.each($("input[name='voucher_value']"), function() {

                template_id = [];

                itemID = $(this).attr('id');
                update_value = $(this).val();

                item = {
                    itemID: itemID,
                    update_value: update_value
                };

                voucher.push(item);

            });

            var form_data = new FormData();
            form_data.append("voucher", JSON.stringify(voucher));

            $.ajax({
                type: 'POST',
                url: '/updateVoucherValue/' + '{{ $voucherMain->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            data.message,
                            'success'
                        )

                        location.reload();

                    } else {
                        Swal.fire(
                            'Notice!',
                            data.message,
                            'warning'
                        )
                    }
                }
            });

            console.log(voucher);
        }

        function deleteVoucherAttachment($id) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            Swal.fire({
                icon: 'warning',
                text: 'delete this file?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteVoucherAttachment/' + $id,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }


        function updateVoucherStatus(voucher_main_id, type) {

            var formData = new FormData();
            office_account_id = 0;

            if ($("#office_account_id").val() != undefined) {
                office_account_id = $("#office_account_id").val();
            }

            if ($("#payee_bank").val() != undefined) {
                payee_bank = $("#payee_bank").val();
            }

            formData.append('status', $("#status").val());
            formData.append('remark', $("#remark").val());
            formData.append('payee', $("#payee").val());
            formData.append('transaction_id', $("#transaction_id").val());
            formData.append('bank_account', $("#bank_account").val());
            formData.append('credit_card_no', $("#credit_card_no").val());
            formData.append('payment_date', $("#payment_date").val());
            formData.append('email', $("#email").val());
            formData.append('cheque_no', $("#cheque_no").val());
            formData.append('payment_type', $("#payment_type").val());
            formData.append('adjudication_no', $("#adjudication_no").val());
            formData.append('OfficeBankAccount_id', office_account_id);
            formData.append('payee_bank', payee_bank);
            formData.append('type', type);

            $.ajax({
                type: 'POST',
                url: '/update_voucher_status/' + voucher_main_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {


                    }

                    Swal.fire(
                        'Success!',
                        data.message,
                        'success'
                    )

                    location.reload();

                }
            });
        }

        function UpdateVoucherStatusV2(voucher_main_id, type) {

            var formData = new FormData();
            office_account_id = 0;

            if ($("#office_account_id").val() != undefined) {
                office_account_id = $("#office_account_id").val();
            }

            formData.append('status', $("#status").val());
            formData.append('remark', $("#remark").val());
            formData.append('payee', $("#payee").val());
            formData.append('transaction_id', $("#transaction_id").val());
            formData.append('bank_account', $("#bank_account").val());
            formData.append('credit_card_no', $("#credit_card_no").val());
            formData.append('email', $("#email").val());
            formData.append('payment_date', $("#payment_date").val());
            formData.append('cheque_no', $("#cheque_no").val());
            formData.append('payment_type', $("#payment_type").val());
            formData.append('adjudication_no', $("#adjudication_no").val());
            formData.append('OfficeBankAccount_id', office_account_id);
            formData.append('type', type);

            $.ajax({
                type: 'POST',
                url: '/update_voucher_status_v2/' + voucher_main_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {


                    }

                    Swal.fire(
                        'Success!',
                        data.message,
                        'success'
                    )

                    location.reload();

                }
            });
        }

        function rejectVoucher(voucher_main_id, type, role) {

            if (role == 'lawyer') {
                if ($("#lawyer_reject_reason").val() == "" || $("#lawyer_reject_reason").val() == null) {
                    Swal.fire('notice!', 'Please provide reason', 'warning')
                    return;
                }

            }

            if (role == 'account') {
                if ($("#account_reject_reason").val() == "" || $("#account_reject_reason").val() == null) {
                    Swal.fire('notice!', 'Please provide reason', 'warning')
                    return;
                }

            }

            var formData = new FormData();

            formData.append('status', $("#status").val());
            formData.append('remark', $("#remark").val());
            formData.append('payee', $("#payee").val());
            formData.append('lawyer_reject_reason', $("#lawyer_reject_reason").val());
            formData.append('account_reject_reason', $("#account_reject_reason").val());
            formData.append('transaction_id', $("#transaction_id").val());
            formData.append('bank_account', $("#bank_account").val());
            formData.append('credit_card_no', $("#credit_card_no").val());
            formData.append('payment_date', $("#payment_date").val());
            formData.append('cheque_no', $("#cheque_no").val());
            formData.append('payment_type', $("#payment_type").val());
            formData.append('adjudication_no', $("#adjudication_no").val());
            formData.append('OfficeBankAccount_id', $("#office_account_id").val());
            formData.append('type', type);

            $.ajax({
                type: 'POST',
                url: '/update_voucher_status/' + voucher_main_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {


                    }

                    Swal.fire(
                        'Success!',
                        data.message,
                        'success'
                    )
                    location.reload();
                }
            });
        }

        function unapproveVoucher(voucher_main_id, type, role) {


            var formData = new FormData();

            formData.append('status', $("#status").val());
            formData.append('remark', $("#remark").val());
            formData.append('payee', $("#payee").val());
            formData.append('lawyer_reject_reason', $("#lawyer_reject_reason").val());
            formData.append('account_reject_reason', $("#account_reject_reason").val());
            formData.append('transaction_id', $("#transaction_id").val());
            formData.append('bank_account', $("#bank_account").val());
            formData.append('credit_card_no', $("#credit_card_no").val());
            formData.append('payment_date', $("#payment_date").val());
            formData.append('cheque_no', $("#cheque_no").val());
            formData.append('payment_type', $("#payment_type").val());
            formData.append('adjudication_no', $("#adjudication_no").val());
            formData.append('OfficeBankAccount_id', $("#office_account_id").val());
            formData.append('type', type);

            $.ajax({
                type: 'POST',
                url: '/unapproveVoucher/' + voucher_main_id,
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire('Success!', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Success!', data.message, 'warning');
                    }


                }
            });
        }

        function resubmitVoucher(voucher_main_id) {


            $.ajax({
                type: 'POST',
                url: '/resubmitVoucher/' + voucher_main_id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire('Success!', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Notice!', data.message, 'warning');
                    }



                }
            });
        }

        function approveVoucher(voucher_main_id) {

            var formData = new FormData();
            office_account_id = 0;

            $.ajax({
                type: 'POST',
                url: '/approveVoucher/' + voucher_main_id,
                data: null,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire('Success!', data.message, 'success');
                        location.reload();
                    } else {
                        Swal.fire('Notice!', data.message, 'warning');
                    }




                }
            });
        }

        function uploadFile() {
            $("#span_update").hide();
            $(".overlay").show();

            var formData = new FormData();

            var files = $('#inp_file')[0].files;
            console.log(files[0]);
            formData.append('inp_file', files[0]);
            formData.append('remarks', $("#file_remark").val());
            formData.append('case_id', '{{ $case->id }}');
            formData.append('case_ref_no', '{{ $case->case_ref_no }}');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: 'POST',
                url: '/upload_account_file/' + '{{ $voucherMain->id }}',
                // data: $('#form_action').serialize(),
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    if (data.status == 1) {
                        Swal.fire(
                            'Success!',
                            'File uploaded',
                            'success'
                        )
                        location.reload();
                    }



                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }

        function printlo() {
            // window.print();

            // $("#dVoucherInvoice").print();

            // jQuery.print();

            $("#dVoucherInvoice").print({
                addGlobalStyles: true,
                stylesheet: null,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                append: null,
                prepend: null
            });
        }

        function PrintElem(elem) {
            var mywindow = window.open('', 'PRINT');

            mywindow.document.write('<html><head><title>' + document.title + '</title>');
            mywindow.document.write('</head><body >');
            mywindow.document.write('<h1>' + document.title + '</h1>');
            mywindow.document.write(document.getElementById(elem).innerHTML);
            mywindow.document.write('</body></html>');

            mywindow.document.close(); // necessary for IE >= 10
            mywindow.focus(); // necessary for IE >= 10*/

            mywindow.print();
            mywindow.close();

            return true;
        }
    </script>
@endsection
