@php
    $total_trust_disb = 0;
@endphp
@if ((isset($case->collected_trust) ? $case->collected_trust : 0) <= 0)
    <span class="text-danger ">No trust receive yet</span>
@else
@endif

<table class="table table-striped table-bordered datatable">
    <tbody>
        <tr class="text-center">
            <th>Voucher No</th>
            <th>Disburse To</th>
            <th>Desc</th>
            <th>Amount (RM)</th>
            <th>Transaction ID</th>
            <th>Lawyer Approval</th>
            <th>Account Approval</th>
            <th>Payment Date</th>
            <th>Request By</th>
            <th>Request Date</th>
            <th>Action</th>
        </tr>
        @if (count($loan_case_trust_main_dis))

            @foreach ($loan_case_trust_main_dis as $index => $transaction)
                @php
                    $total_trust_disb += $transaction->total_amount;
                @endphp
                <tr>
                    <td class="text-center">
                        <a href="/voucher/{{ $transaction->id }}/edit" target="_blank"
                            class="btn btn-info">{{ $transaction->voucher_no }} >></a>
                    </td>
                    <td>{{ $transaction->payee }}</td>
                    <td>{{ $transaction->remark }}</td>
                    <td class="text-right">
                        {{ number_format($transaction->total_amount, 2, '.', ',') }}
                    </td>
                    <td>
                        {{ $transaction->transaction_id }}
                    </td>

                    <!-- <td>{{ $transaction->cheque_no }} </td> -->
                    <td class="text-center">

                        @if ($transaction->lawyer_approval == 0)
                            <span class="badge badge-warning">Pending</span>
                        @elseif($transaction->lawyer_approval == 1)
                            <span class="badge badge-success">Approved</span>
                        @elseif($transaction->lawyer_approval == 2)
                            <span class="badge badge-danger">Reject</span>
                        @endif
                    </td>
                    <td class="text-center">


                        @if ($transaction->account_approval == 0)
                            <span class="badge badge-warning">Pending</span>
                        @elseif($transaction->account_approval == 1)
                            <span class="badge badge-success">Approve</span>
                        @elseif($transaction->account_approval == 2)
                            <span class="badge bg-danger">Rejected</span>
                        @elseif($transaction->account_approval == 5)
                            <span class="badge bg-info">Resubmit</span>
                        @elseif($transaction->account_approval == 6)
                            <span class="badge bg-question">In Progress</span>
                        @endif
                    </td>
                    <td class="text-center">
                        {{ $transaction->payment_date }}
                    </td>
                    <td>
                        {{ $transaction->requestor }}
                    </td>
                    <td>
                        {{ $transaction->created_at }}
                    </td>

                    <td class="text-center">
                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-">Action</button>
                            <button type="button" class="btn btn-info btn- dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu">
                                
                                <a class="dropdown-item" href="javascript:void(0)"
                                    data-backdrop="static" 
                                    onclick="trustEditMode('{{ $transaction->id }}');" data-keyboard="false" data-toggle="modal"
                                    data-target="#modalTrust">
                                    <i style="margin-right: 10px;" class="cil-pencil"></i>Edit </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" target="_blank" href="/voucher/{{ $transaction->id }}/edit">
                                    <i style="margin-right: 10px;" class="cil-airplay"></i>View </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="deleteVoucher('{{ $transaction->id }}')">
                                    <i style="margin-right: 10px;" class="cil-x"></i>Delete</a>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="10">No data</td>
            </tr>
        @endif


    </tbody>
    <tfoot>
        <tr style="background-color: black; color:white">
            <td class="text-left" colspan="3">Total</td>
            <td class="text-right"> {{ number_format($total_trust_disb, 2, '.', ',') }}
            </td>
            <td class="text-center" colspan="7"></td>
        </tr>
    </tfoot>
</table>
