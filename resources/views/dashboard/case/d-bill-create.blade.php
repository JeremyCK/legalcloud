{{-- <div id="dBillCreate" class="card d_operation" style="display:none">
    <div class="card-header">
        <h4>Bill money entry</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header">

                        <div class="form-group row">

                            <div class="col-12">
                                <label>
                                    Quotation template
                                </label>
                            </div>

                            <div class="col-6">

                                <select id="ddl_quotation_template" class="form-control" name="ddl_quotation_template">
                                    <option value="0">-- Select Quotation Template --</option>
                                    @foreach ($quotation_template as $index => $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 float-right">
                                <button class="btn btn-primary" type="button"
                                    onclick="loadQuotationTemplate('{{ $case->id }}');">
                                    <i class="cil-caret-right"></i> Load Template
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body no-padding" style="width:100%;overflow-x:auto">


                        <div class="form-group row">
                            <div class="col-4">
                                <h3 class="box-title">Quotation1</h3>




                            </div>


                            <div class="col-4">
                                <div class="checkbox float-right">
                                    <input type="checkbox" name="check_all" value="1" onchange="quotationItemCheckAllController(1)" id="check_all" checked>
                                    <label for="check_all">Check All</label>
                                </div>

                            </div>

                            <div class="col-4">

                              <div class="checkbox float-left">
                                <input type="checkbox" name="uncheck_all" value="1" onchange="quotationItemCheckAllController(0)" id="uncheck_all" >
                                <label for="uncheck_all">Uncheck All</label>
                            </div>
                          </div>
                        </div>


                        <table class="table table-striped table-bordered datatable">
                            <tbody id="tbl-bill-create">



                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>

        <button class="btn btn-submit btn-success float-right" onclick="CreateBill('{{ $case->id }}')"
            type="button">
            <span id="span_upload">Create</span>
            <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </button>
        <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger">Cancel</a>

    </div>


</div> --}}
