<div id="modalLink" class="modal fade" role="dialog">
    <div class="modal-dialog" style="max-width: 60%">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1"><i style="margin-right: 10px;" class="cil-transfer"></i> Link user</h4>
                        <input type="hidden" id="input_close_abort" />
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <form id="form_add">




                    <div class="form-group row">
                        <div class="col">
                            <label>User</label>
                            <select class="form-control" id="new_user" name="new_user">
                                @foreach ($linkUser as $index => $row)
                                    <option value="{{ $row->id }}" data-name="{{ $row->name }}"
                                        class="option_branch option_branch_{{ $row->branch_id }}" selected>
                                        {{ $row->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                   
                    
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose2" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right" onclick="addLinkUser()">Add
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<script>

    function addLinkUser()
    {
        $link_array = $("#link_case_user").val();
        console.log($link_array);
        
        if($link_array != '')
        {
            $link_array = JSON.parse($link_array);
        }
        else
        {
            $link_array = [];
        }
        
        $selected_user = parseInt($('#new_user').val());

        if(!$link_array.includes($selected_user ))
        {
            $link_array.push($('#new_user').val());
            $name = $('#new_user').find(':selected').attr('data-name');

            $structure = `<li id="link_case_user_` + $('#new_user').val() + `" class="list-group-item">`+ $name +
                            `<a href="javascript:void(0)" onclick="removeLinkUser(` + $('#new_user').val() + `)" class=" float-right"><i class="fa fa-close text-danger"></i></a>
                          </li>`;

            $("#ul-link-user").append($structure );

            $("#link_case_user").val("[" +$link_array + "]");

            toastController('User Added');
        }

       
    }

    function removeLinkUser(id)
    {
        $('#link_case_user_' + id).remove();

        $link_array = $("#link_case_user").val();
        $link_array = JSON.parse($link_array);

        $index = $link_array.indexOf(id);
        $link_array.splice($index,1);
        $("#link_case_user").val("[" +$link_array + "]");
        
        // for($i=0;$i<link_array.length;$i++)
        // {
            
        // }
        // $link_array.splice(index,1);

        // console.log($link_array)

        // $("#link_case_user").val("[" +$link_array + "]");
    }

    
    
</script>