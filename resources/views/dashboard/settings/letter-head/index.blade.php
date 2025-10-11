@extends('dashboard.base')
<style>
    .sortable-list {
        max-width: 300px;
        margin: 0 auto;
    }

    .sortable-list ul {
        list-style-type: none;
        padding: 0;
    }

    .sortable-list li {
        background-color: #f0f0f0;
        padding: 10px;
        margin: 5px;
        cursor: grab;
    }
</style>

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-6">
                                    <h4>Letter Head - Lawyer </h4>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-lg btn-info float-right" href="javascript:void(0)"
                                        data-backdrop="static" data-keyboard="false" data-target="#modalNew"
                                        data-toggle="modal">
                                        <i class="cil-plus"> </i>Add New Lawyer
                                    </a>
                                </div>

                                <div class="col-6">
                                    <a class="btn btn-lg btn-info float-right" href="javascript:void(0)"
                                        onclick="saveList()">
                                        <i class="cil-plus"> </i>save
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">


                            <div class="box-body no-padding " style="width:100%;overflow-x:auto">

                                {{-- <div class="sortable-list">
                                    <ul id="sortable">
                                        <li draggable="true">Item 1</li>
                                        <li draggable="true">Item 2</li>
                                        <li draggable="true">Item 3</li>
                                    </ul>
                                </div> --}}

                                <div class="col-12 sortable-list">
                                    <ul class="list-group" id="sortable">

                                        <li id="link_case_user_1" class="list-group-item" draggable="true" name="2"
                                            data-id="1">
                                            Layer
                                            <a href="javascript:void(0)" onclick="removeLinkUser(1)" class=" float-right"><i
                                                    class="fa fa-close text-danger"></i></a>
                                        </li>

                                        <li id="link_case_user_2" class="list-group-item" draggable="true" name="2"
                                            data-id="2">
                                            Layer2
                                            <a href="javascript:void(0)" onclick="removeLinkUser(1)" class=" float-right"><i
                                                    class="fa fa-close text-danger"></i></a>
                                        </li>


                                        <li id="link_case_user_3" class="list-group-item" draggable="true" name="2"
                                            data-id="3">
                                            Layer3
                                            <a href="javascript:void(0)" onclick="removeLinkUser(1)" class=" float-right"><i
                                                    class="fa fa-close text-danger"></i></a>
                                        </li>


                                        <li id="link_case_user_4" class="list-group-item" draggable="true" name="2"
                                            data-id="4">
                                            Layer4
                                            <a href="javascript:void(0)" onclick="removeLinkUser(1)" class=" float-right"><i
                                                    class="fa fa-close text-danger"></i></a>
                                        </li>
                                        {{-- @foreach ($LinkCaseUser as $index => $row)
                                            <li id="link_case_user_{{ $row->id }}"
                                                class="list-group-item">
                                                {{ $row->name }}
                                                <a href="javascript:void(0)"
                                                    onclick="removeLinkUser({{ $row->id }})"
                                                    class=" float-right"><i
                                                        class="fa fa-close text-danger"></i></a>
                                            </li>
                                        @endforeach --}}
                                    </ul>
                                </div>

                                <table id="tbl-letterhead" class="table table-bordered table-striped yajra-datatable"
                                    style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>IC</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($letterHeadLawyer as $index => $obj)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $obj->parameter_value_1 }}</td>
                                                <td>{{ $obj->parameter_value_2 }}</td>
                                                <td>
                                                    <div class="btn-group  normal-edit-mode">
                                                        <button type="button" class="btn btn-info btn-flat dropdown-toggle"
                                                            data-toggle="dropdown">
                                                            <i class="cil-settings"></i>
                                                        </button>
                                                        <div class="dropdown-menu" style="padding:0">
                                                            <a class="dropdown-item btn-info" href="javascript:void(0)"
                                                                onclick="loadEditData({{ $obj->id }},'{{ $obj->parameter_value_1 }}','{{ $obj->parameter_value_2 }}')"
                                                                data-backdrop="static" data-keyboard="false"
                                                                data-target="#modalEdit" data-toggle="modal"
                                                                style="color:white;margin:0"><i style="margin-right: 10px;"
                                                                    class="cil-pencil"></i>Edit</a>
                                                            <div class="dropdown-divider" style="margin:0"></div>
                                                            <a class="dropdown-item btn-danger" href="javascript:void(0)"
                                                                onclick="deleteLetterHead({{ $obj->id }})"
                                                                style="color:white;margin:0"><i style="margin-right: 10px;"
                                                                    class="cil-x"></i>Delete</a>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('dashboard.settings.letter-head.modal.modal-new')
    @include('dashboard.settings.letter-head.modal.modal-edit')
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#tbl-letterhead').DataTable({
                pagingType: "full_numbers",
                lengthMenu: [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search records",
                }
            });

            var table = $('#datatable').DataTable();

            // Edit record
            table.on('click', '.edit', function() {
                $tr = $(this).closest('tr');

                var data = table.row($tr).data();
                alert('You press on Row: ' + data[0] + ' ' + data[1] + ' ' + data[2] + '\'s row.');
            });

            // Delete a record
            table.on('click', '.remove', function(e) {
                $tr = $(this).closest('tr');
                table.row($tr).remove().draw();
                e.preventDefault();
            });

        });

        function AddNewLawyer() {
            $link_array = '';
            console.log($link_array);

            if ($link_array != '') {
                $link_array = JSON.parse($link_array);
            } else {
                $link_array = [];
            }

            $selected_user = parseInt($('#new_user').val());

            if (!$link_array.includes($selected_user)) {
                $link_array.push($('#lawyer').val());
                $name = $('#lawyer').find(':selected').attr('data-name');

                $structure = `
                          <li id="link_case_user_` + $('#new_user').val() + `" class="list-group-item" draggable="true"  data-id="` + $('#new_user').val() + `"> ` + $name +
                            `<a href="javascript:void(0)" onclick="removeLinkUser(` + $('#new_user').val() + `)" class=" float-right"><i class="fa fa-close text-danger"></i></a>
                          </li>
                          `;



                $("#sortable").append($structure);


                toastController('User Added');
            }


        }

        function saveList() {
            var ul = document.getElementById("sortable");
            var items = ul.getElementsByTagName("li");
            for (var i = 0; i < items.length; ++i) {
                // items[i].attr('');
                console.log(items[i].getAttribute("data-id"));
            }
        }


        function SaveLetterHead() {

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/SaveLetterHead',
                data: $('#form_new').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {
                    $("#div_full_screen_loading").hide();
                    closeUniversalModal();
                    toastController(data.message);
                    location.reload();
                },
                error: function(file, response) {
                    console.log(file.responseJSON);
                    $("#div_full_screen_loading").hide();
                    toastController('Please make sure all required fields are fill', 'warning');
                }
            });
        }

        function loadEditData($id, $name, $ic_no) {
            var form = $("#form_edit");

            form.find('[name=id]').val($id);
            form.find('[name=name]').val($name);
            form.find('[name=ic_no]').val($ic_no);
        }

        function updateLetterHead() {

            $("#div_full_screen_loading").show();

            $.ajax({
                type: 'POST',
                url: '/updateLetterHead',
                data: $('#form_edit').serialize(),
                // processData: false,
                // contentType: false,
                success: function(data) {
                    $("#div_full_screen_loading").hide();
                    closeUniversalModal();
                    toastController(data.message);
                    location.reload();
                },
                error: function(file, response) {
                    console.log(file.responseJSON);
                    $("#div_full_screen_loading").hide();
                    toastController('Please make sure all required fields are fill', 'warning');
                }
            });
        }

        function deleteLetterHead($id) {

            $("#div_full_screen_loading").show();

            Swal.fire({
                icon: 'warning',
                title: 'Delete this record?',
                showCancelButton: true,
                confirmButtonText: `Yes`,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/deleteLetterHead/' + $id,
                        data: $('#form_edit').serialize(),
                        // processData: false,
                        // contentType: false,
                        success: function(data) {
                            $("#div_full_screen_loading").hide();
                            closeUniversalModal();
                            toastController(data.message);
                            location.reload();
                        },
                        error: function(file, response) {
                            console.log(file.responseJSON);
                            $("#div_full_screen_loading").hide();
                            toastController('Please make sure all required fields are fill', 'warning');
                        }
                    });
                }
            })


        }

        const sortableList =
            document.getElementById("sortable");
        let draggedItem = null;

        sortableList.addEventListener(
            "dragstart",
            (e) => {
                draggedItem = e.target;
                setTimeout(() => {
                    e.target.style.display =
                        "none";
                }, 0);
            });

        sortableList.addEventListener(
            "dragend",
            (e) => {
                setTimeout(() => {
                    e.target.style.display = "";
                    draggedItem = null;
                }, 0);
            });

        sortableList.addEventListener(
            "dragover",
            (e) => {
                e.preventDefault();
                const afterElement =
                    getDragAfterElement(
                        sortableList,
                        e.clientY);
                const currentElement =
                    document.querySelector(
                        ".dragging");
                if (afterElement == null) {
                    sortableList.appendChild(
                        draggedItem
                    );
                } else {
                    sortableList.insertBefore(
                        draggedItem,
                        afterElement
                    );
                }
            });

        const getDragAfterElement = (
            container, y
        ) => {
            const draggableElements = [
                ...container.querySelectorAll(
                    "li:not(.dragging)"
                ),
            ];

            return draggableElements.reduce(
                (closest, child) => {
                    const box =
                        child.getBoundingClientRect();
                    const offset =
                        y - box.top - box.height / 2;
                    if (
                        offset < 0 &&
                        offset > closest.offset) {
                        return {
                            offset: offset,
                            element: child,
                        };
                    } else {
                        return closest;
                    }
                }, {
                    offset: Number.NEGATIVE_INFINITY,
                }
            ).element;
        };
    </script>
@endsection
