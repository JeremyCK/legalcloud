<div id="dFileTemplateList" class="card d_operation" style="display:none">

    <div class="card-header">
        <h4>File template list</h4>
    </div>
    <div class="card-body">
        <div id="divList" class="row"
            style="background-color: white; max-height:980px; min-height:800px;padding-top:10px">

            <div class="col-sm-4">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-6">
                                <h4>Folder</h4>
                            </div>
                            <div class="col-6">
                            </div>

                        </div>

                    </div>
                    <div class="card-body">
                        <div class="collection file-manager-drive mt-3" style="max-height:700px;overflow-y:auto">


                            @if (count($fileFolder))
                                @foreach ($fileFolder as $index => $folder)
                                    <div class=" display-inline mr-3" style="font-size: 12px !important">
                                        <input type="hidden" name="selected_folder_id" value="0"
                                            id="selected_folder_id">
                                        <a href="javascript:void(0)"
                                            onclick="addFileMode('{{ $folder->id }}', '{{ $folder->name }}')"
                                            class="btn btn-info shadow sharp mr-1 float-right hide"
                                            data-toggle="tooltip" data-placement="top" title="move"><i
                                                class="cil-folder"></i></a>


                                        <a href="javascript:void(0)" id="{{ $folder->id }}"
                                            class="collection-item file-item-action">
                                            <i
                                                class="@if ($folder->type == 1) cil-folder-open @else cil-folder @endif "></i>
                                            {{ $folder->name }}
                                            @if ($folder->status == 0)
                                                <span class="text-danger">(Draft)</span>
                                            @endif
                                            <br />
                                            <span style="font-size: 10px;color:gray">{{ $folder->count }} files</span>


                                            @php
                                                $span = 'success';

                                                if ($folder->count == 0) {
                                                    $span = 'danger';
                                                }
                                            @endphp
                                    </div>

                                    </a>
                                    <hr style="margin-top:5px;margin-bottom:5px " />
                                @endforeach
                            @else
                            @endif

                        </div>
                    </div>
                </div>
            </div>


            <div class="col-sm-3 hide">

                <div class="collection file-manager-drive mt-3" style="">


                    @if (count($fileFolder))
                        @foreach ($fileFolder as $index => $folder)
                            <a href="javascript:void(0)" id="{{ $folder->id }}"
                                class="collection-item file-item-action">
                                <div class="fonticon-wrap display-inline mr-3">

                                    <i
                                        class="@if ($folder->type == 1) cil-folder-open @else cil-folder @endif "></i>
                                    <span>{{ $folder->name }}</span>

                                </div>

                            </a>
                        @endforeach
                    @else
                    @endif

                </div>
            </div>
            <div class="col-sm-8">
                <div class="tab-content">
                    @if (count($fileFolder))

                        @foreach ($fileFolder as $index2 => $folder)
                            <div class="tab-pane-folder tab-pane-{{ $folder->id }} @if ($folder->id == 1)  @endif"
                                id="tab_{{ $folder->id }}" role="tabpanel"
                                style="max-height:700px;overflow-y:auto;@if ($folder->id != 1) display:none @endif">

                                <div class="card-header">
                                    <h4> {{ $folder->name }}</h4>
                                </div>
                                <table class="table table-striped table-bordered datatable">
                                    <thead>
                                        <tr class="text-center">
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Remark</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $count = 0;
                                        $total_count = 0;
                                        $file_count = 0;
                                        ?>
                                        @if (count($documentTemplateFilev2))
                                            @foreach ($documentTemplateFilev2 as $index => $template)
                                                <?php
                                                $total_count += 1;
                                                if (($index + 1) % 10 == 1) {
                                                    $count += 1;
                                                }
                                                ?>
                                                @if ($template->folder_id == $folder->id)
                                                  @if($template->count > 0)
                                                    <?php $file_count += 1; ?>
                                                    <tr
                                                        class="file-item filte-item-{{ $template->type }}-{{ $count }} ">
                                                        <td class="text-center">
                                                            <div class="checkbox">
                                                                <input type="checkbox" name="files"
                                                                    value="{{ $template->id }}"
                                                                    id="chk_{{ $template->id }}">
                                                                <label for="chk_{{ $template->id }}"></label>
                                                            </div>
                                                        </td>
                                                        <td>{{ $template->name }}</td>
                                                        <td>{{ $template->remarks }}</td>

                                                    </tr>
                                                  @endif
                                                   
                                                @endif
                                            @endforeach
                                        @else
                                            <tr>
                                                <td class="text-center" colspan="5">No data</td>
                                            </tr>
                                        @endif

                                    </tbody>
                                </table>


                            </div>
                        @endforeach
                    @else
                    @endif


                </div>


            </div>
        </div>

        <button class="btn btn-success float-right" onclick="generateFileFromTemplate('{{ $case->id }}')"
            type="button">
            <span id="span_upload">Generate</span>
            <div class="overlay" style="display:none">
                <i class="fa fa-refresh fa-spin"></i>
            </div>
        </button>
        <a href="javascript:void(0);" onclick="viewMode()" class="btn btn-danger">Cancel</a>
    </div>
</div>
