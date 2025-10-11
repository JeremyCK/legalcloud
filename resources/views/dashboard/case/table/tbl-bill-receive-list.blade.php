<thead style="background-color: black;color:white; z-index:100">
    <tr>
        <th class="text-center">No</th>
        <th class="text-center">TRX ID</th>
        <th class="text-center">Payee</th>
        <th class="text-center">Desc</th>
        <th class="text-center">Amount(RM)</th>
        <th class="text-center">Client Bank</th>
        <th class="text-center">Date Receive</th>
        <th class="text-center">System Date</th>
        <th class="text-center">Requested By</th>
        @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']))
            <th class="text-center">Action</th>
        @endif
    </tr>
</thead>
<tbody>
    @php
        $total_amt = 0;
    @endphp
    @if (count($bill_receive))

        @foreach ($bill_receive as $index => $disb)
            @php
                $total_amt += $disb->amount;
            @endphp
            <tr>
                <td>
                    <div class="checkbox">
                        @if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker']))
                            <input type="checkbox" name="receive_list" value="{{ $disb->id }}"
                                id="receive_list{{ $disb->id }}">
                        @endif
                        <label for="receive_list{{ $disb->id }}">{{ $index + 1 }}</label>
                    </div>
                </td>
                <input type="hidden" value="{{ $disb->trx_id }}" id="recv_trx_id_{{ $disb->id }}">
                <input type="hidden" value="{{ $disb->voucher_no }}" id="recv_voucher_no_{{ $disb->id }}">
                <input type="hidden" value="{{ $disb->payee }}" id="recv_payee_{{ $disb->id }}">
                <input type="hidden" value="{{ $disb->remark }}" id="recv_remarks_{{ $disb->id }}">
                <input type="hidden" value="{{ $disb->amount }}" id="recv_amount_{{ $disb->id }}">
                <td>{{ $disb->trx_id }}</td>
                <td>{{ $disb->payee }}</td>
                <td>{{ $disb->remark }}</td>
                <td class="text-right">{{ number_format($disb->amount, 2, '.', ',') }}</td>
                <td>
                    @if ($disb->client_bank_name)
                        {{ $disb->client_bank_name }} <br />({{ $disb->bank_short_code }} )
                    @endif
                </td>

                <td class="text-center">{{ $disb->payment_date }}</td>
                <td class="text-center">{{ $disb->created_at }}</td>
                <td class="text-center">{{ $disb->requestor }}</td>

                @if (in_array($current_user->menuroles, ['admin', 'management', 'account', 'maker']))
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
                                onclick="trustEditMode('{{ $disb->voucher_id }}');" data-keyboard="false" data-toggle="modal"
                                data-target="#modalTrust">
                                <i style="margin-right: 10px;" class="cil-pencil"></i>Edit </a>
                            <div class="dropdown-divider"></div>
                                {{-- <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="billReceiveEditMode('{{ $disb->voucher_id }}');">
                                    <i style="margin-right: 10px;" class="cil-pencil"></i>Edit</a>
                                <div class="dropdown-divider"></div> --}}
                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="generateReceiptController('{{ $disb->voucher_id }}', 'BILL');"
                                    data-backdrop="static" data-keyboard="false" data-target="#modalReceipt"
                                    data-toggle="modal"><i style="margin-right: 10px;" class="cil-print"></i>Print</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="deleteVoucher('{{ $disb->voucher_id }}')"><i style="margin-right: 10px;" class="cil-x"></i>Delete</a>

                            </div>
                        </div>
                    </td>
                @endif
            </tr>
        @endforeach
    @else
        <tr>
            <td class="text-center" colspan="10">No data</td>
        </tr>
    @endif
</tbody>
<tfoot style="background-color: black;color:white; z-index:100">
    <th colspan="4" class="text-left">Total </th>
    <th class="text-right"><span id="span_total_disb"
            class="text-right">{{ number_format($total_amt, 2, '.', ',') }}</span>
    </th>
    <th colspan="6" class="text-left"> </th>
</tfoot>
