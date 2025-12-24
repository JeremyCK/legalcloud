@php
    $LoanAttachmentMarketing = $LoanAttachmentMarketing ?? collect();
    $current_user = $current_user ?? null;
@endphp

<table class="table table-bordered yajra-datatable" id="tbl-marketing-file-yadra" style="width:100%">
  <thead>
    <tr>
      <th>No</th>
      <th>Name</th>
      <th>Remark</th>
      <th>Upload By</th>
      <th>Date</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @if(isset($LoanAttachmentMarketing) && count($LoanAttachmentMarketing))

    @foreach($LoanAttachmentMarketing as $index => $attachment)
    <tr id="referral_row_{{ $attachment->id }}">
      <td>{{ $index+1 }}</td>
      <td>{{ $attachment->display_name ?? ''}}</td>
      <td>{{ $attachment->remark ?? ''}}</td>
      <td>{{ $attachment->user_name ?? ''}}</td>
      <td>{{ $attachment->created_at ?? ''}}</td>
      <td class="text-center">
        <!-- <a href="/{{ $attachment->filename}}"  class="btn btn-primary shadow btn-xs sharp mr-1" data-toggle="tooltip" data-placement="top" title="voucer">Download</a> -->
        {{-- <a target="_blank" href="/{{ $attachment->filename}}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a> --}}
        
        @if (isset($attachment->s3_file_name) && $attachment->s3_file_name)
        <a  href="javascript:void(0)" onclick="openFileFromS3('{{$attachment->s3_file_name}}')" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
        @elseif(isset($attachment->filename))
        <a target="_blank" href="/{{ $attachment->filename}}" class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top" title="Download"><i class="cil-cloud-download"></i></a>
        @endif
        
        @if(isset($current_user) && $current_user->menuroles == 'admin')
        <a href="javascript:void(0)" onclick="deleteMarketingBill('{{ $attachment->id }}')" class="btn btn-danger"><i class="cil-x"></i></a>
        @endif
      </td>
    </tr>

    @endforeach
    @else
    <tr>
      <td class="text-center" colspan="6">No file</td>
    </tr>
    @endif
  </tbody>
</table>