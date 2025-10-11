<div id="modalAddBillto" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg" style="width: 90% !important;max-width: 90% !important;">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1" id="lbl_title_split_inv">Add Party into Invoice</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                
                <!-- Search Section -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fa fa-search"></i> Search Billing Parties</h5>
                                <small class="text-muted">For combined search (clients + masterlist), use "Load Party Data" button below</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search_name">Name</label>
                                            <input type="text" class="form-control" id="search_name" placeholder="Search by name...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search_id_no">ID Number</label>
                                            <input type="text" class="form-control" id="search_id_no" placeholder="Search by ID number...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="search_tin">TIN</label>
                                            <input type="text" class="form-control" id="search_tin" placeholder="Search by TIN...">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-primary" onclick="searchBillingParties()">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="clearSearch()">
                                            <i class="fa fa-times"></i> Clear
                                        </button>
                                        <button type="button" class="btn btn-success" onclick="openCreatePartyModal()">
                                            <i class="fa fa-plus"></i> Create New Party
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Selected Party Display -->
                <div class="row mt-3" id="selectedPartySection" style="display: none;">
                    <div class="col-12">
                        <div class="card border-success">
                            <div class="card-header bg-success text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fa fa-check"></i> Selected Party</h5>
                                    <button type="button" class="btn btn-light btn-sm" onclick="AddBilltoInvoice()" id="btnAddSelectedParty">
                                        <i class="fa fa-plus"></i> Add This Party
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Name:</strong> <span id="selectedPartyName"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>ID Number:</strong> <span id="selectedPartyIdNo"></span>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-6">
                                        <strong>TIN:</strong> <span id="selectedPartyTin"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Customer Code:</strong> <span id="selectedPartyCode"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fa fa-list"></i> Billing Parties</h5>
                                <small class="text-muted">Click on a party to select it</small>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover" id="billingPartiesTable">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>Select</th>
                                                <th>Name</th>
                                                <th>ID Number</th>
                                                <th>TIN</th>
                                                <th>Customer Code</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                            </tr>
                                        </thead>
                                        <tbody id="billingPartiesTableBody">
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    <i class="fa fa-search"></i> Use the search above to find billing parties
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div id="billingPartiesPagination" class="d-flex justify-content-center mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Create New Party Section (Hidden by default) -->
                <div class="row mt-3" id="createPartySection" style="display: none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fa fa-plus"></i> Create New Billing Party</h5>
                                    <button type="button" class="btn btn-info btn-sm" onclick="openClientSelector()">
                                        <i class="fa fa-database"></i> Load Party Data
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="formCreateParty">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_name">Customer Name<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_code">Customer Code<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="customer_code" name="customer_code" readonly placeholder="Auto-generated">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="customer_category">Customer Category<span class="text-danger">*</span></label>
                                                <select class="form-control" id="customer_category" name="customer_category" required>
                                                    <option value="">Select Category</option>
                                                    <option value="1">Individual</option>
                                                    <option value="2">Company</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tin">TIN</label>
                                                <input type="text" class="form-control" id="tin" name="tin">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="brn">BRN</label>
                                                <input type="text" class="form-control" id="brn" name="brn">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="brn2">BRN2</label>
                                                <input type="text" class="form-control" id="brn2" name="brn2">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sales_tax_no">Sales Tax No</label>
                                                <input type="text" class="form-control" id="sales_tax_no" name="sales_tax_no">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="service_tax_no">Service Tax No</label>
                                                <input type="text" class="form-control" id="service_tax_no" name="service_tax_no">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_type">ID Type<span class="text-danger">*</span></label>
                                                <select class="form-control" id="id_type" name="id_type" required>
                                                    <option value="">Select ID Type</option>
                                                    <option value="1">New Reg No</option>
                                                    <option value="2">NRIC</option>
                                                    <option value="3">Passport</option>
                                                    <option value="4">ARMY</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="id_no">ID No</label>
                                                <input type="text" class="form-control" id="id_no" name="id_no">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address_1">Address Line 1</label>
                                                <input type="text" class="form-control" id="address_1" name="address_1">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address_2">Address Line 2</label>
                                                <input type="text" class="form-control" id="address_2" name="address_2">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address_3">Address Line 3</label>
                                                <input type="text" class="form-control" id="address_3" name="address_3">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address_4">Address Line 4</label>
                                                <input type="text" class="form-control" id="address_4" name="address_4">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="postcode">Postcode</label>
                                                <input type="text" class="form-control" id="postcode" name="postcode">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="city">City</label>
                                                <input type="text" class="form-control" id="city" name="city">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="state">State</label>
                                                <input type="text" class="form-control" id="state" name="state">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="country">Country</label>
                                                <select class="form-control" id="country" name="country">
                                                    <option value="">Select Country</option>
                                                    <option value="MY" selected>Malaysia</option>
                                                    <option value="SG">Singapore</option>
                                                    <option value="ID">Indonesia</option>
                                                    <option value="TH">Thailand</option>
                                                    <option value="PH">Philippines</option>
                                                    <option value="VN">Vietnam</option>
                                                    <option value="BN">Brunei</option>
                                                    <option value="MM">Myanmar</option>
                                                    <option value="LA">Laos</option>
                                                    <option value="KH">Cambodia</option>
                                                    <option value="US">United States</option>
                                                    <option value="GB">United Kingdom</option>
                                                    <option value="AU">Australia</option>
                                                    <option value="CN">China</option>
                                                    <option value="JP">Japan</option>
                                                    <option value="KR">South Korea</option>
                                                    <option value="IN">India</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone1">Phone</label>
                                                <input type="text" class="form-control" id="phone1" name="phone1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="mobile">Mobile</label>
                                                <input type="text" class="form-control" id="mobile" name="mobile">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fax1">Fax 1</label>
                                                <input type="text" class="form-control" id="fax1" name="fax1">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="fax2">Fax 2</label>
                                                <input type="text" class="form-control" id="fax2" name="fax2">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input type="email" class="form-control" id="email" name="email">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Selector Section (Hidden by default) -->
                <div class="row mt-3" id="clientSelectorSection" style="display: none;">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fa fa-database"></i> Load Party Data</h5>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="switchToCreateMode()">
                                        <i class="fa fa-arrow-left"></i> Back to Create Form
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                
                                <!-- Source Filter Section -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="fa fa-filter"></i> Data Source Filter</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label>Search in:</label>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="source_filter" id="source_both" value="both" checked>
                                                                <label class="form-check-label" for="source_both">
                                                                    <i class="fa fa-database text-primary"></i> Both (Clients & Masterlist)
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="source_filter" id="source_client" value="client">
                                                                <label class="form-check-label" for="source_client">
                                                                    <i class="fa fa-users text-info"></i> Clients Only
                                                                </label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="radio" name="source_filter" id="source_masterlist" value="masterlist">
                                                                <label class="form-check-label" for="source_masterlist">
                                                                    <i class="fa fa-list text-success"></i> Masterlist Only
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Combined Search Section -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="fa fa-search"></i> Search Parties</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="party_search">Search</label>
                                                            <input type="text" class="form-control" id="party_search" placeholder="Search by name, ID number, or company number...">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="button" class="btn btn-primary" onclick="searchParties()">
                                                            <i class="fa fa-search"></i> Search
                                                        </button>
                                                        <button type="button" class="btn btn-secondary" onclick="clearPartySearch()">
                                                            <i class="fa fa-times"></i> Clear
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Combined Results Section -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0"><i class="fa fa-list"></i> Search Results</h6>
                                                <small class="text-muted">Click on a party to load their data</small>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-hover" id="partiesTable">
                                                        <thead class="thead-dark">
                                                            <tr>
                                                                <th width="50px">Select</th>
                                                                <th>Name</th>
                                                                <th>ID/Company No</th>
                                                                <th>Source</th>
                                                                <th>Type</th>
                                                                <th>Contact</th>
                                                                <th>Case Ref</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="partiesTableBody">
                                                            <tr>
                                                                <td colspan="7" class="text-center text-muted">
                                                                    <i class="fa fa-search"></i> Use the search above to find parties
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div id="partiesPagination" class="d-flex justify-content-center mt-3"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default" data-dismiss="modal">Close</button>
                
                <!-- Search Mode Buttons -->
                <div id="searchModeButtons">
                    <button type="button" id="btnAddBilltoParty" class="btn inv-btn add-party-invoice btn-danger float-right" onclick="AddBilltoInvoice()" disabled>
                        <i class="fa fa-plus"></i> Add Selected Party
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                    <button type="button" id="btnSplitInvoice" class="btn inv-btn split-invoice btn-danger float-right" onclick="SplitInvoice()">
                        Split Invoice
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                </div>
                
                <!-- Create Mode Buttons -->
                <div id="createModeButtons" style="display: none;">
                    <button type="button" class="btn btn-secondary float-left" onclick="switchToSearchMode()">
                        <i class="fa fa-arrow-left"></i> Back to Search
                    </button>
                    <button type="button" class="btn btn-primary float-right" onclick="createNewParty()">
                        <i class="fa fa-save"></i> Create Party
                        <div class="overlay" style="display:none">
                            <i class="fa fa-refresh fa-spin"></i>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    $invoice_id = '';
    let selectedParty = null;
    let currentPage = 1;
    let searchParams = {};

    function SplitInvoiceMode() {
        $(".inv-btn").hide();
        $(".split-invoice").show();
        $("#lbl_title_split_inv").html("Split Invoice");
    }

    function AddPartyInvoiceMode(invoiceId) {
        $(".inv-btn").hide();
        $(".add-party-invoice").show();
        $invoice_id = invoiceId;
        $("#lbl_title_split_inv").html("Add party into Invoice");
        
        // Reset modal to search mode
        switchToSearchMode();
        
        // Initialize search when opening for party selection
        clearSearch();
    }

    function searchBillingParties(page = 1) {
        currentPage = page;
        
        // Get search parameters
        searchParams = {
            name: $('#search_name').val(),
            id_no: $('#search_id_no').val(),
            tin: $('#search_tin').val(),
            page: page
        };

        // Show loading
        $('#billingPartiesTableBody').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

        $.ajax({
            url: '/api/billing-parties/search',
            type: 'GET',
            data: searchParams,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 1) {
                    displayBillingParties(response.data);
                    displayPagination(response.pagination);
                } else {
                    $('#billingPartiesTableBody').html('<tr><td colspan="7" class="text-center text-danger">' + response.message + '</td></tr>');
                }
            },
            error: function() {
                $('#billingPartiesTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading billing parties</td></tr>');
            }
        });
    }

    function displayBillingParties(parties) {
        let html = '';
        
        if (parties.length === 0) {
            html = '<tr><td colspan="7" class="text-center text-muted">No billing parties found</td></tr>';
        } else {
            parties.forEach(function(party) {
                html += `
                    <tr class="party-row" data-party-id="${party.id}" onclick="selectBillingParty(${party.id})" style="cursor: pointer;">
                        <td class="text-center" style="width: 50px;">
                            <input type="radio" name="selected_party" value="${party.id}" class="party-radio" style="cursor: pointer; transform: scale(1.2);">
                        </td>
                        <td>${party.customer_name || 'N/A'}</td>
                        <td>${party.id_no || 'N/A'}</td>
                        <td>${party.tin || 'N/A'}</td>
                        <td>${party.customer_code || 'N/A'}</td>
                        <td>${party.email || 'N/A'}</td>
                        <td>${party.phone || 'N/A'}</td>
                    </tr>
                `;
            });
        }
        
        $('#billingPartiesTableBody').html(html);
    }

    function displayPagination(pagination) {
        if (!pagination || pagination.total_pages <= 1) {
            $('#billingPartiesPagination').html('');
            return;
        }

        let html = '<ul class="pagination">';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchBillingParties(${pagination.current_page - 1})">Previous</a></li>`;
        }
        
        // Page numbers with smart pagination (show max 10 pages)
        let startPage = Math.max(1, pagination.current_page - 4);
        let endPage = Math.min(pagination.total_pages, startPage + 9);
        
        // Adjust start page if we're near the end
        if (endPage - startPage < 9) {
            startPage = Math.max(1, endPage - 9);
        }
        
        // First page if not in range
        if (startPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchBillingParties(1)">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        // Page numbers in range
        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchBillingParties(${i})">${i}</a></li>`;
            }
        }
        
        // Last page if not in range
        if (endPage < pagination.total_pages) {
            if (endPage < pagination.total_pages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchBillingParties(${pagination.total_pages})">${pagination.total_pages}</a></li>`;
        }
        
        // Next button
        if (pagination.current_page < pagination.total_pages) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchBillingParties(${pagination.current_page + 1})">Next</a></li>`;
        }
        
        html += '</ul>';
        $('#billingPartiesPagination').html(html);
    }

    function selectBillingParty(partyId) {
        // Remove previous selection
        $('.party-row').removeClass('table-active');
        $('.party-radio').prop('checked', false);
        
        // Select current party
        $(`tr[data-party-id="${partyId}"]`).addClass('table-active');
        $(`input[value="${partyId}"]`).prop('checked', true);
        
        // Get party details and display
        $.ajax({
            url: '/api/billing-parties/' + partyId,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 1) {
                    selectedParty = response.data;
                    displaySelectedParty(response.data);
                    moveSelectedPartyToTop(partyId);
                    $('#btnAddBilltoParty').prop('disabled', false);
                    $('#btnAddSelectedParty').prop('disabled', false);
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to get party details', 'error');
            }
        });
    }

    function displaySelectedParty(party) {
        $('#selectedPartyName').text(party.customer_name || 'N/A');
        $('#selectedPartyIdNo').text(party.id_no || 'N/A');
        $('#selectedPartyTin').text(party.tin || 'N/A');
        $('#selectedPartyCode').text(party.customer_code || 'N/A');
        $('#selectedPartySection').show();
        
        // Enable the "Add This Party" button
        $('#btnAddSelectedParty').prop('disabled', false);
    }

    function moveSelectedPartyToTop(partyId) {
        // Find the selected party row
        var selectedRow = $(`tr[data-party-id="${partyId}"]`);
        
        if (selectedRow.length > 0) {
            // Get the table body
            var tableBody = $('#billingPartiesTableBody');
            
            // Move the selected row to the top
            tableBody.prepend(selectedRow);
            
            // Add highlight animation
            selectedRow.addClass('highlight-selected');
            
            // Remove highlight after animation
            setTimeout(function() {
                selectedRow.removeClass('highlight-selected');
            }, 2000);
            
            // Scroll to the top of the table to show the selected party
            var modalBody = $('#modalAddBillto .modal-body');
            modalBody.scrollTop(0);
        }
    }

    function clearSearch() {
        $('#search_name').val('');
        $('#search_id_no').val('');
        $('#search_tin').val('');
        searchParams = {};
        $('#billingPartiesTableBody').html('<tr><td colspan="7" class="text-center text-muted"><i class="fa fa-search"></i> Use the search above to find billing parties</td></tr>');
        $('#billingPartiesPagination').html('');
        $('#selectedPartySection').hide();
        selectedParty = null;
        $('#btnAddBilltoParty').prop('disabled', true);
        $('#btnAddSelectedParty').prop('disabled', true);
        
        // Reset table scroll position
        var modalBody = $('#modalAddBillto .modal-body');
        modalBody.scrollTop(0);
    }

    @if(isset($case))
    function SplitInvoice() {
        Swal.fire({
            icon: 'warning',
            text: 'Split Invoice',
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $("#div_full_screen_loading").show();

                var form_data = new FormData();
                form_data.append("bill_to", selectedParty ? selectedParty.customer_name : "");
                form_data.append("case_id", {{ $case->id }});

                $.ajax({
                    type: 'POST',
                    url: '/splitInvoice/' + $("#selected_bill_id").val(),
                    data: form_data,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        console.log(data);
                        $("#div_full_screen_loading").hide();
                        if (data.status == 1) {
                            toastController(data.message);
                            location.reload();
                        } else {
                            toastController(data.message, 'warning');
                        }
                    },
                    error: function(file, response) {
                        $("#div_full_screen_loading").hide();
                    }
                });
            }
        })
    }

    function AddBilltoInvoice() {
        if (!selectedParty) {
            Swal.fire('Error', 'No party selected', 'error');
            return;
        }

        var form_data = new FormData();
        form_data.append("bill_to", selectedParty.customer_name);
        form_data.append("case_id", {{ $case->id }});
        form_data.append("invoice_id", $invoice_id);
        form_data.append("bill_to_type", "existing_party");
        form_data.append("billing_party_id", selectedParty.id);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: '/AddBilltoInvoice/' + $("#selected_bill_id").val(),
            data: form_data,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log(data);
                if (data.status == 1) {
                    toastController(data.message);
                    closeUniversalModal();
                    location.reload();
                } else {
                    toastController(data.message, 'warning');
                }
            }
        });
    }
    @endif

    function removeBillto($id) {
        var form_data = $("#formAddBilltoInfo").serialize();

        Swal.fire({
            icon: 'warning',
            title: 'Remove this client from this invoice?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/removeBillto/' + $id,
                    data: form_data,
                    success: function(data) {
                        console.log(data);
                        if (data.status == 1) {
                            toastController('deleted');
                            location.reload();
                        } else {
                            toastController(data.message, 'warning');
                        }
                    }
                });
            }
        })
    }

    function removeInvoice($id) {
        var form_data = $("#formAddBilltoInfo").serialize();

        Swal.fire({
            icon: 'warning',
            title: 'Remove this invoice?',
            showCancelButton: true,
            confirmButtonText: `Yes`,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: '/removeInvoice/' + $id,
                    data: form_data,
                    success: function(data) {
                        console.log(data);
                        if (data.status == 1) {
                            toastController('deleted');
                            $("#lbl_bill_to_party").html(data.view);
                            location.reload();
                        } else {
                            toastController(data.message, 'warning');
                        }
                    }
                });
            }
        })
    }

    // Auto-search on input change (with debounce)
    let searchTimeout;
    $('#search_name, #search_id_no, #search_tin').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            searchBillingParties(1);
        }, 500);
    });

    // Initialize modal
    $('#modalAddBillto').on('shown.bs.modal', function() {
        // Reset to search mode and clear search
        switchToSearchMode();
        clearSearch();
    });

    // Handle modal backdrop issues
    $(document).on('hidden.bs.modal', function() {
        // Remove any lingering backdrop
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });

    // Add CSS for better styling
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .party-row:hover {
                background-color: #f8f9fa !important;
            }
            .party-row.table-active {
                background-color: #e3f2fd !important;
            }
            .party-radio {
                margin: 0 !important;
            }
            .party-radio:checked {
                background-color: #007bff !important;
                border-color: #007bff !important;
            }
            .client-row:hover {
                background-color: #f8f9fa !important;
            }
            .client-row.table-active {
                background-color: #e3f2fd !important;
            }
            .client-radio {
                margin: 0 !important;
                cursor: pointer;
                transform: scale(1.2);
            }
            .client-radio:checked {
                background-color: #007bff !important;
                border-color: #007bff !important;
            }
            .highlight-selected {
                animation: highlightPulse 2s ease-in-out;
            }
            @keyframes highlightPulse {
                0% { background-color: #fff3cd !important; }
                50% { background-color: #ffeaa7 !important; }
                100% { background-color: #e3f2fd !important; }
            }
        `)
        .appendTo('head');

    function openCreatePartyModal() {
        // Switch to create mode within the same modal
        switchToCreateMode();
    }

    function switchToCreateMode() {
        // Hide all other sections
        $('.card:has(#search_name)').parent().hide();
        $('.card:has(#billingPartiesTable)').parent().hide();
        $('#selectedPartySection').hide();
        $('#clientSelectorSection').hide();
        
        // Disable the "Add This Party" button
        $('#btnAddSelectedParty').prop('disabled', true);
        
        // Show create section
        $('#createPartySection').show();
        
        // Switch buttons
        $('#searchModeButtons').hide();
        $('#createModeButtons').show();
        
        // Update modal title
        $('#lbl_title_split_inv').html('Create New Billing Party');
        
        // Focus on first input
        setTimeout(function() {
            $('#customer_name').focus();
        }, 100);
    }

    function switchToSearchMode() {
        // Hide all other sections
        $('#createPartySection').hide();
        $('#clientSelectorSection').hide();
        
        // Show search sections
        $('.card:has(#search_name)').parent().show();
        $('.card:has(#billingPartiesTable)').parent().show();
        
        // Switch buttons
        $('#createModeButtons').hide();
        $('#searchModeButtons').show();
        
        // Update modal title
        $('#lbl_title_split_inv').html('Add Party into Invoice');
        
        // Clear create form
        $('#formCreateParty')[0].reset();
    }

    // Client Selector Functions
    function openClientSelector() {
        console.log('openClientSelector called');
        // Switch to client selector mode
        switchToClientSelectorMode();
    }

    function switchToClientSelectorMode() {
        console.log('Switching to client selector mode...');
        
        // Hide all other sections
        $('.card:has(#search_name)').parent().hide();
        $('.card:has(#billingPartiesTable)').parent().hide();
        $('#selectedPartySection').hide();
        $('#createPartySection').hide();
        
        // Disable the "Add This Party" button
        $('#btnAddSelectedParty').prop('disabled', true);
        
        // Show client selector section
        $('#clientSelectorSection').show();
        console.log('Client selector section shown:', $('#clientSelectorSection').is(':visible'));
        
        // Hide all buttons
        $('#searchModeButtons').hide();
        $('#createModeButtons').hide();
        
        // Update modal title
        $('#lbl_title_split_inv').html('Select Client to Load Data');
        
        // Focus on first search field
        setTimeout(function() {
            $('#client_search_name').focus();
            console.log('Focused on client search name field');
        }, 100);
    }

    function searchParties(page = 1) {
        console.log('searchParties called with page:', page);
        
        // Get search parameters
        var searchParams = {
            search: $('#party_search').val(),
            source_filter: $('input[name="source_filter"]:checked').val(),
            page: page
        };

        console.log('Search parameters:', searchParams);

        // Show loading
        $('#partiesTableBody').html('<tr><td colspan="7" class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</td></tr>');

        $.ajax({
            url: '/api/parties/search',
            type: 'GET',
            data: searchParams,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Party search response:', response);
                console.log('Response data type:', typeof response.data);
                console.log('Response data is array:', Array.isArray(response.data));
                console.log('Response data:', response.data);
                
                if (response.status === 1) {
                    displayParties(response.data);
                    displayPartyPagination(response.pagination);
                } else {
                    $('#partiesTableBody').html('<tr><td colspan="7" class="text-center text-danger">' + response.message + '</td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Party search error:', xhr, status, error);
                $('#partiesTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading parties</td></tr>');
            }
        });
    }

    function displayParties(parties) {
        let html = '';
        
        // Ensure parties is always an array
        if (!Array.isArray(parties)) {
            console.error('displayParties: parties is not an array:', parties);
            parties = [];
        }
        
        if (parties.length === 0) {
            html = '<tr><td colspan="7" class="text-center text-muted">No parties found</td></tr>';
        } else {
            parties.forEach(function(party) {
                // Determine source badge and color
                let sourceBadge = '';
                if (party.source === 'client') {
                    sourceBadge = '<span class="badge badge-info"><i class="fa fa-users"></i> Client</span>';
                } else if (party.source === 'masterlist') {
                    sourceBadge = '<span class="badge badge-success"><i class="fa fa-list"></i> Masterlist</span>';
                }
                
                // Determine contact info
                let contact = '';
                if (party.phone1) contact += party.phone1;
                if (party.email) contact += (contact ? '<br>' : '') + party.email;
                if (!contact) contact = 'N/A';
                
                // Determine ID/Company number
                let idNumber = party.id_no || party.account_no || 'N/A';
                
                // Determine party type
                let partyType = party.party_type || party.client_type || 'N/A';
                if (party.client_type === 'individual') partyType = 'Individual';
                if (party.client_type === 'company') partyType = 'Company';
                
                html += `
                    <tr class="party-row" data-party-source="${party.source}" data-party-id="${party.id}" onclick="selectParty('${party.source}', ${party.id}, '${party.party_type || ''}', '${party.party_category || ''}')">
                        <td class="text-center">
                            <input type="radio" name="selected_party" value="${party.id}" class="party-radio" style="cursor: pointer; transform: scale(1.2);">
                        </td>
                        <td><strong>${party.name || 'N/A'}</strong></td>
                        <td>${idNumber}</td>
                        <td>${sourceBadge}</td>
                        <td>${partyType}</td>
                        <td>${contact}</td>
                        <td>${party.case_ref_no || 'N/A'}</td>
                    </tr>
                `;
            });
        }
        
        $('#partiesTableBody').html(html);
    }

    function displayPartyPagination(pagination) {
        if (!pagination || pagination.total_pages <= 1) {
            $('#partiesPagination').html('');
            return;
        }

        let html = '<ul class="pagination">';
        
        // Previous button
        if (pagination.current_page > 1) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchParties(${pagination.current_page - 1})">Previous</a></li>`;
        }
        
        // Page numbers with smart pagination
        let startPage = Math.max(1, pagination.current_page - 4);
        let endPage = Math.min(pagination.total_pages, startPage + 9);
        
        if (endPage - startPage < 9) {
            startPage = Math.max(1, endPage - 9);
        }
        
        if (startPage > 1) {
                            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchParties(1)">1</a></li>`;
            if (startPage > 2) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }
        
        for (let i = startPage; i <= endPage; i++) {
            if (i === pagination.current_page) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchParties(${i})">${i}</a></li>`;
            }
        }
        
        if (endPage < pagination.total_pages) {
            if (endPage < pagination.total_pages - 1) {
                html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchParties(${pagination.total_pages})">${pagination.total_pages}</a></li>`;
        }
        
        if (pagination.current_page < pagination.total_pages) {
            html += `<li class="page-item"><a class="page-link" href="javascript:void(0)" onclick="searchParties(${pagination.current_page + 1})">Next</a></li>`;
        }
        
        html += '</ul>';
        $('#partiesPagination').html(html);
    }

    function selectClient(clientId) {
        // Remove previous selection
        $('.client-row').removeClass('table-active');
        $('.client-radio').prop('checked', false);
        
        // Select current client
        $(`tr[data-client-id="${clientId}"]`).addClass('table-active');
        $(`input[value="${clientId}"]`).prop('checked', true);
        
        // Load client data
        loadClientData(clientId);
    }

    function loadClientData(clientId) {
        $.ajax({
            url: '/api/clients/' + clientId + '/billing-party-data',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.status === 1) {
                    // Fill the form with client data
                    fillFormWithClientData(response.data);
                    
                    // Switch back to create mode
                    switchToCreateMode();
                    
                    // Show success message
                    Swal.fire('Success!', 'Client data loaded successfully', 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Failed to load client data', 'error');
            }
        });
    }

    function fillFormWithClientData(clientData) {
        // Fill form fields with client data
        $('#customer_name').val(clientData.customer_name || '');
        $('#customer_category').val(clientData.customer_category || '');
        $('#id_no').val(clientData.id_no || '');
        $('#tin').val(clientData.tin || '');
        $('#brn').val(clientData.brn || '');
        $('#brn2').val(clientData.brn2 || '');
        $('#sales_tax_no').val(clientData.sales_tax_no || '');
        $('#service_tax_no').val(clientData.service_tax_no || '');
        $('#email').val(clientData.email || '');
        $('#phone1').val(clientData.phone1 || '');
        $('#mobile').val(clientData.mobile || '');
        $('#fax1').val(clientData.fax1 || '');
        $('#fax2').val(clientData.fax2 || '');
        $('#address_1').val(clientData.address_1 || '');
        $('#address_2').val(clientData.address_2 || '');
        $('#address_3').val(clientData.address_3 || '');
        $('#address_4').val(clientData.address_4 || '');
        $('#postcode').val(clientData.postcode || '');
        $('#city').val(clientData.city || '');
        $('#state').val(clientData.state || '');
        $('#country').val(mapCountryNameToCode(clientData.country) || 'MY');
    }

    function clearClientSearch() {
        $('#client_search_name').val('');
        $('#client_search_ic').val('');
        $('#client_search_account').val('');
        $('#clientsTableBody').html('<tr><td colspan="7" class="text-center text-muted"><i class="fa fa-search"></i> Use the search above to find clients</td></tr>');
        $('#clientsPagination').html('');
        
        // Focus back on search field
        $('#client_search_name').focus();
    }

    function mapCountryNameToCode(countryName) {
        if (!countryName) return 'MY';
        
        const countryMap = {
            'malaysia': 'MY',
            'singapore': 'SG',
            'indonesia': 'ID',
            'thailand': 'TH',
            'philippines': 'PH',
            'vietnam': 'VN',
            'brunei': 'BN',
            'myanmar': 'MM',
            'laos': 'LA',
            'cambodia': 'KH',
            'united states': 'US',
            'united kingdom': 'GB',
            'australia': 'AU',
            'china': 'CN',
            'japan': 'JP',
            'south korea': 'KR',
            'india': 'IN'
        };
        
        return countryMap[countryName.toLowerCase()] || 'MY';
    }

    // Auto-search for parties
    let partySearchTimeout;
    $('#party_search').on('input', function() {
        clearTimeout(partySearchTimeout);
        partySearchTimeout = setTimeout(function() {
            searchParties(1);
        }, 500);
    });

    // Source filter change handler
    $('input[name="source_filter"]').on('change', function() {
        if ($('#party_search').val()) {
            searchParties(1);
        }
    });

    function selectParty(source, id, partyType, partyCategory) {
        console.log('selectParty called:', { source, id, partyType, partyCategory });
        
        // Validate parameters
        if (!source || !id) {
            console.error('Invalid parameters:', { source, id, partyType, partyCategory });
            Swal.fire('Error', 'Invalid party selection', 'error');
            return;
        }
        
        // Update radio button
        $(`input[name="selected_party"][value="${id}"]`).prop('checked', true);
        
        // Highlight selected row
        $('.party-row').removeClass('table-active');
        $(`.party-row[data-party-id="${id}"]`).addClass('table-active');
        
        // Load party data based on source
        if (source === 'client') {
            console.log('Loading client data for ID:', id);
            loadClientData(id);
        } else if (source === 'masterlist') {
            console.log('Loading masterlist data:', { id, partyType, partyCategory });
            loadMasterlistData(id, partyType, partyCategory);
        } else {
            console.error('Unknown source:', source);
            Swal.fire('Error', 'Unknown party source: ' + source, 'error');
        }
    }

    function loadClientData(clientId) {
        console.log('Loading client data for ID:', clientId);
        
        $.ajax({
            url: `/api/clients/${clientId}/billing-party-data`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Client data response:', response);
                if (response.status === 1) {
                    fillFormWithClientData(response.data);
                    switchToCreateMode();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading client data:', xhr.responseText);
                Swal.fire('Error', 'Failed to load client data', 'error');
            }
        });
    }

    function loadMasterlistData(caseId, partyType, partyCategory) {
        console.log('Loading masterlist data:', { caseId, partyType, partyCategory });
        
        $.ajax({
            url: `/api/masterlist-parties/${caseId}/${partyType}/${partyCategory}/billing-party-data`,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Masterlist data response:', response);
                if (response.status === 1) {
                    fillFormWithClientData(response.data);
                    switchToCreateMode();
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading masterlist data:', xhr.responseText);
                Swal.fire('Error', 'Failed to load masterlist data', 'error');
            }
        });
    }

    function clearPartySearch() {
        $('#party_search').val('');
        $('#partiesTableBody').html('<tr><td colspan="7" class="text-center text-muted"><i class="fa fa-search"></i> Use the search above to find parties</td></tr>');
        $('#partiesPagination').html('');
    }

    function createNewParty() {
        console.log('createNewParty called');
        
        // Get form data
        var formData = {
            customer_name: $('#customer_name').val(),
            customer_category: $('#customer_category').val(),
            id_type: $('#id_type').val(),
            id_no: $('#id_no').val(),
            tin: $('#tin').val(),
            brn: $('#brn').val(),
            brn2: $('#brn2').val(),
            sales_tax_no: $('#sales_tax_no').val(),
            service_tax_no: $('#service_tax_no').val(),
            email: $('#email').val(),
            phone1: $('#phone1').val(),
            mobile: $('#mobile').val(),
            fax1: $('#fax1').val(),
            fax2: $('#fax2').val(),
            address_1: $('#address_1').val(),
            address_2: $('#address_2').val(),
            address_3: $('#address_3').val(),
            address_4: $('#address_4').val(),
            postcode: $('#postcode').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            country: $('#country').val()
        };

        console.log('Form data to send:', formData);

        // First check for duplicates
        checkForDuplicates(formData, function() {
            // If no duplicates or user confirmed, proceed with creation
            proceedWithPartyCreation(formData);
        });
    }

    function checkForDuplicates(formData, callback) {
        $.ajax({
            url: '/api/billing-parties/check-duplicate',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                console.log('Duplicate check response:', response);
                
                if (response.status === 1 && response.has_duplicates) {
                    // Show duplicates and ask for confirmation
                    showDuplicateConfirmation(response.duplicates, callback);
                } else {
                    // No duplicates found, proceed
                    callback();
                }
            },
            error: function(xhr, status, error) {
                console.error('Duplicate check error:', xhr.responseText);
                // If duplicate check fails, proceed anyway
                callback();
            }
        });
    }

    function showDuplicateConfirmation(duplicates, callback) {
        var duplicateHtml = '<div class="text-left">';
        duplicateHtml += '<p><strong>Potential duplicate records found:</strong></p>';
        duplicateHtml += '<div class="table-responsive">';
        duplicateHtml += '<table class="table table-sm table-bordered">';
        duplicateHtml += '<thead><tr><th>Type</th><th>Value</th><th>Existing Record</th></tr></thead>';
        duplicateHtml += '<tbody>';
        
        duplicates.forEach(function(duplicate) {
            var record = duplicate.record;
            duplicateHtml += '<tr>';
            duplicateHtml += '<td><span class="badge badge-warning">' + duplicate.type + '</span></td>';
            duplicateHtml += '<td>' + duplicate.value + '</td>';
            duplicateHtml += '<td>';
            duplicateHtml += '<strong>' + record.customer_name + '</strong><br>';
            duplicateHtml += '<small>ID: ' + (record.id_no || 'N/A') + ' | TIN: ' + (record.tin || 'N/A') + '</small><br>';
            duplicateHtml += '<small>Phone: ' + (record.phone1 || 'N/A') + '</small>';
            duplicateHtml += '</td>';
            duplicateHtml += '</tr>';
        });
        
        duplicateHtml += '</tbody></table></div>';
        duplicateHtml += '<p class="text-muted">Do you want to create a new party anyway?</p>';
        duplicateHtml += '</div>';
        
        Swal.fire({
            title: 'Duplicate Records Found',
            html: duplicateHtml,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Create New Party',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            width: '600px'
        }).then((result) => {
            if (result.isConfirmed) {
                callback();
            }
        });
    }

    function proceedWithPartyCreation(formData) {
        // Show loading
        $('.btn:contains("Create Party") .overlay').show();

        $.ajax({
            url: '/api/billing-parties/create',
            type: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('.btn:contains("Create Party") .overlay').hide();
                
                if (response.status === 1) {
                    // Switch back to search mode
                    switchToSearchMode();
                    
                    // Refresh billing parties list
                    searchBillingParties(1);
                    
                    // Show success message
                    Swal.fire('Success!', 'Billing party created successfully', 'success');
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                $('.btn:contains("Create Party") .overlay').hide();
                
                console.error('Create party error:', xhr.responseText);
                
                if (xhr.status === 422) {
                    // Validation error
                    var response = JSON.parse(xhr.responseText);
                    var errorMessage = response.message || 'Validation failed';
                    if (response.errors) {
                        var errorDetails = Object.values(response.errors).flat().join('\n');
                        errorMessage += '\n\n' + errorDetails;
                    }
                    Swal.fire('Validation Error', errorMessage, 'error');
                } else {
                    Swal.fire('Error', 'Failed to create billing party', 'error');
                }
            }
        });
    }


</script>

<style>
    .party-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    
    .party-row:hover {
        background-color: #f8f9fa !important;
    }
    
    .party-row.table-active {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3;
    }
    
    .party-row.table-active:hover {
        background-color: #e3f2fd !important;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .badge-info {
        background-color: #17a2b8;
    }
    
    .badge-success {
        background-color: #28a745;
    }
    
    .form-check-inline {
        margin-right: 1rem;
    }
    
    .form-check-input:checked + .form-check-label {
        font-weight: bold;
    }
</style>