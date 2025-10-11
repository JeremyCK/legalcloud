<div id="dTrustList" class="row">
    <div class="col-12">
        <div class="box">
            <div class="box-header"></div>

            <div id="d-trust-summary" class="row">
                @include('dashboard.case.section.d-trust-summary')
            </div>

            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']))

                <form id="form_trust_main" enctype="multipart/form-data" style="">
                    <div class="row">
                        <div class="col-12 ">
                            <h4 style="margin-bottom: 20px;"><i class="fa fa-user"></i> Trust Account</h4>

                            <div class="row">
                                <div class="col-12">
                                    <div class="box">
                                        <div class="box-header">
                                            <h3 class="box-title"></h3>
                                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']))

                                                <a class="btn btn-info float-left" href="javascript:void(0)"
                                                    onclick="generateReceiptController(0, 'TRUST');"
                                                    style="color:white;margin:0" data-backdrop="static"
                                                    data-keyboard="false" data-target="#modalReceipt"
                                                    data-toggle="modal">
                                                    <i style="margin-right: 10px;" class="cil-print"></i>Print
                                                    Receipt</a>
                                            @endif



                                            <button class="btn btn-info float-right" type="button"
                                                onclick="updateLoanCaseTrustMain();">
                                                <i class="cil-plus"></i> Update
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                            <div class="form-group row dPersonal">
                                <div class="col">
                                    <label>Payee name</label>
                                    <input class="form-control" name="payee_name" type="text"
                                        value="{{ isset($LoanCaseTrustMain->payee) ? $LoanCaseTrustMain->payee : '' }}" />
                                </div>
                            </div>



                            <div class="form-group row dPersonal">
                                <div class="col">
                                    <label>Transaction ID</label>
                                    <input class="form-control" name="transaction_id" type="text"
                                        value="{{ isset($LoanCaseTrustMain->transaction_id) ? $LoanCaseTrustMain->transaction_id : '' }}" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Date Receive</label>
                                    <input class="form-control" type="date" name="payment_date"
                                        value="{{ isset($LoanCaseTrustMain->payment_date) ? $LoanCaseTrustMain->payment_date : '' }}">
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Transaction Type</label>
                                    <select class="form-control" name="payment_type" required>
                                        <option value="">-- Please select the payment type -- </option>
                                        @foreach ($parameters as $index => $parameter)
                                            <option value="{{ $parameter->parameter_value_3 }}"
                                                @if ((isset($LoanCaseTrustMain->payment_type) ? $LoanCaseTrustMain->payment_type : '') == $parameter->parameter_value_3) selected @endif>
                                                {{ $parameter->parameter_value_2 }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                            <div class="form-group row">
                                <div class="col">
                                    <label>Office bank account</label>
                                    <select class="form-control" name="office_account_id" required>
                                        <option value="">-- Please select a bank -- </option>
                                        @foreach ($OfficeBankAccount as $index => $bank)
                                            <option value="{{ $bank->id }}"
                                                @if ((isset($LoanCaseTrustMain->office_account_id) ? $LoanCaseTrustMain->office_account_id : '') == $bank->id) selected @endif>{{ $bank->name }}
                                                ({{ $bank->account_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>


                            <div class="form-group row">
                                <div class="col">
                                    <label>Client Bank Account</label>
                                    <input class="form-control" name="bank_account" type="text"
                                        value="{{ isset($LoanCaseTrustMain->bank_account) ? $LoanCaseTrustMain->bank_account : '' }}" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Credit Card No</label>
                                    <input class="form-control" name="credit_card_no" type="text"
                                        value="{{ isset($LoanCaseTrustMain->credit_card_no) ? $LoanCaseTrustMain->credit_card_no : '' }}" />
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col">
                                    <label>Sum amount</label></label>
                                    <input class="form-control" name="sum_amount" id="sum_amount" type="text"
                                        value="" />
                                </div>
                            </div>



                        </div>

                        <div class="col-12 ">
                            <div class="form-group row">
                                <div class="col">
                                    <label>Payment Description</label>
                                    <textarea class="form-control" name="payment_desc" rows="3">{{ isset($LoanCaseTrustMain->remark) ? $LoanCaseTrustMain->remark : '' }}</textarea>
                                </div>
                            </div>
                        </div>


                    </div>

                </form>

                <hr />

            @endif




            <div class="nav-tabs-custom" style="margin:20px">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" style="width:50%;margin:0px"><a class="nav-link active text-center"
                            data-toggle="tab" href="#disbursement" role="tab" aria-controls="disbursement"
                            aria-selected="true">Disbursement</a></li>
                    <li class="nav-item" style="width:50%;margin:0px"><a class="nav-link  text-center"
                            data-toggle="tab" href="#receive" role="tab" aria-controls="receive"
                            aria-selected="true">Received</a></li>
            
                </ul>
            </div>

            <div class="tab-content">

                <div class="tab-pane " id="receive" role="tabpanel">
                    <div class="box-body no-padding" style="width:100%;overflow-x:auto; min-height:500px;">
                        <h4 style="margin-bottom: 20px;"><i class="fa fa-money"></i> Received Trust</h4>


                        @if (!in_array($case->status, [0, 99]))
                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']) ||
                                    (in_array($current_user->id, [51]) && in_array($case->branch_id, [5, 6])) ||
                                    (in_array($current_user->id, [187])))

                                <a class="btn btn-primary float-right" href="javascript:void(0)"
                                    data-backdrop="static" style="margin-bottom:10px"
                                    onclick="trustModeV2('receive');" data-keyboard="false" data-toggle="modal"
                                    data-target="#modalTrust">
                                    <i style="margin-right: 10px;" class="cil-transfer"></i>Receive Payment</a>
                            @endif
                        @endif



                    
                        <table id="tbl-trust-recv" class="table table-striped table-bordered datatable">
                           @include('dashboard.case.table.tbl-trust-recv-list')
                        </table>
                    </div>
                </div>

                <div class="tab-pane active" id="disbursement" role="tabpanel">
                    <div class="box-body no-padding" style="width:100%;overflow-x:auto; min-height:500px;">
                        <h4 style="margin-bottom: 20px;"><i class="fa fa-money"></i> Trust Disbursement</h4>
                        @if (!in_array($case->status, [0, 99]))
                            {{-- <button class="btn btn-info float-right " style="margin-bottom:10px" type="button"
                                onclick="trustDisburseMode('0', '{{ $case->id }}');"
                                @if ((isset($LoanCaseTrustMain->total_received) ? $LoanCaseTrustMain->total_received : 0) <= 0) disabled @endif>
                                <i class="cil-plus"></i> Add Disbursement
                            </button> --}}

                            <a class="btn btn-primary float-right" href="javascript:void(0)" data-backdrop="static" style="margin-bottom:10px"  onclick="trustModeV2('request');"
                            data-keyboard="false" data-toggle="modal" data-target="#modalTrust">
                            <i style="margin-right: 10px;" class="cil-transfer"></i>Add Disbursement</a>
                        @endif

                        <div id="tbl-trust-disb">
                            @include('dashboard.case.table.tbl-trust-disb-list')
                        </div>

                        
                    </div>
                </div>
            </div>




            <!-- /.box-header -->

            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>

@include('dashboard.case.d-trust_receipt-print')
