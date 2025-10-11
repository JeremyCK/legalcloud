@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">

@section('content')
    <div class="container-fluid">
        <div class="fade-in">


            <div class="row">
                <div class="col-sm-12">

                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Journal Entry</h4>
                                </div>

                                <div class="col-6">
                                    <a class="btn btn-lg btn-primary  float-right" href="/journal-entry-create">
                                        <i class="cil-plus"> </i>Create New Journal Entry
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body" style="width:100%;overflow-x:auto">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <form id="form_journal_filter" method="POST">
                                <div class="row">


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>TRX ID</label>
                                                <input class="form-control" type="text" id="trx_id" name="trx_id">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Bank</label>
                                                <select class="form-control" name="bank_account" id="bank_account">
                                                    <option value="0">-- Select bank account --</option>
                                                    @foreach ($OfficeBankAccount as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}">
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
                                            <div class="col">
                                                <label>Date From</label>
                                                <input class="form-control" type="date" id="date_from" name="date_from">
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <div class="col">
                                                <label>Date To</label>
                                                <input class="form-control" type="date" id="date_to" name="date_to">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            
                                            <div class="col">
                                                <label>Branch</label>
                                                <select class="form-control" name="branch_id" id="branch_id">
                                                    <option value="0">-- Select bank account --</option>
                                                    @foreach ($Branchs as $branch)
                                                        <option value="{{ $branch->id }}" >{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                    </div>



                                    <div class="col-sm-12">
                                      <a class="btn btn-lg btn-success  float-left" href="javascript:void(0)"
                                            onclick="document.getElementById('form_journal_filter').reset();">
                                            Clear Filter
                                        </a>

                                        <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                            onclick="reloadTable();">
                                            <i class="fa cil-search"> </i>Filter
                                        </a>
                                    </div>

                                    <div class="col-sm-12">
                                        <hr />
                                    </div>

                                </div>
                            </form>
                            <br>

                            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

                                <table class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Date</th>
                                            <th>Journal No</th>
                                            <th>Transaction ID</th>
                                            <th>Purpose</th>
                                            <th>Ref No</th>
                                            <th>Bank</th>
                                            <th>Debit</th>
                                            <th>Credit</th>
                                            <th>Created By</th>
                                            <th>Recon</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        function reloadTable() {
            var table = $('.yajra-datatable').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('journalEntrytMainList.list') }}",
                    data: function(d) {
                        d.date_from = $("#date_from").val();
                        d.date_to = $("#date_to").val();
                        d.trx_id = $("#trx_id").val();
                        d.bank_account = $("#bank_account").val();
                        d.branch_id = $("#branch_id").val();
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'journal_no',
                        name: 'journal_no'
                    },
                    {
                        data: 'transaction_id',
                        name: 'transaction_id'
                    },
                    {
                        data: 'remarks',
                        name: 'remarks'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    {
                        data: 'bank_account',
                        name: 'bank_account'
                    },
                    {
                        data: 'total_debit',
                        name: 'total_debit',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'total_credit',
                        name: 'total_credit',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'user_name',
                        name: 'user_name'
                    },
                    {
                        data: 'is_recon',
                        name: 'is_recon'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ]
            });
        }

        $(function() {
            reloadTable();
        });
    </script>
@endsection
