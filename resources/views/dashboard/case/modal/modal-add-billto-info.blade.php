<div id="modalAddBilltoInfo" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="display: block !important">
                <div class="row">
                    <div class="col-6">
                        <h4 class="card-title mb-0 flex-grow-1">Add Party into Invoice</h4>
                    </div>
                    <div class="col-6">
                        <button type="button" class="close btn_close_all" data-dismiss="modal">&times;</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">

                <form id="formAddBilltoInfo">
                    {{-- <div class="row">
                        <div class="col-6">
                            <div class="form-group row">
                                <div class="col">
                                    <label>Bill To</label>
                                    <select class="form-control ddl-party" id="ddl_party_inv_v2" name="ddl_party_inv_v2">
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Customer Name</label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Ref No</label>
                                <input type="text" class="form-control" id="customer_code" name="customer_code"
                                    readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Customer Category</label>
                                <select class="form-control" id="customer_category" name="customer_category">
                                    <option value="">Select Category</option>
                                    <option value="1">Individual</option>
                                    <option value="2">Company</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label>TIN</label>
                                <input type="text" class="form-control" id="tin" name="tin">
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>BRN</label>
                                <input type="text" class="form-control" id="brn" name="brn">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>BRN2</label>
                                <input type="text" class="form-control" id="brn2" name="brn2">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Sales Tax No</label>
                                <input type="text" class="form-control" id="sales_tax_no" name="sales_tax_no">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Service Tax No</label>
                                <input type="text" class="form-control" id="service_tax_no" name="service_tax_no">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>ID Type<span class="text-danger">*</span></label>
                                <select class="form-control" id="id_type" name="id_type" required>
                                    <option value="">Select ID Type</option>
                                    <option value="1">New Reg No</option>
                                    <option value="2">NRIC</option>
                                    <option value="3">Passport</option>
                                    <option value="4">ARMY</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>ID No</label>
                                <input type="text" class="form-control" id="id_no" name="id_no">
                            </div>
                        </div>
                    </div>



                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Address Line 1</label>
                                <input type="text" class="form-control" id="address_1" name="address_1">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Address Line 2</label>
                                <input type="text" class="form-control" id="address_2" name="address_2">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Address Line 3</label>
                                <input type="text" class="form-control" id="address_3" name="address_3">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>Address Line 4</label>
                                <input type="text" class="form-control" id="address_4" name="address_4">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Postcode</label>
                                <input type="text" class="form-control" id="postcode" name="postcode">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>City</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>State</label>
                                <input type="text" class="form-control" id="state" name="state">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Country</label>
                                <select class="form-control" id="country" name="country">
                                    <option value="">Select Country</option>
                                    <option value="AF">Afghanistan</option>
                                    <option value="AL">Albania</option>
                                    <option value="DZ">Algeria</option>
                                    <option value="AS">American Samoa</option>
                                    <option value="AD">Andorra</option>
                                    <option value="AO">Angola</option>
                                    <option value="AI">Anguilla</option>
                                    <option value="AQ">Antarctica</option>
                                    <option value="AG">Antigua and Barbuda</option>
                                    <option value="AR">Argentina</option>
                                    <option value="AM">Armenia</option>
                                    <option value="AW">Aruba</option>
                                    <option value="AU">Australia</option>
                                    <option value="AT">Austria</option>
                                    <option value="AZ">Azerbaijan</option>
                                    <option value="BS">Bahamas</option>
                                    <option value="BH">Bahrain</option>
                                    <option value="BD">Bangladesh</option>
                                    <option value="BB">Barbados</option>
                                    <option value="BY">Belarus</option>
                                    <option value="BE">Belgium</option>
                                    <option value="BZ">Belize</option>
                                    <option value="BJ">Benin</option>
                                    <option value="BM">Bermuda</option>
                                    <option value="BT">Bhutan</option>
                                    <option value="BO">Bolivia</option>
                                    <option value="BA">Bosnia and Herzegovina</option>
                                    <option value="BW">Botswana</option>
                                    <option value="BV">Bouvet Island</option>
                                    <option value="BR">Brazil</option>
                                    <option value="IO">British Indian Ocean Territory</option>
                                    <option value="BN">Brunei Darussalam</option>
                                    <option value="BG">Bulgaria</option>
                                    <option value="BF">Burkina Faso</option>
                                    <option value="BI">Burundi</option>
                                    <option value="KH">Cambodia</option>
                                    <option value="CM">Cameroon</option>
                                    <option value="CA">Canada</option>
                                    <option value="CV">Cape Verde</option>
                                    <option value="KY">Cayman Islands</option>
                                    <option value="CF">Central African Republic</option>
                                    <option value="TD">Chad</option>
                                    <option value="CL">Chile</option>
                                    <option value="CN">China</option>
                                    <option value="CX">Christmas Island</option>
                                    <option value="CC">Cocos (Keeling) Islands</option>
                                    <option value="CO">Colombia</option>
                                    <option value="KM">Comoros</option>
                                    <option value="CG">Congo</option>
                                    <option value="CD">Congo, the Democratic Republic of the</option>
                                    <option value="CK">Cook Islands</option>
                                    <option value="CR">Costa Rica</option>
                                    <option value="CI">Cote D'Ivoire</option>
                                    <option value="HR">Croatia</option>
                                    <option value="CU">Cuba</option>
                                    <option value="CY">Cyprus</option>
                                    <option value="CZ">Czech Republic</option>
                                    <option value="DK">Denmark</option>
                                    <option value="DJ">Djibouti</option>
                                    <option value="DM">Dominica</option>
                                    <option value="DO">Dominican Republic</option>
                                    <option value="EC">Ecuador</option>
                                    <option value="EG">Egypt</option>
                                    <option value="SV">El Salvador</option>
                                    <option value="GQ">Equatorial Guinea</option>
                                    <option value="ER">Eritrea</option>
                                    <option value="EE">Estonia</option>
                                    <option value="ET">Ethiopia</option>
                                    <option value="FK">Falkland Islands (Malvinas)</option>
                                    <option value="FO">Faroe Islands</option>
                                    <option value="FJ">Fiji</option>
                                    <option value="FI">Finland</option>
                                    <option value="FR">France</option>
                                    <option value="GF">French Guiana</option>
                                    <option value="PF">French Polynesia</option>
                                    <option value="TF">French Southern Territories</option>
                                    <option value="GA">Gabon</option>
                                    <option value="GM">Gambia</option>
                                    <option value="GE">Georgia</option>
                                    <option value="DE">Germany</option>
                                    <option value="GH">Ghana</option>
                                    <option value="GI">Gibraltar</option>
                                    <option value="GR">Greece</option>
                                    <option value="GL">Greenland</option>
                                    <option value="GD">Grenada</option>
                                    <option value="GP">Guadeloupe</option>
                                    <option value="GU">Guam</option>
                                    <option value="GT">Guatemala</option>
                                    <option value="GN">Guinea</option>
                                    <option value="GW">Guinea-Bissau</option>
                                    <option value="GY">Guyana</option>
                                    <option value="HT">Haiti</option>
                                    <option value="HM">Heard Island and Mcdonald Islands</option>
                                    <option value="VA">Holy See (Vatican City State)</option>
                                    <option value="HN">Honduras</option>
                                    <option value="HK">Hong Kong</option>
                                    <option value="HU">Hungary</option>
                                    <option value="IS">Iceland</option>
                                    <option value="IN">India</option>
                                    <option value="ID">Indonesia</option>
                                    <option value="IR">Iran, Islamic Republic of</option>
                                    <option value="IQ">Iraq</option>
                                    <option value="IE">Ireland</option>
                                    <option value="IL">Israel</option>
                                    <option value="IT">Italy</option>
                                    <option value="JM">Jamaica</option>
                                    <option value="JP">Japan</option>
                                    <option value="JO">Jordan</option>
                                    <option value="KZ">Kazakhstan</option>
                                    <option value="KE">Kenya</option>
                                    <option value="KI">Kiribati</option>
                                    <option value="KP">Korea, Democratic People's Republic of</option>
                                    <option value="KR">Korea, Republic of</option>
                                    <option value="KW">Kuwait</option>
                                    <option value="KG">Kyrgyzstan</option>
                                    <option value="LA">Lao People's Democratic Republic</option>
                                    <option value="LV">Latvia</option>
                                    <option value="LB">Lebanon</option>
                                    <option value="LS">Lesotho</option>
                                    <option value="LR">Liberia</option>
                                    <option value="LY">Libyan Arab Jamahiriya</option>
                                    <option value="LI">Liechtenstein</option>
                                    <option value="LT">Lithuania</option>
                                    <option value="LU">Luxembourg</option>
                                    <option value="MO">Macao</option>
                                    <option value="MK">Macedonia, the Former Yugoslav Republic of</option>
                                    <option value="MG">Madagascar</option>
                                    <option value="MW">Malawi</option>
                                    <option value="MY">Malaysia</option>
                                    <option value="MV">Maldives</option>
                                    <option value="ML">Mali</option>
                                    <option value="MT">Malta</option>
                                    <option value="MH">Marshall Islands</option>
                                    <option value="MQ">Martinique</option>
                                    <option value="MR">Mauritania</option>
                                    <option value="MU">Mauritius</option>
                                    <option value="YT">Mayotte</option>
                                    <option value="MX">Mexico</option>
                                    <option value="FM">Micronesia, Federated States of</option>
                                    <option value="MD">Moldova, Republic of</option>
                                    <option value="MC">Monaco</option>
                                    <option value="MN">Mongolia</option>
                                    <option value="MS">Montserrat</option>
                                    <option value="MA">Morocco</option>
                                    <option value="MZ">Mozambique</option>
                                    <option value="MM">Myanmar</option>
                                    <option value="NA">Namibia</option>
                                    <option value="NR">Nauru</option>
                                    <option value="NP">Nepal</option>
                                    <option value="NL">Netherlands</option>
                                    <option value="AN">Netherlands Antilles</option>
                                    <option value="NC">New Caledonia</option>
                                    <option value="NZ">New Zealand</option>
                                    <option value="NI">Nicaragua</option>
                                    <option value="NE">Niger</option>
                                    <option value="NG">Nigeria</option>
                                    <option value="NU">Niue</option>
                                    <option value="NF">Norfolk Island</option>
                                    <option value="MP">Northern Mariana Islands</option>
                                    <option value="NO">Norway</option>
                                    <option value="OM">Oman</option>
                                    <option value="PK">Pakistan</option>
                                    <option value="PW">Palau</option>
                                    <option value="PS">Palestinian Territory, Occupied</option>
                                    <option value="PA">Panama</option>
                                    <option value="PG">Papua New Guinea</option>
                                    <option value="PY">Paraguay</option>
                                    <option value="PE">Peru</option>
                                    <option value="PH">Philippines</option>
                                    <option value="PN">Pitcairn</option>
                                    <option value="PL">Poland</option>
                                    <option value="PT">Portugal</option>
                                    <option value="PR">Puerto Rico</option>
                                    <option value="QA">Qatar</option>
                                    <option value="RE">Reunion</option>
                                    <option value="RO">Romania</option>
                                    <option value="RU">Russian Federation</option>
                                    <option value="RW">Rwanda</option>
                                    <option value="SH">Saint Helena</option>
                                    <option value="KN">Saint Kitts and Nevis</option>
                                    <option value="LC">Saint Lucia</option>
                                    <option value="PM">Saint Pierre and Miquelon</option>
                                    <option value="VC">Saint Vincent and the Grenadines</option>
                                    <option value="WS">Samoa</option>
                                    <option value="SM">San Marino</option>
                                    <option value="ST">Sao Tome and Principe</option>
                                    <option value="SA">Saudi Arabia</option>
                                    <option value="SN">Senegal</option>
                                    <option value="CS">Serbia and Montenegro</option>
                                    <option value="SC">Seychelles</option>
                                    <option value="SL">Sierra Leone</option>
                                    <option value="SG">Singapore</option>
                                    <option value="SK">Slovakia</option>
                                    <option value="SI">Slovenia</option>
                                    <option value="SB">Solomon Islands</option>
                                    <option value="SO">Somalia</option>
                                    <option value="ZA">South Africa</option>
                                    <option value="GS">South Georgia and the South Sandwich Islands</option>
                                    <option value="ES">Spain</option>
                                    <option value="LK">Sri Lanka</option>
                                    <option value="SD">Sudan</option>
                                    <option value="SR">Suriname</option>
                                    <option value="SJ">Svalbard and Jan Mayen</option>
                                    <option value="SZ">Swaziland</option>
                                    <option value="SE">Sweden</option>
                                    <option value="CH">Switzerland</option>
                                    <option value="SY">Syrian Arab Republic</option>
                                    <option value="TW">Taiwan, Province of China</option>
                                    <option value="TJ">Tajikistan</option>
                                    <option value="TZ">Tanzania, United Republic of</option>
                                    <option value="TH">Thailand</option>
                                    <option value="TL">Timor-Leste</option>
                                    <option value="TG">Togo</option>
                                    <option value="TK">Tokelau</option>
                                    <option value="TO">Tonga</option>
                                    <option value="TT">Trinidad and Tobago</option>
                                    <option value="TN">Tunisia</option>
                                    <option value="TR">Turkey</option>
                                    <option value="TM">Turkmenistan</option>
                                    <option value="TC">Turks and Caicos Islands</option>
                                    <option value="TV">Tuvalu</option>
                                    <option value="UG">Uganda</option>
                                    <option value="UA">Ukraine</option>
                                    <option value="AE">United Arab Emirates</option>
                                    <option value="GB">United Kingdom</option>
                                    <option value="US">United States</option>
                                    <option value="UM">United States Minor Outlying Islands</option>
                                    <option value="UY">Uruguay</option>
                                    <option value="UZ">Uzbekistan</option>
                                    <option value="VU">Vanuatu</option>
                                    <option value="VE">Venezuela</option>
                                    <option value="VN">Viet Nam</option>
                                    <option value="VG">Virgin Islands, British</option>
                                    <option value="VI">Virgin Islands, U.S.</option>
                                    <option value="WF">Wallis and Futuna</option>
                                    <option value="EH">Western Sahara</option>
                                    <option value="YE">Yemen</option>
                                    <option value="ZM">Zambia</option>
                                    <option value="ZW">Zimbabwe</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" class="form-control" id="phone1" name="phone1">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Fax 1</label>
                                <input type="text" class="form-control" id="fax1" name="fax1">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Fax 2</label>
                                <input type="text" class="form-control" id="fax2" name="fax2">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" id="email_einvoice" name="email_einvoice">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnClose" class="btn btn_close_all btn-default"
                    data-dismiss="modal">Close</button>


                <button type="button" id="btnAddBilltoParty" class="btn btn-success float-right"
                    onclick="UpdateBillToInfo()">Save
                    <div class="overlay" style="display:none">
                        <i class="fa fa-refresh fa-spin"></i>
                    </div>
                </button>
            </div>
        </div>

    </div>
</div>

<script>
    $party_id = '';

    function loadInvBilltoDetails($id) {
        console.log('loadInvBilltoDetails called with ID:', $id);
        $party_id = $id;

        // Clear all fields first - use specific modal context to avoid conflicts
        $("#modalAddBilltoInfo #customer_name").val('');
        $("#modalAddBilltoInfo #customer_code").val('');
        $("#modalAddBilltoInfo #brn").val('');
        $("#modalAddBilltoInfo #brn2").val('');
        $("#modalAddBilltoInfo #sales_tax_no").val('');
        $("#modalAddBilltoInfo #service_tax_no").val('');
        $("#modalAddBilltoInfo #customer_category").val('');
        $("#modalAddBilltoInfo #id_type").val('');
        $("#modalAddBilltoInfo #id_no").val('');
        $("#modalAddBilltoInfo #tin").val('');
        $("#modalAddBilltoInfo #address_1").val('');
        $("#modalAddBilltoInfo #address_2").val('');
        $("#modalAddBilltoInfo #address_3").val('');
        $("#modalAddBilltoInfo #address_4").val('');
        $("#modalAddBilltoInfo #postcode").val('');
        $("#modalAddBilltoInfo #city").val('');
        $("#modalAddBilltoInfo #state").val('');
        $("#modalAddBilltoInfo #country").val('');
        $("#modalAddBilltoInfo #phone1").val('');
        $("#modalAddBilltoInfo #mobile").val('');
        $("#modalAddBilltoInfo #fax1").val('');
        $("#modalAddBilltoInfo #fax2").val('');
        $("#modalAddBilltoInfo #email_einvoice").val('');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: '/loadBillToInv/' + $id,
            data: null,
            processData: false,
            contentType: false,
            success: function(data) {
                console.log('AJAX Success - Full response:', data);
                console.log('Data object:', data.data);
                
                if (data.status === 1 && data.data) {
                    console.log('Populating form fields...');
                    console.log('Customer name:', data.data.customer_name);
                    console.log('Email:', data.data.email);
                    
                    // Now populate with data - use specific modal context to avoid conflicts
                    $("#modalAddBilltoInfo #customer_name").val(data.data.customer_name || '');
                    $("#modalAddBilltoInfo #customer_code").val(data.data.customer_code || '');
                    $("#modalAddBilltoInfo #brn").val(data.data.brn || '');
                    $("#modalAddBilltoInfo #brn2").val(data.data.brn2 || '');
                    $("#modalAddBilltoInfo #sales_tax_no").val(data.data.sales_tax_no || '');
                    $("#modalAddBilltoInfo #service_tax_no").val(data.data.service_tax_no || '');
                    $("#modalAddBilltoInfo #customer_category").val(data.data.customer_category || '');
                    $("#modalAddBilltoInfo #id_type").val(data.data.id_type || '');
                    $("#modalAddBilltoInfo #id_no").val(data.data.id_no || '');
                    $("#modalAddBilltoInfo #tin").val(data.data.tin || '');
                    $("#modalAddBilltoInfo #address_1").val(data.data.address_1 || '');
                    $("#modalAddBilltoInfo #address_2").val(data.data.address_2 || '');
                    $("#modalAddBilltoInfo #address_3").val(data.data.address_3 || '');
                    $("#modalAddBilltoInfo #address_4").val(data.data.address_4 || '');
                    $("#modalAddBilltoInfo #postcode").val(data.data.postcode || '');
                    $("#modalAddBilltoInfo #city").val(data.data.city || '');
                    $("#modalAddBilltoInfo #state").val(data.data.state || '');
                    $("#modalAddBilltoInfo #country").val(data.data.country || '');
                    $("#modalAddBilltoInfo #phone1").val(data.data.phone1 || '');
                    $("#modalAddBilltoInfo #mobile").val(data.data.mobile || '');
                    $("#modalAddBilltoInfo #fax1").val(data.data.fax1 || '');
                    $("#modalAddBilltoInfo #fax2").val(data.data.fax2 || '');
                    $("#modalAddBilltoInfo #email_einvoice").val(data.data.email || '');
                    
                    console.log('Form fields populated successfully');
                    console.log('Customer name field value:', $("#modalAddBilltoInfo #customer_name").val());
                    console.log('Email field value:', $("#modalAddBilltoInfo #email_einvoice").val());
                } else {
                    console.error('Error: Invalid response data', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                console.error('Status:', status);
                console.error('Error:', error);
            }
        });
    }




    function UpdateBillToInfo() {

        var form_data = $("#formAddBilltoInfo").serialize();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'POST',
            url: '/UpdateBillToInfo/' + $party_id,
            data: form_data,
            // processData: false,
            // contentType: false,
            success: function(data) {
                console.log(data);
                if (data.status == 1) {
                    toastController('Saved');
                    $("#lbl_bill_to_party").html(data.view);
                    location.reload();
                }

            }

        });

    }

    // Shared function for ID Type to default ID No
    function getDefaultIdNoByType(idType) {
        switch (idType) {
            case '2': return 'EI00000000010'; // General Public (NRIC)
            case '3': return 'EI00000000020'; // Foreign Buyer (Passport)
            case '4': return 'EI00000000040'; // Government (ARMY)
            default: return '';
        }
    }

    $(document).ready(function() {
        $('#modalAddBilltoInfo #id_type').on('change', function() {
            var idType = $(this).val();
            var defaultIdNo = getDefaultIdNoByType(idType);
            $('#modalAddBilltoInfo #tin').val(defaultIdNo);
        });
        
        // Add modal event handlers for debugging
        $('#modalAddBilltoInfo').on('show.bs.modal', function () {
            console.log('Modal is about to be shown');
        });
        
        $('#modalAddBilltoInfo').on('shown.bs.modal', function () {
            console.log('Modal has been shown');
            
            // Load data when modal is shown
            if (window.pendingPartyId) {
                console.log('Loading data for party ID:', window.pendingPartyId);
                loadInvBilltoDetails(window.pendingPartyId);
                window.pendingPartyId = null; // Clear the pending ID
            }
        });
        
        $('#modalAddBilltoInfo').on('hide.bs.modal', function () {
            console.log('Modal is about to be hidden');
        });
        
        $('#modalAddBilltoInfo').on('hidden.bs.modal', function () {
            console.log('Modal has been hidden');
        });
    });
  
</script>
