<div class="row">
    <div class="col-12">
        <div class="box">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Notes</h3>

                            <div class="box-tools">

                                <button class="btn btn-primary" type="button" onclick="notesMode('2');">
                                    <i class="cil-plus"></i> Add new note
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-header d-->
            <div class="box-body no-padding" style="width:100%;overflow-x:auto;min-height:300px;">

                @if (count($LoanCaseKIVNotes))

                    @foreach ($LoanCaseKIVNotes as $index => $notes)
                        <?php
                        $color = 'info';
                        if ($notes->menuroles == 'account') {
                            $color = 'warning';
                        } elseif ($notes->menuroles == 'admin') {
                            $color = 'danger';
                        } elseif ($notes->menuroles == 'sales') {
                            $color = 'success';
                        } elseif ($notes->menuroles == 'clerk') {
                            $color = 'primary';
                        } elseif ($notes->menuroles == 'lawyer') {
                            $color = 'info';
                        }elseif ($notes->menuroles == 'receptionist') {
                            $color = 'question';
                        } 
                        ?>

                        <div id="note_box_{{ $notes->id }}" class="div-botes-box row"
                            style="border: solid 1px black;padding: 10px;margin:10px;border-radius: 5px">
                            <div class="col-6">
                                <div class="row">
                                    <div class="col-1">
                                        <div class="c-avatar float-left" style="margin-left:10px"><img
                                                class="c-avatar-img" src="../assets/img/avatars/img-default-profile.png"
                                                alt="user@email.com"> </div>
                                    </div>
                                    <div class="col-11">
                                        <span style="padding-left: 10px;"
                                            class="text-{{ $color }}"><b>{{ $notes->user_name }}</b></span>
                                    </div>

                                </div>


                            </div>

                            <div class="col-6">

                                <span class=" float-right "
                                    style="font-size:10px;color:#908d8d">{{ date('d-m-Y h:i A', strtotime($notes->created_at)) }}
                                    @if ($notes->updated_by != null)
                                        (Edited)
                                    @endif
                                    @php
                                        // Check if this is a system-created note (has non-empty label)
                                        // System notes have labels like: 'operation|dispatch', 'setkiv', 'case_status', etc.
                                        // Handle null, empty string, or whitespace-only labels
                                        $labelValue = isset($notes->label) ? trim($notes->label) : '';
                                        $isSystemNote = $labelValue !== '';
                                        
                                        // Check if user is admin/management
                                        $isAdmin = in_array($current_user->menuroles, ['admin', 'management']);
                                        
                                        // Check if user can delete
                                        $canDelete = false;
                                        if ($isSystemNote) {
                                            // For system notes, only admin/management can delete
                                            $canDelete = $isAdmin;
                                        } else {
                                            // For user-created notes, only the creator can delete
                                            $canDelete = $notes->created_by == $current_user->id;
                                        }
                                        
                                        // Check if user can edit (only for non-system notes that they created)
                                        // System notes cannot be edited by anyone
                                        $canEdit = !$isSystemNote && $notes->created_by == $current_user->id;
                                        
                                        // Only show dropdown if user has permission to delete or edit
                                        $showDropdown = $canDelete || $canEdit;
                                    @endphp
                                    @if ($showDropdown)
                                        <div class="btn-group"  style="padding-left:5px">
                                            <button type="button" class="btn btn-info btn-sm dropdown-toggle"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                                style="min-width: 35px; padding: 5px 10px; font-size: 14px; line-height: 1.2;">
                                                <span class="caret" style="border-width: 5px 4px 0 4px;"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu">
                                                @if ($canEdit)
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="notesEditMode('{{ $notes->id }}')"><i
                                                            class="cil-pencil"></i>Edit</a>
                                                    @if ($canDelete)
                                                        <div class="dropdown-divider"></div>
                                                    @endif
                                                @endif
                                                @if ($canDelete)
                                                    <a class="dropdown-item" href="javascript:void(0)"
                                                        onclick="deleteNotes('{{ $notes->id }}')"><i
                                                            class="cil-x"></i>Delete</a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                    <br />

                                </span>

                                <br />
                            </div>

                            <div class="col-12" style="padding-right:0px !important;padding-left:0px !important">
                                <hr style="margin-bottom:5px !important;margin-top:5px !important" />
                            </div>
                            <div id="notes_{{ $notes->id }}" class="col-12">
                                {{-- @if ($notes->label == 'operation|safekeeping')
                                    {!! str_replace('/app/documents/safe_keeping/', '/app/documents/safekeeping/', $notes->notes) !!}
                                @elseif($notes->label == 'operation|landoffice')
                                    {!! str_replace('/app/documents/land_office/', '/app/documents/landoffice/', $notes->notes) !!}
                                @else
                                    {!! $notes->notes !!}
                                @endif --}}

                                @php
                                    $message = $notes->notes;
                                    if ($notes->label == 'operation|dispatch')
                                    {
                                        $prefix = '<a target="_blank" href="/app/documents/dispatch/';

                                        $postfix = '" class="mailbox-attachment-name"';
                                        $replace = '<a href="javascript:void(0)" onclick="openFileFromS3(\'';
                                        $replace2 = '\')"  class="mailbox-attachment-name"';

                                        if (!str_contains($message , '<a target="_blank" href="/app/documents/dispatch/dispatch/'))
                                        {
                                            $prefix = '<a target="_blank" href="/app/documents/';
                                        }

                                        $message = str_replace($prefix,$replace,$message);
                                        $message = str_replace($postfix,$replace2,$message);
                                    }
                                    else  if ($notes->label == 'operation|safekeeping')
                                    {
                                        $prefix = '<a target="_blank" href="/app/documents/safe_keeping/';

                                        $postfix = '" class="mailbox-attachment-name"';
                                        $replace = '<a href="javascript:void(0)" onclick="openFileFromS3(\'';
                                        $replace2 = '\')"  class="mailbox-attachment-name"';

                                        $message = str_replace($prefix,$replace,$message);
                                        $message = str_replace($postfix,$replace2,$message);
                                    }
                                    else  if ($notes->label == 'operation|landoffice')
                                    {
                                        $prefix = '<a target="_blank" href="/app/documents/land_office/';

                                        $postfix = '" class="mailbox-attachment-name"';
                                        $replace = '<a href="javascript:void(0)" onclick="openFileFromS3(\'';
                                        $replace2 = '\')"  class="mailbox-attachment-name"';

                                        $message = str_replace($prefix,$replace,$message);
                                        $message = str_replace($postfix,$replace2,$message);
                                    }

                                @endphp
                                
                                {!! $message !!}

                            </div>

                        </div>
                    @endforeach
                @else
                    <div class="div-botes-box"
                        style="border: solid 1px black;padding: 20px;margin:10px;border-radius: 15px;">

                        No note yet
                    </div>
                @endif
            </div>
            <!-- /.box-body -->
        </div>
        <!-- /.box -->
    </div>
</div>
