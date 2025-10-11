<div id="modalCloseFile" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1"><span id="span_close_abort">Close File</span> -
                            [{{ $case->case_ref_no }}]</h4>
                        <input type="hidden" id="input_close_abort" />
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <form id="formCloseFile">
                    <div class="form-group row div-loading-close-file" id="div-loading-close-file">
                        <div class="col-12">
                            <textarea class="form-control text-center" disabled>Loading</textarea>
                        </div>

                    </div>


                    <div class="form-group row div-result-close-file">


                        
                            <div class="col-12 @if(!in_array($current_user->id, [1,34,36])) hidden @endif" >
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" for="cf_transfer_type">Transfer Type</label>
                                    <div class="col-md-8">
                                        <select class="form-control" name="cf_transfer_type" id="cf_transfer_type"
                                            onchange="changeTransferType()">
                                            <option value="oa" selected>Transfer To OA</option>
                                            <option value="bill">Transfer To Other Case Bill</option>
                                            <option value="trust">Transfer To Other Case Trust</option>
                                        </select>
                                    </div>
                                </div>
                                <hr />
                            </div>
                        

                       


                        <div class="col-12">
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="cf_trx_id" id="cf_trx_id" type="text">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 transfer_to_case" style="display: none">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_case">Case ref No</label>
                                <div class="col-md-8">

                                    <div class="input-group">
                                        <input class="form-control" name="cf_transfer_case" id="cf_transfer_case"
                                            value="" type="text" readonly="">
                                        <input class="form-control" name="cf_transfer_case_id" id="cf_transfer_case_id"
                                            value="0" type="hidden">
                                        <div class="input-group-append"><span class="input-group-text">
                                                <a class="" href="javascript:void(0)" style="margin:0"
                                                    onclick="openCaseList()"><i style="margin-right: 10px;"
                                                        class="fa fa-refresh"></i>Select</a>
                                            </span></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 transfer_to_bill" style="display: none">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_bill">Bill No</label>
                                <div class="col-md-8">

                                    <div class="input-group">
                                        <input class="form-control" name="cf_transfer_bill" id="cf_transfer_bill"
                                            value="" type="text" readonly="">
                                        <input class="form-control" name="cf_transfer_bill_id" id="cf_transfer_bill_id"
                                            value="0" type="hidden">
                                        <div class="input-group-append"><span class="input-group-text">
                                                <a class="" href="javascript:void(0)" style="margin:0"
                                                    onclick="openBillList()"><i style="margin-right: 10px;"
                                                        class="fa fa-refresh"></i>Select</a>
                                            </span></div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_amount">Transfer Total
                                    Amount</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="cf_transfer_amount" id="cf_transfer_amount"
                                        value="0.00" type="number" readonly="">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_from">Transfer From</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="cf_transfer_from" id="cf_transfer_from">
                                        <option value="0">-- Select bank account --</option>
                                        @foreach ($OfficeBankAccountCA as $bankAccount)
                                            <option value="{{ $bankAccount->id }}"
                                                data-account-type="{{ $bankAccount->account_type }}">
                                                {{ $bankAccount->name }}
                                                ({{ $bankAccount->account_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 transfer_to_oa">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_to">Transfer To OA</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="cf_transfer_to" id="cf_transfer_to">
                                        <option value="0">-- Select bank account --</option>
                                        @foreach ($OfficeBankAccountOA as $bankAccount)
                                            <option value="{{ $bankAccount->id }}"
                                                data-account-type="{{ $bankAccount->account_type }}">
                                                {{ $bankAccount->name }}
                                                ({{ $bankAccount->account_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 transfer_to_case transfer_to_bill" style="display: none">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_to_ca">Transfer To CA</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="cf_transfer_to_ca" id="cf_transfer_to_ca">
                                        <option value="0">-- Select bank account --</option>
                                        @foreach ($OfficeBankAccountCA as $bankAccount)
                                            <option value="{{ $bankAccount->id }}"
                                                data-account-type="{{ $bankAccount->account_type }}">
                                                {{ $bankAccount->name }}
                                                ({{ $bankAccount->account_no }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_transfer_date">Transfer Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="cf_transfer_date" id="cf_transfer_date"
                                        type="date">
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="cf_remark">Remarks</label>
                                <div class="col-md-8">
                                    <textarea class="form-control" id="cf_remark" name="cf_remark" row="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>


                    <table class="table mb-0 div-result-close-file">
                        <thead style="background-color: black;color:white">
                            <td>Item</td>
                            <td>Desc</td>
                            {{-- <td class="text-right">Used Amt</td> --}}
                            <td class="text-right">Balance to transfer</td>
                        </thead>
                        <tbody id="tblCloseFile">
                        </tbody>
                    </table>
                </form>

                <div id="div_search_case" style="display:none">

                    <div class="row">
                        <div class="col-6">
                            <h5 class="card-title mb-0 flex-grow-1">Case List</h5>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn  btn-danger float-right" onclick="backToCloseFile()">Back </button>
                        </div>
                    </div>
                    <hr />
                    <table id="tbl-case-list" class="table table-bordered table-striped yajra-datatable"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Case Ref No</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-case">
                        </tbody>
                    </table>
                </div>


                <div id="div_search_bill" style="display:none">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="card-title mb-0 flex-grow-1">Bill List</h5>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn  btn-danger float-right" onclick="backToCloseFile()">Back </button>
                        </div>
                    </div>
                    <hr />
                    <table id="tbl-search-bill-list" class="table table-bordered table-striped yajra-datatable"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Bill No</th>
                                <th>Case Ref No</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-case">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" id="btnCloseFile"
                    class="btn btn-close-abort btn-close-file btn-success float-right"
                    onclick="closeFile('close')">Close File
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>

                <button type="button" id="btnAbortFile"
                    class="btn btn-close-abort btn-abort-file btn-danger float-right"
                    onclick="closeFile('abort')">Abort File
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    function updateCloseFileTotalAmt() {
        $sumCloseFileTotal = 0;

        $.each($("input[name='close_file_bill']:checked"), function() {
            itemID = $(this).val();

            $sumCloseFileTotal += parseFloat($("#sum_close_file_" + itemID).val());
            console.log($("#sum_close_file_" + itemID).val());
        });

        $("#cf_transfer_amount").val($sumCloseFileTotal.toFixed(2));
    }

    function openCaseList() {
        $("#formCloseFile").hide();
        $("#div_search_bill").hide();
        $("#div_search_case").show();
    }

    function openBillList() {
        $("#formCloseFile").hide();
        $("#div_search_case").hide();
        $("#div_search_bill").show();
    }

    function backToCloseFile() {
        $("#formCloseFile").show();
        $("#div_search_case").hide();
        $("#div_search_bill").hide();
    }

    function transferToCase() {
        // $("#formCloseFile").hide();
        // $("#div_search_case").show();

        if (document.getElementById("chk_transfer_to_case").checked == true) {
            $(".transfer_to_case").show();
            $(".transfer_to_oa").hide();
        } else {
            $(".transfer_to_oa").show();
            $(".transfer_to_case").hide();
        }


    }

    function changeTransferType() {
        if ($("#cf_transfer_type").val() == 'oa') {
            $(".transfer_to_oa").show();
            $(".transfer_to_bill").hide();
            $(".transfer_to_case").hide();
        } else if ($("#cf_transfer_type").val() == 'bill') {
            $(".transfer_to_oa").hide();
            $(".transfer_to_case").hide();
            $(".transfer_to_bill").show();
        } else if ($("#cf_transfer_type").val() == 'trust') {
            $(".transfer_to_bill").hide();
            $(".transfer_to_oa").hide();
            $(".transfer_to_case").show();
        }

    }

    function selectCase($case_ref_no, $id) {
        $("#cf_transfer_case").val($case_ref_no);
        $("#cf_transfer_case_id").val($id);

        $("#formCloseFile").show();
        $("#div_search_case").hide();
        $("#div_search_bill").hide();
    }

    function selectSearchBill($bill_no, $bill_id, $case_id) {
        $("#cf_transfer_bill").val($bill_no);
        $("#cf_transfer_bill_id").val($bill_id);
        $("#cf_transfer_case_id").val($case_id);

        $("#formCloseFile").show();
        $("#div_search_case").hide();
        $("#div_search_bill").hide();
    }


    function loadCaseList() {
        var table3 = $('#tbl-case-list').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            pageLength: 100,
            ajax: {
                url: "{{ route('casesearch.list') }}",
                // data: {
                //     "type": $type,
                // }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'case_ref_no',
                    name: 'case_ref_no'
                },
                {
                    data: 'client_name',
                    name: 'client_name'
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                },



            ]
        });
    }

    function loadBillList() {
        var table3 = $('#tbl-search-bill-list').DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            pageLength: 100,
            ajax: {
                url: "{{ route('billsearch.list') }}",
                // data: {
                //     "type": $type,
                // }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'bill_no',
                    name: 'bill_no'
                },
                {
                    data: 'case_ref_no',
                    name: 'case_ref_no'
                },
                {
                    data: 'client_name',
                    name: 'client_name'
                },
                {
                    data: 'action',
                    name: 'action',
                    searchable: false,
                },



            ]
        });
    }
</script>
