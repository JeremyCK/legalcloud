<table class="table table-bordered yajra-datatable" id="tbl-attachment-yadra1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Remark</th>
            <th>Upload By</th>
            <th>Receipt Done</th>
            <th>Type</th>
            <th>Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @if (count($LoanAttachment))

            @foreach ($LoanAttachment as $index => $attachment)
                <tr id="referral_row_{{ $attachment->id }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $attachment->display_name }}</td>
                    <td>{{ $attachment->remark }}</td>
                    <td>{{ $attachment->user_name }}</td>
                    <td class="text-center">
                        @if ($attachment->receipt_done == 1)
                            <span class=" badge badge-pill badge-success">Done</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-center">
                        @if ($attachment->attachment_type == 1)
                            <span class=" badge badge-pill badge-warning">Correspondences</span>
                        @elseif($attachment->attachment_type == 2)
                            <span class=" badge badge-pill badge-info">Documents</span>
                        @elseif($attachment->attachment_type == 3)
                            <span class=" badge badge-pill badge-success">Account Receipt</span>
                        @elseif($attachment->attachment_type == 4)
                            <span class=" badge badge-pill badge-danger">Adjudicate</span>
                        @elseif($attachment->attachment_type == 5)
                            <span class=" badge badge-pill bg-question">Marketing</span>
                        @elseif($attachment->attachment_type == 6)
                            <span class=" badge badge-pill bg-question">Official Receipt</span>
                        @elseif($attachment->attachment_type == 7)
                            <span class=" badge badge-pill bg-red">Other Receipt</span>
                        @elseif($attachment->attachment_type == 8)
                            <span class=" badge badge-pill bg-light-blue">Presentation Receipt</span>
                        @elseif($attachment->attachment_type == 9)
                            <span class=" badge badge-pill " style="background-color: rgb(0, 255, 55)">Checklist document</span>
                        @else
                        <span class=" badge badge-pill " style="background-color: #ebedef">Payment Voucher</span>
                        @endif
                    </td>
                    <td>{{ $attachment->created_at }}</td>
                    <td class="text-center">
                        @if ($attachment->s3_file_name)
                            <a href="javascript:void(0)" onclick="openFileFromS3('{{ $attachment->s3_file_name }}')"
                                class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top"
                                title="Download"><i class="cil-cloud-download"></i></a>
                        @else
                            <a target="_blank" href="/{{ $attachment->filename }}"
                                class="btn btn-info shadow sharp mr-1" data-toggle="tooltip" data-placement="top"
                                title="Download"><i class="cil-cloud-download"></i></a>
                        @endif

                        {{-- @if (in_array($current_user->menuroles, ['account', 'admin', 'management'])) --}}
                        <a href="javascript:void(0)" onclick="deleteMarketingBill('{{ $attachment->id }}')"
                            class="btn btn-danger"><i class="cil-x"></i></a>
                        {{-- @endif --}}
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
