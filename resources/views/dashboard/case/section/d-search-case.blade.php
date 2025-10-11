<div class="row">
    <div class="col-12 ">
        <div class="accordion card" id="accordion" role="tablist">
            <div class="card-header" id="headingOne" role="tab">
                <h5 class="mb-0">Advance Search <a data-toggle="collapse" href="#collapseOne" aria-expanded="true"
                        aria-controls="collapseOne">[Expand/Collapse]</a>
                </h5>
            </div>
            <div class="collapse show" id="collapseOne" role="tabpanel" aria-labelledby="headingOne"
                data-parent="#accordion">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6 " style="border-right: solid 1px #d8dbe0">
                            <form id="form_filter_case">
                                <div class="row">
                                    <div class="col-12">
                                        <h5 class="mb-0">Filter By</h5>
                                        <hr />
                                    </div>

                                    @if (in_array($current_user->menuroles, ['admin', 'management', 'sales', 'account', 'receptionist']) ||
                                    in_array($current_user->id, [14,102]))

                                        <div class="col-6 col-xl-6">
                                            <div class="form-group ">
                                                <label>Referral</label>

                                                <select id="ddl-referral" class="form-control" name="ddl-referral"
                                                    required>
                                                    <option value="0">-- All --</option>
                                                    @foreach ($referralList as $index => $referral)
                                                        <option value="{{ $referral->id }}">
                                                            {{ $referral->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    @if (in_array($current_user->menuroles, ['admin', 'management', 'sales', 'account']) ||
                                    in_array($current_user->id, [14]))

                                        <div class="col-6 col-xl-6">
                                            <div class="form-group ">
                                                <label>Lawyer</label>

                                                <select id="ddl-lawyer" class="form-control" name="ddl-lawyer" required>
                                                    <option value="0">-- All --</option>
                                                    @foreach ($lawyerList as $index => $lawyers)
                                                        <option value="{{ $lawyers->id }}">
                                                            {{ $lawyers->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="col-6 col-xl-6">
                                            <div class="form-group ">
                                                <label>Clerk</label>

                                                <select id="ddl-clerk" class="form-control" name="ddl-clerk" required>
                                                    <option value="0">-- All --</option>
                                                    @foreach ($clerkList as $index => $clerk)
                                                        <option value="{{ $clerk->id }}">
                                                            {{ $clerk->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        

                                        <div class="col-6">
                                            <div class="form-group row">
                                                <div class="col">
                                                    <label>Filter by Branch</label>
                                                    <select class="form-control" id="branch" name="branch">
                                                        <option value="0">-- All --
                                                        </option>
                                                        @foreach ($branchs as $branch)
                                                            <option value="{{ $branch->id }}">
                                                                {{ $branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if (in_array($current_user->menuroles, ['admin', 'management', 'account']) || in_array($current_user->id, [80,32]))
                                        <div class="col-6 col-xl-6">
                                            <div class="form-group ">
                                                <label>Sales</label>

                                                <select id="ddl-sales" class="form-control" name="ddl-sales" required>
                                                    <option value="0">-- All --</option>
                                                    @foreach ($salesList as $index => $sales)
                                                        <option value="{{ $sales->id }}">
                                                            {{ $sales->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- <div class="col-6 col-xl-6">
                                        <div class="form-group ">
                                            <label>Status</label>

                                            <select id="ddl_status" class="form-control" name="ddl_status">
                                                <option value="">-- All --</option>
                                                <option value="2">Open</option>
                                                <option value="1">Running</option>
                                                <option value="3">KIV</option>
                                            </select>
                                        </div>
                                    </div> --}}


                                    <div class="col-6 col-xl-6">
                                        <div class="form-group ">
                                            <label>Case Type</label>

                                            <select id="ddl_portfolio" class="form-control" name="ddl_portfolio"
                                                required>
                                                <option value="0">-- All --</option>
                                                @foreach ($portfolio as $index => $port)
                                                    <option value="{{ $port->id }}">
                                                        {{ $port->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6 col-xl-6">
                                        <div class="form-group ">
                                            <label>Year</label>
                                            <select id="ddl_year" class="form-control" name="ddl_year" required>
                                                <option value="0">-- All --</option>
                                                <option value="2022">2022</option>
                                                <option value='2023'>2023</option>
                                                <option value='2024'>2024</option>
                                                <option value='2025'>2025</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-6 col-xl-6">
                                        <div class="form-group ">
                                            <label>Month</label>
                                            <select id="ddl_month" class="form-control" name="ddl_month" required>
                                                <option value="0">-- All --</option>
                                                <option value="1">January</option>
                                                <option value='2'>February</option>
                                                <option value='3'>March</option>
                                                <option value='4'>April</option>
                                                <option value='5'>May</option>
                                                <option value='6'>June</option>
                                                <option value='7'>July</option>
                                                <option value='8'>August</option>
                                                <option value='9'>September</option>
                                                <option value='10'>October</option>
                                                <option value='11'>November</option>
                                                <option value='12'>December</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-12">
                                        <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)"
                                            onclick="filterCase();">
                                            <i class="fa cil-search"> </i>Filter
                                        </a>
                                    </div>

                            </form>
                        </div>
                    </div>

                    <div class="col-sm-6 col-md-10 col-lg-8 col-xl-6  ">
                        <div class="row">

                            <div class="col-12">
                                <h5 class="mb-0">Search Cases</h5>
                                <hr />
                            </div>
                            <div class="col-12 col-xl-12">
                                <div class="form-group ">
                                    <label>Search By (Parties, case ref no, Bill No, Invoice No) </label>
                                    <input class="form-control" id="parties_name" type="text"
                                        name="parties_name">

                                </div>
                            </div>



                            <div class="col-sm-6">

                                <label class="float-left"><b> Case(s) found: </b><span id="case_count">0</span>
                                    </Span>
                            </div>


                            <div class="col-sm-6">

                                <a class="btn btn-lg btn-success  float-right" href="javascript:void(0)"
                                    onclick="searchCaseAll();">
                                    <i class="fa cil-search"> </i>Search
                                </a>
                            </div>

                            <div class="col-12" style="width:100%;max-height:300px;overflow:scroll">
                                <table class="table table-bordered table-striped ">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Case Number <a href=""
                                                    class="btn btn-info btn-xs rounded shadow  mr-1"
                                                    data-toggle="tooltip" data-placement="top"
                                                    title="Sales/Lawyer/Bank/Running No/Client/Clerk">?</a>
                                            </th>
                                            {{-- <th>Client</th> --}}
                                            <th>PIC</th>
                                            {{-- <th>Open file</th>
                                            <th>SPA Date</th>
                                            <th>Completion Date</th> --}}
                                            <th>Status</th>
                                            <th>Branch</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tbl-search_case">
                                        <tr>
                                            <td class="text-center" colspan="4">No result</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<script>
    function searchCaseAll() {
        var formData = new FormData();

        if ($('#case_ref_no_search').val() == '') {
            return;
        }

        formData.append('parties', $("#parties_name").val());

        $.ajax({
            type: 'POST',
            url: '/getSearchCase',
            data: formData,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log(data);

                $('#tbl-search_case').html(data.view);
                $("#case_count").html(data.case_count);
                // $('ul.pagination').replaceWith(data.links);
            }
        });
    }

    function filterCase() {
        $("#parties_name").val('');
        reloadTable();
    }
</script>
