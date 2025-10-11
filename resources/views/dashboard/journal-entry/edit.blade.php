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
                                    
                                    <h4>Journal Entry Details - {{ $JournalEntryMain->journal_no }}
                                        @if ($JournalEntryMain->is_recon == 1)
                                        <i class="fa fa-lock"></i>
                                        @endif
                                    </h4>
                                </div>

                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                    <a class="btn btn-lg btn-info  float-right" href="{{ route('journal.list') }}">
                                        <i class="cil-arrow-left"> </i>Back to list
                                      </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (Session::has('message'))
                                <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
                            @endif

                            <form id="form_journal_entry" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <input class="form-control" type="hidden" name="journal_id"
                                        value="{{ $JournalEntryMain->id }}" id="journal_id">
                                    <input class="form-control" type="hidden" name="selected_details_id"
                                        value="0" id="selected_details_id">

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="date">Date</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="date" id="date" type="date"
                                                    value="{{ $JournalEntryMain->date }}" />
                                            </div>
                                        </div>
                                    </div>


                                    {{-- <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">

                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="name">Ref No</label>
                                            <div class="col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text" name="ref_no" id="ref_no"
                                                        value="{{ $JournalEntryMain->case_ref_no }}" readonly>
                                                    <input class="form-control" type="hidden" name="case_id"
                                                        value="{{ $JournalEntryMain->case_id }}" id="case_id">
                                                    <a class="btn btn-primary " href="javascript:void(0)"
                                                        data-backdrop="static" data-keyboard="false" data-toggle="modal"
                                                        data-target="#myModalInvoice" id="button-addon2">Search</a>
                                                </div>
                                            </div>
                                        </div>



                                    </div> --}}

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="name">Name</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="name" id="name" type="text"
                                                    value="{{ $JournalEntryMain->name }}" />
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Transaction ID</label>
                                            <div class="col-md-8">
                                                <input class="form-control" name="trx_id" id="trx_id" type="text"
                                                    value="{{ $JournalEntryMain->transaction_id }}" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="hf-email">Description</label>
                                            <div class="col-md-8">
                                                <textarea class="form-control" id="desc" name="desc" row="3">{{ $JournalEntryMain->remarks }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                        <div class="form-group row">
                                            <label class="col-md-4 col-form-label" for="transfer_from">Bank</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="bank_account" id="bank_account">
                                                    <option value="0">-- Select bank account --</option>
                                                    @foreach ($OfficeBankAccount as $bankAccount)
                                                        <option value="{{ $bankAccount->id }}"
                                                            @if ($JournalEntryMain->bank_id == $bankAccount->id) selected @endif>
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
                                            <label class="col-md-4 col-form-label" for="transfer_from">Branch</label>
                                            <div class="col-md-8">
                                                <select class="form-control" name="branch_id" id="branch_id">
                                                    <option value="0">-- Select branch --</option>
                                                    @foreach ($Branchs as $branch)
                                                        <option value="{{ $branch->id }}" 
                                                            @if ($JournalEntryMain->branch_id == $branch->id) selected @endif>{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </form>

                            
                            <div class="row">
                                <div class="col-12 ">

                                    <div class="btn-group float-right">
                                        <button type="button" class="btn btn-info btn-flat">Action</button>
                                        <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                            data-toggle="dropdown">
                                            <span class="caret"></span>
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>
                                        <div class="dropdown-menu" style="padding:0">
                                            @if ($JournalEntryMain->is_recon == 0)
                                                <a class="dropdown-item btn-success" href="javascript:void(0)"
                                                    style="color:white;margin:0" onclick="SaveJournalEntry();"><i
                                                        style="margin-right: 10px;" class="fa fa-save"></i>Update</a>
                                                <a class="dropdown-item btn-warning" href="javascript:void(0)"
                                                    style="color:white;margin:0" onclick="refreshToOriginal();"><i
                                                        style="margin-right: 10px;" class="fa fa-repeat"></i>Reset to
                                                    orignal</a>
                                                <a class="dropdown-item btn-info" href="javascript:void(0)"
                                                    style="color:white;margin:0" onclick="lockJournal();"><i
                                                        style="margin-right: 10px;" class="fa fa-lock"></i>Recon Journal</a>
                                                <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                                    style="color:white;margin:0" onclick="deleteJournal();"><i
                                                        style="margin-right: 10px;" class="fa fa-close"></i>Delete this
                                                    journal</a>
                                            @elseif ($JournalEntryMain->is_recon == 1)
                                                {{-- <a class="dropdown-item btn-info" href="javascript:void(0)"
                                                    style="color:white;margin:0" onclick="unlockJournal();"><i
                                                        style="margin-right: 10px;" class="fa fa-unlock"></i>Unlock
                                                    Journal</a> --}}
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            

                            

                            
                            <div class="col-12 ">
                                @if ($JournalEntryMain->is_recon == 1)
                                <span style="color:red">This journal entry locked, edit/edit are disabled </span>
                                @endif
                            </div>

                            <div class="col-12 ">
                                <hr />
                            </div>

                            <div class="row">
                                <div class="col-12 " style="margin-bottom:20px;">
                                    <a class="btn btn-danger " href="javascript:void(0)"
                                        onclick="addNewRow('','','','','');">
                                        <i class="fa cil-plus"> </i>Add new row
                                    </a>

                                </div>

                                <div class="col-12 ">

                                    <table class="table table-striped table-bordered datatable">

                                        <tbody id="tbl-journal-entry" >
                                            <tr style="background-color:grey;color:white">
                                                {{-- <td>No</td> --}}
                                                <td width="15%">Account Code</td>
                                                <td width="20%">Description</td>
                                                <td width="30%">Description</td>
                                                <td width="5%">SST</td>
                                                <td width="10%">Debit</td>
                                                <td width="10%">Credit</td>
                                                <td width="10%">Debit (SST)</td>
                                                <td width="10%">Credit (SST)</td>
                                                <td width="5%">Action</td>
                                            </tr>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>Total</td>
                                                <td style="text-align:right" class="quotation-total-colspan"
                                                    colspan="3">
                                                </td>
                                                <td class="text-right"><span style="font-size: 10px !important" id="total_debit">0.00</span></td>
                                                <td class="text-right"><span style="font-size: 10px !important" id="total_credit">0.00</span></td>
                                                <td class="text-right"><span style="font-size: 10px !important" id="total_debit_sst">0.00</span></td>
                                                <td class="text-right"><span style="font-size: 10px !important" id="total_credit_sst">0.00</span></td>


                                            </tr>
                                        </tfoot>

                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


    <div id="myModalInvoice" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="form_edit_quotation">
                        <div class="form-group row ">

                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                <div class="col">
                                    <label>Search Case</label>
                                    <input type="text" id="search_case" name="search_case"
                                        class="form-control search_referral" placeholder="Search Case"
                                        autocomplete="off" />
                                </div>
                            </div>


                            <div class="col-12" style="margin-top:10px">
                                <table class="table  table-bordered datatable">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Case Ref No</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl-case">
                                        @if (count($loan_case))
                                            @foreach ($loan_case as $index => $case)
                                                <tr id="case_{{ $case->id }}" style="display:none">
                                                    <td>{{ $case->case_ref_no }}</td>
                                                    <td style="display:none">{{ $case->id }}</td>
                                                    <td class="text-center">
                                                        <a href="javascript:void(0)"
                                                            onclick="selectedCase('{{ $case->id }}');"
                                                            class="btn btn-primary shadow btn-xs sharp mr-1"
                                                            data-toggle="tooltip" data-placement="top"
                                                            title="case">Select</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center" colspan="3">No data</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('javascript')
    <!-- <script src="{{ asset('js/paperfish/jquery-2.2.4.min.js') }}"></script> -->
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $('#search_case').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_case");
            filter = input.value.toUpperCase();
            $("#search_client").val();

            $("#tbl-case tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(0)").text().trim();

                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            })

            if (filter == "") {
                $("#tbl-case tr").each(function() {
                    var self = $(this);
                    $(this).hide();
                })
            }
        });

        function refreshToOriginal() {
            Swal.fire({
                icon: 'warning',
                text: 'Reset to original data?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            })
        }

        function lockJournal() {
            Swal.fire({
                icon: 'warning',
                text: 'Recon this journal entry?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/lockJournal/{{ $JournalEntryMain->id }}',
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {

                                Swal.fire(
                                    'Success!', result.message,
                                    'success'
                                )

                                location.reload();

                            } else {
                                Swal.fire('notice!', result.message, 'warning');
                            }
                        }
                    });


                }
            })
        }

        function unlockJournal() {
            Swal.fire({
                icon: 'warning',
                text: 'Unlock this journal entry?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/unlockJournal/{{ $JournalEntryMain->id }}',
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {

                                Swal.fire(
                                    'Success!', result.message,
                                    'success'
                                )

                                location.reload();

                            } else {
                                Swal.fire('notice!', result.message, 'warning');
                            }
                        }
                    });


                }
            })
        }

        function deleteJournal() {
            Swal.fire({
                icon: 'error',
                text: 'Delete this journal entry? (This action will remove this entry from bank recon and ledger)',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteJournal/{{ $JournalEntryMain->id }}',
                        data: null,
                        processData: false,
                        contentType: false,
                        success: function(result) {
                            console.log(result);
                            if (result.status == 1) {
                                Swal.fire('Success!', result.message, 'success');
                                window.location.href = '/journal-entry-list';
                            } else {
                                Swal.fire('notice!', result.message, 'warning');
                            }
                        }
                    });
                }
            })
        }


        function selectedCase(id) {
            $("#tbl-case tr#case_" + id).each(function() {
                var self = $(this);
                var case_ref_no = self.find("td:eq(0)").text().trim();
                var case_id = self.find("td:eq(1)").text().trim();

                var form = $("#form_journal_entry");

                form.find('[name=ref_no]').val(case_ref_no);
                form.find('[name=case_id]').val(case_id);
                // form.find('[name=client]').val(client);
                // form.find('[name=client_id]').val(client_id);

                var selected_id = $("#selected_details_id").val();
                console.log(selected_id);

                $("#case_id_" + selected_id).val(case_id);
                $("#ref_no_" + selected_id).val(case_ref_no);

                $('#btnClose').click();
                // $(".modal-backdrop").remove();

            })
        }

        var blockID = 1;
        var AccountCodeID = 1;

        function addNewRow($desc, $account_code_id, $amount, $type, $sst) {

            $html_desc = '';
            $html_account_code_id = '';
            $html_debit = '';
            $html_credit = '';
            $html_sst = '';
            $html_debit_sst = '0';
            $html_credit_sst = '0';

            AccountCodeID = $account_code_id;

            if ($desc != '') {
                $html_desc = 'value="' + $desc + '"';
            }

            if ($account_code_id != '') {
                $html_account_code_id = 'value="' + $account_code_id + '"';
            }

            if ($sst != 0) {
                $html_sst = 'checked';
            }


            if ($type != '') {
                if ($type == 'D') {
                    $html_debit = 'value="' + $amount + '"';
                    $html_credit = 'value="0.00"';
                    $html_debit_sst = parseFloat($amount) + parseFloat($sst);
                    $html_credit_sst = '0.00';
                } else {
                    $html_credit = 'value="' + $amount + '"';
                    $html_debit = 'value="0.00"';
                    $html_debit_sst = '0.00';
                    $html_credit_sst = parseFloat($amount) + parseFloat($sst);
                }
            }

            $html_account_code_1 = '';
            $html_account_code_2 = '';
            $html_account_code_3 = '';
            $html_account_code_4 = '';
            $html_account_code_5 = '';


         



            var htmlBlock = `
            <tr id="row` + blockID + `">
                <td>
                    <input class="form-control" type="hidden" name="hiddenRowID" value="` + blockID + `" >
                    <select class="form-control" name="accountCode" id="accountCode` + blockID + `" style="font-size: 10px !important">
                        <option value="0" >-- Select Account Code --</option>
                        @foreach ($AccountCode as $code)
                            <option value="{{ $code->id }}" >
                                {{ $code->code }}: {{ $code->name }}
                            </option>
                        @endforeach
                        
                    </select>
                </td>
                <td>
                    <div class="input-group">
                                    <input class="form-control" type="text" name="ref_no" id="ref_no_` + blockID + `"  readonly="" style="font-size: 10px !important">
                                    <input class="form-control" type="hidden" name="case_id"  id="case_id_` + blockID + `">
                                    <a class="btn btn-primary btn-xs" href="javascript:void(0)" data-backdrop="static" onclick="openJournalModal('` + blockID + `')" data-keyboard="false" data-toggle="modal" data-target="#myModalInvoice" id="button-addon2"><i class="fa fa-search"></i></a>
                                </div>
                </td>
                <td>
                    <input class="form-control " type="text" name="desc" id="desc` + blockID + `" ` + $html_desc + ` style="font-size: 10px !important">
                </td>
                <td class="text-center">
                    <div class="checkbox">
                        <input type="checkbox" name="chkSST" value="1" id="sst` + blockID +
                `" ` + $html_sst + ` onchange="sstSelected(` + blockID + `)">
                        <label for="sst` + blockID +
                `"></label>
                    </div>
                </td>
                <td>
                    <input style="font-size: 10px !important" class="form-control number-decimal text-right" type="number"    onchange="convertDecimal('debit` +
                blockID + `',` + blockID + `,'spanDebit` + blockID + `')" name="debit" id="debit` + blockID +
                `" ` + $html_debit +
                ` value="0.00">
                </td>
                <td>
                    <input style="font-size: 10px !important" class="form-control number-decimal text-right" type="number"    onchange="convertDecimal('credit` +
                blockID + `',` + blockID + `,'spanCrebit` +
                blockID + `')"  name="credit" id="credit` + blockID + `" ` + $html_credit + `  value="0.00">
                </td>
                <td class="text-right">
                     <span  style="font-size: 10px !important"  id="spanDebit` + blockID + `">` + $html_debit_sst + `</span>
                </td>
                <td class="text-right">
                     <span style="font-size: 10px !important" id="spanCrebit` + blockID + `">` + $html_credit_sst + `</span>
                </td>
                <td>
                    <a href="javascript:void(0)" onclick="deleteRow(` + blockID + `)" class="btn btn-danger shadow sharp " data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>
                </td>

            </tr>
            `;

            blockID += 1;


            $("#tbl-journal-entry").append(htmlBlock);
        }

        var transfer_fee_add_list = [];

        $(function() {
            @if (count($JournalEntryDetails))
                @foreach ($JournalEntryDetails as $index => $details)
                
                    // addNewRow('{{ $details->remarks }}', '{{ $details->account_code_id }}',
                    //     {{ $details->amount }}, '{{ $details->transaction_type }}',
                    //     {{ $details->sst_amount }});


                    $html_desc = '';
                    // $html_account_code_id = '';
                    $html_debit = '';
                    $html_credit = '';
                    $html_sst = '';
                    $html_debit_sst = '0';
                    $html_credit_sst = '0';


                    
                    $html_desc = 'value="{{ $details->remarks }}"';

                    // if ($account_code_id != '') {
                    //     $html_account_code_id = 'value="' + $account_code_id + '"';
                    // }

                    // if ($sst != 0) {
                    //     $html_sst = 'checked';
                    // }


                    // if ($type != '') {
                    //     if ($type == 'D') {
                    //         $html_debit = 'value="' + $amount + '"';
                    //         $html_credit = 'value="0.00"';
                    //         $html_debit_sst = parseFloat($amount) + parseFloat($sst);
                    //         $html_credit_sst = '0.00';
                    //     } else {
                    //         $html_credit = 'value="' + $amount + '"';
                    //         $html_debit = 'value="0.00"';
                    //         $html_debit_sst = '0.00';
                    //         $html_credit_sst = parseFloat($amount) + parseFloat($sst);
                    //     }
                    // }

                    $html_account_code_1 = '';
                    $html_account_code_2 = '';
                    $html_account_code_3 = '';
                    $html_account_code_4 = '';
                    $html_account_code_5 = '';


                    



                    var htmlBlock = `
                    <tr id="row` + blockID + `" >
                        <td>
                            <input class="form-control" type="hidden" name="hiddenRowID" value="` + blockID + `">
                            <select class="form-control" name="accountCode" id="accountCode` + blockID + `" style="font-size: 10px !important">
                                <option value="0" >-- Select Account Code --</option>
                                @foreach ($AccountCode as $code)
                                    <option value="{{ $code->id }}" @if($details->account_code_id == $code->id) selected @endif>
                                        {{ $code->code }}: {{ $code->name }}
                                    </option>
                                @endforeach
                                
                            </select>
                            
                        </td>

                        
                        <td>
                            <div class="input-group">
                                    <input class="form-control" type="text" name="ref_no" id="ref_no_` + blockID + `" value="{{$details->case_ref_no}}" readonly="" style="font-size: 10px !important">
                                    <input class="form-control" type="hidden" name="case_id" value="{{$details->case_id}}" id="case_id_` + blockID + `">
                                    <a class="btn btn-primary btn-xs" href="javascript:void(0)" data-backdrop="static" onclick="openJournalModal('` + blockID + `')" data-keyboard="false" data-toggle="modal" data-target="#myModalInvoice" id="button-addon2"><i class="fa fa-search"></i></a>
                                </div>
                        </td>
                        
                        <td>
                            <input style="font-size: 10px !important" class="form-control " type="text" name="desc" id="desc` + blockID + `" ` + $html_desc + `>
                        </td>
                        <td class="text-center">
                            <div class="checkbox">
                                <input type="checkbox" name="chkSST" value="1" id="sst` + blockID +
                        `" @if($details->sst_amount > 0) checked @endif onchange="sstSelected(` + blockID + `)">
                                <label for="sst` + blockID +
                        `"></label>
                            </div>
                        </td>
                        <td>
                            <input style="font-size: 10px !important" class="form-control number-decimal text-right" type="number"    onchange="convertDecimal('debit` +
                        blockID + `',` + blockID + `,'spanDebit` + blockID + `')" name="debit" id="debit` + blockID +
                        `"  @if($details->transaction_type == 'D') value="{{$details->amount}}" @else value="0" @endif>
                        </td>
                        <td>
                            <input style="font-size: 10px !important" class="form-control number-decimal text-right" type="number"    onchange="convertDecimal('credit` +
                        blockID + `',` + blockID + `,'spanCrebit` +
                        blockID + `')"  name="credit" id="credit` + blockID + `" @if($details->transaction_type == 'C') value="{{$details->amount}}" @else value="0" @endif>
                        </td>
                        <td class="text-right">
                            <span style="font-size: 10px !important"   id="spanDebit` + blockID + `">@if($details->transaction_type == 'D') {{number_format($details->amount + $details->sst_amount, 2, '.', ',') }} @else 0 @endif</span>
                        </td>
                        <td class="text-right">
                            <span style="font-size: 10px !important" id="spanCrebit` + blockID + `">@if($details->transaction_type == 'C') {{number_format($details->amount + $details->sst_amount, 2, '.', ',') }} @else 0 @endif</span>
                        </td>
                        <td>
                            <a href="javascript:void(0)" onclick="deleteRow(` + blockID + `)" class="btn btn-danger shadow sharp " data-toggle="tooltip" data-placement="top" title="Delete"><i class="cil-x"></i></a>
                        </td>

                    </tr>
                    `;

                    blockID += 1;


                    $("#tbl-journal-entry").append(htmlBlock);

                @endforeach
            @endif

            sumTotal();

        });

        function openJournalModal(case_id)
        {
            $("#selected_details_id").val(case_id);

        }

        function sumTotal() {

            $totalDedit = 0;
            $totalCredit = 0;
            $totalDeditSST = 0;
            $totalCreditSST = 0;

            $.each($("input[name='hiddenRowID']"), function() {

                var itemID = $(this).val();
                $sst = 1;



                if ($("#sst" + itemID).prop('checked') == true) {
                    $sst = 1.06;
                }

                $totalDedit += parseFloat($('#debit' + itemID).val());
                $totalCredit += parseFloat($('#credit' + itemID).val());

                console.log($totalDeditSST);
                console.log($totalCredit);
                console.log($totalCredit);


                $totalDeditSST += (parseFloat($('#debit' + itemID).val()) * $sst)
                $totalCreditSST += (parseFloat($('#credit' + itemID).val()) * $sst)

            });

            $('#total_debit').html(parseFloat($totalDedit).toFixed(2));
            $('#total_credit').html(parseFloat($totalCredit).toFixed(2));
            $('#total_debit_sst').html(parseFloat($totalDeditSST).toFixed(2));
            $('#total_credit_sst').html(parseFloat($totalCreditSST).toFixed(2));
        }

        function deleteRow(id) {
            $("#row" + id).remove();
            sumTotal();
        }

        function convertDecimal(object, id, spanObject) {
            console.log(spanObject);
            var Value = $('#' + object).val();
            $sst = 1;


            if ($("#sst" + id).prop('checked') == true) {
                $sst = 1.06;
            }


            if (Value == "") {
                Value = 0;
            }

            if (isNaN(Value)) {
                Value = 0;
            }

            $('#' + object).val(parseFloat(Value).toFixed(2));
            $('#' + spanObject).html(parseFloat(Value * $sst).toFixed(2));

            sumTotal();
        }

        function sstSelected(id) {
            if ($("#sst" + id).prop('checked') == true) {
                $sst = 1.06;
            } else {
                $sst = 1;
            }

            $debit = $('#debit' + id).val();
            $credit = $('#credit' + id).val();
            $('#spanDebit' + id).html(parseFloat($debit * $sst).toFixed(2));
            $('#spanCrebit' + id).html(parseFloat($credit * $sst).toFixed(2));

            sumTotal();
        }

        function SaveJournalEntry() {

            var entries_list = [];
            var entries = {};

            var errorCount = 0;

            if ($("#name").val() == '' || $("#desc").val() == '' || $("#trx_id").val() == '' || $("#date").val() == '' || $(
                    "#case_id").val() == '') {

                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure all mandatory fields fill',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            $count = 0;
            $errorCount = 0;

            $.each($("input[name='hiddenRowID']"), function() {

                $count += 1;
                $sst_amount = 0;

                var itemID = $(this).val();
                $sst = 1;

                if ($("#desc" + itemID).val() == '') {
                    $("#desc" + itemID).addClass("highLight_input_red");
                    errorCount += 1;
                }

                if ($("#accountCode" + itemID).val() == 0) {
                    $("#accountCode" + itemID).addClass("highLight_input_red");
                    errorCount += 1;
                }

                if ($("#debit" + itemID).val() == 0 && $("#credit" + itemID).val() == 0) {
                    $("#debit" + itemID).addClass("highLight_input_red");
                    $("#credit" + itemID).addClass("highLight_input_red");
                    errorCount += 1;
                }

                if ($("#sst" + itemID).prop('checked') == true) {

                    if ($("#debit" + itemID).val() > 0) {
                        $sst_amount = parseFloat($("#debit" + itemID).val()) * 0.06;
                    }

                    if ($("#credit" + itemID).val() > 0) {
                        $sst_amount = parseFloat($("#credit" + itemID).val()) * 0.06;
                    }

                }

                entries = {
                    account_code_id: $("#accountCode" + itemID).val(),
                    desc: $("#desc" + itemID).val(),
                    debit: $("#debit" + itemID).val(),
                    credit: $("#credit" + itemID).val(),
                    case_id: $("#case_id_" + itemID).val(),
                    sst_amount: $sst_amount,
                };

                entries_list.push(entries);


            });

            if ($count <= 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'No Entries',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }

            if (errorCount > 0) {
                Swal.fire({
                    icon: 'warning',
                    text: 'Please make sure fill in account code, description and amount for the entries',
                    confirmButtonText: `Yes`,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                });
                return;
            }


            var form_data = new FormData();
            form_data.append("entries_list", JSON.stringify(entries_list));
            form_data.append("name", $("#name").val());
            form_data.append("desc", $("#desc").val());
            form_data.append("trx_id", $("#trx_id").val());
            form_data.append("date", $("#date").val());
            form_data.append("case_id", $("#case_id").val());
            form_data.append("bank_account", $("#bank_account").val());
            form_data.append("branch_id", $("#branch_id").val());

            $.ajax({
                type: 'POST',
                url: '/updateJournalEntry/{{ $JournalEntryMain->id }}',
                data: form_data,
                processData: false,
                contentType: false,
                success: function(result) {
                    console.log(result);
                    if (result.status == 1) {

                        Swal.fire(
                            'Success!', result.message,
                            'success'
                        )

                        location.reload();

                    } else {
                        Swal.fire('notice!', result.message, 'warning');
                    }
                }
            });
        }

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
                        url: '/deleteTransferFee/{{ $JournalEntryMain->id }}',
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

            $.ajax({
                type: 'POST',
                url: '/updateTranferFee/{{ $JournalEntryMain->id }}',
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

        function reloadTransferredTable() {
            var table = $('#tbl-transferred-fee').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                ajax: {
                    url: "{{ route('transferFeeBillList.list') }}",
                    data: function(d) {
                        d.transfer_list = JSON.stringify(transfer_fee_add_list);
                        d.type = 'transferred';
                        d.transaction_id = {{ $JournalEntryMain->id }};
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    }, {
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
                ]
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
                        d.transaction_id = {{ $JournalEntryMain->id }};
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
                        data: 'invoice_no',
                        name: 'invoice_no'
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
                        data: 'pfee1_inv',
                        name: 'pfee1_inv',
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

                    console.log(monTotal);

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
