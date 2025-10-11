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

    .branch_selection .active .card {
        background-color: gray;
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

                            </div>
                        </div>
                        <div class="card-body">


                            <div class="box-body no-padding " >

                                <div class="row branch_selection">

                                    @foreach ($branchList as $index => $branch)
                                        <div id="btn_branch_{{ $branch->id }}" class="col-sm-6 col-lg-2 btn_branch_all"
                                            onclick="filterBranch({{ $branch->id }})">
                                            <div class="card ">
                                                <div class="card-body text-center">
                                                    {{ $branch->name }}

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="col-12 ">
                                    <ul class="list-group" id="sortable">

                                        @foreach ($lawyers as $index => $lawyer)
                                            <li id="lawyer_{{ $lawyer->id }}"
                                                class="list-group-item branch_all branch_{{ $lawyer->branch_id }}"
                                                name="2" data-id="{{ $lawyer->id }}">
                                                {{ $lawyer->name }}
                                                <hr />
                                                <b>Short Code:</b> {{$lawyer_ic_short_code->parameter_value_1}}{{ $lawyer->id }} <a
                                                    href="javascript:void(0)" onclick="copyContent('{{$lawyer_ic_short_code->parameter_value_1}}{{ $lawyer->id }}')"
                                                    class="btn btn-xs btn-primary"><i class="cil-copy"></i></a>
                                                <br />
                                                <b>IC Display Name:</b>
                                                @if (isset($lawyer->ic_name))
                                                    {{ $lawyer->ic_name }}
                                                @else
                                                    -
                                                @endif


                                            </li>
                                        @endforeach


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

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script type="text/javascript">
        $(document).ready(function() {
            filterBranch({{ $current_user->branch_id }});
        });

        function copyContent(content) {
            navigator.clipboard.writeText("${" + content + "}");

            toastController('Short code copied');
        }

        function filterBranch(id) {
            $(".branch_all").hide();
            $(".branch_" + id).show();


            $(".btn_branch_all").removeClass('active');
            $("#btn_branch_" + id).addClass('active');

        }

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
                          <li id="link_case_user_` + $('#new_user').val() +
                    `" class="list-group-item" draggable="true"  data-id="` + $('#new_user').val() + `"> ` + $name +
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
