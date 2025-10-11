@if(count($bank_recon))
<tr>
  <td width="10%">Date</td>
  <td width="10%">Type</td>
  <td width="10%">Trx ID</td>
  <td width="60%">Desc</td>
  <td width="10%">Amount</td>
</tr>
@foreach($bank_recon as $index => $recon)
<tr>
  <td class="text-left" style="width:10%">{{ date('d-m-Y', strtotime($recon->date)); }}</td>
  <td class="text-left">
  @if($recon->transaction_type == 3 || $recon->transaction_type == 2)
  Trust 
  @elseif($recon->transaction_type == 1 || $recon->transaction_type == 4)
  Bill
  @else
    @if(in_array($recon->type, ['JOURNALIN', 'JOURNALOUT']))
    Journal
    @elseif(in_array($recon->type, ['TRANSFERIN', 'TRANSFEROUT','TRANSFERINRECON','TRANSFEROUTRECON']))
    Transfer
    @elseif(in_array($recon->type, ['SSTIN', 'SSTOUT','SSTINRECON', 'SSTOUTRECON']))
    SST
    @elseif(in_array($recon->type, ['CLOSEFILEIN', 'CLOSEFILEOUT']))
    Close File
    @endif
  @endif
  </td>
  <td class="text-left">{{$recon->transaction_id}}</td>
  {{-- <td class="text-left">{{$recon->type}}</td> --}}
  <td class="text-left">
    @if($recon->transaction_type == 1)
    <b>Bill Acc Disb:</b> 
    @elseif($recon->transaction_type == 2)
    <b>Trust Acc Disb:</b> 
    @elseif($recon->transaction_type == 3)
    <b>Trust Acc Payment:</b> 
    @elseif($recon->transaction_type == 4)
    @endif
    @if(in_array($recon->type,['JOURNALINRECON','JOURNALOUTRECON']))
    {{$recon->payee}} - {{$recon->sys_desc}}<br/>
    @endif
    {{$recon->remark}}
    
      @if($recon->transaction_type == 'C' || $recon->transaction_type == 'D')
      <br/>
        @if($recon->type == 'SSTINRECON' || $recon->type == 'SSTOUTRECON')
        SST
        <br/>
        @endif
      <b>Ref No:</b><a href="/case/{{$recon->case_id}}" target="_blank">[{{$recon->case_ref_no}}]</a>
      <br/>

      @if(in_array($recon->type,['JOURNALINRECON','JOURNALOUTRECON']))
      <b>Journal No: </b>  <a href="/journal-entry/{{$recon->key_id}}" target="_blank">[{{$recon->cheque_no}}]</a>
      @elseif(in_array($recon->type, ['CLOSEFILEIN', 'CLOSEFILEOUT']))
      @else
      <b>Invoice No: </b> [{{$recon->invoice_no}}]
      @endif
      
      @else
        <a href="/voucher/{{$recon->key_id}}/edit" target="_blank">[{{$recon->cheque_no}}]</a>
      @endif 
    @if($recon->transaction_type == 1)
    <br/>
    {{$recon->sys_desc}}={{$recon->amount}}
    @endif
   </td>
  <td class="text-right">{{ number_format($recon->amount, 2, '.', ',') }}</td>
</tr>

@endforeach
@else
<tr>
  <td class="text-center" colspan="11">No data</td>
</tr>
@endif