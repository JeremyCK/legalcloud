<div id="modalPIC" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 50% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Assign PIC </h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>
               
            </div>
            <div class="modal-body">
                <form id="formCaseSummary">
                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Role</label>

                                <select class="form-control" name="role" id="ddlRole">
                                    <option value="role_lawyer">Lawyer </option>
                                    <option value="role_clerk">Clerk </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col">
                                <label>Staff</label>

                                <select class="form-control" id="ddlPIC" name="ddlPIC" required>
                                    <option value="0"> -- Select PIC -- </option>
                                       
                                    @if (count($Staffs))
                                        @foreach ($Staffs as $index => $staff)
                                            <option class="role_all role_{{ $staff->menuroles }}" @if($staff->menurole == 'clerk') style="display:none" @endif value="{{ $staff->id }}"> {{ $staff->name }} </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success float-right"
                    onclick="assignPic()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>