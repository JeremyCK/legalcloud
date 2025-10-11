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
                                    <h4>Transfer Prof Fee</h4>
                                </div>

                                <div class="col-6">
                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('transfer-fee-list') }}">
                                        <i class="cil-arrow-left"> </i>Back to list </a>
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
                                            <label class="col-md-4 col-form-label" for="transfer_date">Transfer Date
                                            </label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="transfer_date" id="transfer_date"
                                                    value="{{ $TransferFeeMain->transfer_date }}" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transfer_amount">Transfer Total
                                                Amount</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="transfer_amount_hidden"
                                                    value="{{ $TransferFeeMain->transfer_amount }}"
                                                    id="transfer_amount_hidden" value="0.00" type="hidden" />
                                                <input class="form-control" name="transfer_amount"
                                                    value="{{ $TransferFeeMain->transfer_amount }}" id="transfer_amount"
                                                    value="0.00" type="number" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transfer_from">Transfer From</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="transfer_from" id="transfer_from">
                                                    <option value="0">-- Select bank account --</option>
                                                    @foreach ($OfficeBankAccount as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}"
                                                            @if ($TransferFeeMain->transfer_from == $bankAccount->id) selected @endif>
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
                                            <label class="col-md-4 col-form-label" for="transfer_from">Transfer To</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="transfer_to" id="transfer_to">
                                                    <option value="0">-- Select bank account --</option>
                                                    @foreach ($OfficeBankAccount as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}"
                                                            @if ($TransferFeeMain->transfer_to == $bankAccount->id) selected @endif>
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
                                            <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="trx_id" id="trx_id" type="text"
                                                    value="{{ $TransferFeeMain->transaction_id }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Purpose</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="purpose" name="purpose" row="3">{{ $TransferFeeMain->purpose }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                  


                                  
                                        <div class="col-sm-12">

                                            <div class="btn-group  float-right">
                                                <button type="button" class="btn btn-info btn-flat">Action</button>
                                                <button type="button" class="btn btn-info btn-flat dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>

                                                <div class="dropdown-menu" style="padding:0">
                                                    <a class="dropdown-item btn-info" href="javascript:void(0)" data-target="#modalPrint" data-backdrop="static" data-keyboard="false" data-toggle="modal" style="color:white;margin:0"><i style="margin-right: 10px;" class="fa fa-print"></i>Print</a>
                                                          
                                                    @if ($TransferFeeMain->is_recon == 0)
                                                        <div class="dropdown-divider" style="margin:0"></div>
                                                        <a class="dropdown-item btn-warning" href="javascript:void(0)" onclick="reconTransferFee()" style="color:white;margin:0"><i style="margin-right: 10px;" class="fa cil-save"></i>Bank Recon</a>
                                                        <div class="dropdown-divider" style="margin:0"></div>
                                                        <a class="dropdown-item btn-success" href="javascript:void(0)" id="btnSave"  onclick="saveTransferFee()" style="color:white;margin:0"><i style="margin-right: 10px;" class="fa cil-save"></i>Save</a>
                                                    @endif
                                                    {{-- <a class="btn btn-warning  float-left" href="javascript:void(0)"
                                                    onclick="reconTransferFee();">
                                                    <i class="fa cil-save"> </i>Bank Recon
                                                </a>
                                                <div class="dropdown-divider" style="margin:0"></div>
                                                <a class="btn btn-success  float-right" href="javascript:void(0)"
                                                    onclick="saveTransferFee();">
                                                    <i class="fa cil-save"> </i>Save
                                                </a> --}}
                                                </div>
                                                    
                                              
        
                                            </div>

                                            

                                            {{-- <a class="btn btn-info  float-left" href="javascript:void(0)"
                                                data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                                data-target="#modalPrint">
                                                <i class="fa fa-print"></i>Print
                                            </a> --}}

                                            
                                        </div>

                                    @if ($TransferFeeMain->is_recon == 1)
                                        <div class="col-sm-12">
                                            <hr />
                                        </div>

                                        <div class="col-sm-12">
                                            <span class="badge badge-danger">RECONCILED</span> This batch of transfer fee
                                            already reconciled
                                        </div>
                                    @endif

                                    <div class="col-sm-12">
                                        <hr />
                                    </div>



                                </div>


                                <div class="row">
                                    <div class="col-12 ">
                                        <h4>Transferred List</h4>
                                    </div>
                                    <div class="col-12 ">
                                        <hr />
                                    </div>

                                    <div class="col-12 " style="margin-bottom:20px;">
                                        @if ($TransferFeeMain->is_recon == 0)
                                            <a class="btn btn-danger " href="javascript:void(0)"
                                                onclick="deleteTransferSelected();">
                                                <i class="fa cil-x"> </i>Delete selected
                                            </a>
                                        @endif

                                    </div>
                                    <div class="col-sm-12">
                                        <table id="tbl-transferred-fee"
                                            class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Action</th>
                                                    <th>Ref No</th>
                                                    <th>Invoice No</th>
                                                    <th>Invoice Date</th>
                                                    <th>Total amt</th>
                                                    <th>Collected amt</th>
                                                    <th>pfee</th>
                                                    <th>sst</th>
                                                    <th>Pfee to transfer</th>
                                                    <th>SST to transfer</th>
                                                    <th>Transferred Bal</th>
                                                    <th>Transferred SST</th>
                                                    <th>Payment Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot style="background-color: black;color:white">
                                                <th colspan="5" class="text-left">Total </th>
                                                <th><span id="span_total_amount">0.00</span></th>
                                                <th><span id="span_collected_amount">0.00</span> </th>
                                                <th><span id="span_total_pfee">0.00</span> </th>
                                                <th><span id="span_total_sst">0.00</span> </th>
                                                <th><span id="span_total_pfee_to_transfer">0.00</span> </th>
                                                <th><span id="span_total_sst_to_transfer">0.00</span> </th>
                                                <th><span id="span_total_transferred_pfee">0.00</span> </th>
                                                <th><span id="span_total_transferred_sst">0.00</span> </th>
                                                <th class="text-left"> </th>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>



                            </form>

                            <div class="col-12 ">
                                <hr />
                            </div>

                            @if ($TransferFeeMain->is_recon == 0)
                                <div class="col-12 ">
                                    <h4>New transfer list</h4>
                                </div>



                                <div class="col-12 ">
                                    <hr />
                                </div>

                                <div class="col-12 " style="margin-bottom:20px;">
                                    <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteAll();">
                                        <i class="fa cil-x"> </i>Delete all
                                    </a>

                                    <a class="btn btn-danger " href="javascript:void(0)" onclick="deleteSelected();">
                                        <i class="fa cil-x"> </i>Delete selected
                                    </a>

                                    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                        data-toggle="modal" data-target="#modalTransferFee"
                                        class="btn btn-info float-right">Add new payment <i class="cil-plus"></i> </a>
                                </div>

                                <table id="tbl-transfer-bill-added"
                                    class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Ref No</th>
                                            {{-- <th>Client Name</th>
                                        <th>Bill No</th> --}}
                                            <th>Invoice No</th>
                                            <th>Invoice Date</th>
                                            <th>Total amt</th>
                                            <th>Collected amt</th>

                                            <th>pfee</th>
                                            {{-- <th>pfee2</th> --}}
                                            <th>sst</th>
                                            <th>Pfee to transfer</th>
                                            <th>SST to transfer</th>
                                            <th>Transferred Bal</th>
                                            <th>Transferred SST</th>
                                            {{-- <th>pfee1</th>
                                        <th>pfee2</th>
                                        <th>sst</th>
                                        <th>Bal to transfer</th> --}}
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            @endif
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
                                                    <i class="fa fa-print"></i> Approve</a>

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

    <div id="modalTransferFee" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width:1200px;width: 100% !important">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    {{-- <div style="height: 600px; overflow: auto">
                        <table id="tbl_bill" class="table  " style="height: 600px; overflow: auto" >
                        </table>
                    </div> --}}
                    {{-- <table id="tbl_bill" class="table  datatable" style="overflow-x: auto; width:100%; max-height:700px">
                    </table> --}}

                    <div class="row">

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_date">Recv Start Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="recv_start_date" id="recv_start_date"
                                        type="date" />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_date">Recv End Date</label>
                                <div class="col-md-8">
                                    <input class="form-control" name="recv_end_date" id="recv_end_date"
                                        type="date" />
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                            <div class="form-group row">
                                <label class="col-md-4 col-form-label" for="transfer_from">Branch</label>
                                <div class="col-md-8">
                                    <select class="form-control" name="branch" id="branch">
                                        <option value="0">-- Select Branch --</option>
                                        @foreach ($Branchs as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>


                        <div class="col-sm-12">

                            <a class="btn btn-lg btn-info  float-right" href="javascript:void(0)"
                                onclick="reloadTable();">
                                <i class="fa cil-search"> </i>Filter
                            </a>
                        </div>

                    </div>

                    <table id="tbl-transfer-bill" class="table table-bordered table-striped yajra-datatable"
                        style="width:100%;height:300px !important; overflow: auto"">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Ref No</th>
                                <th>Client Name</th>
                                {{-- <th>Bill No</th> --}}
                                <th>Invoice No</th>
                                <th>Invoice Date</th>
                                <th>Total amt</th>
                                <th>Collected amt</th>
                                <th>pfee</th>
                                {{-- <th>pfee2</th> --}}
                                <th>sst</th>
                                <th>Pfee to transfer</th>
                                <th>SST to transfer</th>
                                <th>Transferred Bal</th>
                                <th>Transferred SST</th>
                                <th>Payment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success float-right" onclick="AddIntoTransferList()">Add
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                </div>
            </div>

        </div>
    </div>


    <div id="modalPrint" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width:1200px;width: 100% !important">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">

                    <div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation"
                        style="padding-top:20px;padding-bottom:20px;padding-left:40px;padding-right:40px;background-color:white !important; border:1px solid black">

                        <div class="row">

                            <div class="col-12 text-center mb-2">
                                <h4>Transfer Fee Record</h4>

                            </div>

                            <div class="col-6 ">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" >Transfer Date
                                    </label>
                                    <div class="col-md-8">
                                        <input class="form-control" disabled value="{{ $TransferFeeMain->transfer_date }}" type="date" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 ">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" for="transfer_amount">Transfer Total
                                        Amount</label>
                                    <div class="col-md-8">
                                        <input class="form-control" disabled value="{{ number_format($TransferFeeMain->transfer_amount, 2, '.', ',') }}"  type="text" readonly />
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 ">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" for="transfer_from">Transfer From</label>
                                    <div class="col-md-8">
                                        <select class="form-control" disabled>
                                            <option value="0">-- Select bank account --</option>
                                            @foreach ($OfficeBankAccount as $bankAccount)
                                                <option value="{{ $bankAccount->id }}"
                                                    @if ($TransferFeeMain->transfer_from == $bankAccount->id) selected @endif>
                                                    {{ $bankAccount->name }}
                                                    ({{ $bankAccount->account_no }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 ">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" for="transfer_from">Transfer To</label>
                                    <div class="col-md-8">
                                        <select class="form-control" disabled>
                                            <option value="0">-- Select bank account --</option>
                                            @foreach ($OfficeBankAccount as $bankAccount)
                                                <option value="{{ $bankAccount->id }}"
                                                    @if ($TransferFeeMain->transfer_to == $bankAccount->id) selected @endif>
                                                    {{ $bankAccount->name }}
                                                    ({{ $bankAccount->account_no }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 ">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                    <div class="col-md-8">
                                        <input class="form-control" disabled type="text"
                                            value="{{ $TransferFeeMain->transaction_id }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="col-6 ">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" for="hf-email">Purpose</label>
                                    <div class="col-md-8">
                                        <textarea class="form-control"  disabled row="3">{{ $TransferFeeMain->purpose }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">

                            <div class="col-sm-12">
                                <table  id="tbl-transferred-print"
                                    class="table table-bordered table-striped yajra-datatable" style="width:100%">
                                    {{-- <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Action</th>
                                            <th>Ref No</th>
                                            <th>Invoice No</th>
                                            <th>Total amt</th>
                                            <th>Collected amt</th>
                                            <th>pfee</th>
                                            <th>sst</th>
                                            <th>Pfee to transfer</th>
                                            <th>SST to transfer</th>
                                            <th>Transferred Bal</th>
                                            <th>Transferred SST</th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl-transferred-print">
                                    </tbody>
                                    <tfoot style="background-color: black;color:white">
                                        <th colspan="3" class="text-left">Total </th>
                                        <th><span id="span_total_amount">0.00</span></th>
                                        <th><span id="span_collected_amount">0.00</span> </th>
                                        <th><span id="span_total_pfee">0.00</span> </th>
                                        <th><span id="span_total_sst">0.00</span> </th>
                                        <th><span id="span_total_pfee_to_transfer">0.00</span> </th>
                                        <th><span id="span_total_sst_to_transfer">0.00</span> </th>
                                        <th><span id="span_total_transferred_pfee">0.00</span> </th>
                                        <th><span id="span_total_transferred_sst">0.00</span> </th>
                                        <th class="text-left"> </th>
                                    </tfoot> --}}
                                </table>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success float-right" onclick="PrintAreaQuotation()">
                        <i class="fa fa-print"></i>Print
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('javascript')
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PrintArea/2.4.1/jquery.PrintArea.min.js"></script>
    <script src="{{ asset('js/jquery.print.js') }}"></script>
    <script>
        // document.getElementById('ddl_month').onchange = function() {
        //     reconDateController();
        // };

        function PrintAreaQuotation() {


            $("#dQuotationInvoice-p").print({
                addGlobalStyles: true,
                stylesheet: true,
                rejectWindow: true,
                noPrintSelector: ".no-print",
                iframe: false,
                append: null,
                prepend: null
            });
        }

        var transfer_fee_add_list = [];

        $(function() {
            // reconDateController();
            // getTransferList();
            reloadTable();
            reloadAddTable();
            reloadTransferredTable();
            loadTransferredBill();
        });

        function getTransferBillList() {

        }

        function deleteAll() {
            Swal.fire({
                icon: 'warning',
                text: 'Delete all payments?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    transfer_fee_add_list = [];

                    reloadAddTable();
                    reloadTable();
                }
            })
        }

        function deleteSelected() {
            Swal.fire({
                icon: 'warning',
                text: 'Delete selected payments?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    transfer_fee_add_list = [];
                    $.each($("input[name='add_bill']:not(:checked)"), function() {
                        itemID = $(this).val();
                        bill = {
                            id: itemID,
                        };
                        transfer_fee_add_list.push(bill);
                    })

                    console.log(transfer_fee_add_list);
                    reloadAddTable();
                    reloadTable();
                }
            })
        }

        function reconTransferFee() {
            Swal.fire({
                icon: 'warning',
                text: 'Recon this batch of transfer fee?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/reconTransferFee/{{ $TransferFeeMain->id }}',
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {

                                Swal.fire(
                                    'Success!', 'Record deleted',
                                    'success'
                                )
                                location.reload();

                            } else {}
                        }
                    });

                    console.log(transfer_fee_delete_list);

                }
            })
        }

        function deleteTransferSelected() {
            Swal.fire({
                icon: 'warning',
                text: 'Delete selected payments?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    transfer_fee_add_list = [];
                    $.each($("input[name='trans_bill']:not(:checked)"), function() {
                        itemID = $(this).val();
                        bill = {
                            id: itemID,
                        };
                        transfer_fee_add_list.push(bill);
                    })

                    


                    transfer_fee_delete_list = [];

                    $.each($("input[name='trans_bill']:checked"), function() {
                        itemID = $(this).val();
                        bill = {
                            id: itemID,
                        };
                        transfer_fee_delete_list.push(bill);
                    })


                    var form_data = new FormData();
                    form_data.append("delete_bill", JSON.stringify(transfer_fee_delete_list));

                    $.ajax({
                        type: 'POST',
                        url: '/deleteTransferFee/{{ $TransferFeeMain->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {

                                Swal.fire(
                                    'Success!', 'Record deleted',
                                    'success'
                                )

                                location.reload();

                                // reloadAddTable();
                                // reloadTable();
                                // reloadTransferredTable();

                                // window.location.href = '/transfer-fee-list';

                                // $("#tbl_bill").html(result.billList);
                            } else {
                                // Swal.fire('notice!', result.message, 'warning');
                            }
                        }
                    });

                    console.log(transfer_fee_delete_list);

                }
            })
        }

        function balUpdate() {
            var sum = 0;
            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                // value = $("#selected_amt_" + itemID).val();
                sum += parseFloat($("#ban_to_transfer" + itemID).val());
            })

            $("#transfer_amount").val(sum);
        }

        function saveTransferFee() {

            var voucher_list = [];
            var voucher = {};
            var errorCount = 0;

            if ($("#transfer_from").val() == 0 || $("#transfer_to").val() == 0 || $("#trx_id").val() == '' || $(
                    "#transfer_date").val() == '') {

                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure all mandatory fields fill',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            $test = 0;

            $.each($("input[name='add_bill']"), function() {
                itemID = $(this).val();
                // value = $("#selected_amt_" + itemID).val();
                value = $("#ban_to_transfer" + itemID).val();
                value_limit = $("#ban_to_transfer_limt_" + itemID).val();
                sst = $("#sst_to_transfer_" + itemID).val();

                if (parseFloat(value) > parseFloat(value_limit)) {
                    $("#ban_to_transfer" + itemID).addClass("highLight_input_red");
                    errorCount += 1;

                }

                $test += value;
                voucher = {
                    id: itemID,
                    value: value,
                    sst: sst,
                };

                voucher_list.push(voucher);
            })

            if (errorCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure the balance to transfer not exceed the limit',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }


            // if (voucher_list.length <= 0) {
            //     Swal.fire({
            //         icon: 'warning',
            //         text: 'No bill selected',
            //         confirmButtonText: `Yes`,
            //         confirmButtonColor: '#3085d6',
            //         cancelButtonColor: '#d33',
            //     });
            //     return;
            // }

            var form_data = new FormData();
            form_data.append("add_bill", JSON.stringify(voucher_list));
            form_data.append("transfer_from", $("#transfer_from").val());
            form_data.append("transfer_to", $("#transfer_to").val());
            form_data.append("trx_id", $("#trx_id").val());
            form_data.append("transfer_date", $("#transfer_date").val());
            form_data.append("purpose", $("#purpose").val());

            $("#btnSave").attr("disabled", true);

            $.ajax({
                type: 'POST',
                url: '/updateTranferFee/{{ $TransferFeeMain->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {

                        Swal.fire(
                            'Success!', 'Record created',
                            'success'
                        )


                        location.reload();

                        // reloadAddTable();
                        // reloadTable();
                        // reloadTransferredTable();

                        // alert(result.total_amount);

                        $("#transfer_amount").val(result.total_amount);

                        // window.location.href = '/transfer-fee-list';

                        // $("#tbl_bill").html(result.billList);
                    } else {
                        $("#btnSave").attr("disabled", false);
                        // Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }


        function reconDateController() {
            var date = new Date(),
                y = date.getFullYear(),
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
                        value = $("#selected_amt_" + itemID).val();

                        voucher = {
                            id: itemID,
                            value: value,
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

        function getTransferList() {
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
                url: '/getTransferList',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {

                        $("#tbl_bill").html(result.billList);
                    } else {
                        // Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
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
                    } else {
                        Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }

        function AddIntoTransferList() {
            $.each($("input[name='bill']:checked"), function() {
                itemID = $(this).val();
                // itemID = $(this).attr('id');

                bill = {
                    id: itemID,
                };
                transfer_fee_add_list.push(bill);
            })

            console.log(transfer_fee_add_list);
            reloadAddTable();
            reloadTable();
            closeUniversalModal();
        }

        function loadTransferredBill(){

        var form_data = new FormData();
            form_data.append("transfer_list", JSON.stringify(transfer_fee_add_list));
            form_data.append("type", 'transferred');
            form_data.append("transaction_id", {{ $TransferFeeMain->id }});

            $.ajax({
                type: 'POST',
                url: '/getTransferFeeBillListV2',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(data) {
                    console.log(data);
                    $('#tbl-transferred-print').html(data.view);
                }
            });
        }

        function reloadTransferredTable() {
            var table = $('#tbl-transferred-fee').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 200,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'transferred';
                        d.transaction_id = {{ $TransferFeeMain->id }};
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'action',
                        className: 'text-center no-print',
                        name: 'action'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'total_amt_inv',
                        name: 'total_amt_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'collected_amt',
                        name: 'collected_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },

                    {
                        data: 'pfee_sum',
                        name: 'pfee_sum',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'bal_to_transfer_v3',
                        name: 'bal_to_transfer_v3',
                        render: $.fn.dataTable.render.number(',', '.', 2),
                        class: 'text-right',
                    },
                    {
                        data: 'sst_to_transfer',
                        name: 'sst_to_transfer',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transfer_amount',
                        name: 'transfer_amount',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'sst_amount',
                        name: 'sst_amount',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
                ],
                drawCallback: function(settings) {

                    // var api = this.api(),
                    //     data;

                    // var intVal = function(i) {
                    //     return typeof i === 'string' ?
                    //         i.replace(/[\$,]/g, '') * 1 :
                    //         typeof i === 'number' ?
                    //         i : 0;
                    // };

                    // var span_total_amount = api.column(5).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_collected_amount = api.column(6).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_pfee = api.column(7).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_sst = api.column(8).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_pfee_to_transfer = api.column(9).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_sst_to_transfer = api.column(10).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_transferred_pfee = api.column(11).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);
                    // var span_total_transferred_sst = api.column(12).data().reduce(function(a, b) {
                    //     return intVal(a) + intVal(b);
                    // }, 0);


                    // $("#span_total_amount").html(numberWithCommas(span_total_amount.toFixed(2)));
                    // $("#span_collected_amount").html(numberWithCommas(span_collected_amount.toFixed(2)));
                    // $("#span_total_pfee").html(numberWithCommas(span_total_pfee.toFixed(2)));
                    // $("#span_total_sst").html(numberWithCommas(span_total_sst.toFixed(2)));
                    // $("#span_total_pfee_to_transfer").html(numberWithCommas(span_total_pfee_to_transfer.toFixed(
                    //     2)));
                    // $("#span_total_sst_to_transfer").html(numberWithCommas(span_total_sst_to_transfer.toFixed(
                    //     2)));
                    // $("#span_total_transferred_pfee").html(numberWithCommas(span_total_transferred_pfee.toFixed(
                    //     2)));
                    // $("#span_total_transferred_sst").html(numberWithCommas(span_total_transferred_sst.toFixed(
                    //     2)));


                }
            });




        }

        function reloadTransferredTableBak() {
            var table = $('#tbl-transferred-fee').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 200,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'transferred';
                        d.transaction_id = {{ $TransferFeeMain->id }};
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
                        data: 'action',
                        className: 'text-center no-print',
                        name: 'action'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    // {
                    //     data: 'client_name',
                    //     name: 'client_name'
                    // },
                    // {
                    //     data: 'bill_no',
                    //     name: 'bill_no'
                    // },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'total_amt_inv',
                        name: 'total_amt_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'collected_amt',
                        name: 'collected_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },

                    {
                        data: 'pfee_sum',
                        name: 'pfee_sum',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'bal_to_transfer_v3',
                        name: 'bal_to_transfer_v3',
                        render: $.fn.dataTable.render.number(',', '.', 2),
                        class: 'text-right',
                    },
                    {
                        data: 'sst_to_transfer',
                        name: 'sst_to_transfer',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'cal_pfee_bal',
                    //     name: 'cal_pfee_bal',
                    //     render: $.fn.dataTable.render.number(',', '.', 2),
                    //     class: 'text-right',
                    // },
                    // {
                    //     data: 'cal_sst_bal',
                    //     name: 'sst_to_transfer',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    {
                        data: 'transfer_amount',
                        name: 'transfer_amount',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'sst_amount',
                        name: 'sst_amount',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee1_inv',
                    //     name: 'pfee1_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    // {
                    //     data: 'sst_inv',
                    //     name: 'sst_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    // {
                    //     data: 'bal_to_transfer',
                    //     name: 'bal_to_transfer',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
                ],
                drawCallback: function(settings) {

                    var api = this.api(),
                        data;

                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    var span_total_amount = api.column(5).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_collected_amount = api.column(6).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_pfee = api.column(7).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_sst = api.column(8).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_pfee_to_transfer = api.column(9).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_sst_to_transfer = api.column(10).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_transferred_pfee = api.column(11).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);
                    var span_total_transferred_sst = api.column(12).data().reduce(function(a, b) {
                        return intVal(a) + intVal(b);
                    }, 0);


                    $("#span_total_amount").html(numberWithCommas(span_total_amount.toFixed(2)));
                    $("#span_collected_amount").html(numberWithCommas(span_collected_amount.toFixed(2)));
                    $("#span_total_pfee").html(numberWithCommas(span_total_pfee.toFixed(2)));
                    $("#span_total_sst").html(numberWithCommas(span_total_sst.toFixed(2)));
                    $("#span_total_pfee_to_transfer").html(numberWithCommas(span_total_pfee_to_transfer.toFixed(
                        2)));
                    $("#span_total_sst_to_transfer").html(numberWithCommas(span_total_sst_to_transfer.toFixed(
                        2)));
                    $("#span_total_transferred_pfee").html(numberWithCommas(span_total_transferred_pfee.toFixed(
                        2)));
                    $("#span_total_transferred_sst").html(numberWithCommas(span_total_transferred_sst.toFixed(
                        2)));




                    // transfer_amt_hidden = parseFloat($("#transfer_amount_hidden").val());
                    console.log(span_total_amount);

                    // transfer_amount = monTotal + transfer_amt_hidden;
                    // alert($("#transfer_amount_hidden").val());
                    // $("#transfer_amount").val(transfer_amount);
                }
            });




        }

        function maxValue(id, value) {
            $("#ban_to_transfer" + id).val(value);
        }

        function reloadTable() {
            var table = $('#tbl-transfer-bill').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'not_transfer';
                        d.recv_start_date = $("#recv_start_date").val();
                        d.recv_end_date = $("#recv_end_date").val();
                        d.branch = $("#branch").val();
                        d.transaction_id = {{ $TransferFeeMain->id }};
                    },
                },
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        name: 'action'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    // {
                    //     data: 'client_name',
                    //     name: 'client_name'
                    // },
                    {
                        data: 'bill_no',
                        name: 'bill_no'
                    },
                    {
                        data: 'invoice_no_v2',
                        name: 'invoice_no_v2'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'total_amt_inv',
                        name: 'total_amt_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'collected_amt',
                        name: 'collected_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },

                    {
                        data: 'pfee_sum',
                        name: 'pfee_sum',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'bal_to_transfer_v2',
                        name: 'bal_to_transfer_v2',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'sst_to_transfer',
                        name: 'sst_to_transfer',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_pfee_amt',
                        name: 'transferred_pfee_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_sst_amt',
                        name: 'transferred_sst_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee1_inv',
                    //     name: 'pfee1_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    // {
                    //     data: 'sst_inv',
                    //     name: 'sst_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    // {
                    //     data: 'bal_to_transfer',
                    //     name: 'bal_to_transfer',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
                ]
            });

        }

        function reloadAddTable() {
            var table = $('#tbl-transfer-bill-added').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillAddList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'add';
                    },
                },
                columns: [{
                        data: 'action',
                        className: 'text-center',
                        name: 'action'
                    },
                    {
                        data: 'case_ref_no',
                        name: 'case_ref_no'
                    },
                    // {
                    //     data: 'client_name',
                    //     name: 'client_name'
                    // }, 
                    // {
                    //     data: 'bill_no',
                    //     name: 'bill_no'
                    // },
                    {
                        data: 'invoice_no',
                        name: 'invoice_no'
                    },
                    {
                        data: 'invoice_date',
                        name: 'invoice_date'
                    },
                    {
                        data: 'total_amt_inv',
                        name: 'total_amt_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'collected_amt',
                        name: 'collected_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },

                    {
                        data: 'pfee_sum',
                        name: 'pfee_sum',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    {
                        data: 'sst_inv',
                        name: 'sst_inv',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'bal_to_transfer_v2',
                        name: 'bal_to_transfer_v2',
                        class: 'text-right',
                    },
                    {
                        data: 'sst_to_transfer',
                        name: 'sst_to_transfer',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_pfee_amt',
                        name: 'transferred_pfee_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    {
                        data: 'transferred_sst_amt',
                        name: 'transferred_sst_amt',
                        class: 'text-right',
                        render: $.fn.dataTable.render.number(',', '.', 2)
                    },
                    // {
                    //     data: 'pfee1_inv',
                    //     name: 'pfee1_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    // {
                    //     data: 'pfee2_inv',
                    //     name: 'pfee2_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },

                    // {
                    //     data: 'sst_inv',
                    //     name: 'sst_inv',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    // {
                    //     data: 'bal_to_transfer',
                    //     name: 'bal_to_transfer',
                    //     class: 'text-right',
                    //     render: $.fn.dataTable.render.number(',', '.', 2)
                    // },
                    {
                        data: 'payment_receipt_date',
                        name: 'payment_receipt_date'
                    },
                ],
                drawCallback: function(settings) {

                    var api = this.api(),
                        data;

                    var intVal = function(i) {
                        return typeof i === 'string' ?
                            i.replace(/[\$,]/g, '') * 1 :
                            typeof i === 'number' ?
                            i : 0;
                    };

                    var monTotal = api
                        .column(10)
                        .data()
                        .reduce(function(a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);



                    transfer_amt_hidden = parseFloat($("#transfer_amount_hidden").val());
                    console.log(transfer_amt_hidden);

                    transfer_amount = monTotal + transfer_amt_hidden;
                    // alert($("#transfer_amount_hidden").val());
                    $("#transfer_amount").val(transfer_amount);
                }
            });

        }



        function getLastDay() {

        }
    </script>
@endsection
