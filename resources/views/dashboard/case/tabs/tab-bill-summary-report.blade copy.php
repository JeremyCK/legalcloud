<div id="div_case_referral">
    <div class="row">
        <div class="col-12">
            <div class="box-tools">

                @if (in_array($current_user->menuroles, ['admin', 'management', 'sales', 'account', 'maker']) ||
                        $accessSummaryReportReferral == 1)
                    <h4>Summary report </h4>
                    <div id="dSummaryReport" class="div2  printableArea  "
                        style="overflow-x: auto; width:100%">
                        <table class="table table-striped">
                            <tr>
                                <!-- <th class="text-center">Invoice No</th> -->
                                <th class="text-center">Pfee 1</th>
                                <th class="text-center">Pfee 2</th>
                                <th class="text-center">Disb</th>
                                <th class="text-center">SST</th>
                                <th id="referral_1_table" class="text-center ">Referral(A1)</th>
                                <th id="referral_2_table" class="text-center ">Referral(A2)</th>
                                <th id="referral_3_table" class="text-center ">Referral(A3)</th>
                                <th id="referral_4_table" class="text-center ">Referral(A4)</th>
                                <th class="text-center ">Marketing</th>
                                <th class="text-center ">Uncollected</th>
                            </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <!-- <td class="text-center" style="border-right: solid 1px black;" id="sum_invoice_no"> </td> -->
                                    <td class="text-right" style="border-right: solid 1px black;"
                                        id="sum_pfee1"> </td>
                                    <td class="text-right" style="border-right: solid 1px black;"
                                        id="sum_pfee2"> </td>
                                    <td class="text-right" style="border-right: solid 1px black;"
                                        id="sum_disb"> </td>
                                    <td class="text-right" style="border-right: solid 1px black;"
                                        id="sum_sst"> </td>
                                    <td class="text-right " id="sum_referral_a1"
                                        style="border-right: solid 1px black;">0.00</td>
                                    <td class="text-right " id="sum_referral_a2"
                                        style="border-right: solid 1px black;">0.00</td>
                                    <td class="text-right " id="sum_referral_a3"
                                        style="border-right: solid 1px black;">0.00</td>
                                    <td class="text-right " id="sum_referral_a4"
                                        style="border-right: solid 1px black;">0.00</td>
                                    <td class="text-right " id="sum_marketing"
                                        style="border-right: solid 1px black;"> 0.00</td>
                                    <td class="text-right " id="sum_uncollected"> 0.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-12" style="margin-top:20px">
                        <hr />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <form id="form_bill_summary" enctype="multipart/form-data">
        {{-- @if ($case->status != 99) --}}
        <button class="btn btn-success  float-right " type="button" onclick="SaveSummaryInfo();"
            style="margin-right: 5px;">
            <i class="cil-plus"></i> Save
            <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </button>
        {{-- @endif --}}

        <div class="row">
            @if (in_array($current_user->menuroles, ['admin', 'management', 'sales', 'account', 'maker']) ||
                    $accessSummaryReportReferral == 1)

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral A(1)
                        <button name="referral_name_1_clear_btn"
                            class="btn btn-warning btn-xs   float-right quotation" type="button"
                            onclick="clearReferral(1);" style="margin-left:5px"> clear</button>
                    </h4>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Referral Name</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control referral_name" onclick="referralMode(1)"
                                type="text" name="referral_name_1" id="referral_name_1" readonly
                                autocomplete="off">
                            <input class="form-control" type="hidden" name="referral_id_1"
                                id="referral_id_1" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Amount</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="ref_a1_amt"
                                id="ref_a1_amt">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="ref_a1_payment_date"
                                id="ref_a1_payment_date"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="ref_a1_payment_trx_id"
                                id="ref_a1_payment_trx_id"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Bank</label>
                        </div>

                        <div class="col-9">
                            <select id="referral_bank_1" class="form-control" name="referral_bank_1"
                                readonly disabled>
                                <option value="0">-- Select bank --</option>
                                @foreach ($banks as $index => $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Account No</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control referral_name" name="referral_account_no_1"
                                id="referral_account_no_1" readonly autocomplete="off">
                        </div>
                    </div>


                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral
                        A(2)<button name="referral_name_2_clear_btn"
                            class="btn btn-warning btn-xs  float-right quotation" type="button"
                            onclick="clearReferral(2);" style="margin-left:5px"> clear</button></h4>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Referral Name </label>
                        </div>

                        <div class="col-9"> <input class="form-control referral_name"
                                onclick="referralMode(2)" type="text" name="referral_name_2"
                                id="referral_name_2" readonly autocomplete="off">
                            <input class="form-control" type="hidden" name="referral_id_2"
                                id="referral_id_2" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Amount </label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="ref_a2_amt"
                                id="ref_a2_amt">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date </label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="ref_a2_payment_date"
                                id="ref_a2_payment_date"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID </label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="ref_a2_payment_trx_id"
                                id="ref_a2_payment_trx_id"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Bank</label>
                        </div>

                        <div class="col-9">
                            <select id="referral_bank_2" class="form-control" name="referral_bank_2"
                                readonly disabled>
                                <option value="0">-- Select bank --</option>
                                @foreach ($banks as $index => $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Account No</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control referral_name" name="referral_account_no_2"
                                id="referral_account_no_2" readonly autocomplete="off">
                        </div>
                    </div>


                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral
                        A(3)<button name="referral_name_3_clear_btn"
                            class="btn btn-warning btn-xs  float-right quotation" type="button"
                            onclick="clearReferral(3);" style="margin-left:5px"> clear</button></h4>

                    <div class="form-group row">

                        <div class="col-3">
                            <label>Referral Name </label>
                        </div>

                        <div class="col-9">
                            <input class="form-control referral_name" type="text"
                                name="referral_name_3" onclick="referralMode(3)" id="referral_name_3"
                                readonly autocomplete="off">
                            <input class="form-control" type="hidden" name="referral_id_3"
                                id="referral_id_3" autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Amount</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="ref_a3_amt"
                                id="ref_a3_amt">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="ref_a3_payment_date"
                                id="ref_a3_payment_date"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="ref_a3_payment_trx_id"
                                id="ref_a3_payment_trx_id"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Bank</label>
                        </div>

                        <div class="col-9">
                            <select id="referral_bank_3" class="form-control" name="referral_bank_3"
                                readonly disabled>
                                <option value="0">-- Select bank --</option>
                                @foreach ($banks as $index => $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Account No</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control referral_name" name="referral_account_no_3"
                                id="referral_account_no_3" readonly autocomplete="off">
                        </div>
                    </div>

                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral
                        A(4)<button name="referral_name_4_clear_btn"
                            class="btn btn-warning btn-xs  float-right quotation" type="button"
                            onclick="clearReferral(4);" style="margin-left:5px"> clear</button></h4>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Referral Name</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control referral_name" type="text"
                                name="referral_name_4" onclick="referralMode(4)" id="referral_name_4"
                                readonly autocomplete="off">
                            <input class="form-control" type="hidden" name="referral_id_4"
                                id="referral_id_4" autocomplete="off">
                        </div>
                    </div>



                    <div class="form-group row">

                        <div class="col-3">
                            <label>Amount</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="ref_a4_amt"
                                id="ref_a4_amt">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="ref_a4_payment_date"
                                id="ref_a4_payment_date"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="ref_a4_payment_trx_id"
                                id="ref_a4_payment_trx_id"
                                @if ($current_user->menuroles == 'sales') disabled @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Bank</label>
                        </div>

                        <div class="col-9">
                            <select id="referral_bank_4" class="form-control" name="referral_bank_4"
                                readonly disabled>
                                <option value="0">-- Select bank --</option>
                                @foreach ($banks as $index => $bank)
                                    <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Account No</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control referral_name" name="referral_account_no_4"
                                id="referral_account_no_4" readonly autocomplete="off">
                        </div>
                    </div>

                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Uncollected</h4>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Uncollected amount</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="uncollected_amt"
                                id="uncollected_amt" value="" autocomplete="off">
                        </div>

                    </div>
                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> <span
                            class="text-danger">*</span> Collected</h4>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Collected Amount</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="collection_amount"
                                id="collection_amount" value="" autocomplete="off">
                        </div>

                    </div>
                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Marketing</h4>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Sales Name</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="name"
                                id="sales_name" value="{{ $sales->name }}" readonly
                                autocomplete="off">
                            <input class="form-control" type="hidden" name="sales_id"
                                id="sales_id" value="{{ $case->sales_user_id }}"
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Amount</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="marketing_amt"
                                id="marketing_amt">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="sales_payment_date"
                                id="sales_payment_date"
                                @if ($current_user->menuroles == 'sales') readonly @endif>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="sales_payment_trx_id"
                                id="sales_payment_trx_id"
                                @if ($current_user->menuroles == 'sales') readonly @endif>
                        </div>
                    </div>
                </div>


            @endif

            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']))
                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> SST</h4>


                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="sst_payment_date"
                                id="sst_payment_date">
                        </div>
                    </div>



                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="sst_payment_trx_id"
                                id="sst_payment_trx_id">
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Disb</h4>


                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="disb_payment_date"
                                id="disb_payment_date">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Name</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="disb_name"
                                id="disb_name">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Amount</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="number" name="disb_amt_manual"
                                id="disb_amt_manual">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="disb_trx_id"
                                id="disb_trx_id">
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 hide" style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> PFee 1</h4>


                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="pfee1_receipt_date"
                                id="pfee1_receipt_date">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="pfee1_receipt_trx_id"
                                id="pfee1_receipt_trx_id">
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 hide" style="padding:30px">
                    <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> PFee 2</h4>


                    <div class="form-group row">
                        <div class="col-3">
                            <label>Payment Date</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="date" name="pfee2_receipt_date"
                                id="pfee2_receipt_date">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-3">
                            <label>Transaction ID</label>
                        </div>

                        <div class="col-9">
                            <input class="form-control" type="text" name="pfee2_receipt_trx_id"
                                id="pfee2_receipt_trx_id">
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="padding:30px">
                <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> FINANCED</h4>


                <div class="form-group row">
                    <div class="col-3">
                        <label>Financed Fee</label>
                    </div>

                    <div class="col-9">
                        <input class="form-control" type="number" name="financed_fee"
                            id="financed_fee">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-3">
                        <label>Financed Sum</label>
                    </div>

                    <div class="col-9">
                        <input class="form-control" type="number" name="financed_sum"
                            id="financed_sum">
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-3">
                        <label>Payment Date</label>
                    </div>

                    <div class="col-9">
                        <input class="form-control" type="date" name="financed_payment_date"
                            id="financed_payment_date">
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>