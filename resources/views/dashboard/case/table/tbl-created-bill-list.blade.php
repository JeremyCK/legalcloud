<table class="table table-striped table-bordered datatable">
    <thead>
        <tr class="text-center">
            <th>Bill No</th>
            <th>Bill To</th>
            <th>Bill</th>
            <th>Total Amount</th>
            <th>Collected Amount</th>
            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillBalancePermission()) == true)
            <th>Spent Amount</th> 
            @endif
            
            <th>Prepare by</th>
            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ConvertInvoicePermission()) == true)
                <th>Invoice</th>
            @endif
            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ConvertInvoicePermission()) == true)
                <th>Status</th>
            @endif
            <th>Created Date</th>
            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ConvertInvoicePermission()) == true)
                <th>Paid SST</th>
            @endif
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (count($loanCaseBillMain))
            @foreach ($loanCaseBillMain as $index => $main)
                <tr>
                    <input class="form-control" type="hidden" value="{{ $main->total_amt }}"
                        id="txt_hd_total_amt_{{ $main->id }}" name="txt_hd_total_amt">
                    <input class="form-control" type="hidden" value="{{ $main->collected_amt }}"
                        id="txt_hd_collcted_amt_{{ $main->id }}" name="txt_hd_collcted_amt">
                    <input class="form-control" type="hidden" value="{{ $main->used_amt }}"
                        id="txt_hd_spent_amt_{{ $main->id }}" name="txt_hd_spent_amt">
                    <td class="text-center">{{ $main->bill_no }}</td>
                    <td class="text-center">{{ $main->bill_to }}</td>
                    <td>{{ $main->name }}</td>
                    <td class="text-right">RM {{ number_format($main->total_amt, 2, '.', ',') }}</td>
                    <td class="text-right">RM {{ number_format($main->collected_amt, 2, '.', ',') }}</td>
                    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillBalancePermission()) == true)
                    <td class="text-right">RM {{ number_format($main->used_amt, 2, '.', ',') }}</td>
                    @endif
                    
                    <td class="text-center">{{ $main->prepare_by }}</td>
                    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ConvertInvoicePermission()) == true)
                        <td>
                            @if(isset($main->invoices) && count($main->invoices) > 0)
                                @foreach($main->invoices as $index => $invoice)
                                    <b>Inv No: </b>{{ $invoice->invoice_no }}<br />
                                    <b>Inv Amt: </b>{{ number_format($invoice->amount ?? 0, 2, '.', ',') }}
                                    @if($index < count($main->invoices) - 1)
                                        <br /><br />
                                    @endif
                                @endforeach
                            @elseif($main->invoice_no)
                                {{-- Fallback for backward compatibility --}}
                                <b>Inv No: </b>{{ $main->invoice_no }}<br />
                                <b>Inv Amt: </b>{{ number_format($main->total_amt_inv ?? 0, 2, '.', ',') }}
                            @endif
                        </td>
                    @endif

                    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ConvertInvoicePermission()) == true)
                        <td class="text-center">
                            @if ($main->bln_invoice == 1)
                                Invoice
                            @else
                                Quotation
                            @endif
                        </td>
                    @endif
                    <td class="text-center">{{ $main->created_at }}</td>

                    @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::ConvertInvoicePermission()) == true)
                        <td class="text-center">
                            @if ($main->bln_sst == 1)
                                Paid
                            @else
                                Unpaid
                            @endif
                        </td>
                    @endif
                    <td class="text-center">

                        <?php
                        $allowDelete = 1;
                        
                        if ($main->referral_a1_trx_id != null && $main->referral_a1_trx_id != '') {
                            $allowDelete = 0;
                        } elseif ($main->referral_a2_trx_id != null && $main->referral_a2_trx_id != '') {
                            $allowDelete = 0;
                        } elseif ($main->referral_a3_trx_id != null && $main->referral_a3_trx_id != '') {
                            $allowDelete = 0;
                        } elseif ($main->referral_a4_trx_id != null && $main->referral_a4_trx_id != '') {
                            $allowDelete = 0;
                        } elseif (floatval($main->marketing) > 0) {
                            $allowDelete = 0;
                        }
                        
                        ?>

                        <div class="btn-group">
                            <button type="button" class="btn btn-info btn-lg">Action</button>
                            <button type="button" class="btn btn-info btn- dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu" style="padding:0">

                                <div class="dropdown-divider" style="margin:0"></div>


                                <a class="dropdown-item btn-success" href="javascript:void(0)"
                                    onclick="billListMode('{{ $main->id }}', '{{ $main->name }}')"
                                    style="color:white;margin:0"><i style="margin-right: 10px;"
                                        class="cil-pencil"></i>View</a>

                               

                                @if (!in_array($case->status, [99, 0]))
                                    @if ($allowDelete == 1 && $main->bln_invoice == 0)
                                    <div class="dropdown-divider" style="margin:0"></div>
                                        <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                            onclick="deleteBill('{{ $main->id }}')" style="color:white"><i
                                                class="cil-x" style="margin-right: 10px;"></i> <span></span>
                                            Delete</a>
                                    @endif
                                @endif






                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td class="text-center" colspan="12">No bill</td>
            </tr>
        @endif
    </tbody>
</table>