<div id="modalCloseFileUpdate" class="modal fade" role="dialog">
    <div class="modal-dialog" style="width: 80% !important;max-width: 80% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Quick update masterlist for subject matter</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <form id="formMasterListLetterHead">

                    <div class="form-group row div-result-close-file">

                        @foreach($caseMasterListField as $index => $field)

                            @if ($field->letter_head == 1)

                            <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 ">
                                <div class="form-group row">
                                    <label class="col-md-4 col-form-label" for="{{$field->id}}"> {{ str_replace('_', ' ', $field->code) }} </label>
                                    
                                    <div class="col-md-8">
                                        @if ($field->type == 'area')
                                        <textarea class="form-control" id="modal_{{$field->id}}" name="{{$field->id}}" rows="3">{{$field->value}}</textarea>
                                        @else
                                        <input class="form-control" id="modal_{{$field->id}}" type="{{$field->type}}" name="{{$field->id}}" value="{{$field->value}}">
                                        @endif
                                    </div>
                                </div>
                            </div>


                            @endif


                        @endforeach

                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>
                

                <button type="button" id="btnAbortFile" class="btn btn-danger float-right" onclick="submitMasterListLetterHead()">Update
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>
