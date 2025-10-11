<div id="modalCreateNewReferral" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Change Client</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <div class="box-body " style="width:100%;overflow-x:auto">
                    <div class="row">
                        <div class="col-12 ">
                            <form method="POST" id="form_referral">
                                <div class="row" style="margin-top:40px;">
                                    <div class="col-12 ">
                                        <h4 style="margin-bottom: 20px;"><i class="fa fa-user-plus"></i> Referral Information
                                        </h4>
                                    </div>
        
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
        
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Referral Name</label>
                                                <input class="form-control" type="text" name="name"
                                                    id="referral_name_new">
                                            </div>
                                        </div>
        
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Referral email</label>
                                                <input class="form-control" type="text" name="email"
                                                    id="referral_email_new">
                                            </div>
                                        </div>
        
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Bank</label>
                                                <select id="ddl_bank" class="form-control" name="bank_id">
                                                    <option value="0">-- Select bank --</option>
                                                    @foreach ($banks as $index => $bank)
                                                        <option value="{{ $bank->id }}">{{ $bank->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
        
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>IC NO</label>
                                                <input class="form-control" name="ic_no" id="referral_ic_no_new"
                                                    type="text" />
                                            </div>
                                        </div>
        
        
                                    </div>
        
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Referral phone no</label>
                                                <input class="form-control" name="phone_no" id="referral_phone_no_new"
                                                    type="text" />
                                            </div>
                                        </div>
        
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Company</label>
                                                <input class="form-control" name="company" id="referral_company_new"
                                                    type="text" />
                                            </div>
                                        </div>
        
                                        <div class="form-group row ">
                                            <div class="col">
                                                <label>Bank Account</label>
                                                <input class="form-control" name="bank_account" id="referral_bank_account_new"
                                                    type="text" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
        
                            </form>
                        </div>

                    </div>

                   


                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>

                <button id="btnSubmit" class="btn btn-success float-right" type="button"
                    onclick="createReferral()">Create Referral</button>
                {{-- <button type="button" id="btnCloseFile" class="btn btn-close-abort btn-close-file btn-success float-right" onclick="closeFile('close')">Close File
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button> --}}

            </div>
        </div>

    </div>
</div>
