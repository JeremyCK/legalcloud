@extends('dashboard.base')
<link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
<link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
}

.sortable:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.sortable i {
    margin-left: 5px;
    font-size: 12px;
}

.sortable .fa-sort {
    opacity: 0.5;
}

.sortable:hover .fa-sort {
    opacity: 1;
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
                                 <h4><i class="fa fa-exchange-alt"></i> Transfer Fee</h4>
              </div>

                                    <div class="col-6">
                        <a class="btn btn-lg btn-primary float-right" href="/transferfee/create">
                          <i class="fa fa-plus"> </i>Create New Transfer Fee
                        </a>
                      </div>
            </div>
          </div>
          <div class="card-body" style="width:100%;overflow-x:auto">
            @if(Session::has('message'))
            <div class="alert alert-success" role="alert">{{ Session::get('message') }}</div>
            @endif

            @if(Session::has('error'))
            <div class="alert alert-danger" role="alert">{{ Session::get('error') }}</div>
            @endif

                         <!-- Filter Form -->
             <form id="filterForm">
               <!-- Hidden field for per page -->
               <input type="hidden" name="per_page" id="perPageHidden" value="{{ $TransferFeeMain->perPage() ?? 10 }}">
               <div class="row">
                 <div class="col-sm-6 col-md-2">
                   <div class="form-group">
                     <label>Transaction ID</label>
                     <input class="form-control" type="text" name="transaction_id" placeholder="Search transaction ID" value="{{ $filters['transaction_id'] ?? '' }}">
                   </div>
                 </div>

                 <div class="col-sm-6 col-md-2">
                   <div class="form-group">
                     <label>Transfer date from</label>
                     <input class="form-control" type="date" name="transfer_date_from" value="{{ $filters['transfer_date_from'] ?? '' }}">
                   </div>
                 </div>

                 <div class="col-sm-6 col-md-2">
                   <div class="form-group">
                     <label>Transfer date to</label>
                     <input class="form-control" type="date" name="transfer_date_to" value="{{ $filters['transfer_date_to'] ?? '' }}">
                   </div>
                 </div>

                 @if(in_array($current_user->menuroles, ['admin','sales','account']))
                 <div class="col-sm-6 col-md-2">
                   <div class="form-group">
                     <label>Branch</label>
                     <select class="form-control" name="branch_id">
                       <option value="0"> -- All branch -- </option>
                       @foreach($Branchs as $index => $branch)
                       <option value="{{$branch->id}}" {{ ($filters['branch_id'] ?? '') == $branch->id ? 'selected' : '' }}>{{$branch->name}}</option>
                       @endforeach
                     </select>
                   </div>
                 </div>
                 @endif

                 <div class="col-sm-6 col-md-4">
                   <div class="form-group">
                     <label>&nbsp;</label>
                     <div>
                       <button type="button" class="btn btn-info" onclick="loadData()">
                         <i class="fa fa-search"> </i>Filter
                       </button>
                       <button type="button" class="btn btn-secondary" onclick="clearFilters()">
                         <i class="fa fa-refresh"> </i>Clear
                       </button>
                     </div>
                   </div>
                 </div>
               </div>
             </form>

            <hr>

            <!-- Records Per Page Control -->
            <div class="row mb-3">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <label for="perPageSelect" class="mb-0 mr-2 text-muted">
                                <small>Show</small>
                            </label>
                            <select id="perPageSelect" class="form-control form-control-sm" style="width: auto; display: inline-block;">
                                <option value="5" {{ ($TransferFeeMain->perPage() ?? 10) == 5 ? 'selected' : '' }}>5</option>
                                <option value="10" {{ ($TransferFeeMain->perPage() ?? 10) == 10 ? 'selected' : '' }}>10</option>
                                <option value="25" {{ ($TransferFeeMain->perPage() ?? 10) == 25 ? 'selected' : '' }}>25</option>
                                <option value="50" {{ ($TransferFeeMain->perPage() ?? 10) == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ ($TransferFeeMain->perPage() ?? 10) == 100 ? 'selected' : '' }}>100</option>
                            </select>
                            <label for="perPageSelect" class="mb-0 ml-2 text-muted">
                                <small>entries</small>
                            </label>
                        </div>
                        <div class="text-muted entries-info">
                            <small>
                                <i class="fa fa-info-circle"></i>
                                Showing {{ $TransferFeeMain->firstItem() ?? 0 }} to {{ $TransferFeeMain->lastItem() ?? 0 }} of {{ $TransferFeeMain->total() ?? 0 }} entries
                            </small>
                        </div>
                    </div>
                </div>
            </div>

                         <!-- Data Table -->
             <div class="table-responsive">
               <table id="transferTable" class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                  <tr>
                    <th>No</th>
                    <th class="sortable" data-sort="transaction_id">
                      Transaction ID
                      @if(($sortBy ?? '') === 'transaction_id')
                        <i class="fa fa-sort-{{ $sortOrder === 'ASC' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fa fa-sort text-muted"></i>
                      @endif
                    </th>
                    <th>Purpose</th>
                    <th class="sortable" data-sort="transfer_amount">
                      Transfer AMT
                      @if(($sortBy ?? '') === 'transfer_amount')
                        <i class="fa fa-sort-{{ $sortOrder === 'ASC' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fa fa-sort text-muted"></i>
                      @endif
                    </th>
                    <th>Transfer From</th>
                    <th>Transfer To</th>
                    <th class="sortable" data-sort="transfer_date">
                      Transfer Date
                      @if(($sortBy ?? '') === 'transfer_date')
                        <i class="fa fa-sort-{{ $sortOrder === 'ASC' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fa fa-sort text-muted"></i>
                      @endif
                    </th>
                    <th class="sortable" data-sort="branch_name">
                      Branch
                      @if(($sortBy ?? '') === 'branch_name')
                        <i class="fa fa-sort-{{ $sortOrder === 'ASC' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fa fa-sort text-muted"></i>
                      @endif
                    </th>
                    <th class="sortable" data-sort="created_by_name">
                      Created By
                      @if(($sortBy ?? '') === 'created_by_name')
                        <i class="fa fa-sort-{{ $sortOrder === 'ASC' ? 'up' : 'down' }}"></i>
                      @else
                        <i class="fa fa-sort text-muted"></i>
                      @endif
                    </th>
                    <th>Recon</th>
                    <th>Action</th>
                  </tr>
                </thead>
                                 <tbody>
                   @include('dashboard.transfer-fee-v3.partials.table-body')
                 </tbody>
              </table>
                         </div>

             <!-- Pagination -->
             <div class="row mt-3">
               <div class="col-12">
                 <div class="d-flex justify-content-center">
                   <div class="pagination-container">
                     @php
                         $paginationHtml = '';
                         $currentPage = $TransferFeeMain->currentPage();
                         $lastPage = $TransferFeeMain->lastPage();
                         
                         if ($lastPage > 1) {
                             $paginationHtml = '<nav><ul class="pagination justify-content-center">';
                             
                             // Previous button
                             if ($currentPage > 1) {
                                 $paginationHtml .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . ($currentPage - 1) . '">Previous</a></li>';
                             } else {
                                 $paginationHtml .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
                             }
                             
                             // Page numbers
                             $start = max(1, $currentPage - 2);
                             $end = min($lastPage, $currentPage + 2);
                             
                             if ($start > 1) {
                                 $paginationHtml .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="1">1</a></li>';
                                 if ($start > 2) {
                                     $paginationHtml .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                 }
                             }
                             
                             for ($i = $start; $i <= $end; $i++) {
                                 if ($i == $currentPage) {
                                     $paginationHtml .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                 } else {
                                     $paginationHtml .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . $i . '">' . $i . '</a></li>';
                                 }
                             }
                             
                             if ($end < $lastPage) {
                                 if ($end < $lastPage - 1) {
                                     $paginationHtml .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                 }
                                 $paginationHtml .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . $lastPage . '">' . $lastPage . '</a></li>';
                             }
                             
                             // Next button
                             if ($currentPage < $lastPage) {
                                 $paginationHtml .= '<li class="page-item"><a class="page-link ajax-pagination" href="javascript:void(0)" data-page="' . ($currentPage + 1) . '">Next</a></li>';
                             } else {
                                 $paginationHtml .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
                             }
                             
                             $paginationHtml .= '</ul></nav>';
                         } else {
                             $paginationHtml = '<div class="text-center text-muted">No pagination needed</div>';
                         }
                     @endphp
                     {!! $paginationHtml !!}
                   </div>
                 </div>
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
 <script>
 function deleteTransferFee(id) {
     Swal.fire({
         title: 'Are you sure?',
         text: "You won't be able to revert this!",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#3085d6',
         cancelButtonColor: '#d33',
         confirmButtonText: 'Yes, delete it!'
     }).then((result) => {
         if (result.isConfirmed) {
             $.ajax({
                                     url: '/transferfee/' + id,
                 type: 'DELETE',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 success: function(response) {
                     if (response.status == 1) {
                         Swal.fire('Deleted!', response.message, 'success').then(() => {
                             loadData();
                         });
                     } else {
                         Swal.fire('Error!', response.message, 'error');
                     }
                 },
                 error: function() {
                     Swal.fire('Error!', 'Something went wrong', 'error');
                 }
             });
         }
     });
 }

 function loadData(page = 1) {
     // Get per page value
     var perPageValue = $('#perPageSelect').val() || 10;
     
     // Update hidden per_page field before serializing
     $('#perPageHidden').val(perPageValue);
     
     // Get form data (now includes per_page)
     var formData = $('#filterForm').serialize();
     formData += '&page=' + page;
     
     // Add sorting parameters
     var currentSortBy = $('.sortable.active').data('sort') || '{{ $sortBy ?? "created_at" }}';
     var currentSortOrder = '{{ $sortOrder ?? "DESC" }}';
     formData += '&sort_by=' + currentSortBy + '&sort_order=' + currentSortOrder;
     
     console.log('Loading data with formData:', formData);
     console.log('Per page value from dropdown:', perPageValue);
     console.log('Hidden field value after update:', $('#perPageHidden').val());
     
     // Force per_page parameter to be included
     if (!formData.includes('per_page=')) {
         formData += '&per_page=' + perPageValue;
         console.log('Added per_page parameter manually:', formData);
     }
     
     // Show loading indicator
     $('.pagination-container').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading...</div>');
     
     // Set a timeout to clear loading if request takes too long
     var loadingTimeout = setTimeout(function() {
         $('.pagination-container').html('<div class="text-center text-danger">Request timeout - please try again</div>');
     }, 10000); // 10 second timeout
     
     $.ajax({
         url: '{{ route("transferfee.index") }}',
         type: 'GET',
         data: formData,
         beforeSend: function() {
             console.log('AJAX request starting with data:', formData);
         },
         success: function(response) {
             // Clear the loading timeout
             clearTimeout(loadingTimeout);
             
             console.log('AJAX Response received:', response);
             
             // Check if response is valid
             if (!response || typeof response !== 'object') {
                 console.error('Invalid response received:', response);
                 Swal.fire('Error!', 'Invalid response from server', 'error');
                 $('.pagination-container').html('<div class="text-center text-danger">Invalid response</div>');
                 return;
             }
             
             // Show debug information
             if (response.debug) {
                 console.log('Debug info:', response.debug);
                 // alert('Debug - Requested per_page: ' + response.debug.requested_per_page + 
                 //       ', Actual per_page: ' + response.debug.actual_per_page + 
                 //       ', Total items: ' + response.debug.total_items);
             }
             
             // Update the table body
             if (response.tableBody) {
                 $('#transferTable tbody').html(response.tableBody);
             } else {
                 console.error('No tableBody in response');
             }
             
             // Update pagination - ensure it's properly rendered
             if (response.pagination) {
                 console.log('Updating pagination with:', response.pagination);
                 console.log('Pagination type:', typeof response.pagination);
                 console.log('Pagination constructor:', response.pagination.constructor.name);
                 
                 // Check if pagination is a string or object
                 var paginationHtml = '';
                 if (typeof response.pagination === 'string') {
                     paginationHtml = response.pagination;
                 } else if (typeof response.pagination === 'object' && response.pagination.html) {
                     paginationHtml = response.pagination.html;
                 } else if (typeof response.pagination === 'object' && response.pagination.toString) {
                     // Try to convert object to string
                     paginationHtml = response.pagination.toString();
                 } else {
                     console.log('Pagination is not a string or valid object:', response.pagination);
                     paginationHtml = '<div class="text-center text-warning">Invalid pagination format</div>';
                 }
                 
                 if (paginationHtml && paginationHtml.trim() !== '') {
                     $('.pagination-container').html(paginationHtml);
                     
                     // Re-bind pagination events after content update
                     setTimeout(function() {
                         console.log('Re-binding pagination events');
                         initializePaginationEvents();
                     }, 100);
                 } else {
                     console.log('No pagination HTML available');
                     $('.pagination-container').html('<div class="text-center text-warning">No pagination available</div>');
                 }
             } else {
                 console.log('No pagination in response');
                 $('.pagination-container').html('<div class="text-center text-warning">No pagination available</div>');
             }
             
             // Ensure pagination is always cleared from loading state
             if ($('.pagination-container').html().includes('Loading')) {
                 console.log('Clearing loading state from pagination');
                 $('.pagination-container').html('<div class="text-center text-info">Pagination updated</div>');
             }
             
             // Fallback: If pagination is still empty, show a simple message
             setTimeout(function() {
                 if ($('.pagination-container').html().trim() === '' || $('.pagination-container').html().includes('Loading')) {
                     console.log('Setting fallback pagination');
                     $('.pagination-container').html('<div class="text-center text-muted">Pagination not available <button class="btn btn-sm btn-outline-primary clear-loading">Clear</button></div>');
                 }
             }, 500);
             
             // Update sorting indicators
             updateSortingIndicators(response.sortBy, response.sortOrder);
             
             // Update per page dropdown if it changed
             if (response.perPage) {
                 $('#perPageSelect').val(response.perPage);
             }
             
             // Update entries count display
             if (response.entriesInfo) {
                 $('.entries-info').html(response.entriesInfo);
             }
             
             // Keep URL clean - no parameters in URL
             // var newUrl = '{{ route("transferfee.index") }}?' + formData;
             // history.pushState(null, null, newUrl);
         },
         error: function(xhr, status, error) {
             // Clear the loading timeout
             clearTimeout(loadingTimeout);
             
             console.error('AJAX Error:', status, error);
             console.error('Response:', xhr.responseText);
             
             // Show specific error message
             var errorMessage = 'Failed to load data';
             if (xhr.status === 500) {
                 errorMessage = 'Server error - please check logs';
             } else if (xhr.status === 404) {
                 errorMessage = 'Page not found';
             } else if (xhr.status === 0) {
                 errorMessage = 'Network error - please check connection';
             }
             
             Swal.fire('Error!', errorMessage + ': ' + error, 'error');
             
             // Restore previous pagination on error
             $('.pagination-container').html('<div class="text-center text-danger">Error loading pagination - please refresh the page</div>');
         }
     });
 }

 function clearFilters() {
     $('#filterForm')[0].reset();
     // Reset per page to default
     $('#perPageSelect').val(10);
     $('#perPageHidden').val(10);
     // Reset sorting to default
     $('.sortable').removeClass('active');
     $('.sortable[data-sort="created_at"]').addClass('active');
     loadData();
 }
 
 function updateSortingIndicators(sortBy, sortOrder) {
     // Remove all active classes and reset icons
     $('.sortable').removeClass('active');
     $('.sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort text-muted');
     
     // Add active class to current sort column
     $('.sortable[data-sort="' + sortBy + '"]').addClass('active');
     
     // Update icon for current sort column
     var currentIcon = $('.sortable[data-sort="' + sortBy + '"] i');
     currentIcon.removeClass('fa-sort text-muted').addClass('fa-sort-' + (sortOrder === 'ASC' ? 'up' : 'down'));
 }
 
 function initializePaginationEvents() {
     console.log('Initializing pagination events');
     
     // Remove any existing event handlers to avoid duplicates
     $('.ajax-pagination').off('click');
     
     // Bind new event handlers for AJAX pagination
     $('.ajax-pagination').on('click', function(e) {
         e.preventDefault();
         e.stopPropagation();
         
         var page = $(this).data('page');
         console.log('Initial AJAX pagination click - Page:', page);
         loadData(page);
     });
 }
 
 function handleSort(column) {
     var currentSortBy = $('.sortable.active').data('sort') || '{{ $sortBy ?? "created_at" }}';
     var currentSortOrder = '{{ $sortOrder ?? "DESC" }}';
     
     // If clicking the same column, toggle order
     if (currentSortBy === column) {
         currentSortOrder = currentSortOrder === 'ASC' ? 'DESC' : 'ASC';
     } else {
         // If clicking a different column, default to DESC
         currentSortOrder = 'DESC';
     }
     
     // Update active class
     $('.sortable').removeClass('active');
     $('.sortable[data-sort="' + column + '"]').addClass('active');
     
     // Reload data with new sorting
     loadData();
 }

 // Auto-submit form when inputs change
 $(document).ready(function() {
     // Initialize sorting - set default sort column as active
     $('.sortable[data-sort="created_at"]').addClass('active');
     
     // Initialize pagination event binding
     initializePaginationEvents();
     
     // Initialize per page dropdown
     $('#perPageSelect').val($('#perPageHidden').val());
     
     // Add manual loading clear button (for debugging)
     $(document).on('click', '.clear-loading', function() {
         $('.pagination-container').html('<div class="text-center text-info">Loading cleared manually</div>');
     });
     
     $('input[name="transaction_id"]').on('input', function() {
         // Add a small delay to avoid too many requests while typing
         clearTimeout($(this).data('timeout'));
         $(this).data('timeout', setTimeout(function() {
             loadData();
         }, 500));
     });
     
     $('input[type="date"]').change(function() {
         loadData();
     });
     
     $('select[name="branch_id"]').change(function() {
         loadData();
     });
     
     // Handle per page dropdown change
     $('#perPageSelect').change(function() {
         var selectedPerPage = $(this).val();
         console.log('Per page changed to:', selectedPerPage);
         // alert('Changing per page to: ' + selectedPerPage); // Debug alert
         // Update hidden field
         $('#perPageHidden').val(selectedPerPage);
         // Reset to page 1 when changing per page
         loadData(1);
     });
     
     // Handle AJAX pagination clicks
     $(document).on('click', '.ajax-pagination', function(e) {
         e.preventDefault();
         e.stopPropagation();
         
         var page = $(this).data('page');
         console.log('AJAX pagination clicked - Page:', page);
         
         loadData(page);
     });
     
     // Backup pagination handler for dynamically added content
     $(document).on('click', 'a[href*="page="]', function(e) {
         if ($(this).closest('.pagination, .pagination-container').length > 0) {
             e.preventDefault();
             e.stopPropagation();
             
             var href = $(this).attr('href');
             var page = 1;
             
             if (href && href.includes('page=')) {
                 var match = href.match(/page=(\d+)/);
                 if (match) {
                     page = match[1];
                 }
             }
             
             console.log('Backup pagination click - Page:', page, 'Href:', href);
             loadData(page);
         }
     });
     
     // Handle sortable column clicks
     $(document).on('click', '.sortable', function() {
         var column = $(this).data('sort');
         handleSort(column);
     });
 });
 </script>
 @endsection
