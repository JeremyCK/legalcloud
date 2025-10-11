@extends('dashboard.base')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
<style>
.sortable-helper {
    background: white !important;
    border: 2px dashed #007bff !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
}

.sortable-placeholder {
    background: #f8f9fa !important;
    border: 2px dashed #dee2e6 !important;
    height: 60px !important;
}

.drag-handle {
    cursor: move;
    color: #6c757d;
    margin-right: 8px;
}

.drag-handle:hover {
    color: #007bff;
}

.table-row-item {
    transition: background-color 0.2s ease;
}

.table-row-item:hover {
    background-color: #f8f9fa !important;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="fade-in">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">
                            <i class="fas fa-edit mr-2"></i>
                            Update Quotation Template
                        </h1>
                        <p class="text-muted">Edit template details and manage account items</p>
                    </div>
                    <div>
                        <a href="{{ route('quotation.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ Session::get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ Session::get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <!-- Template Information Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    Template Information
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('quotation.update', $quotation->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="quotation_id" value="{{ $quotation->id }}" required>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Quotation Name</label>
                                <input class="form-control" type="text" name="name" value="{{ $quotation->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status</label>
                                <select class="form-control" name="status">
                                    <option value="1" @if ($quotation->status == 1) selected @endif>Active</option>
                                    <option value="0" @if ($quotation->status == 0) selected @endif>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="Enter template description...">{{ $quotation->remark }}</textarea>
                    </div>

                    <div class="text-right">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-save mr-2"></i>
                            Update Template
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="h4 mb-0 text-primary">{{ count($quotation_details) }}</div>
                        <small class="text-muted">Categories</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h4 mb-0 text-success">
                            @php
                                $totalItems = 0;
                                foreach($quotation_details as $cat) {
                                    $totalItems += count($cat['account_details']);
                                }
                            @endphp
                            {{ $totalItems }}
                        </div>
                        <small class="text-muted">Total Items</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h4 mb-0 text-info">
                            @php
                                $totalAmount = 0;
                                foreach($quotation_details as $cat) {
                                    foreach($cat['account_details'] as $details) {
                                        $totalAmount += $details->amount;
                                    }
                                }
                            @endphp
                            RM {{ number_format($totalAmount, 2) }}
                        </div>
                        <small class="text-muted">Total Amount</small>
                    </div>
                    <div class="col-md-3">
                        <div class="h6 mb-0 text-muted">
                            <i class="fas fa-clock mr-2"></i>
                            {{ $quotation->updated_at ? \Carbon\Carbon::parse($quotation->updated_at)->diffForHumans() : 'Never' }}
                        </div>
                        <small class="text-muted">Last Updated</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Items Management -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list-alt mr-2"></i>
                    Account Items
                </h5>
                <button class="btn btn-success" onclick="saveAll('{{ $quotation->id }}')" type="button">
                    <i class="fas fa-save mr-2"></i>
                    Save All Changes
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered mb-0">
                        <thead>
                            <tr class="text-center">
                                <th width="5%">No</th>
                                <th width="35%">Item</th>
                                <th width="10%">Order</th>
                                <th width="12%">Min (RM)</th>
                                <th width="12%">Max (RM)</th>
                                <th width="12%">Amount (RM)</th>
                                <th width="14%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="tbl-case-bill">
                            @if(count($quotation_details))
                                @foreach($quotation_details as $index => $cat)
                                <tr class="table-secondary">
                                    <td colspan="7">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="fas fa-folder mr-2"></i>
                                                {{ $cat['category']->category }}
                                            </span>
                                            <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#addAccountItemModal" onclick="prepareModal('{{ $cat['category']->id }}')" type="button">
                                                <i class="fas fa-plus mr-1"></i>
                                                Add Item
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tbody class="sortable-items" data-category-id="{{ $cat['category']->id }}">
                                @foreach($cat['account_details'] as $index => $details)
                                @php
                                    $min_val = ($details->min == 0) ? $details->account_min : $details->min;
                                @endphp
                                <tr class="table-row-item" data-item-id="{{ $details->id }}">
                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <i class="fas fa-grip-vertical drag-handle"></i>
                                            <span style="display: none;" id="ic_modified_{{ $details->id }}" class="text-warning">
                                                <i class="fas fa-pencil-alt"></i>
                                            </span>
                                            <span id="item_number_{{ $details->id }}">{{ $index + 1 }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="mr-3">
                                                <i class="fas fa-file-invoice text-primary"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $details->account_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $details->account_formula }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input class="form-control" onchange="modifiedCheck('{{ $details->id }}')" 
                                               type="number" value="{{ $details->order_no }}" id="order_no{{ $details->id }}">
                                    </td>
                                    <td class="text-center">
                                        <input class="form-control" onchange="modifiedCheck('{{ $details->id }}')" 
                                               type="number" step="0.01" value="{{ $min_val }}" id="quo_min_{{ $details->id }}">
                                    </td>
                                    <td class="text-center">
                                        <input class="form-control" onchange="modifiedCheck('{{ $details->id }}')" 
                                               type="number" step="0.01" value="{{ $details->max }}" id="quo_max_{{ $details->id }}">
                                    </td>
                                    <td class="text-center">
                                        <input class="form-control" onchange="modifiedCheck('{{ $details->id }}')" 
                                               type="number" step="0.01" value="{{ $details->amount }}" id="quo_amt_{{ $details->id }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm" 
                                                onclick="deleteAccountItem('{{ $details->id }}')" 
                                                title="Delete Item">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Hidden fields for tracking changes -->
                                <input type="hidden" id="item_id_{{ $details->id }}" value="{{ $details->id }}">
                                <input type="hidden" id="modified_{{ $details->id }}" value="0">
                                <input type="hidden" id="bln_modified_{{ $details->id }}" value="0">
                                <input type="hidden" id="quo_min_{{ $details->id }}_ori" value="{{ $min_val }}">
                                <input type="hidden" id="quo_max_{{ $details->id }}_ori" value="{{ $details->max }}">
                                <input type="hidden" id="quo_amt_{{ $details->id }}_ori" value="{{ $details->amount }}">
                                <input type="hidden" id="order_no{{ $details->id }}_ori" value="{{ $details->order_no }}">
                                @endforeach
                                </tbody>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-file-invoice-dollar fa-3x mb-3"></i>
                                            <h5>No Account Items Found</h5>
                                            <p>Start by adding account items to this template</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Add Account Item Modal -->
        <div class="modal fade" id="addAccountItemModal" tabindex="-1" role="dialog" aria-labelledby="addAccountItemModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAccountItemModalLabel">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Add Account Item
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="form_account">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">Select Account Item</label>
                                <select class="form-control" id="selected_account_id" name="selected_account_id">
                                    <option value="0">-- Please select an account item --</option>
                                    @foreach($accounts as $account)
                                        <option class="account_item_all account_cat_{{$account->account_cat_id}} account_{{$account->id}}" 
                                                value="{{$account->id}}">
                                            {{ $account->name }} - {{ $account->category }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">
                                    Choose an account item to add to this quotation template
                                </small>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" onclick="addNewAccount()">
                            <i class="fas fa-plus mr-2"></i>
                            Add Item
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="modal fade" id="loadingOverlay" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <h5>Saving Changes...</h5>
                <p>Please wait while we update your template</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
let blnEdited = false;

function prepareModal(id) {
    console.log('prepareModal called with id:', id);
    
    // Hide all account items first
    $(".account_item_all").hide();
    
    // Show only items from the selected category
    $(".account_cat_" + id).show();
    
    // Hide existing items that are already in the template
    hideExistAccount();
}

function viewMode() {
    // Hide the add account modal
    $("#addAccountItemModal").modal('hide');
}

function modifiedCheck(id) {
    blnEdited = false;
    
    convertDecimal("#quo_min_" + id);
    convertDecimal("#quo_max_" + id);
    convertDecimal("#quo_amt_" + id);
    
    if ($("#quo_min_" + id).val() != $("#quo_min_" + id + "_ori").val()) {
        blnEdited = true;
    }
    
    if ($("#quo_max_" + id).val() != $("#quo_max_" + id + "_ori").val()) {
        blnEdited = true;
    }
    
    if ($("#quo_amt_" + id).val() != $("#quo_amt_" + id + "_ori").val()) {
        blnEdited = true;
    }
    
    if ($("#order_no" + id).val() != $("#order_no" + id + "_ori").val()) {
        blnEdited = true;
    }
    
    if (blnEdited == true) {
        $("#bln_modified_" + id).val(1);
        $("#modified_" + id).val(1);
        $("#ic_modified_" + id).show();
    } else {
        $("#bln_modified_" + id).val(0);
        $("#ic_modified_" + id).hide();
    }
}

function convertDecimal(object) {
    var Value = $(object).val();
    
    if (Value == "") {
        Value = 0;
    }
    
    $(object).val(parseFloat(Value).toFixed(2));
}

function updateOrderNumbers() {
    $('.sortable-items').each(function() {
        var categoryId = $(this).data('category-id');
        var items = $(this).find('tr.table-row-item');
        
        items.each(function(index) {
            var itemId = $(this).data('item-id');
            var newOrder = index + 1;
            var oldOrder = $('#order_no' + itemId + '_ori').val();
            
            // Update the display number
            $('#item_number_' + itemId).text(newOrder);
            
            // Update the order input field
            $('#order_no' + itemId).val(newOrder);
            
            // Only mark as modified if the order actually changed
            if (newOrder != oldOrder) {
                // Update the original value to match the new order
                $('#order_no' + itemId + '_ori').val(newOrder);
                
                // Mark as modified since this is a reorder action
                $("#bln_modified_" + itemId).val(1);
                $("#modified_" + itemId).val(1);
                $("#ic_modified_" + itemId).show();
                
            }
        });
    });
}

function saveAll(quotation_id) {
    var bill_list = [];
    var bill = {};
    var intCountModified = 0;
    
    // Look for all hidden modified fields directly
    $("input[id^='modified_']").each(function() {
        var item_id = $(this).attr('id').replace('modified_', '');
        
        if ($(this).val() == "1") {
            intCountModified += 1;
            
            var order_no = $("#order_no" + item_id).val();
            var amount = $("#quo_amt_" + item_id).val();
            var min = $("#quo_min_" + item_id).val();
            var max = $("#quo_max_" + item_id).val();
            
            bill = {
                id: item_id,
                order_no: order_no,
                amount: amount,
                min: min,
                max: max
            };
            
            bill_list.push(bill);
        }
    });
    
    if (intCountModified == 0) {
        Swal.fire({
            title: 'No Changes',
            text: 'No changes detected to save.',
            icon: 'info',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show loading overlay with robust modal detection
    showLoadingOverlay();
    
    var form_data = new FormData();
    form_data.append("bill_list", JSON.stringify(bill_list));
    form_data.append('_token', '{{ csrf_token() }}');
    
    $.ajax({
        type: 'POST',
        url: '/update_quotation_bill/' + quotation_id,
        data: form_data,
        processData: false,
        contentType: false,
        success: function(data) {
            hideLoadingOverlay();
            
            if (data.status == 1) {
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'An error occurred while saving.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function() {
            hideLoadingOverlay();
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while saving changes.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

function hideExistAccount() {
    $("#tbl-case-bill tr").each(function() {
        var self = $(this);
        // Look for hidden input with item_id
        var item_id = self.find("input[id^='item_id_']").val();
        
        // Only process if item_id exists and is not "0"
        if (item_id && item_id != "0" && item_id.length > 0) {
            // Use a more specific selector to avoid CSS selector issues
            $("option[value='" + item_id + "']").hide();
        }
    });
}

function addNewAccount() {
    if ($("#selected_account_id").val() == "0" || $("#selected_account_id").val() == "") {
        Swal.fire({
            title: 'Warning!',
            text: 'Please select an account item.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    showLoadingOverlay();
    
    var form_data = new FormData();
    form_data.append("selected_account_id", $("#selected_account_id").val());
    form_data.append('_token', '{{ csrf_token() }}');
    
    $.ajax({
        type: 'POST',
        url: '/add_account_item_to_quotation/' + $("#quotation_id").val(),
        data: form_data,
        processData: false,
        contentType: false,
        success: function(data) {
            hideLoadingOverlay();
            
            if (data.status == 1) {
                // Close the modal
                $("#addAccountItemModal").modal('hide');
                
                // Reset the form
                $("#selected_account_id").val("0");
                
                Swal.fire({
                    title: 'Success!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'An error occurred while adding the item.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function() {
            hideLoadingOverlay();
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while adding the item.',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

function deleteAccountItem(id) {
    Swal.fire({
        title: 'Delete Account Item?',
        text: 'Are you sure you want to delete this account item? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            showLoadingOverlay();
            
            $.ajax({
                type: 'POST',
                url: '/delete_account_item_from_quotation/' + id,
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    hideLoadingOverlay();
                    
                    if (data.status == 1) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message || 'An error occurred while deleting.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    hideLoadingOverlay();
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred while deleting the item.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}

function showLoadingOverlay() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Bootstrap 5 approach
        var modalElement = document.getElementById('loadingOverlay');
        var modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        // Bootstrap 4 approach
        $("#loadingOverlay").modal('show');
    } else {
        // Fallback - show a simple overlay
        var overlay = document.createElement('div');
        overlay.id = 'simpleLoadingOverlay';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;display:flex;align-items:center;justify-content:center;color:white;';
        overlay.innerHTML = '<div style="text-align:center;"><div style="border:4px solid #f3f3f3;border-top:4px solid #007bff;border-radius:50%;width:50px;height:50px;animation:spin 1s linear infinite;margin:0 auto 20px;"></div><h5>Saving Changes...</h5><p>Please wait while we update your template</p></div>';
        document.body.appendChild(overlay);
    }
}

function hideLoadingOverlay() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
        // Bootstrap 5 approach
        var modalElement = document.getElementById('loadingOverlay');
        var modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    } else if (typeof $ !== 'undefined' && $.fn.modal) {
        // Bootstrap 4 approach
        $("#loadingOverlay").modal('hide');
    } else {
        // Remove fallback overlay
        var overlay = document.getElementById('simpleLoadingOverlay');
        if (overlay) {
            overlay.remove();
        }
    }
}

// Initialize page
$(document).ready(function() {
    console.log('Document ready...');
    
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
    
    // Initialize sortable functionality
    $('.sortable-items').sortable({
        handle: '.drag-handle',
        helper: function(e, tr) {
            var $originals = tr.children();
            var $helper = tr.clone();
            $helper.children().each(function(index) {
                $(this).width($originals.eq(index).width());
            });
            return $helper;
        },
        placeholder: 'sortable-placeholder',
        start: function(e, ui) {
            ui.item.addClass('sortable-helper');
        },
        stop: function(e, ui) {
            ui.item.removeClass('sortable-helper');
            updateOrderNumbers();
        },
        update: function(e, ui) {
            // This will be called after the order has been updated
            console.log('Order updated for category');
        }
    });
    
    // Handle modal events
    $('#addAccountItemModal').on('hidden.bs.modal', function () {
        console.log('Modal hidden');
        // Reset the form
        $("#selected_account_id").val("0");
        
        // Show all account items again
        $(".account_item_all").show();
    });
    
    // Handle modal show event
    $('#addAccountItemModal').on('show.bs.modal', function () {
        console.log('Modal showing...');
        // Reset the form when modal opens
        $("#selected_account_id").val("0");
    });
    
    // Handle modal shown event
    $('#addAccountItemModal').on('shown.bs.modal', function () {
        console.log('Modal shown');
        // Focus on the first form element
        $("#selected_account_id").focus();
    });
});
</script>
@endsection