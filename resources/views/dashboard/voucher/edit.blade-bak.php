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
        <div class="row no-print">
            <div class="col-12">
                <!-- <a  class="btn btn-danger" href="/voucher"><span><i class="ion-reply"></i> Cancel</span> </a> -->
                <a href="/voucher" class="btn btn-danger pull-left"><i class="ion-reply"></i> Back
                </a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">


                <div id="dVoucherInvoice" class="div2 invoice printableArea d_operation" style="margin-bottom:20px">
                    <!-- title row -->
                    <div class="row">
                        <div class="col-12">
                            <h2 class="page-header">
                                <!-- Payment Voucher -->
                                Voucher
                                <small class="pull-right">Date: <?php echo date('d/m/Y') ?> </small>
                            </h2>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- info row -->
                    <div class="row invoice-info">
                        <div class="col-sm-6 invoice-col">
                            <b>From</b>
                            <address>
                                <strong style="color: #2d659d">L H YEO & CO.</strong><br>
                                No, 62B, 2nd Floor, Jalan SS21/62, <br>
                                Damansara Utama<br>
                                47400 Petaling Jaya<br>
                                Selangor Darul Ehsan<br>
                                <b>Phone</b>: 03-7727 1818<br>
                                <b>Fax</b>: 03-7732 8818
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-6 invoice-col text-right">
                            <b>To</b>
                            <address>
                                <strong class="text-blue"> {{ $voucherMain->payee }}</strong><br>
                                <!-- <?php echo $customer->address ?><br>
                            Phone: <?php echo $customer->phone_no ?><br>
                            Email: <?php echo $customer->email ?> -->
                            </address>
                        </div>
                        <!-- /.col -->
                        <div class="col-sm-12 invoice-col">
                            <div class="invoice-details row no-margin">
                                <div class="col-md-6 col-lg-6"><b>Case Ref No: </b>#{{ $case->case_ref_no }} </div>
                                <!-- <div class="col-md-6 col-lg-3"><b>Order ID:</b> FC12548</div> -->
                                <!-- <div class="col-md-6 col-lg-3"><b>Payment Due:</b> 14/08/2017</div> -->
                                <div class="col-md-6 col-lg-6 pull-right">
                                    <span class="pull-right"><b>Voucher No:</b> {{ $voucherMain->voucher_no }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <!-- Table row -->
                    <div class="row">
                        <?php $total_amt = 0; ?>
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Description</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit Cost</th>
                                        <th class="text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="tbl-submit-voucher">
                                    @if(count($voucherDetails))
                                    @foreach($voucherDetails as $index => $details)
                                    <?php $total_amt += $details->amount; ?>
                                    <tr>
                                        <td>{{$index+1}}</td>
                                        <td>{{$details->account_item_name}}</td>
                                        <td class="text-right">1</td>
                                        <td class="text-right td_item_price">{{$details->amount}}</td>
                                        <td class="text-right td_item_price">{{$details->amount}}</td>
                                    </tr>
                                    @endforeach
                                    @endif


                                </tbody>
                            </table>
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row">
                        <!-- accepted payments column -->
                        <div class="col-12 col-sm-6 text-left">
                            <div class=""><b>Payment Type: </b>
                                @if($voucherMain->payment_type == "1")
                                Cash
                                @elseif($voucherMain->payment_type == "2")
                                Cheque
                                @elseif($voucherMain->payment_type == "3")
                                Bank transfer
                                @elseif($voucherMain->payment_type == "4")
                                Credit Card
                                @endif
                            </div>
                            <div class=""><b>Payment Date: </b>{{$voucherMain->payment_date}}</div>
                            <div class=""><b>Transaction ID: </b>{{$voucherMain->cheque_no}}</div>
                            <div class=""><b>Approved By: </b>{{$lawyerName}}</div>
                            <div class=""><b>Request By: </b>{{$requestName}}</div>
                            <div id="div_payment_details"></div>
                            <div class=""><b>Payee bank: </b>
                            @if(!empty($bank->name))
                            {{$bank->name}}
                            @else
                            -
                            @endif
                            </div>
                            <div class=""><b>Payee account no: </b>{{$voucherMain->bank_account}}</div>
                        </div>
                        <!-- /.col -->
                        <div class="col-12 col-sm-6 text-right">
                            <!-- <p class="lead"><b>Payment Due</b><span class="text-danger"> 14/08/2017 </span></p> -->


                            <div class="total-payment">
                                <h3><b>Total :</b><span id="span_total_amount" class="">RM {{ number_format($total_amt, 2, '.', ','); }}</span> </h3>
                            </div>

                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->

                    <div class="row no-print" style="margin-top: 50px;">


                        <div class="col-12">

                            <!-- @if ($userRoles == "lawyer")
                        <div class="form-group row">
                            <div class="col">
                                <label>{{ __('coreuiforms.notes.status') }}</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="0">-- Please select the status--</option>
                                    <option value="1">Approve</option>
                                    <option value="2">Reject</option>
                                </select>
                            </div>
                        </div>
                        @endif -->





                            <div class="form-group row">
                                <div class="col">
                                    <label>Transaction ID/Cheque No</label>
                                    <input class="form-control" name="cheque_no" id="cheque_no" value="{{ $voucherMain->cheque_no }}" type="text" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Payee</label>
                                    <input class="form-control" name="payee" id="payee" value="{{ $voucherMain->payee }}" type="text" />
                                </div>
                            </div>

                            @if($current_user->menuroles == 'admin' || $current_user->menuroles == 'account')

                            <div class="form-group row">
                                <div class="col">
                                    <label>Office bank account</label>
                                    <select class="form-control" name="OfficeBankAccount_id" id="OfficeBankAccount_id" required>
                                        <option value="">-- Please select a bank -- </option>
                                        @foreach($OfficeBankAccount as $index => $bank)
                                        <option value="{{ $bank->id }}" @if($bank->id== $voucherMain->office_account_id) selected @endif>{{ $bank->name }} ({{ $bank->account_no }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            @endif
                            <!-- <div class="form-group row ">
                                <label class="col-md-3 col-form-label" for="txt_bank_name">Office bank account</label>
                                <div class="col-md-9">
                                    <select class="form-control" name="OfficeBankAccount_id" id="OfficeBankAccount_id" required>
                                        <option value="">-- Please select a bank -- </option>
                                        @foreach($OfficeBankAccount as $index => $bank)
                                        <option value="{{ $bank->id }}">{{ $bank->name }} ({{ $bank->account_no }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> -->

                            <div class="form-group row">
                                <div class="col">
                                    <label>Remark</label>
                                    <textarea class="form-control" id="remark" name="remark" rows="3">{{ $voucherMain->remark }}</textarea>
                                </div>
                            </div>
                            <!-- <a  class="btn btn-danger" href="/voucher"><span><i class="ion-reply"></i> Cancel</span> </a> -->
                            <!-- <button type="button" onclick="updateVoucherStatus(2, '{{ $voucherMain->id}}')" class="btn btn-danger pull-left"><i class="ion-reply"></i> Reject
                        </button> -->

                            @if ( ($voucherMain->lawyer_approval == 1))
                            <button type="button" class="btn btn-warning pull-left" onclick="printlo()" style="margin-right: 5px;">
                                <span><i class="fa fa-print"></i> Print</span>
                            </button>
                            @endif

                           

                            <!-- @if ( ($voucherMain->lawyer_id == "0" || $voucherMain->lawyer_id == null) && $voucherMain->status != 2 && $voucherMain->status != 1)
                            @if ($userRoles != "clerk" && $userRoles != "account")
                            <button type="button" class="btn btn-info pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',2)" style="margin-right: 5px;">
                                <span><i class="fa  fa-check"></i> Approve</span>
                            </button>

                            <button type="button" class="btn btn-danger pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',3)" style="margin-right: 5px;">
                                <span><i class="fa fa-close"></i> Reject</span>
                            </button>
                            @endif
                            @endif -->

                            @if ($userRoles == "admin" || $userRoles == "account" || $userRoles == "management")
                            @if ( ($voucherMain->account_approval == 0))
                            <button type="button" class="btn btn-info pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',4)" style="margin-right: 5px;">
                                <span><i class="fa  fa-check"></i> Approve</span>
                            </button>

                            <button type="button" class="btn btn-danger pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',3)" style="margin-right: 5px;">
                                <span><i class="fa fa-close"></i> Reject</span>
                            </button>
                            @endif
                            @endif

                            @if ($userRoles == "lawyer")
                            @if ( ($voucherMain->lawyer_approval == 0))
                            <button type="button" class="btn btn-info pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',2)" style="margin-right: 5px;">
                                <span><i class="fa  fa-check"></i> Approve</span>
                            </button>

                            <button type="button" class="btn btn-danger pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',3)" style="margin-right: 5px;">
                                <span><i class="fa fa-close"></i> Reject</span>
                            </button>
                            @endif

                            
                           
                            @endif


                            <!-- @if ( $voucherMain->lawyer_id != "0" && $voucherMain->lawyer_id != null && $voucherMain->status == 0)
                            @if ($userRoles == "account")
                            <button type="button" class="btn btn-info pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',4)" style="margin-right: 5px;">
                                <span><i class="fa  fa-check"></i> Approve</span>
                            </button>

                            <button type="button" class="btn btn-danger pull-left" onclick="updateVoucherStatus('{{ $voucherMain->id}}',3)" style="margin-right: 5px;">
                                <span><i class="fa fa-close"></i> Reject</span>
                            </button>
                            @endif
                            @endif -->

                            <button type="button" onclick="updateVoucherStatus('{{ $voucherMain->id}}',1)" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Update
                            </button>
                        </div>
                    </div>



                    <!-- @if ($userRoles != "clerk")

                @if ( $voucherMain->status == "0")
                <div class="row no-print" style="margin-top: 50px;">


                    <div class="col-12">
                        <div class="form-group row">
                            <div class="col">
                                <label>{{ __('coreuiforms.notes.status') }}</label>
                                <select class="form-control" name="status" id="status">
                                    <option value="0">-- Please select the status--</option>
                                    <option value="1">Approve</option>
                                    <option value="2">Reject</option>
                                </select>
                            </div>
                        </div>



                        <div class="form-group row">
                            <div class="col">
                                <label>Transaction ID/Cheque No</label>
                                <input class="form-control" name="cheque_no" id="cheque_no" value="" type="text" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Payee</label>
                                <input class="form-control" name="payee" id="payee" value="{{ $voucherMain->payee }}" type="text" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Remark</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3">{{ $voucherMain->remark }}</textarea>
                            </div>
                        </div>

                        <button type="button" onclick="updateVoucherStatus('{{ $voucherMain->id}}',0)" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Submit
                        </button>
                    </div>
                </div>
                @else
                <div class="row no-print" style="margin-top: 50px;">



                    <div class="col-12">

                        <div class="form-group row">
                            <div class="col">
                                <label>{{ __('coreuiforms.notes.status') }}</label>
                                <select class="form-control" name="status" id="status" disabled>
                                    <option value="0">-- Please select the status--</option>
                                    <option value="1" @if($voucherMain->status == 1) selected @endif>Approve</option>
                                    <option value="2" @if($voucherMain->status == 2) selected @endif>Reject</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Transaction ID/Cheque No</label>
                                <input class="form-control" name="cheque_no" id="cheque_no" value="{{ $voucherMain->cheque_no }}" type="text" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Payee</label>
                                <input class="form-control" name="payee" id="payee" value="{{ $voucherMain->payee }}" type="text" />
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col">
                                <label>Remark</label>
                                <textarea class="form-control" rows="3" disabled>{{ $voucherMain->remark }}</textarea>
                            </div>
                        </div>

                        <button type="button" class="btn btn-warning pull-left" onclick="printlo()" style="margin-right: 5px;">
                            <span><i class="fa fa-print"></i> Print</span>
                        </button>

                        <button type="button" onclick="updateVoucherStatus('{{ $voucherMain->id}}', 1)" class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Update transaction ID
                        </button>
                    </div>
                </div>
                @endif

                @endif -->
                </div>

                <div id="d-listing" class="card">
                    <div class="card-header">
                        <h4>Files</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-lg btn-primary  float-right" href="javascript:void(0)" onclick="fileMode()">
                                    <i class="cil-cloud-upload"> </i>Upload new file
                                </a>
                            </div>

                        </div>
                        <br>
                        <table class="table table-striped table-bordered datatable">
                            <thead>
                                <tr class="text-center">
                                    <th>No</th>
                                    <th>File Name</th>
                                    <th>Date</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($LoanCaseAccountFiles))
                                @foreach($LoanCaseAccountFiles as $index => $file)
                                <tr>
                                    <td class="text-center">{{ $index+1 }}</td>
                                    <td>{{ $file->ori_name }}</td>
                                    <td class="text-center">{{ $file->created_at }}</td>
                                    <td>
                                        {{ $file->remarks }}
                                    </td>
                                    <td class="text-center">
                                        <a target="_blank" href="/{{ $template_path.$file->file_name }}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>

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
                                    <input class="form-control" type="hidden" id="case_id" name="case_id" value="">
                                    <div id="field_file" class="form-group row">
                                        <div class="col">
                                            <label>File</label>
                                            <input class="form-control" type="file" id="inp_file" name="inp_file">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col">
                                            <label>Remark</label>
                                            <textarea class="form-control" id="file_remark" name="file_remark" rows="3"></textarea>
                                        </div>
                                    </div>

                                    <button class="btn btn-success float-right" onclick="uploadFile()" type="button">
                                        <span id="span_upload">Upload</span>
                                        <div class="overlay" style="display:none">
                                            <i class="fa fa-refresh fa-spin"></i>
                                        </div>
                                    </button>
                                    <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>

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
    function fileMode() {
        $("#d-listing").hide();
        $("#dFile").show();
    }

    function viewMode() {
        $("#d-listing").show();
        $("#dFile").hide();
    }

    function updateVoucherStatus(voucher_main_id, type) {

        var formData = new FormData();

        formData.append('status', $("#status").val());
        formData.append('remark', $("#remark").val());
        formData.append('payee', $("#payee").val());
        formData.append('cheque_no', $("#cheque_no").val());
        formData.append('OfficeBankAccount_id', $("#OfficeBankAccount_id").val());
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

                    location.reload();
                }

                Swal.fire(
                    'Success!',
                    data.message,
                    'success'
                )

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
        formData.append('case_id', '{{$case-> id}}');
        formData.append('case_ref_no', '{{ $case->case_ref_no }}');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            type: 'POST',
            url: '/upload_account_file/' + '{{ $voucherMain->id}}',
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