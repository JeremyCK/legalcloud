<tbody>
    <tr class="text-center">
        <th>No</th>
        <th>Received Date</th>
        <th>Transaction ID</th>
        <th>Payee</th>
        <th>Office Account</th>
        <th>Desc</th>
        <th>Amount (RM)</th>
        <th>Date</th>
        <th>Receive By</th>
        @if (isset($current_user) && in_array($current_user->menuroles, ['account','admin','management','maker']))
            <th>Action</th>
        @endif
    </tr>
    @php
    $total_trust_received = 0;
    $loan_case_trust_main_receive = $loan_case_trust_main_receive ?? collect();
@endphp
    @if (isset($loan_case_trust_main_receive) && count($loan_case_trust_main_receive))

        @foreach ($loan_case_trust_main_receive as $index => $transaction)
            @php
                $total_trust_received += $transaction->total_amount;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="text-center">
                    {{ $transaction->payment_date }}
                </td>
                <td>{{ $transaction->transaction_id }} </td>
                <td>{{ $transaction->payee }}</td>
                <td>
                    @if ($transaction->office_account_id != 0)
                        {{ $transaction->office_account }}
                        ({{ $transaction->office_account_no }})
                    @endif
                </td>
                <td>{{ $transaction->remark }}</td>
                <td class="text-right">
                    {{ number_format($transaction->total_amount, 2, '.', ',') }}
                </td>

                <td class="text-center">
                    {{ $transaction->created_at }}
                </td>

                <td>
                    {{ $transaction->requestor }}
                </td>
                @if (isset($current_user) && in_array($current_user->menuroles, ['account','admin','management','maker']))
                    <td class="text-center">


                        <div class="btn-group">
                            <button type="button"
                                class="btn btn-info btn-">Action</button>
                            <button type="button"
                                class="btn btn-info btn- dropdown-toggle"
                                data-toggle="dropdown">
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
                                {{-- <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="trustEditMode('{{ $transaction->id }}');"><i
                                        class="cil-pencil"></i>Edit</a>
                                <div class="dropdown-divider"></div> --}}
                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="generateReceiptController('{{ $transaction->id }}', 'BILL');"
                                    data-backdrop="static" data-keyboard="false"
                                    data-target="#modalReceipt" data-toggle="modal">
                                    <i style="margin-right: 10px;" class="cil-print"></i>Print</a>

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="deleteVoucher('{{ $transaction->id }}')">
                                    <i style="margin-right: 10px;" class="cil-x"></i>Delete</a>
                            </div>
                        </div>
                    </td>
                @endif
            </tr>
        @endforeach
    @else
        <tr>
            <td class="text-center" colspan="{{ isset($current_user) && in_array($current_user->menuroles, ['account','admin','management','maker']) ? '10' : '9' }}">No data</td>
        </tr>
    @endif
</tbody>

<tfoot>
    <tr style="background-color: black; color:white">
        <td class="text-left" colspan="6">Total</td>
        <td class="text-right"> {{ number_format($total_trust_received, 2, '.', ',') }}
        </td>
        <td class="text-center" colspan="{{ isset($current_user) && in_array($current_user->menuroles, ['account','admin','management','maker']) ? '3' : '2' }}"></td>
    </tr>
</tfoot>