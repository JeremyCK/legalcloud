    @if (count($CasesNotes))

    @foreach ($CasesNotes as $index => $notes)
        <?php
        $color = 'info';
        if ($notes->menuroles == 'account') {
            $color = 'warning';
        } elseif ($notes->menuroles == 'admin') {
            $color = 'danger';
        } elseif ($notes->menuroles == 'sales') {
            $color = 'success';
        } elseif ($notes->menuroles == 'clerk') {
            $color = 'question';
        } elseif ($notes->menuroles == 'lawyer') {
            $color = 'info';
        }
        ?>

        <div id="note_box_{{ $notes->id }}" class="div-botes-box row"
            style="border: solid 1px black;padding: 10px;margin:10px;border-radius: 5px">
            <div class="col-6">
                <div class="row">
                    <div class="col-1">
                        <div class="c-avatar float-left" style="margin-left:10px"><img class="c-avatar-img"
                                src="../assets/img/avatars/img-default-profile.png" alt="user@email.com"> </div>
                    </div>
                    <div class="col-11">
                        <span style="padding-left: 10px;"
                            class="text-{{ $color }}"><b>{{ $notes->user_name }}</b></span>
                    </div>
                </div>
            </div>

            <div class="col-6">

                <span class=" float-right "
                    style="font-size:10px;color:#908d8d">{{ date('d-m-Y h:i A', strtotime($notes->created_at)) }} @if ($notes->updated_by != null)
                        (Edited)
                    @endif
                    @if ($notes->created_by == $current_user->id)
                        <div class="btn-group" style="padding-left:10px">
                            <button type="button" class="btn btn-info btn-flat btn-xs dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu"  style="padding:0">
                                <a class="dropdown-item btn-info" href="javascript:void(0)" data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#modalNotes"
                                style="color:white;margin:0" onclick="EditNotesMode('{{ $notes->id }}')"><i style="margin-right: 10px;" class="fa  fa-pencil"></i>Edit</a>

                                
                                <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                style="color:white;margin:0" onclick="deleteCaseNote('{{ $notes->id }}')"><i style="margin-right: 10px;" class="cil-x"></i>Delete</a>

                            </div>
                        </div>
                    @endif
                    <br />

                </span>

                <br />
            </div>
            <div  class="col-12" style="padding-right:0px !important;padding-left:0px !important"><hr style="margin-bottom:5px !important;margin-top:5px !important"/></div>
            <div id="notes_{{ $notes->id }}" class="col-12">
                {!! $notes->notes !!}
            </div>

        </div>
    @endforeach
@else
    <div class="div-botes-box" style="border: solid 1px black;padding: 20px;margin:10px;border-radius: 15px;">
        No note yet
    </div>
@endif
