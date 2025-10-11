@extends('dashboard.base')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
                            <i class="fas fa-file-invoice-dollar mr-2"></i>
                            Quotation Templates
                        </h1>
                        <p class="text-muted">Manage and organize your quotation templates</p>
                    </div>
                    <div>
                        <a href="{{ route('quotation.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Create New Template
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(Session::has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i>
            {{ Session::get('message') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <!-- Error Message -->
        @if(Session::has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            {{ Session::get('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-primary">
                        <i class="fas fa-file-invoice"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-number">{{ $stats['total'] ?? 0 }}</span>
                        <span class="info-box-text">Total Templates</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-number">{{ $stats['active'] ?? 0 }}</span>
                        <span class="info-box-text">Active Templates</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-info">
                        <i class="fas fa-calendar-alt"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-number">{{ $stats['this_month'] ?? 0 }}</span>
                        <span class="info-box-text">This Month</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="info-box">
                    <span class="info-box-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-number">{{ $stats['last_updated'] ? \Carbon\Carbon::parse($stats['last_updated'])->diffForHumans() : 'Never' }}</span>
                        <span class="info-box-text">Last Updated</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="text" id="searchInput" class="form-control" 
                                           placeholder="Search quotation templates..." 
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select id="statusFilter" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <select id="sortBy" class="form-control">
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest First</option>
                                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                                    <option value="updated_at" {{ request('sort') == 'updated_at' ? 'selected' : '' }}>Recently Updated</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quotation Templates Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover datatable">
                        <thead class="thead-dark">
                            <tr class="text-center">
                                <th width="5%">No</th>
                                <th width="25%">Template Name</th>
                                <th width="20%">Remark</th>
                                <th width="10%">Status</th>
                                <th width="15%">Items</th>
                                <th width="10%">Generated</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="quotationsContainer">
                            @if(count($quotations))
                                @foreach($quotations as $index => $quotation)
                                <tr class="quotation-item" 
                                    data-name="{{ strtolower($quotation->name) }}" 
                                    data-status="{{ $quotation->status }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $quotation->name }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar mr-1"></i>
                                            Created {{ $quotation->created_at ? \Carbon\Carbon::parse($quotation->created_at)->diffForHumans() : 'Unknown' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($quotation->remark)
                                            {{ Str::limit($quotation->remark, 50) }}
                                        @else
                                            <span class="text-muted">No remark</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($quotation->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-primary">{{ $quotation->details_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-success">{{ $quotation->generated_count ?? 0 }}</span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('quotation.show', $quotation->id) }}" 
                                               class="btn btn-sm btn-success" 
                                               data-toggle="tooltip" 
                                               title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('quotation.edit', $quotation->id) }}" 
                                               class="btn btn-sm btn-primary" 
                                               data-toggle="tooltip" 
                                               title="Edit Template">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="deleteQuotation({{ $quotation->id }}, '{{ $quotation->name }}')"
                                                    data-toggle="tooltip" 
                                                    title="Delete Template">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                                            <h5>No Quotation Templates Found</h5>
                                            <p class="text-muted">Get started by creating your first quotation template</p>
                                            <a href="{{ route('quotation.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus mr-2"></i>
                                                Create Your First Template
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($quotations->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $quotations->appends(request()->query())->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the quotation template "<strong id="quotationName"></strong>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Template</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/quotation/quotation-manager.js') }}"></script>
@endsection