<div id="modalRequestVoucher" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header modal-header-print" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Request Voucher</h4>
                    </div>
                    <div class="col-6">
                        {{-- <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button> --}}
                        <a href="javascript:void(0);" type="button" data-dismiss="modal"
                            class="btn btn-danger float-right no-print btn_close_all"> <i class=" cli-x"></i> Close</a>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <div >

                    <div class="card-body">
                        <form id="form_request_voucher">
                
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="transfer_date"><span class="text-danger">*</span> Payment type</label>
                                        <div class="col-md-8">
                                            <select id="ddl_payment_type" class="form-control" name="ddl_payment_type" required>
                                                <option value="">-- Please select the payment type -- </option>
                                                @foreach ($parameters as $index => $parameter)
                                                    <option value="{{ $parameter->parameter_value_3 }}">
                                                        {{ $parameter->parameter_value_2 }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="voucher_payment_time">Payment Date</label>
                                        <div class="col-md-8">
                                            <input class="form-control" type="datetime-local" id="voucher_payment_time"
                                                name="voucher_payment_time">
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="txt_payee"><span class="text-danger">*</span> Payee</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="txt_payee" name="txt_payee" type="text" value="" />
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="txt_bank_name">Payee Bank Name</label>
                                        <div class="col-md-8">
                                            <select id="txt_bank_name" class="form-control" name="bank" required>
                                                <option value="">-- Please select a bank -- </option>
                                                @foreach ($banks as $index => $bank)
                                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="adjudication_no">Adjudication/Smartbox No</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="adjudication_no" name="adjudication_no" type="text"
                                                value="" />
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="txt_bank_account">Payee Bank Account</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="txt_bank_account" name="txt_bank_account" type="text"
                                                value="" />
                                        </div>
                                    </div>
                                </div>
                
                               
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                  <div class="form-group row">
                                      <label class="col-md-4 col-form-label" for="adjudication_no">Payee Email</label>
                                      <div class="col-md-8">
                                          <input class="form-control" id="payee_email_voucher" name="payee_email_voucher" type="text"
                                              value="" />
                                      </div>
                                  </div>
                              </div>
                
                
                                <div id="dChequeNo" class="col-sm-6 col-md-10 col-lg-8 col-xl-6 form-group dPaymentType  row dChequeNo"
                                    style="display: none;">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="txt_cheque_no">Cheque No</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="txt_cheque_no" name="txt_cheque_no" type="text"
                                                value="" />
                                        </div>
                                    </div>
                                </div>
                
                               
                
                                <div id="dChequeNo" class="col-sm-6 col-md-10 col-lg-8 col-xl-6 dPaymentType  dCreditCard" style="display: none;">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="txt_card_no">Credit Card No</label>
                                        <div class="col-md-8">
                                            <input class="form-control" id="txt_card_no" name="txt_card_no" type="text"
                                                value="" />
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="attachment_file">Attachment</label>
                                        <div class="col-md-8">
                                          <input class="form-control" id="attachment_file" name="attachment_file" type="file">
                                        </div>
                                    </div>
                                </div>
                
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="txt_payee">Payment Description</label>
                                        <div class="col-md-8">
                                            <textarea class="form-control" id="voucher_remark" name="remark" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                
                         
                            @csrf
                            <div class="box-body no-padding" style="width:100%;overflow-x:auto">
                                <div class="box-header with-border" style="margin-top: 30px;;">
                                    <h3 class="box-title">Bill items</h3>
                                </div>
                                <table class="table table-striped table-bordered datatable">
                                    <thead>
                                        <tr class="text-center">
                                            <td>No</td>
                                            <td>Item(s)</td>
                                            <td>Available amount</td>
                                            <td>Amount</td>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl-voucher-request">
                                        <tr>
                                            <td class="text-center" colspan="5">No data</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                      <tr >
                                          <td colspan="3">Total</td>
                                          <td>RM <span id="voucherSum">0.00</span></td>
                                      </tr>
                                  </tfoot>
                                </table>
                            </div>
                        </form>
                        {{-- <div class="row " style="margin-top:30px;">
                            <div class="col-12">
                                <button onclick="exitBillCreateMode()" class="btn btn-danger" type="button"><span><i class="ion-reply"></i>
                                        Back</span> </button>
                                <button type="button" onclick="generateVoucher('{{ $case->id }}')"
                                    class="btn btn-success pull-right"><i class="fa fa-credit-card"></i> Generate Voucher
                                </button>
                            </div>
                        </div> --}}
                    </div>
                </div>
                

            </div>
            <div class="modal-footer">

                <button id="btnSubmitVoucherRequest" class="btn btn-success float-right" onclick="submitVoucherRequestV2()" type="button">
                    <span id="span_upload">Submit </span>
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
            </div>
        </div>

    </div>
</div>
