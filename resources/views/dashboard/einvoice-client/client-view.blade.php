<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>E-Invoice Data - {{ $billingParty->customer_name ?? 'Client' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }
        .info-row {
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: 600;
            color: #495057;
        }
        .value {
            color: #6c757d;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .edit-form {
            display: none;
        }
        .edit-form.active {
            display: block;
        }
        .view-mode {
            display: block;
        }
        .view-mode.hidden {
            display: none;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-edit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-edit:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            color: white;
        }
        .alert {
            border-radius: 10px;
            border: none;
        }
        .is-invalid {
            border-color: #dc3545 !important;
            background-color: #fff6f6 !important;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8 d-flex align-items-center">
                    <img src="/assets/brand/lhyeo-logo-white.png" alt="Logo" style="height:56px; max-width:220px; width:auto; margin-right:18px;">
                    <!-- Optionally, you can add a tagline here if you want -->
                </div>
                <div class="col-md-4 text-right">
                    <div class="status-badge {{ $billingParty->completed == 1 ? 'status-completed' : 'status-pending' }}">
                        {{ $billingParty->completed == 1 ? 'Profile Completed' : 'Profile Pending' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Alert Messages -->
        <div id="alertContainer"></div>

        <!-- Customer Information -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-user"></i> Customer Information
                </h4>
                <button type="button" class="btn btn-edit btn-sm" onclick="toggleEditMode()" id="editBtn">
                    <i class="fas fa-edit"></i> Edit Information
                </button>
            </div>
            <div class="card-body">
                <!-- View Mode -->
                <div class="view-mode" id="viewMode">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="label">Customer Name:</div>
                                <div class="value">{{ $billingParty->customer_name ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Customer Code:</div>
                                <div class="value">{{ $billingParty->customer_code ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Customer Category:</div>
                                <div class="value">
                                    @if($billingParty->customer_category == 1)
                                        Individual
                                    @elseif($billingParty->customer_category == 2)
                                        Company
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="label">TIN:</div>
                                <div class="value">{{ $billingParty->tin ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <div class="label">BRN:</div>
                                <div class="value">{{ $billingParty->brn ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">BRN2:</div>
                                <div class="value">{{ $billingParty->brn2 ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Sales Tax No:</div>
                                <div class="value">{{ $billingParty->sales_tax_no ?? 'N/A' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="label">Service Tax No:</div>
                                <div class="value">{{ $billingParty->service_tax_no ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <div class="edit-form" id="editMode">
                    <form id="clientEditForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Name<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_name" name="customer_name" 
                                           value="{{ $billingParty->customer_name ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Code<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="customer_code" name="customer_code" 
                                           value="{{ $billingParty->customer_code ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Customer Category<span class="text-danger">*</span></label>
                                    <select class="form-control" id="customer_category" name="customer_category">
                                        <option value="" {{ ($billingParty->customer_category ?? '') == '' ? 'selected' : '' }}>Select Category</option>
                                        <option value="1" {{ ($billingParty->customer_category ?? '') == '1' ? 'selected' : '' }}>Individual</option>
                                        <option value="2" {{ ($billingParty->customer_category ?? '') == '2' ? 'selected' : '' }}>Company</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>TIN<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="tin" name="tin" 
                                           value="{{ $billingParty->tin ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>BRN<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="brn" name="brn" 
                                           value="{{ $billingParty->brn ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>BRN2<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="brn2" name="brn2" 
                                           value="{{ $billingParty->brn2 ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sales Tax No<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="sales_tax_no" name="sales_tax_no" 
                                           value="{{ $billingParty->sales_tax_no ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Service Tax No<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="service_tax_no" name="service_tax_no" 
                                           value="{{ $billingParty->service_tax_no ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ID No<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="id_no" name="id_no" 
                                           value="{{ $billingParty->id_no ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address Line 1<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address_1" name="address_1" 
                                           value="{{ $billingParty->address_1 ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address Line 2<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address_2" name="address_2" 
                                           value="{{ $billingParty->address_2 ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address Line 3<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address_3" name="address_3" 
                                           value="{{ $billingParty->address_3 ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Address Line 4<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address_4" name="address_4" 
                                           value="{{ $billingParty->address_4 ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Postcode<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="postcode" name="postcode" 
                                           value="{{ $billingParty->postcode ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="{{ $billingParty->city ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>State<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="state" name="state" 
                                           value="{{ $billingParty->state ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Country<span class="text-danger">*</span></label>
                                    <select class="form-control" id="country" name="country">
                                        <option value="" {{ ($billingParty->country ?? '') == '' ? 'selected' : '' }}>Select Country</option>
                                        <option value="MY" {{ ($billingParty->country ?? '') == 'MY' ? 'selected' : '' }}>Malaysia</option>
                                        <option value="SG" {{ ($billingParty->country ?? '') == 'SG' ? 'selected' : '' }}>Singapore</option>
                                        <option value="US" {{ ($billingParty->country ?? '') == 'US' ? 'selected' : '' }}>United States</option>
                                        <option value="GB" {{ ($billingParty->country ?? '') == 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                        <option value="AU" {{ ($billingParty->country ?? '') == 'AU' ? 'selected' : '' }}>Australia</option>
                                        <option value="CA" {{ ($billingParty->country ?? '') == 'CA' ? 'selected' : '' }}>Canada</option>
                                        <option value="CN" {{ ($billingParty->country ?? '') == 'CN' ? 'selected' : '' }}>China</option>
                                        <option value="JP" {{ ($billingParty->country ?? '') == 'JP' ? 'selected' : '' }}>Japan</option>
                                        <option value="KR" {{ ($billingParty->country ?? '') == 'KR' ? 'selected' : '' }}>South Korea</option>
                                        <option value="IN" {{ ($billingParty->country ?? '') == 'IN' ? 'selected' : '' }}>India</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone<span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="phone1" name="phone1" 
                                           value="{{ $billingParty->phone1 ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mobile</label>
                                    <input type="text" class="form-control" id="mobile" name="mobile" 
                                           value="{{ $billingParty->mobile ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fax 1</label>
                                    <input type="text" class="form-control" id="fax1" name="fax1" 
                                           value="{{ $billingParty->fax1 ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ $billingParty->email ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-success" onclick="saveClientData()">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-secondary ml-2" onclick="cancelEdit()">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Address Information -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-map-marker-alt"></i> Address Information
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="label">Address Line 1:</div>
                            <div class="value">{{ $billingParty->address_1 ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Address Line 2:</div>
                            <div class="value">{{ $billingParty->address_2 ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Address Line 3:</div>
                            <div class="value">{{ $billingParty->address_3 ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Address Line 4:</div>
                            <div class="value">{{ $billingParty->address_4 ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="label">Postcode:</div>
                            <div class="value">{{ $billingParty->postcode ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">City:</div>
                            <div class="value">{{ $billingParty->city ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">State:</div>
                            <div class="value">{{ $billingParty->state ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Country:</div>
                            <div class="value">{{ $billingParty->country ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-phone"></i> Contact Information
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="label">Phone:</div>
                            <div class="value">{{ $billingParty->phone1 ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Mobile:</div>
                            <div class="value">{{ $billingParty->mobile ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-row">
                            <div class="label">Fax 1:</div>
                            <div class="value">{{ $billingParty->fax1 ?? 'N/A' }}</div>
                        </div>
                        <div class="info-row">
                            <div class="label">Email:</div>
                            <div class="value">{{ $billingParty->email ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-4 mb-4">
            <p class="text-muted">
                <i class="fas fa-shield-alt"></i> This is a secure link for accessing your e-invoice data.
                <br>Please do not share this link with unauthorized parties.
            </p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleEditMode() {
            $('#viewMode').addClass('hidden');
            $('#editMode').addClass('active');
            $('#editBtn').html('<i class="fas fa-eye"></i> View Information');
            $('#editBtn').attr('onclick', 'toggleViewMode()');
        }

        function toggleViewMode() {
            $('#viewMode').removeClass('hidden');
            $('#editMode').removeClass('active');
            $('#editBtn').html('<i class="fas fa-edit"></i> Edit Information');
            $('#editBtn').attr('onclick', 'toggleEditMode()');
        }

        function cancelEdit() {
            toggleViewMode();
        }

        function saveClientData() {
            // Character length limits based on DB schema
            var limits = {
                customer_code: 10,
                customer_name: 100,
                brn: 30,
                brn2: 30,
                sales_tax_no: 25,
                service_tax_no: 25,
                id_no: 20,
                tin: 14,
                address_1: 60,
                address_2: 60,
                address_3: 60,
                address_4: 60,
                postcode: 10,
                city: 50,
                state: 50,
                country: 2,
                phone1: 200,
                mobile: 200,
                fax1: 200,
                email: 200
            };

            var formData = {
                customer_name: $('#customer_name').val(),
                customer_code: $('#customer_code').val(),
                customer_category: $('#customer_category').val(),
                tin: $('#tin').val(),
                brn: $('#brn').val(),
                brn2: $('#brn2').val(),
                sales_tax_no: $('#sales_tax_no').val(),
                service_tax_no: $('#service_tax_no').val(),
                id_no: $('#id_no').val(),
                address_1: $('#address_1').val(),
                address_2: $('#address_2').val(),
                address_3: $('#address_3').val(),
                address_4: $('#address_4').val(),
                postcode: $('#postcode').val(),
                city: $('#city').val(),
                state: $('#state').val(),
                country: $('#country').val(),
                phone1: $('#phone1').val(),
                mobile: $('#mobile').val(),
                fax1: $('#fax1').val(),
                email: $('#email').val()
            };

            // Validate length
            var errors = [];
            var errorFields = [];
            Object.keys(limits).forEach(function(key) {
                var input = $('#' + key);
                input.removeClass('is-invalid');
                if (formData[key] && formData[key].length > limits[key]) {
                    errors.push('<b>' + key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase()) + '</b> exceeds max length of <b>' + limits[key] + '</b> characters.');
                    errorFields.push(key);
                    input.addClass('is-invalid');
                }
            });

            if (errors.length > 0) {
                showAlert('danger', '<strong>Please correct the following errors:</strong><ul style="margin-bottom:0">' + errors.map(e => '<li>' + e + '</li>').join('') + '</ul>');
                // Scroll to alert
                $('html, body').animate({ scrollTop: $('#alertContainer').offset().top - 80 }, 400);
                return;
            }

            $.ajax({
                url: '/client-einvoice-data/update/{{ $billingParty->token }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 1) {
                        showAlert('success', 'Information updated successfully!');
                        toggleViewMode();
                        // Refresh the page to show updated data
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showAlert('danger', 'Error updating information: ' + response.message);
                    }
                },
                error: function(xhr) {
                    showAlert('danger', 'Error updating information. Please try again.');
                }
            });
        }

        function showAlert(type, message) {
            var alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">
                        <span>&times;</span>
                    </button>
                </div>
            `;
            $('#alertContainer').html(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
</body>
</html> 