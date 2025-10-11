@if (count($CasesPIC) > 0)
    @foreach ($CasesPIC as $index => $pic)
        <tr>
            <td>
                <div class="row">
                    <div class="col-xl-3 col-lg-4">
                        <div class="c-avatar"><img class="c-avatar-img"
                                src="../assets/img/avatars/img-default-profile.png">
                        </div>
                    </div>

                    <div class="col-xl-7 col-lg-8">
                        <div>{{ $pic->name }}</div>
                        <div class=" text-muted"><span>{{ $pic->menuroles }}</span>
                        </div>

                        
                    </div>
                    @if (in_array($current_user->menuroles, ['admin', 'management']) || in_array($current_user->id, [14]))
                        <div class="col-xl-2 col-lg-8">
                            <div class="btn-group" style="padding-left:10px">
                                <button type="button" class="btn btn-info btn-flat btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu"  style="padding:0">
                                    
                                    <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                    style="color:white;margin:0" onclick="removePIC('{{ $pic->id }}')"><i style="margin-right: 10px;" class="cil-x"></i>Remove</a>

                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
@else
<tr>
    <td class="text-center">No PIC yet</td>
</tr>
@endif
