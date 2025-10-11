@extends('dashboard.base')
@section('content')
<div class="container-fluid">
    <div class="fade-in">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>Create New Transfer Prof Fee Record</h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <a class="btn btn-lg btn-info" href="{{ route('transfer-fee-list') }}">
                                    <i class="cil-arrow-left"></i> Back to list
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (Session::has('message'))
                            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                        @endif
                        <form id="form_transfer_fee" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="transfer_date">Transfer Date</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="transfer_date" id="transfer_date" type="date" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="transfer_amount">Transfer Total Amount</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="transfer_amount" id="transfer_amount" value="0.00" type="number" readonly />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="transfer_from">Transfer From</label>
                                        <div class="col-md-8">
                                            <select class="form-control" name="transfer_from" id="transfer_from">
                                                <option value="0">-- Select bank account --</option>
                                                @foreach ($OfficeBankAccount as $bankAccount)
                                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }} ({{ $bankAccount->account_no }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="transfer_to">Transfer To</label>
                                        <div class="col-md-8">
                                            <select class="form-control" name="transfer_to" id="transfer_to">
                                                <option value="0">-- Select bank account --</option>
                                                @foreach ($OfficeBankAccount as $bankAccount)
                                                    <option value="{{ $bankAccount->id }}">{{ $bankAccount->name }} ({{ $bankAccount->account_no }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="trx_id">Transaction ID</label>
                                        <div class="col-md-8">
                                            <input class="form-control" name="trx_id" id="trx_id" type="text" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-md-4 col-form-label" for="purpose">Purpose</label>
                                        <div class="col-md-8">
                                            <textarea class="form-control" id="purpose" name="purpose" rows="3"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-right">
                                    <a id="btnSave" class="btn btn-success" href="javascript:void(0)" onclick="saveTransferFee();">
                                        <i class="fa cil-save"></i> Save
                                    </a>
                                </div>
                            </div>
                        </form>
                        <hr />
                        <div class="card mt-4">
                            <div class="card-header bg-light">
                                <label for="invoice_search" class="mb-0"><strong>Add Invoice(s) to Transfer</strong></label>
                                <select id="invoice_search" class="form-control mt-2" multiple="multiple" style="width:100%"></select>
                            </div>
                            <div class="card-body">
                                <h6 class="mb-3"><strong>Selected Invoices for Transfer</strong></h6>
                                <table id="tbl-transfer-bill-added" class="table table-bordered table-striped" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
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
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                                <div class="mt-2">
                                    <a class="btn btn-danger" href="javascript:void(0)" onclick="deleteAll();">
                                        <i class="fa cil-x"></i> Delete all
                                    </a>
                                    <a class="btn btn-danger" href="javascript:void(0)" onclick="deleteSelected();">
                                        <i class="fa cil-x"></i> Delete selected
                                    </a>
                                </div>
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
<script>
// Placeholder: Initialize Select2, handle invoice search, table population, inline editing, validation, and save logic.
</script>
@endsection 