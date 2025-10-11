@extends('dashboard.base')

@section('content')


    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">


                <div class="col-sm-12 col-md-10 col-lg-8 col-xl-12 ">
                    <h3>Claims Request Details</h3>
                    <div class="card ">

                        <div class="card-header"> 
                            <div class="row">
                                <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                    <h4> <i class="cil-balance-scale"></i> Case Summary</h4>
                                </div>

                            </div>
                        </div>
                        <div class="card-body ">
                            <div class="row ">

                                <input type="hidden" id="input_selected_bill" />
                                <table class="table mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="fw-medium"><b>Ref No</b></td>
                                            <td> <a target="_blank" href="/case/{{ $Claims->case_id }}" data-toggle="tooltip"
                                                    data-placement="top"
                                                    title="Sales/Lawyer/Bank/Running No/Client/Clerk">{{ $Claims->case_ref_no }}
                                                    >></a></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium"><b>Bonus Type</b></td>
                                            <td class="fw-medium">
                                                {{ $Claims->type_name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-medium"><b>Bonus Status</b></td>
                                            <td>
                                                @if ($Claims->status == 2)
                                                    <span class="badge badge-warning">Reviewing</span>
                                                @elseif($Claims->status == 1)
                                                    <span class="badge badge-success">Approved</span>
                                                @elseif($Claims->status == 3)
                                                    <span class="badge badge-danger">Rejected</span>
                                                @endif
                                            </td>
                                        </tr>



                                    </tbody>
                                </table>


                            </div>

                            <div class="row pt-3 border-top border-top-dashed mt-4">
                                <div class="col-lg-3 col-sm-6">
                                    <div>
                                        <p class=""><b>Start Date</b></p>
                                        <i class="fa fa-calendar "></i> {{ date('d-m-Y', strtotime($case->created_at)) }}
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div>
                                        <p class=""><b>Completion Date</b></p>
                                        <i class="fa fa-calendar "></i> -
                                    </div>
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <div>
                                        <p class=""><b>Status</b></p>
                                        @if ($case->status == 2)
                                            <span class="badge badge-info">Open</span>
                                        @elseif($case->status == 0)
                                            <span class="badge badge-success">Closed</span>
                                        @elseif($case->status == 1)
                                            <span class="badge bg-purple">In progress</span>
                                        @elseif($case->status == 2)
                                            <span class="badge badge-danger">Overdue</span>
                                        @elseif($case->status == 3)
                                            <span class="badge badge-warning">KIV</span>
                                        @elseif($case->status == 4)
                                            <span class="badge badge-warning">Pending Close</span>
                                        @elseif($case->status == 99)
                                            <span class="badge badge-danger">Aborted</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-3">
                    <div class="card">
                        <div class="card-header align-items-center d-flex border-bottom-dashed">
                            <h4 class="card-title mb-0 flex-grow-1">Staff</h4>
                        </div>

                        <div class="card-body">

                            <div class="vstack gap-2">
                                <div class="border rounded border-dashed p-2">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 me-3">
                                            <div class="avatar-sm">
                                                <div class="avatar-title bg-light text-secondary rounded fs-24">
                                                    <i class="ri-folder-zip-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            {{-- <h5 class="fs-13 mb-1"><a id="bonus_lawyer" href="javascript:void(0)"
                                                    class="text-body text-truncate d-block">0.00</a></h5> --}}
                                            @php
                                            @endphp
                                            <input type="number" id="input_claims"
                                                value="{{ number_format($Claims->amount, 2, '.', '') }}"  />
                                            <div>{{ $Claims->user_name }} </div>
                                            <span class="text-success">[Lawyer]</span>
                                        </div>
                                        <div class="flex-shrink-0 ms-2">
                                            <input type="hidden" id="input_claims_per"
                                                value="{{ $Claims->percentage }}" />
                                            <div id="bonus_lawyer_per" class="d-flex gap-1">
                                                {{ $Claims->percentage }}%

                                            </div>
                                        </div>
                                    </div>
                                </div>


                                @if ($Claims->status != 1)
                                    <div class="row">
                                        {{-- <div class="col-6">
                                            <div class="mt-2 text-left">
                                                <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                                    data-toggle="modal" data-target="#modalRejectReason"
                                                    class="btn btn-danger  sharp " data-toggle="tooltip"
                                                    data-placement="top" title="View">Reject Claim</a>
                                            </div>
                                        </div> --}}
                                        <div class="col-12">
                                            <div class="mt-2 text-right">
                                                <button onclick="approveClaim()" type="button"
                                                    class="btn btn-success">Approve Claim</button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mt-2 text-right">
                                            <button onclick="editClaim()" type="button"
                                                class="btn btn-success">Edit Claim</button>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                </div>



                {{-- @if (count($LoanCaseBillMain))
                @foreach ($LoanCaseBillMain as $index => $row) --}}
                <div class="col-md-9">

                    @if (count($LoanCaseBillMain))
                        @foreach ($LoanCaseBillMain as $index => $row)
                            <div class="card" style="max-height:600px;overflow:scroll">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-6">
                                            <h4>[{{ $row->bill_no }}] {{ $row->name }} <span
                                                    @if ($Claims->selected_bill_id != $row->id) style="display: none" @endif
                                                    class="span_selected span_selected_{{ $row->id }} badge badge-success">
                                                    Selected</span> </h4>
                                        </div>
                                        <div class="col-6">

                                            <div class="form-check float-right">
                                                <input class="form-check-input" type="radio"
                                                    value="{{ $row->id }}"
                                                    onclick="updateSelectedBill({{ $row->id }});"
                                                    name="rdSelectBill" @if ($Claims->status == 1) disabled @endif
                                                    id="rdBill_{{ $row->id }}"
                                                    @if ($Claims->selected_bill_id == $row->id) checked @endif>
                                                <label class="form-check-label" for="rdBill_{{ $row->id }}">Select
                                                    This bill for bonus</label>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="card-body ">
                                    <br>
                                    <table id="table_notes_month"
                                        class="table table-responsive-sm table-striped table-hover  mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center">Bill collect Amt</th>
                                                <th class="text-center">Collected Amt</th>
                                                <th class="text-center">Uncollected</th>
                                                <th class="text-center">Pfee</th>
                                                <th class="text-center">Referral</th>
                                                <th class="text-center">Marketing</th>
                                                <th class="text-center">Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $uncollected = $row->total_amt - $row->collected_amt;
                                                // $final_value = $row->pfee1_inv + $row->pfee2_inv - $row->referral_a1 - $row->referral_a2 - $row->referral_a3 - $row->referral_a4 - $row->marketing - $uncollected;
                                                // $final_value = $row->pfee1 - $uncollected - $row->referral_a1 - $row->referral_a2 - $row->referral_a3 - $row->referral_a4 - $row->marketing ;
                                                $final_value = ($row->pfee1 + $row->pfee2) - $row->uncollected - $row->referral_a1 - $row->referral_a2 - $row->referral_a3 - $row->referral_a4 - $row->marketing;
                                            @endphp

                                            <tr>
                                                {{-- <td class="text-center">{{ $index + 1 }}</td> --}}
                                                {{-- <td>{{ $row->name }} <br />[{{ $row->bill_no }}]<br />
                                                {{ date('d-m-Y h:i A', strtotime($row->created_at)) }}
                                            </td> --}}
                                                <td class="text-center">
                                                    {{ number_format($row->total_amt, 2, '.', ',') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($row->collected_amt, 2, '.', ',') }}
                                                </td>
                                                <td class="text-center">
                                                    {{ number_format($row->uncollected, 2, '.', ',') }}
                                                </td>
                                                <td class="text-left">
                                                    <b>Pfee 1:</b>
                                                    {{ number_format($row->pfee1, 2, '.', ',') }} <br />
                                                    <b>Pfee 2:</b>
                                                    {{ number_format($row->pfee2, 2, '.', ',') }} <br />
                                                </td>
                                                <td class="text-left">
                                                    @if ($row->referral_a1_id != 0 && $row->referral_a1_id != '')
                                                        <b>(R1) {{ $row->referral_a1_id }}</b>
                                                    @else
                                                        <b>R1</b>
                                                    @endif
                                                    : RM{{ number_format($row->referral_a1, 2, '.', ',') }}<br />

                                                    @if ($row->referral_a2_id != 0 && $row->referral_a2_id != '')
                                                        <b>(R2) {{ $row->referral_a2_id }}</b>
                                                    @else
                                                        <b>R2</b>
                                                    @endif
                                                    : RM{{ number_format($row->referral_a2, 2, '.', ',') }}<br />

                                                    @if ($row->referral_a3_id != 0 && $row->referral_a3_id != '')
                                                        <b>(R3) {{ $row->referral_a3_id }}</b>
                                                    @else
                                                        <b>R3</b>
                                                    @endif
                                                    : RM{{ number_format($row->referral_a3, 2, '.', ',') }}<br />

                                                    @if ($row->referral_a4_id != 0 && $row->referral_a4_id != '')
                                                        <b>(R4) {{ $row->referral_a4_id }}</b>
                                                    @else
                                                        <b>R4</b>
                                                    @endif
                                                    : RM{{ number_format($row->referral_a4, 2, '.', ',') }}<br />

                                                </td>
                                                <td class="text-left">
                                                    {{ $case->sales_name }}</b>: RM
                                                    {{ number_format($row->marketing, 2, '.', ',') }}<br />
                                                </td>
                                                <td class="text-center">
                                                    {{ date('d-m-Y h:i A', strtotime($row->created_at)) }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="7">
                                                    {{-- P1 - discount(Uncollected) - referral (A1 + A2 + A3 + A4 + marketing) --}}
                                                    (P1 + P2) - discount(Uncollected) - referral (A1 + A2 + A3 + A4 + marketing)
                                                    <br />
                                                    ({{ number_format($row->pfee1, 2, '.', ',') }} + {{ number_format($row->pfee2, 2, '.', ',') }}) -
                                                    ({{ number_format($row->uncollected, 2, '.', ',') }})
                                                    -
                                                    ({{ number_format($row->referral_a1, 2, '.', ',') }} +
                                                    {{ number_format($row->referral_a2, 2, '.', ',') }} +
                                                    {{ number_format($row->referral_a3, 2, '.', ',') }} +
                                                    {{ number_format($row->referral_a4, 2, '.', ',') }}+
                                                    {{ number_format($row->marketing, 2, '.', ',') }}
                                                    
                                                    ) =
                                                    {{ number_format($final_value, 2, '.', ',') }}
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>

                                </div>

                                <div class="card-body ">
                                    <br>
                                    <table id="table_notes_month"
                                        class="table table-responsive-sm table-striped table-hover  mb-0">
                                        <thead class="thead-light">
                                            <tr>
                                                <th class="text-center"> {{ $Claims->percentage }} %</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr>
                                                <td class="text-center">{{ number_format($final_value *  ($Claims->percentage/100) , 2, '.', ',') }} </td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">
                                                    <div class="form-check">
                                                    <input class="form-check-input" type="radio"
                                                    onclick="updateSelectedBonus({{ $final_value }}, {{ $Claims->percentage }} ,{{ $row->id }});"
                                                    
                                                    @if ($Claims->status == 1) disabled @endif
                                                    name="rdPercentage" id="rdPer5_{{ $row->id }}">

                                                    <label class="form-check-label"
                                                            for="rdPer5_{{ $row->id }}"></label>
                                                    </div>
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>


            </div>

        </div>
    </div>

    <div id="modalRejectReason" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="formReject">
                        <div class="col-12 ">
                            <div class="form-group row">
                                <div class="col">
                                    <label>Rejection reason</label>
                                    <textarea class="form-control" id="reject_reason" name="reject_reason" rows="3"></textarea>
                                </div>
                            </div>

                        </div>


                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success float-right" onclick="rejectBonus()">Reject
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div id="modalBonusClaimNotes" class="modal fade" role="dialog">
        <div class="modal-dialog" style="max-width:1200px;width: 900px !important">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <div class="row">
                        <div class="col-12 ">
                            <h4>Bonus Claim Rules</h4>
                        </div>


                    </div>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <p>
                                A. The Estimated Bonus will be auto adjusted after deduction of the Uncollected Sum
                            </p>
                            <p>
                                B. Bonus is payable every June & Dec subjected to following:
                            <ul class="a">
                                <li> 1. All claims made before 30th June will be paid on next 31st Dec</li>
                                <li> 2. All claims made before 31st Dec will be paid on next 30th June</li>
                                <li> 3. Upon resignation / termination of employment, closed files claim will be forfeited,
                                    active files claim will be transferred to next PIC</li>
                                <li> 4. No KIV /defective files which cause deactivation / suspension of Bank Panelship</li>
                                <li>5. No complain to Bar Council </li>
                                <li>6. No public complain in google review or any social media </li>
                            </ul>
                            </p>
                            <p>
                                C. 2% claim will be rewarded based on KPI below:
                            <ul class="a">
                                <li> 1. PV cases (S&P stamped within 10 days from the file opened & upload to LC)</li>
                                <li> 2. Represented cases (S&P stamped within 21 days from the file opened & upload to LC)
                                </li>
                                <li> 3. Loan cases (received Bank executed loan docs within 14 days from the S&P
                                    Commencement Date & upload to LC our acknowledgement of the letter from bank returning
                                    executed docs & letter from SPA solicitor confirm actual CD)</li>
                            </ul>
                            </p>
                            <p>
                                D. 3% claim will be rewarded based on KPI below
                            <ul class="a">
                                <li> 1. Files must be completed within ACTUAL Completion Date (not extended completion date
                                    free of interest)</li>
                                <li> 2. SPA cases (to upload in LC letter from SPA solicitors confirm actual CD, delivery of
                                    keys, BPP released, legal fees all paid & title registered)</li>
                                <li> 3. Loan cases (to upload in LC letter from SPA solicitors confirm actual CD, all loan
                                    sum disbursed, legal fees all paid, title registered & acknowledgement from Bank confirm
                                    received original title / agreements)</li>
                            </ul>
                            </p>
                        </div>
















                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                        data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('javascript')
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

        $('#search_client').on('input', function() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("search_client");
            filter = input.value.toUpperCase();
            $("#search_case").val();

            $("#tbl-case tr").each(function() {
                var self = $(this);
                var txtValue = self.find("td:eq(1)").text().trim();

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



        function updateSelectedBonus(value, percentage, bill_id) {
            result = value * percentage / 100;
            $("#input_claims").val(result.toFixed(2));
            // $("#input_bonus_clerk").val(result.toFixed(2));


            $("#bonus_lawyer_per").html(percentage + '%');
            // $("#bonus_clerk_per").hStml(percentage + '%');
            $("#input_claims_per").val(percentage);
            // $("#input_bonus_clerk_per").val(percentage);

            $(".span_selected").hide();
            $(".span_selected_" + bill_id).show();

            $("#input_selected_bill").val(bill_id);

            $("#rdBill_" + bill_id).prop("checked", true);

            // $("#rdBill_" + bill_id).attr('checked', 'checked');
        }

        function updateSelectedBill(bill_id) {
            $(".span_selected").hide();
            $(".span_selected_" + bill_id).show();
            $("#input_claims").val(0.00);
            $("#input_bonus_clerk").val(0.00);
            $('input[name="rdPercentage"]').prop('checked', false);
        }

        function approveClaim() {
            if ($("#input_claims").val() == 0) {
                Swal.fire('notice!', 'Claims rate not selected', 'warning');
                return;
            }

            if ($("#input_claims").val() <= 0) {
                Swal.fire('notice!', 'The claims cannot lesser than zero', 'warning');
                return;
            }

            var form_data = new FormData();

            form_data.append("claims", $("#input_claims").val());
            form_data.append("selected_bill", $("#input_selected_bill").val());

            Swal.fire({
                icon: 'warning',
                text: 'Approve this claims with RM ' + $("#input_claims").val() +
                    '?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/approveClaim/{{ $Claims->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                closeUniversalModal();
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });

                }
            })
        }

        function editClaim() {
 

            if ($("#input_claims").val() <= 0) {
                Swal.fire('notice!', 'The claims cannot lesser than zero', 'warning');
                return;
            }

            var form_data = new FormData();

            form_data.append("claims", $("#input_claims").val());
            form_data.append("selected_bill", $("#input_selected_bill").val());

            Swal.fire({
                icon: 'warning',
                text: 'Edit this claims amount?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/editClaim/{{ $Claims->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                closeUniversalModal();
                                location.reload();
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
                            }

                        }
                    });

                }
            })
        }

        function rejectBonus() {

            if ($("#reject_reason").val() == '') {
                Swal.fire('notice!', 'Please provide reason of rejection', 'warning');
                return;
            }

            var form_data = new FormData();

            form_data.append("reject_reason", $("#reject_reason").val());

            Swal.fire({
                icon: 'warning',
                text: 'Reject this bonus application?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/rejectBonus/{{ $Claims->id }}',
                        data: form_data,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            console.log(data);
                            if (data.status == 1) {

                                Swal.fire('Success!', data.message, 'success');
                                closeUniversalModal();
                                location.reload();
                                // window.location.href = '/case';
                            } else {
                                Swal.fire('notice!', data.message, 'warning');
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
                var client = self.find("td:eq(1)").text().trim();
                var case_id = self.find("td:eq(2)").text().trim();
                var client_id = self.find("td:eq(3)").text().trim();

                var form = $("#form_adjudication");

                form.find('[name=case_ref_no]').val(case_ref_no);
                form.find('[name=case_id]').val(case_id);
                form.find('[name=client]').val(client);
                form.find('[name=client_id]').val(client_id);

                $('#btnClose').click();
                // $(".modal-backdrop").remove();

            })
        }
    </script>
@endsection
