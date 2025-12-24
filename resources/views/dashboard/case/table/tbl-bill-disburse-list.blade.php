<thead style="background-color: black;color:white; z-index:100"> 
    <tr>
        <th class="text-center">No</th>
        <th class="text-center">Voucher No</th>
        <th class="text-center">Trx Id</th>
        <th class="text-center">Item</th>
        <th class="text-center">Desc</th>
        <th class="text-center">Amount(RM)</th>
        <th class="text-center">Client Bank</th>
        <th class="text-center">Payment Date</th>
        <th class="text-center">Date</th>
        <th class="text-center">Requested By</th>
        <th class="text-center">Status</th>
        <th class="text-center remove-this">Action</th>
    </tr>
</thead>
<tbody id="tbl-bill-disburse">
    @php
        $total_disb = 0;    
    @endphp
    @if (count($bill_disburse))
        @foreach ($bill_disburse as $index => $disb)
            @php 
                if($disb->account_approval != 2)
                {
                    $total_disb += $disb->amount;
                } 
            @endphp
            <tr data-account-type="{{ $disb->account_type ?? '' }}" data-item-name="{{ strtolower($disb->account_name ?? '') }}" data-amount="{{ $disb->account_approval != 2 ? $disb->amount : 0 }}">
                <td>{{ $index + 1 }}</td>
                <td><a target="_blank" href="/voucher/{{ $disb->voucher_id }}/edit">{{ $disb->voucher_no }}</a> </td>
                <td>{{ $disb->transaction_id }}</td>
                <td>{{ $disb->account_name }}</td>
                <td>{{ $disb->remark }}</td>
                <td class="text-right">{{  number_format($disb->amount, 2, '.', ',') }}</td>
                <td>
                    @if ($disb->client_bank_name)
                        {{ $disb->client_bank_name }} <br />({{ $disb->bank_short_code }} )
                    
                    @endif
                </td>
                <td class="text-center">{{ $disb->payment_date }}</td>
                <td class="text-center">{{ $disb->created_at }}</td>
                <td >{{ $disb->requestor }}</td>
                <td class="text-center">
                    @if ($disb->account_approval == 0)
                        <span class="badge badge-warning">Pending</span>
                    @elseif($disb->account_approval == 1)
                        <span class="badge badge-success">Approve</span>
                    @elseif($disb->account_approval == 2)
                        <span class="badge bg-danger">Rejected</span>
                    @elseif($disb->account_approval == 5)
                        <span class="badge bg-info">Resubmit</span>
                    @elseif($disb->account_approval == 6)
                        <span class="badge bg-question">In Progress</span>
                    @endif
                </td>
                <td class="text-center remove-this">

                    <div class="btn-group">
                        <button type="button" class="btn btn-info btn-">Action</button>
                        <button type="button" class="btn btn-info btn- dropdown-toggle" data-toggle="dropdown">
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <div class="dropdown-menu">
                
                            <a class="dropdown-item" target="_blank" href="/voucher/{{ $disb->voucher_id }}/edit">
                                <i style="margin-right: 10px;" class="cil-airplay"></i>Edit</a>
                            @if ($disb->account_approval != 1)
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0)"
                                    onclick="deleteVoucher('{{ $disb->voucher_id }}')"><i style="margin-right: 10px;"  class="cil-x"></i>Delete</a>
                            @else
                                @if ($current_user->menuroles == 'admin')
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="javascript:void(0)"
                                        onclick="deleteVoucher('{{ $disb->voucher_id }}')"><i style="margin-right: 10px;" class="cil-x"></i>Delete</a>
                                @endif
                            @endif
                        </div>
                    </div>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td class="text-center" colspan="12">No data</td>
        </tr>
    @endif
</tbody>
<tfoot style="background-color: black;color:white; z-index:100">
    <th colspan="5" class="text-left">Total </th>
    <th class="text-right"><span id="span_total_disb" class="text-right">{{  number_format($total_disb, 2, '.', ',') }}</span>
    </th>
    <th colspan="6" class="text-left"> </th>
</tfoot>


