@extends('dashboard.base')
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@section('content')
    <div class="container-fluid">
        <div class="fade-in">

            <div class="row">
                <div class="col-sm-12">


                    <div id="dList" class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <h4>Bank Reconcilation</h4>
                                </div>
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <a class="btn btn-lg btn-success  float-right" href="/report-bank-recon">
                                        <i class="fa cil-file"> </i>Recon Report
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <form method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">


                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Bank Account</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="bank_id" id="bank_id">
                                                    @foreach ($OfficeBankAccount as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }}
                                                            ({{ $bankAccount->account_no }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="trx_id" id="trx_id" type="text" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Transaction Amount</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="trx_amt" id="trx_amt" type="number" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Transaction Type</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="transaction_type" id="transaction_type">
                                                    <option value="0">All</option>
                                                    <option value="1">In Only</option>
                                                    <option value="2">Out Only</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Total Amount</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="total_amount_sum" id="total_amount_sum" type="text" readonly/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">From</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="date_from" id="date_from"
                                                    type="date" />
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">To</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="date_to" id="date_to" type="date" />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Is Recon</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="is_recon" id="is_recon">
                                                    <option value="99">All</option>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Voucher Type</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="voucher_type" id="voucher_type">
                                                    <option value="0">All</option>
                                                    <option value="1">Bill</option>
                                                    <option value="2">Trust</option>
                                                </select>
                                            </div>
                                        </div>

                                       

                                    </div>


                                    <div class="col-sm-12">



                                        <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                            onclick="reloadTable();">
                                            <i class="fa cil-search"> </i>Filter
                                        </a>

                                        <div class="form-check  float-right" style="margin-right:10px">
                                            <input class="form-check-input" type="checkbox" value="1"
                                                name="no_date_range_filter" id="no_date_range_filter">
                                            <label class="form-check-label" for="no_date_range_filter">
                                                Filter without date range
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <hr />
                                    </div>



                                </div>

                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Recon Month</label>
                                            <div class="col-md-4">
                                                <select id="ddl_month" class="form-control" name="ddl_month">
                                                    <option value="1"
                                                        @if (date('m') == '01') selected @endif>January</option>
                                                    <option value='2'
                                                        @if (date('m') == '02') selected @endif>February</option>
                                                    <option value='3'
                                                        @if (date('m') == '03') selected @endif>March</option>
                                                    <option value='4'
                                                        @if (date('m') == '04') selected @endif>April</option>
                                                    <option value='5'
                                                        @if (date('m') == '05') selected @endif>May</option>
                                                    <option value='6'
                                                        @if (date('m') == '06') selected @endif>June</option>
                                                    <option value='7'
                                                        @if (date('m') == '07') selected @endif>July</option>
                                                    <option value='8'
                                                        @if (date('m') == '08') selected @endif>August</option>
                                                    <option value='9'
                                                        @if (date('m') == '09') selected @endif>September</option>
                                                    <option value='10'
                                                        @if (date('m') == '10') selected @endif>October</option>
                                                    <option value='11'
                                                        @if (date('m') == '11') selected @endif>November
                                                    </option>
                                                    <option value='12'
                                                        @if (date('m') == '12') selected @endif>December
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="ddl_year" class="form-control" name="ddl_year">
                                                    <option value="2022" >2022</option>
                                                        
                                                    <option value="2023" selected>2023</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Recon Date</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="recon_date" id="recon_date"
                                                    type="date" readonly />
                                            </div>
                                        </div>


                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Add Cleared
                                                Deposits</label>
                                            <div class="col-md-8">
                                                <input class="form-control float-right" name="total_add_clr_deposit"
                                                    id="total_add_clr_deposit" type="text" readonly />
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Less Cleared
                                                Deposits</label>
                                            <div class="col-md-8">
                                                <input class="form-control float-right" name="total_less_clr_deposit"
                                                    id="total_less_clr_deposit" type="text" readonly />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12 ">
                                        <a class="btn btn-lg btn-success  float-left" style="margin-right:10px"
                                            href="javascript:void(0)" onclick="checkAll(true);">
                                            Check all
                                        </a>

                                        <a class="btn btn-lg bg-question  float-left" href="javascript:void(0)"
                                            onclick="checkAll(false);">
                                            Uncheck all
                                        </a>

                                        <div class="btn-group float-right">
                                            <button type="button" class="btn btn-warning btn-flat">Action</button>
                                            <button type="button" class="btn btn-warning btn-flat dropdown-toggle"
                                                data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                <!-- <a class="dropdown-item" href="javascript:void(0)" data-backdrop="static"  data-keyboard="false" onclick="completionDateModal(' . $row->id . ')" data-toggle="modal" data-target="#modalCompletionDate"><i class="cil-calendar-check"></i> Update completion date</a>
                                                  <div class="dropdown-divider"></div> -->
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="updateRecon('UPDATE')"><i class="cil-badge"></i>Update
                                                    Recon</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="javascript:void(0)"
                                                    onclick="updateRecon('REVERT')"><i class="cil-badge"></i>Revert
                                                    Recon</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </form>

                            <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Payment Date</th>
                                        <th>Transaction ID</th>
                                        <th>Amount</th>
                                        <th>Transaction Type</th>
                                        <th>Recon Date</th>
                                        <th>Is Recon</th>
                                        <th>Payee</th>
                                        <th>Desc</th>
                                        <th>Ref No</th>
                                        <th>Voucher No</th>
                                        <th>Voucher Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot  style="background-color: black;color:white">
                                    <th  colspan="7" class="text-left">Total </th>
                                    <th><span id="span_total_amount_sum"></span> </th>
                                    <th  colspan="4" class="text-left"> </th>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div id="dAction" class="card" style="display:none">
                        <div class="card-header">
                            <h4>Voucher</h4>
                        </div>
                        <div class="card-body">
                            <form id="form_voucher" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-12 ">

                                        <input class="form-control" type="hidden" id="selected_id" name="selected_id"
                                            value="">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Item</label>
                                                <input class="form-control" type="hidden" value=""
                                                    id="voucher_id" name="voucher_id">
                                                <input class="form-control" type="hidden" value="" id="status"
                                                    name="status">
                                                <input class="form-control" type="text" value="" id="item"
                                                    name="item" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Available Amount</label>
                                                <input class="form-control" type="text" value="" id="amt"
                                                    name="amt" disabled>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Remarks</label>
                                                <textarea class="form-control" id="remarks" name="remarks" rows="5"></textarea>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-bottom: 20px;">
                                            <div class="col-sm-12">
                                                <div class="overlay">
                                                    <i class="fa fa-refresh fa-spin"></i>
                                                </div>
                                                <a id="btnBackToEditMode"
                                                    class="btn btn-sm btn-info float-left mr-1 d-print-none"
                                                    href="javascript:void(0)" onclick="modeController('list');">
                                                    <i class="ion-reply"> </i> Back
                                                </a>
                                                <a id="btnPrint"
                                                    class="btn btn-sm btn-success float-right mr-1 d-print-none"
                                                    href="javascript:void(0)" onclick="updateVoucher(1)">
                                                    <i class="cil-check-alt"></i> Approve</a>

                                                <a id="btnPrint"
                                                    class="btn btn-sm btn-danger float-right mr-1 d-print-none"
                                                    href="javascript:void(0)" onclick="updateVoucher(2)">
                                                    <i class="cil-x"></i> Reject</a>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('javascript')
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        document.getElementById('ddl_month').onchange = function() {
            reconDateController();
        };

        document.getElementById('ddl_year').onchange = function() {
            reconDateController();
        };

        document.getElementById('bank_id').onchange = function() {
            reconDateController();
        };

        $(function() {
            reconDateController();
            reloadTable();
        });

        function reconDateController() {
            var date = new Date(),
                // y = date.getFullYear(),
                y = $('#ddl_year').val(),
                m = $('#ddl_month').val() - 1;
            var firstDay = new Date(y, m, 1);
            var lastDate = new Date(y, m + 1, 0);
            var lastDay = new Date(y, m + 1, 0);

            if ($("#bank_id").val() == 7) {
                lastDay = 28;
            } else {
                lastDay = (("0" + lastDay.getDate()).slice(-2));
            }

            var recon_date = lastDate.getFullYear() + "-" + (("0" + (lastDate.getMonth() + 1)).slice(-2)) + "-" + lastDay;
            var start_date = firstDay.getFullYear() + "-" + (("0" + (firstDay.getMonth() + 1)).slice(-2)) + "-" + (("0" +
                firstDay.getDate()).slice(-2));
            $("#recon_date").val(recon_date);
            $("#date_from").val(start_date);
            $("#date_to").val(recon_date);
        }

        function checkAll(bool) {
            $.each($("input[name='voucher']"), function() {
                template_id = [];

                itemID = $(this).attr('id');
                console.log(itemID)
                $("#" + itemID).prop('checked', bool);

            });
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function updateRecon($type) {

            var voucher_list = [];
            var voucher = {};

            Swal.fire({
                icon: 'warning',
                text: 'Update this record?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.each($("input[name='voucher']:checked"), function() {
                        itemID = $(this).val();

                        voucher = {
                            id: itemID,
                        };

                        voucher_list.push(voucher);
                    })

                    var form_data = new FormData();
                    form_data.append("voucher_list", JSON.stringify(voucher_list));
                    form_data.append("type", $type);
                    form_data.append("bank_id", $("#bank_id").val());
                    form_data.append("recon_date", $("#recon_date").val());
                    form_data.append("month", $("#ddl_month").val());
                    form_data.append("year", $("#ddl_year").val());

                    $.ajax({
                        type: 'POST',
                        url: '/updateRecon',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {
                                toastController('Record updated');
                                // updateReconValue(result);
                                reloadTable();
                            } else {
                                Swal.fire('notice!', result.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function getMonthRecon() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var form_data = new FormData();
            form_data.append("bank_id", $("#bank_id").val());
            form_data.append("recon_date", $("#recon_date").val());
            form_data.append("month", $("#ddl_month").val());
            form_data.append("year", $("#ddl_year").val());

            $.ajax({
                type: 'POST',
                url: '/getMonthRecon',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {
                        $("#total_add_clr_deposit").val(numberWithCommas(result.totalAddCLRDeposit));
                        $("#total_less_clr_deposit").val(numberWithCommas(result.totalLessCLRDeposit));
                        // console.log(result);
                    } else {
                        Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }

        function revertRecon($type) {

            var voucher_list = [];
            var voucher = {};

            Swal.fire({
                icon: 'warning',
                text: 'revert these record?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    $.each($("input[name='voucher']:checked"), function() {
                        itemID = $(this).val();

                        voucher = {
                            id: itemID,
                        };

                        voucher_list.push(voucher);
                    })

                    var form_data = new FormData();
                    form_data.append("voucher_list", JSON.stringify(voucher_list));
                    form_data.append("type", $type);
                    form_data.append("bank_id", $("#bank_id").val());

                    $.ajax({
                        type: 'POST',
                        url: '/updateRecon',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {
                                toastController('Record updated');
                                reloadTable();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });
                }
            })
        }

        function getBankReconTotal() {

            var voucher_list = [];
            var voucher = {};

            no_date_range_filter = 0;

            checkBox = document.getElementById('no_date_range_filter');
            if (checkBox.checked) {
                if ($("#trx_id").val() != '' || $("#trx_amt").val() != '') {
                    no_date_range_filter = 1;
                }

            }

            var form_data = new FormData();

            form_data.append("bank_id", $("#bank_id").val());
            form_data.append("trx_id", $("#trx_id").val());
            form_data.append("trx_amt", $("#trx_amt").val());
            form_data.append("branch", $("#ddl_branch").val());
            form_data.append("date_from", $("#date_from").val());
            form_data.append("date_to", $("#date_to").val());
            form_data.append("transaction_type", $("#transaction_type").val());
            form_data.append("voucher_type", $("#voucher_type").val());
            form_data.append("is_recon", $("#is_recon").val());
            form_data.append("no_date_range_filter", no_date_range_filter);

            $.ajax({
                type: 'POST',
                url: '/getBankReconTotal',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);

                    $("#total_amount_sum").val("RM " + numberWithCommas(data));
                    $("#span_total_amount_sum").html(numberWithCommas(data));
                    // if (data.status == 1) {
                    //     toastController('Record updated');
                    //     reloadTable();
                    // } else {
                    //     Swal.fire('notice!', data.message, 'warning');
                    // }

                }
            });
        }

        function reloadTable() {

            no_date_range_filter = 0;

            checkBox = document.getElementById('no_date_range_filter');
            if (checkBox.checked) {
                if ($("#trx_id").val() != '' || $("#trx_amt").val() != '') {
                    no_date_range_filter = 1;
                }

            }

            getBankReconTotal();

            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                pageLength:100,
                ajax: {
                    url: "{{ route('recon_list.list') }}",
                    data: function(d) {
                        d.bank_id = $("#bank_id").val();
                        d.trx_id = $("#trx_id").val();
                        d.trx_amt = $("#trx_amt").val();
                        d.branch = $("#ddl_branch").val();
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.transaction_type = $("#transaction_type").val();
                        d.voucher_type = $("#voucher_type").val();
                        d.is_recon = $("#is_recon").val();
                        d.no_date_range_filter = no_date_range_filter;
                    },
                },
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        name: 'action'
                    },
                    {
                        data: 'payment_date',
                        name: 'payment_date'
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id'
                    },
                    {
                        data: 'total_amount',
                        className: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2),
                        name: 'total_amount'
                    },
                    {
                        data: 'transaction_type',
                        className: 'text-center',
                        name: 'transaction_type'
                    },
                    {
                        data: 'recon_date',
                        name: 'recon_date'
                    },
                    {
                        data: 'is_recon',
                        className: 'text-center',
                        name: 'is_recon'
                    },
                    {
                        data: 'payee',
                        name: 'payee'
                    },
                    {
                        data: 'remark',
                        name: 'remark'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    {
                        data: 'voucher_no',
                        name: 'voucher_no'
                    },
                    {
                        data: 'voucher_type',
                        className: 'text-center',
                        name: 'voucher_type'
                    },
                ]
            });

            getMonthRecon();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function getLastDay() {

        }
    </script>
@endsection
