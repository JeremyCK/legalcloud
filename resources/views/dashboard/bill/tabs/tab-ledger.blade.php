<div class="row">
  <div class="col-12">
    <div class="box">
      <div class="box-header">
        <h3 class="box-title">Ledger</h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body no-padding" style="width:100%;overflow-x:auto">
        <table class="table table-striped table-bordered datatable">
          <tbody>
            <tr class="text-center">
              <th>Transaction ID</th>
              <th>Item</th>
              <th>Credit (RM)</th>
              <th>Debit (RM)</th>
              <th>Cheque No</th>
              <th>Bank</th>
              <th>Date</th>
              <th>Action</th>
            </tr>
            @if(count($transactions))

            @foreach($transactions as $index => $transaction)
            <tr>
              <td class="text-center">{{ $transaction->transaction_id }}</td>
              <td>{{ $transaction->name }}</td>
              <td>
                @if($transaction->transaction_type == 'C')
                {{ $transaction->amount }}
                @else
                -
                @endif
              </td>
              <td>
                @if($transaction->transaction_type == 'D')
                {{ $transaction->amount }}
                @else
                -
                @endif
              </td>

              <td>{{ $transaction->cheque_no }} </td>
              <td>{{ $transaction->bank_name }} </td>
              <td class="text-center">
                {{ $transaction->created_at }}
              </td>

              <td class="text-center">
                <a href="javascript:void(0)" onclick="dispatchMode('{{ $transaction->id }}', '{{ $case->id }}');" class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Edit</a>

              </td>
            </tr>

            @endforeach
            @else
            <tr>
              <td class="text-center" colspan="5">No data</td>
            </tr>
            @endif


          </tbody>
        </table>
      </div>
      <!-- /.box-body -->
    </div>
    <!-- /.box -->
  </div>
</div>