@section('css')
    <!-- <script src="{{ asset('js/timeline-js.js') }}"></script> -->
    <!-- <link href="{{ asset('css/timeline-style.css') }}" rel="stylesheet"> -->
    <!-- <link href="{{ asset('css/paperfish/bootstrap.min.css') }}" rel="stylesheet"> -->
    <!-- <link href="{{ asset('css/paperfish/paper-bootstrap-wizard.css?0001') }}" rel="stylesheet"> -->

    <link href='http://fonts.googleapis.com/css?family=Titillium+Web:400,200,300,600,700' rel='stylesheet' type='text/css'>
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/external-master.css') }}" rel="stylesheet">
@endsection
@extends('dashboard.base')

@section('content')
    <div class="container-fluid">
        <div class="fade-in">
            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Edit E-Invoice Client Info</h4><br/>
                            <span class="text-danger">*</span> Kindly ensure that all required data fields are filled in prior to transmitting the records to SQL Accounting
                        </div>
                        <div class="card-body">
                            @if ($billingParty->completed == 1)
                                <div class="alert alert-success">
                                    <i class="fa fa-check-circle"></i> Status: Completed
                                </div>
                            @endif
                            @if ($billingParty->sent_to_sql == 1)
                                <div class="alert alert-info">
                                    <i class="fa fa-database"></i> Status: Already sent to SQL accounting system
                                </div>
                            @endif

                            <div class="box box-default">
                                <!-- /.box-header -->
                                <div class="box-body wizard-content">
                                    <div class="tab-wizard wizard-circle wizard clearfix">

                                        <div class="tab-content bg-color-none">

                                            <div class="tab-pane active" id="tab1">
                                                <div class="">

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
                                                                    <label>Customer Name<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="customer_name" name="customer_name" readonly
                                                                        value="{{ $billingParty->customer_name ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Ref No<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="customer_code" name="customer_code" readonly
                                                                        value="{{ $billingParty->customer_code ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Customer Category<span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-control" id="customer_category"
                                                                        name="customer_category">
                                                                        <option value=""
                                                                            {{ ($billingParty->customer_category ?? '') == '' ? 'selected' : '' }}>
                                                                            Select Category</option>
                                                                        <option value="1"
                                                                            {{ ($billingParty->customer_category ?? '') == '1' ? 'selected' : '' }}>
                                                                            Individual</option>
                                                                        <option value="2"
                                                                            {{ ($billingParty->customer_category ?? '') == '2' ? 'selected' : '' }}>
                                                                            Company</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>TIN<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="tin" name="tin"
                                                                        value="{{ $billingParty->tin ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>



                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>BRN</label>
                                                                    <input type="text" class="form-control"
                                                                        id="brn" name="brn"
                                                                        value="{{ $billingParty->brn ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>BRN2</label>
                                                                    <input type="text" class="form-control"
                                                                        id="brn2" name="brn2"
                                                                        value="{{ $billingParty->brn2 ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Sales Tax No</label>
                                                                    <input type="text" class="form-control"
                                                                        id="sales_tax_no" name="sales_tax_no"
                                                                        value="{{ $billingParty->sales_tax_no ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Service Tax No</label>
                                                                    <input type="text" class="form-control"
                                                                        id="service_tax_no" name="service_tax_no"
                                                                        value="{{ $billingParty->service_tax_no ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>ID Type<span class="text-danger">*</span></label>
                                                                    <select class="form-control" id="id_type" name="id_type">
                                                                        <option value=""
                                                                            {{ ($billingParty->id_type ?? '') == '' ? 'selected' : '' }}>
                                                                            Select ID Type</option>
                                                                        <option value="1"
                                                                            {{ ($billingParty->id_type ?? '') == '1' ? 'selected' : '' }}>
                                                                            New Reg No</option>
                                                                        <option value="2"
                                                                            {{ ($billingParty->id_type ?? '') == '2' ? 'selected' : '' }}>
                                                                            NRIC</option>
                                                                        <option value="3"
                                                                            {{ ($billingParty->id_type ?? '') == '3' ? 'selected' : '' }}>
                                                                            Passport</option>
                                                                        <option value="4"
                                                                            {{ ($billingParty->id_type ?? '') == '4' ? 'selected' : '' }}>
                                                                            ARMY</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>ID No<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="id_no" name="id_no"
                                                                        value="{{ $billingParty->id_no ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>


                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label>Address Line 1<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="address_1" name="address_1"
                                                                        value="{{ $billingParty->address_1 ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label>Address Line 2</label>
                                                                    <input type="text" class="form-control"
                                                                        id="address_2" name="address_2"
                                                                        value="{{ $billingParty->address_2 ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label>Address Line 3</label>
                                                                    <input type="text" class="form-control"
                                                                        id="address_3" name="address_3"
                                                                        value="{{ $billingParty->address_3 ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-12">
                                                                <div class="form-group">
                                                                    <label>Address Line 4</label>
                                                                    <input type="text" class="form-control"
                                                                        id="address_4" name="address_4"
                                                                        value="{{ $billingParty->address_4 ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Postcode<span
                                                                            class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="postcode" name="postcode"
                                                                        value="{{ $billingParty->postcode ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>City<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="city" name="city"
                                                                        value="{{ $billingParty->city ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>State<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="state" name="state"
                                                                        value="{{ $billingParty->state ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Country<span
                                                                            class="text-danger">*</span></label>
                                                                    <select class="form-control" id="country"
                                                                        name="country">
                                                                        <option value=""
                                                                            {{ ($billingParty->country ?? '') == '' ? 'selected' : '' }}>
                                                                            Select Country</option>
                                                                        <option value="AF"
                                                                            {{ ($billingParty->country ?? '') == 'AF' ? 'selected' : '' }}>
                                                                            Afghanistan</option>
                                                                        <option value="AL"
                                                                            {{ ($billingParty->country ?? '') == 'AL' ? 'selected' : '' }}>
                                                                            Albania</option>
                                                                        <option value="DZ"
                                                                            {{ ($billingParty->country ?? '') == 'DZ' ? 'selected' : '' }}>
                                                                            Algeria</option>
                                                                        <option value="AS"
                                                                            {{ ($billingParty->country ?? '') == 'AS' ? 'selected' : '' }}>
                                                                            American Samoa</option>
                                                                        <option value="AD"
                                                                            {{ ($billingParty->country ?? '') == 'AD' ? 'selected' : '' }}>
                                                                            Andorra</option>
                                                                        <option value="AO"
                                                                            {{ ($billingParty->country ?? '') == 'AO' ? 'selected' : '' }}>
                                                                            Angola</option>
                                                                        <option value="AI"
                                                                            {{ ($billingParty->country ?? '') == 'AI' ? 'selected' : '' }}>
                                                                            Anguilla</option>
                                                                        <option value="AQ"
                                                                            {{ ($billingParty->country ?? '') == 'AQ' ? 'selected' : '' }}>
                                                                            Antarctica</option>
                                                                        <option value="AG"
                                                                            {{ ($billingParty->country ?? '') == 'AG' ? 'selected' : '' }}>
                                                                            Antigua and Barbuda</option>
                                                                        <option value="AR"
                                                                            {{ ($billingParty->country ?? '') == 'AR' ? 'selected' : '' }}>
                                                                            Argentina</option>
                                                                        <option value="AM"
                                                                            {{ ($billingParty->country ?? '') == 'AM' ? 'selected' : '' }}>
                                                                            Armenia</option>
                                                                        <option value="AW"
                                                                            {{ ($billingParty->country ?? '') == 'AW' ? 'selected' : '' }}>
                                                                            Aruba</option>
                                                                        <option value="AU"
                                                                            {{ ($billingParty->country ?? '') == 'AU' ? 'selected' : '' }}>
                                                                            Australia</option>
                                                                        <option value="AT"
                                                                            {{ ($billingParty->country ?? '') == 'AT' ? 'selected' : '' }}>
                                                                            Austria</option>
                                                                        <option value="AZ"
                                                                            {{ ($billingParty->country ?? '') == 'AZ' ? 'selected' : '' }}>
                                                                            Azerbaijan</option>
                                                                        <option value="BS"
                                                                            {{ ($billingParty->country ?? '') == 'BS' ? 'selected' : '' }}>
                                                                            Bahamas</option>
                                                                        <option value="BH"
                                                                            {{ ($billingParty->country ?? '') == 'BH' ? 'selected' : '' }}>
                                                                            Bahrain</option>
                                                                        <option value="BD"
                                                                            {{ ($billingParty->country ?? '') == 'BD' ? 'selected' : '' }}>
                                                                            Bangladesh</option>
                                                                        <option value="BB"
                                                                            {{ ($billingParty->country ?? '') == 'BB' ? 'selected' : '' }}>
                                                                            Barbados</option>
                                                                        <option value="BY"
                                                                            {{ ($billingParty->country ?? '') == 'BY' ? 'selected' : '' }}>
                                                                            Belarus</option>
                                                                        <option value="BE"
                                                                            {{ ($billingParty->country ?? '') == 'BE' ? 'selected' : '' }}>
                                                                            Belgium</option>
                                                                        <option value="BZ"
                                                                            {{ ($billingParty->country ?? '') == 'BZ' ? 'selected' : '' }}>
                                                                            Belize</option>
                                                                        <option value="BJ"
                                                                            {{ ($billingParty->country ?? '') == 'BJ' ? 'selected' : '' }}>
                                                                            Benin</option>
                                                                        <option value="BM"
                                                                            {{ ($billingParty->country ?? '') == 'BM' ? 'selected' : '' }}>
                                                                            Bermuda</option>
                                                                        <option value="BT"
                                                                            {{ ($billingParty->country ?? '') == 'BT' ? 'selected' : '' }}>
                                                                            Bhutan</option>
                                                                        <option value="BO"
                                                                            {{ ($billingParty->country ?? '') == 'BO' ? 'selected' : '' }}>
                                                                            Bolivia</option>
                                                                        <option value="BA"
                                                                            {{ ($billingParty->country ?? '') == 'BA' ? 'selected' : '' }}>
                                                                            Bosnia and Herzegovina
                                                                        </option>
                                                                        <option value="BW"
                                                                            {{ ($billingParty->country ?? '') == 'BW' ? 'selected' : '' }}>
                                                                            Botswana</option>
                                                                        <option value="BV"
                                                                            {{ ($billingParty->country ?? '') == 'BV' ? 'selected' : '' }}>
                                                                            Bouvet Island</option>
                                                                        <option value="BR"
                                                                            {{ ($billingParty->country ?? '') == 'BR' ? 'selected' : '' }}>
                                                                            Brazil</option>
                                                                        <option value="IO"
                                                                            {{ ($billingParty->country ?? '') == 'IO' ? 'selected' : '' }}>
                                                                            British Indian Ocean
                                                                            Territory</option>
                                                                        <option value="BN"
                                                                            {{ ($billingParty->country ?? '') == 'BN' ? 'selected' : '' }}>
                                                                            Brunei Darussalam</option>
                                                                        <option value="BG"
                                                                            {{ ($billingParty->country ?? '') == 'BG' ? 'selected' : '' }}>
                                                                            Bulgaria</option>
                                                                        <option value="BF"
                                                                            {{ ($billingParty->country ?? '') == 'BF' ? 'selected' : '' }}>
                                                                            Burkina Faso</option>
                                                                        <option value="BI"
                                                                            {{ ($billingParty->country ?? '') == 'BI' ? 'selected' : '' }}>
                                                                            Burundi</option>
                                                                        <option value="KH"
                                                                            {{ ($billingParty->country ?? '') == 'KH' ? 'selected' : '' }}>
                                                                            Cambodia</option>
                                                                        <option value="CM"
                                                                            {{ ($billingParty->country ?? '') == 'CM' ? 'selected' : '' }}>
                                                                            Cameroon</option>
                                                                        <option value="CA"
                                                                            {{ ($billingParty->country ?? '') == 'CA' ? 'selected' : '' }}>
                                                                            Canada</option>
                                                                        <option value="CV"
                                                                            {{ ($billingParty->country ?? '') == 'CV' ? 'selected' : '' }}>
                                                                            Cape Verde</option>
                                                                        <option value="KY"
                                                                            {{ ($billingParty->country ?? '') == 'KY' ? 'selected' : '' }}>
                                                                            Cayman Islands</option>
                                                                        <option value="CF"
                                                                            {{ ($billingParty->country ?? '') == 'CF' ? 'selected' : '' }}>
                                                                            Central African Republic
                                                                        </option>
                                                                        <option value="TD"
                                                                            {{ ($billingParty->country ?? '') == 'TD' ? 'selected' : '' }}>
                                                                            Chad</option>
                                                                        <option value="CL"
                                                                            {{ ($billingParty->country ?? '') == 'CL' ? 'selected' : '' }}>
                                                                            Chile</option>
                                                                        <option value="CN"
                                                                            {{ ($billingParty->country ?? '') == 'CN' ? 'selected' : '' }}>
                                                                            China</option>
                                                                        <option value="CX"
                                                                            {{ ($billingParty->country ?? '') == 'CX' ? 'selected' : '' }}>
                                                                            Christmas Island</option>
                                                                        <option value="CC"
                                                                            {{ ($billingParty->country ?? '') == 'CC' ? 'selected' : '' }}>
                                                                            Cocos (Keeling) Islands
                                                                        </option>
                                                                        <option value="CO"
                                                                            {{ ($billingParty->country ?? '') == 'CO' ? 'selected' : '' }}>
                                                                            Colombia</option>
                                                                        <option value="KM"
                                                                            {{ ($billingParty->country ?? '') == 'KM' ? 'selected' : '' }}>
                                                                            Comoros</option>
                                                                        <option value="CG"
                                                                            {{ ($billingParty->country ?? '') == 'CG' ? 'selected' : '' }}>
                                                                            Congo</option>
                                                                        <option value="CD"
                                                                            {{ ($billingParty->country ?? '') == 'CD' ? 'selected' : '' }}>
                                                                            Congo, the Democratic
                                                                            Republic of the</option>
                                                                        <option value="CK"
                                                                            {{ ($billingParty->country ?? '') == 'CK' ? 'selected' : '' }}>
                                                                            Cook Islands</option>
                                                                        <option value="CR"
                                                                            {{ ($billingParty->country ?? '') == 'CR' ? 'selected' : '' }}>
                                                                            Costa Rica</option>
                                                                        <option value="CI"
                                                                            {{ ($billingParty->country ?? '') == 'CI' ? 'selected' : '' }}>
                                                                            Cote D'Ivoire</option>
                                                                        <option value="HR"
                                                                            {{ ($billingParty->country ?? '') == 'HR' ? 'selected' : '' }}>
                                                                            Croatia</option>
                                                                        <option value="CU"
                                                                            {{ ($billingParty->country ?? '') == 'CU' ? 'selected' : '' }}>
                                                                            Cuba</option>
                                                                        <option value="CY"
                                                                            {{ ($billingParty->country ?? '') == 'CY' ? 'selected' : '' }}>
                                                                            Cyprus</option>
                                                                        <option value="CZ"
                                                                            {{ ($billingParty->country ?? '') == 'CZ' ? 'selected' : '' }}>
                                                                            Czech Republic</option>
                                                                        <option value="DK"
                                                                            {{ ($billingParty->country ?? '') == 'DK' ? 'selected' : '' }}>
                                                                            Denmark</option>
                                                                        <option value="DJ"
                                                                            {{ ($billingParty->country ?? '') == 'DJ' ? 'selected' : '' }}>
                                                                            Djibouti</option>
                                                                        <option value="DM"
                                                                            {{ ($billingParty->country ?? '') == 'DM' ? 'selected' : '' }}>
                                                                            Dominica</option>
                                                                        <option value="DO"
                                                                            {{ ($billingParty->country ?? '') == 'DO' ? 'selected' : '' }}>
                                                                            Dominican Republic</option>
                                                                        <option value="EC"
                                                                            {{ ($billingParty->country ?? '') == 'EC' ? 'selected' : '' }}>
                                                                            Ecuador</option>
                                                                        <option value="EG"
                                                                            {{ ($billingParty->country ?? '') == 'EG' ? 'selected' : '' }}>
                                                                            Egypt</option>
                                                                        <option value="SV"
                                                                            {{ ($billingParty->country ?? '') == 'SV' ? 'selected' : '' }}>
                                                                            El Salvador</option>
                                                                        <option value="GQ"
                                                                            {{ ($billingParty->country ?? '') == 'GQ' ? 'selected' : '' }}>
                                                                            Equatorial Guinea</option>
                                                                        <option value="ER"
                                                                            {{ ($billingParty->country ?? '') == 'ER' ? 'selected' : '' }}>
                                                                            Eritrea</option>
                                                                        <option value="EE"
                                                                            {{ ($billingParty->country ?? '') == 'EE' ? 'selected' : '' }}>
                                                                            Estonia</option>
                                                                        <option value="ET"
                                                                            {{ ($billingParty->country ?? '') == 'ET' ? 'selected' : '' }}>
                                                                            Ethiopia</option>
                                                                        <option value="FK"
                                                                            {{ ($billingParty->country ?? '') == 'FK' ? 'selected' : '' }}>
                                                                            Falkland Islands (Malvinas)
                                                                        </option>
                                                                        <option value="FO"
                                                                            {{ ($billingParty->country ?? '') == 'FO' ? 'selected' : '' }}>
                                                                            Faroe Islands</option>
                                                                        <option value="FJ"
                                                                            {{ ($billingParty->country ?? '') == 'FJ' ? 'selected' : '' }}>
                                                                            Fiji</option>
                                                                        <option value="FI"
                                                                            {{ ($billingParty->country ?? '') == 'FI' ? 'selected' : '' }}>
                                                                            Finland</option>
                                                                        <option value="FR"
                                                                            {{ ($billingParty->country ?? '') == 'FR' ? 'selected' : '' }}>
                                                                            France</option>
                                                                        <option value="GF"
                                                                            {{ ($billingParty->country ?? '') == 'GF' ? 'selected' : '' }}>
                                                                            French Guiana</option>
                                                                        <option value="PF"
                                                                            {{ ($billingParty->country ?? '') == 'PF' ? 'selected' : '' }}>
                                                                            French Polynesia</option>
                                                                        <option value="TF"
                                                                            {{ ($billingParty->country ?? '') == 'TF' ? 'selected' : '' }}>
                                                                            French Southern Territories
                                                                        </option>
                                                                        <option value="GA"
                                                                            {{ ($billingParty->country ?? '') == 'GA' ? 'selected' : '' }}>
                                                                            Gabon</option>
                                                                        <option value="GM"
                                                                            {{ ($billingParty->country ?? '') == 'GM' ? 'selected' : '' }}>
                                                                            Gambia</option>
                                                                        <option value="GE"
                                                                            {{ ($billingParty->country ?? '') == 'GE' ? 'selected' : '' }}>
                                                                            Georgia</option>
                                                                        <option value="DE"
                                                                            {{ ($billingParty->country ?? '') == 'DE' ? 'selected' : '' }}>
                                                                            Germany</option>
                                                                        <option value="GH"
                                                                            {{ ($billingParty->country ?? '') == 'GH' ? 'selected' : '' }}>
                                                                            Ghana</option>
                                                                        <option value="GI"
                                                                            {{ ($billingParty->country ?? '') == 'GI' ? 'selected' : '' }}>
                                                                            Gibraltar</option>
                                                                        <option value="GR"
                                                                            {{ ($billingParty->country ?? '') == 'GR' ? 'selected' : '' }}>
                                                                            Greece</option>
                                                                        <option value="GL"
                                                                            {{ ($billingParty->country ?? '') == 'GL' ? 'selected' : '' }}>
                                                                            Greenland</option>
                                                                        <option value="GD"
                                                                            {{ ($billingParty->country ?? '') == 'GD' ? 'selected' : '' }}>
                                                                            Grenada</option>
                                                                        <option value="GP"
                                                                            {{ ($billingParty->country ?? '') == 'GP' ? 'selected' : '' }}>
                                                                            Guadeloupe</option>
                                                                        <option value="GU"
                                                                            {{ ($billingParty->country ?? '') == 'GU' ? 'selected' : '' }}>
                                                                            Guam</option>
                                                                        <option value="GT"
                                                                            {{ ($billingParty->country ?? '') == 'GT' ? 'selected' : '' }}>
                                                                            Guatemala</option>
                                                                        <option value="GN"
                                                                            {{ ($billingParty->country ?? '') == 'GN' ? 'selected' : '' }}>
                                                                            Guinea</option>
                                                                        <option value="GW"
                                                                            {{ ($billingParty->country ?? '') == 'GW' ? 'selected' : '' }}>
                                                                            Guinea-Bissau</option>
                                                                        <option value="GY"
                                                                            {{ ($billingParty->country ?? '') == 'GY' ? 'selected' : '' }}>
                                                                            Guyana</option>
                                                                        <option value="HT"
                                                                            {{ ($billingParty->country ?? '') == 'HT' ? 'selected' : '' }}>
                                                                            Haiti</option>
                                                                        <option value="HM"
                                                                            {{ ($billingParty->country ?? '') == 'HM' ? 'selected' : '' }}>
                                                                            Heard Island and Mcdonald
                                                                            Islands</option>
                                                                        <option value="VA"
                                                                            {{ ($billingParty->country ?? '') == 'VA' ? 'selected' : '' }}>
                                                                            Holy See (Vatican City
                                                                            State)</option>
                                                                        <option value="HN"
                                                                            {{ ($billingParty->country ?? '') == 'HN' ? 'selected' : '' }}>
                                                                            Honduras</option>
                                                                        <option value="HK"
                                                                            {{ ($billingParty->country ?? '') == 'HK' ? 'selected' : '' }}>
                                                                            Hong Kong</option>
                                                                        <option value="HU"
                                                                            {{ ($billingParty->country ?? '') == 'HU' ? 'selected' : '' }}>
                                                                            Hungary</option>
                                                                        <option value="IS"
                                                                            {{ ($billingParty->country ?? '') == 'IS' ? 'selected' : '' }}>
                                                                            Iceland</option>
                                                                        <option value="IN"
                                                                            {{ ($billingParty->country ?? '') == 'IN' ? 'selected' : '' }}>
                                                                            India</option>
                                                                        <option value="ID"
                                                                            {{ ($billingParty->country ?? '') == 'ID' ? 'selected' : '' }}>
                                                                            Indonesia</option>
                                                                        <option value="IR"
                                                                            {{ ($billingParty->country ?? '') == 'IR' ? 'selected' : '' }}>
                                                                            Iran, Islamic Republic of
                                                                        </option>
                                                                        <option value="IQ"
                                                                            {{ ($billingParty->country ?? '') == 'IQ' ? 'selected' : '' }}>
                                                                            Iraq</option>
                                                                        <option value="IE"
                                                                            {{ ($billingParty->country ?? '') == 'IE' ? 'selected' : '' }}>
                                                                            Ireland</option>
                                                                        <option value="IL"
                                                                            {{ ($billingParty->country ?? '') == 'IL' ? 'selected' : '' }}>
                                                                            Israel</option>
                                                                        <option value="IT"
                                                                            {{ ($billingParty->country ?? '') == 'IT' ? 'selected' : '' }}>
                                                                            Italy</option>
                                                                        <option value="JM"
                                                                            {{ ($billingParty->country ?? '') == 'JM' ? 'selected' : '' }}>
                                                                            Jamaica</option>
                                                                        <option value="JP"
                                                                            {{ ($billingParty->country ?? '') == 'JP' ? 'selected' : '' }}>
                                                                            Japan</option>
                                                                        <option value="JO"
                                                                            {{ ($billingParty->country ?? '') == 'JO' ? 'selected' : '' }}>
                                                                            Jordan</option>
                                                                        <option value="KZ"
                                                                            {{ ($billingParty->country ?? '') == 'KZ' ? 'selected' : '' }}>
                                                                            Kazakhstan</option>
                                                                        <option value="KE"
                                                                            {{ ($billingParty->country ?? '') == 'KE' ? 'selected' : '' }}>
                                                                            Kenya</option>
                                                                        <option value="KI"
                                                                            {{ ($billingParty->country ?? '') == 'KI' ? 'selected' : '' }}>
                                                                            Kiribati</option>
                                                                        <option value="KP"
                                                                            {{ ($billingParty->country ?? '') == 'KP' ? 'selected' : '' }}>
                                                                            Korea, Democratic People's
                                                                            Republic of</option>
                                                                        <option value="KR"
                                                                            {{ ($billingParty->country ?? '') == 'KR' ? 'selected' : '' }}>
                                                                            Korea, Republic of</option>
                                                                        <option value="KW"
                                                                            {{ ($billingParty->country ?? '') == 'KW' ? 'selected' : '' }}>
                                                                            Kuwait</option>
                                                                        <option value="KG"
                                                                            {{ ($billingParty->country ?? '') == 'KG' ? 'selected' : '' }}>
                                                                            Kyrgyzstan</option>
                                                                        <option value="LA"
                                                                            {{ ($billingParty->country ?? '') == 'LA' ? 'selected' : '' }}>
                                                                            Lao People's Democratic
                                                                            Republic</option>
                                                                        <option value="LV"
                                                                            {{ ($billingParty->country ?? '') == 'LV' ? 'selected' : '' }}>
                                                                            Latvia</option>
                                                                        <option value="LB"
                                                                            {{ ($billingParty->country ?? '') == 'LB' ? 'selected' : '' }}>
                                                                            Lebanon</option>
                                                                        <option value="LS"
                                                                            {{ ($billingParty->country ?? '') == 'LS' ? 'selected' : '' }}>
                                                                            Lesotho</option>
                                                                        <option value="LR"
                                                                            {{ ($billingParty->country ?? '') == 'LR' ? 'selected' : '' }}>
                                                                            Liberia</option>
                                                                        <option value="LY"
                                                                            {{ ($billingParty->country ?? '') == 'LY' ? 'selected' : '' }}>
                                                                            Libyan Arab Jamahiriya
                                                                        </option>
                                                                        <option value="LI"
                                                                            {{ ($billingParty->country ?? '') == 'LI' ? 'selected' : '' }}>
                                                                            Liechtenstein</option>
                                                                        <option value="LT"
                                                                            {{ ($billingParty->country ?? '') == 'LT' ? 'selected' : '' }}>
                                                                            Lithuania</option>
                                                                        <option value="LU"
                                                                            {{ ($billingParty->country ?? '') == 'LU' ? 'selected' : '' }}>
                                                                            Luxembourg</option>
                                                                        <option value="MO"
                                                                            {{ ($billingParty->country ?? '') == 'MO' ? 'selected' : '' }}>
                                                                            Macao</option>
                                                                        <option value="MK"
                                                                            {{ ($billingParty->country ?? '') == 'MK' ? 'selected' : '' }}>
                                                                            Macedonia, the Former
                                                                            Yugoslav Republic of</option>
                                                                        <option value="MG"
                                                                            {{ ($billingParty->country ?? '') == 'MG' ? 'selected' : '' }}>
                                                                            Madagascar</option>
                                                                        <option value="MW"
                                                                            {{ ($billingParty->country ?? '') == 'MW' ? 'selected' : '' }}>
                                                                            Malawi</option>
                                                                        <option value="MY"
                                                                            {{ ($billingParty->country ?? '') == 'MY' ? 'selected' : '' }}>
                                                                            Malaysia</option>
                                                                        <option value="MV"
                                                                            {{ ($billingParty->country ?? '') == 'MV' ? 'selected' : '' }}>
                                                                            Maldives</option>
                                                                        <option value="ML"
                                                                            {{ ($billingParty->country ?? '') == 'ML' ? 'selected' : '' }}>
                                                                            Mali</option>
                                                                        <option value="MT"
                                                                            {{ ($billingParty->country ?? '') == 'MT' ? 'selected' : '' }}>
                                                                            Malta</option>
                                                                        <option value="MH"
                                                                            {{ ($billingParty->country ?? '') == 'MH' ? 'selected' : '' }}>
                                                                            Marshall Islands</option>
                                                                        <option value="MQ"
                                                                            {{ ($billingParty->country ?? '') == 'MQ' ? 'selected' : '' }}>
                                                                            Martinique</option>
                                                                        <option value="MR"
                                                                            {{ ($billingParty->country ?? '') == 'MR' ? 'selected' : '' }}>
                                                                            Mauritania</option>
                                                                        <option value="MU"
                                                                            {{ ($billingParty->country ?? '') == 'MU' ? 'selected' : '' }}>
                                                                            Mauritius</option>
                                                                        <option value="YT"
                                                                            {{ ($billingParty->country ?? '') == 'YT' ? 'selected' : '' }}>
                                                                            Mayotte</option>
                                                                        <option value="MX"
                                                                            {{ ($billingParty->country ?? '') == 'MX' ? 'selected' : '' }}>
                                                                            Mexico</option>
                                                                        <option value="FM"
                                                                            {{ ($billingParty->country ?? '') == 'FM' ? 'selected' : '' }}>
                                                                            Micronesia, Federated States
                                                                            of</option>
                                                                        <option value="MD"
                                                                            {{ ($billingParty->country ?? '') == 'MD' ? 'selected' : '' }}>
                                                                            Moldova, Republic of
                                                                        </option>
                                                                        <option value="MC"
                                                                            {{ ($billingParty->country ?? '') == 'MC' ? 'selected' : '' }}>
                                                                            Monaco</option>
                                                                        <option value="MN"
                                                                            {{ ($billingParty->country ?? '') == 'MN' ? 'selected' : '' }}>
                                                                            Mongolia</option>
                                                                        <option value="MS"
                                                                            {{ ($billingParty->country ?? '') == 'MS' ? 'selected' : '' }}>
                                                                            Montserrat</option>
                                                                        <option value="MA"
                                                                            {{ ($billingParty->country ?? '') == 'MA' ? 'selected' : '' }}>
                                                                            Morocco</option>
                                                                        <option value="MZ"
                                                                            {{ ($billingParty->country ?? '') == 'MZ' ? 'selected' : '' }}>
                                                                            Mozambique</option>
                                                                        <option value="MM"
                                                                            {{ ($billingParty->country ?? '') == 'MM' ? 'selected' : '' }}>
                                                                            Myanmar</option>
                                                                        <option value="NA"
                                                                            {{ ($billingParty->country ?? '') == 'NA' ? 'selected' : '' }}>
                                                                            Namibia</option>
                                                                        <option value="NR"
                                                                            {{ ($billingParty->country ?? '') == 'NR' ? 'selected' : '' }}>
                                                                            Nauru</option>
                                                                        <option value="NP"
                                                                            {{ ($billingParty->country ?? '') == 'NP' ? 'selected' : '' }}>
                                                                            Nepal</option>
                                                                        <option value="NL"
                                                                            {{ ($billingParty->country ?? '') == 'NL' ? 'selected' : '' }}>
                                                                            Netherlands</option>
                                                                        <option value="AN"
                                                                            {{ ($billingParty->country ?? '') == 'AN' ? 'selected' : '' }}>
                                                                            Netherlands Antilles
                                                                        </option>
                                                                        <option value="NC"
                                                                            {{ ($billingParty->country ?? '') == 'NC' ? 'selected' : '' }}>
                                                                            New Caledonia</option>
                                                                        <option value="NZ"
                                                                            {{ ($billingParty->country ?? '') == 'NZ' ? 'selected' : '' }}>
                                                                            New Zealand</option>
                                                                        <option value="NI"
                                                                            {{ ($billingParty->country ?? '') == 'NI' ? 'selected' : '' }}>
                                                                            Nicaragua</option>
                                                                        <option value="NE"
                                                                            {{ ($billingParty->country ?? '') == 'NE' ? 'selected' : '' }}>
                                                                            Niger</option>
                                                                        <option value="NG"
                                                                            {{ ($billingParty->country ?? '') == 'NG' ? 'selected' : '' }}>
                                                                            Nigeria</option>
                                                                        <option value="NU"
                                                                            {{ ($billingParty->country ?? '') == 'NU' ? 'selected' : '' }}>
                                                                            Niue</option>
                                                                        <option value="NF"
                                                                            {{ ($billingParty->country ?? '') == 'NF' ? 'selected' : '' }}>
                                                                            Norfolk Island</option>
                                                                        <option value="MP"
                                                                            {{ ($billingParty->country ?? '') == 'MP' ? 'selected' : '' }}>
                                                                            Northern Mariana Islands
                                                                        </option>
                                                                        <option value="NO"
                                                                            {{ ($billingParty->country ?? '') == 'NO' ? 'selected' : '' }}>
                                                                            Norway</option>
                                                                        <option value="OM"
                                                                            {{ ($billingParty->country ?? '') == 'OM' ? 'selected' : '' }}>
                                                                            Oman</option>
                                                                        <option value="PK"
                                                                            {{ ($billingParty->country ?? '') == 'PK' ? 'selected' : '' }}>
                                                                            Pakistan</option>
                                                                        <option value="PW"
                                                                            {{ ($billingParty->country ?? '') == 'PW' ? 'selected' : '' }}>
                                                                            Palau</option>
                                                                        <option value="PS"
                                                                            {{ ($billingParty->country ?? '') == 'PS' ? 'selected' : '' }}>
                                                                            Palestinian Territory,
                                                                            Occupied</option>
                                                                        <option value="PA"
                                                                            {{ ($billingParty->country ?? '') == 'PA' ? 'selected' : '' }}>
                                                                            Panama</option>
                                                                        <option value="PG"
                                                                            {{ ($billingParty->country ?? '') == 'PG' ? 'selected' : '' }}>
                                                                            Papua New Guinea</option>
                                                                        <option value="PY"
                                                                            {{ ($billingParty->country ?? '') == 'PY' ? 'selected' : '' }}>
                                                                            Paraguay</option>
                                                                        <option value="PE"
                                                                            {{ ($billingParty->country ?? '') == 'PE' ? 'selected' : '' }}>
                                                                            Peru</option>
                                                                        <option value="PH"
                                                                            {{ ($billingParty->country ?? '') == 'PH' ? 'selected' : '' }}>
                                                                            Philippines</option>
                                                                        <option value="PN"
                                                                            {{ ($billingParty->country ?? '') == 'PN' ? 'selected' : '' }}>
                                                                            Pitcairn</option>
                                                                        <option value="PL"
                                                                            {{ ($billingParty->country ?? '') == 'PL' ? 'selected' : '' }}>
                                                                            Poland</option>
                                                                        <option value="PT"
                                                                            {{ ($billingParty->country ?? '') == 'PT' ? 'selected' : '' }}>
                                                                            Portugal</option>
                                                                        <option value="PR"
                                                                            {{ ($billingParty->country ?? '') == 'PR' ? 'selected' : '' }}>
                                                                            Puerto Rico</option>
                                                                        <option value="QA"
                                                                            {{ ($billingParty->country ?? '') == 'QA' ? 'selected' : '' }}>
                                                                            Qatar</option>
                                                                        <option value="RE"
                                                                            {{ ($billingParty->country ?? '') == 'RE' ? 'selected' : '' }}>
                                                                            Reunion</option>
                                                                        <option value="RO"
                                                                            {{ ($billingParty->country ?? '') == 'RO' ? 'selected' : '' }}>
                                                                            Romania</option>
                                                                        <option value="RU"
                                                                            {{ ($billingParty->country ?? '') == 'RU' ? 'selected' : '' }}>
                                                                            Russian Federation</option>
                                                                        <option value="RW"
                                                                            {{ ($billingParty->country ?? '') == 'RW' ? 'selected' : '' }}>
                                                                            Rwanda</option>
                                                                        <option value="SH"
                                                                            {{ ($billingParty->country ?? '') == 'SH' ? 'selected' : '' }}>
                                                                            Saint Helena</option>
                                                                        <option value="KN"
                                                                            {{ ($billingParty->country ?? '') == 'KN' ? 'selected' : '' }}>
                                                                            Saint Kitts and Nevis
                                                                        </option>
                                                                        <option value="LC"
                                                                            {{ ($billingParty->country ?? '') == 'LC' ? 'selected' : '' }}>
                                                                            Saint Lucia</option>
                                                                        <option value="PM"
                                                                            {{ ($billingParty->country ?? '') == 'PM' ? 'selected' : '' }}>
                                                                            Saint Pierre and Miquelon
                                                                        </option>
                                                                        <option value="VC"
                                                                            {{ ($billingParty->country ?? '') == 'VC' ? 'selected' : '' }}>
                                                                            Saint Vincent and the
                                                                            Grenadines</option>
                                                                        <option value="WS"
                                                                            {{ ($billingParty->country ?? '') == 'WS' ? 'selected' : '' }}>
                                                                            Samoa</option>
                                                                        <option value="SM"
                                                                            {{ ($billingParty->country ?? '') == 'SM' ? 'selected' : '' }}>
                                                                            San Marino</option>
                                                                        <option value="ST"
                                                                            {{ ($billingParty->country ?? '') == 'ST' ? 'selected' : '' }}>
                                                                            Sao Tome and Principe
                                                                        </option>
                                                                        <option value="SA"
                                                                            {{ ($billingParty->country ?? '') == 'SA' ? 'selected' : '' }}>
                                                                            Saudi Arabia</option>
                                                                        <option value="SN"
                                                                            {{ ($billingParty->country ?? '') == 'SN' ? 'selected' : '' }}>
                                                                            Senegal</option>
                                                                        <option value="CS"
                                                                            {{ ($billingParty->country ?? '') == 'CS' ? 'selected' : '' }}>
                                                                            Serbia and Montenegro
                                                                        </option>
                                                                        <option value="SC"
                                                                            {{ ($billingParty->country ?? '') == 'SC' ? 'selected' : '' }}>
                                                                            Seychelles</option>
                                                                        <option value="SL"
                                                                            {{ ($billingParty->country ?? '') == 'SL' ? 'selected' : '' }}>
                                                                            Sierra Leone</option>
                                                                        <option value="SG"
                                                                            {{ ($billingParty->country ?? '') == 'SG' ? 'selected' : '' }}>
                                                                            Singapore</option>
                                                                        <option value="SK"
                                                                            {{ ($billingParty->country ?? '') == 'SK' ? 'selected' : '' }}>
                                                                            Slovakia</option>
                                                                        <option value="SI"
                                                                            {{ ($billingParty->country ?? '') == 'SI' ? 'selected' : '' }}>
                                                                            Slovenia</option>
                                                                        <option value="SB"
                                                                            {{ ($billingParty->country ?? '') == 'SB' ? 'selected' : '' }}>
                                                                            Solomon Islands</option>
                                                                        <option value="SO"
                                                                            {{ ($billingParty->country ?? '') == 'SO' ? 'selected' : '' }}>
                                                                            Somalia</option>
                                                                        <option value="ZA"
                                                                            {{ ($billingParty->country ?? '') == 'ZA' ? 'selected' : '' }}>
                                                                            South Africa</option>
                                                                        <option value="GS"
                                                                            {{ ($billingParty->country ?? '') == 'GS' ? 'selected' : '' }}>
                                                                            South Georgia and the South
                                                                            Sandwich Islands</option>
                                                                        <option value="ES"
                                                                            {{ ($billingParty->country ?? '') == 'ES' ? 'selected' : '' }}>
                                                                            Spain</option>
                                                                        <option value="LK"
                                                                            {{ ($billingParty->country ?? '') == 'LK' ? 'selected' : '' }}>
                                                                            Sri Lanka</option>
                                                                        <option value="SD"
                                                                            {{ ($billingParty->country ?? '') == 'SD' ? 'selected' : '' }}>
                                                                            Sudan</option>
                                                                        <option value="SR"
                                                                            {{ ($billingParty->country ?? '') == 'SR' ? 'selected' : '' }}>
                                                                            Suriname</option>
                                                                        <option value="SJ"
                                                                            {{ ($billingParty->country ?? '') == 'SJ' ? 'selected' : '' }}>
                                                                            Svalbard and Jan Mayen
                                                                        </option>
                                                                        <option value="SZ"
                                                                            {{ ($billingParty->country ?? '') == 'SZ' ? 'selected' : '' }}>
                                                                            Swaziland</option>
                                                                        <option value="SE"
                                                                            {{ ($billingParty->country ?? '') == 'SE' ? 'selected' : '' }}>
                                                                            Sweden</option>
                                                                        <option value="CH"
                                                                            {{ ($billingParty->country ?? '') == 'CH' ? 'selected' : '' }}>
                                                                            Switzerland</option>
                                                                        <option value="SY"
                                                                            {{ ($billingParty->country ?? '') == 'SY' ? 'selected' : '' }}>
                                                                            Syrian Arab Republic
                                                                        </option>
                                                                        <option value="TW"
                                                                            {{ ($billingParty->country ?? '') == 'TW' ? 'selected' : '' }}>
                                                                            Taiwan, Province of China
                                                                        </option>
                                                                        <option value="TJ"
                                                                            {{ ($billingParty->country ?? '') == 'TJ' ? 'selected' : '' }}>
                                                                            Tajikistan</option>
                                                                        <option value="TZ"
                                                                            {{ ($billingParty->country ?? '') == 'TZ' ? 'selected' : '' }}>
                                                                            Tanzania, United Republic of
                                                                        </option>
                                                                        <option value="TH"
                                                                            {{ ($billingParty->country ?? '') == 'TH' ? 'selected' : '' }}>
                                                                            Thailand</option>
                                                                        <option value="TL"
                                                                            {{ ($billingParty->country ?? '') == 'TL' ? 'selected' : '' }}>
                                                                            Timor-Leste</option>
                                                                        <option value="TG"
                                                                            {{ ($billingParty->country ?? '') == 'TG' ? 'selected' : '' }}>
                                                                            Togo</option>
                                                                        <option value="TK"
                                                                            {{ ($billingParty->country ?? '') == 'TK' ? 'selected' : '' }}>
                                                                            Tokelau</option>
                                                                        <option value="TO"
                                                                            {{ ($billingParty->country ?? '') == 'TO' ? 'selected' : '' }}>
                                                                            Tonga</option>
                                                                        <option value="TT"
                                                                            {{ ($billingParty->country ?? '') == 'TT' ? 'selected' : '' }}>
                                                                            Trinidad and Tobago</option>
                                                                        <option value="TN"
                                                                            {{ ($billingParty->country ?? '') == 'TN' ? 'selected' : '' }}>
                                                                            Tunisia</option>
                                                                        <option value="TR"
                                                                            {{ ($billingParty->country ?? '') == 'TR' ? 'selected' : '' }}>
                                                                            Turkey</option>
                                                                        <option value="TM"
                                                                            {{ ($billingParty->country ?? '') == 'TM' ? 'selected' : '' }}>
                                                                            Turkmenistan</option>
                                                                        <option value="TC"
                                                                            {{ ($billingParty->country ?? '') == 'TC' ? 'selected' : '' }}>
                                                                            Turks and Caicos Islands
                                                                        </option>
                                                                        <option value="TV"
                                                                            {{ ($billingParty->country ?? '') == 'TV' ? 'selected' : '' }}>
                                                                            Tuvalu</option>
                                                                        <option value="UG"
                                                                            {{ ($billingParty->country ?? '') == 'UG' ? 'selected' : '' }}>
                                                                            Uganda</option>
                                                                        <option value="UA"
                                                                            {{ ($billingParty->country ?? '') == 'UA' ? 'selected' : '' }}>
                                                                            Ukraine</option>
                                                                        <option value="AE"
                                                                            {{ ($billingParty->country ?? '') == 'AE' ? 'selected' : '' }}>
                                                                            United Arab Emirates
                                                                        </option>
                                                                        <option value="GB"
                                                                            {{ ($billingParty->country ?? '') == 'GB' ? 'selected' : '' }}>
                                                                            United Kingdom</option>
                                                                        <option value="US"
                                                                            {{ ($billingParty->country ?? '') == 'US' ? 'selected' : '' }}>
                                                                            United States</option>
                                                                        <option value="UM"
                                                                            {{ ($billingParty->country ?? '') == 'UM' ? 'selected' : '' }}>
                                                                            United States Minor Outlying
                                                                            Islands</option>
                                                                        <option value="UY"
                                                                            {{ ($billingParty->country ?? '') == 'UY' ? 'selected' : '' }}>
                                                                            Uruguay</option>
                                                                        <option value="UZ"
                                                                            {{ ($billingParty->country ?? '') == 'UZ' ? 'selected' : '' }}>
                                                                            Uzbekistan</option>
                                                                        <option value="VU"
                                                                            {{ ($billingParty->country ?? '') == 'VU' ? 'selected' : '' }}>
                                                                            Vanuatu</option>
                                                                        <option value="VE"
                                                                            {{ ($billingParty->country ?? '') == 'VE' ? 'selected' : '' }}>
                                                                            Venezuela</option>
                                                                        <option value="VN"
                                                                            {{ ($billingParty->country ?? '') == 'VN' ? 'selected' : '' }}>
                                                                            Viet Nam</option>
                                                                        <option value="VG"
                                                                            {{ ($billingParty->country ?? '') == 'VG' ? 'selected' : '' }}>
                                                                            Virgin Islands, British
                                                                        </option>
                                                                        <option value="VI"
                                                                            {{ ($billingParty->country ?? '') == 'VI' ? 'selected' : '' }}>
                                                                            Virgin Islands, U.S.
                                                                        </option>
                                                                        <option value="WF"
                                                                            {{ ($billingParty->country ?? '') == 'WF' ? 'selected' : '' }}>
                                                                            Wallis and Futuna</option>
                                                                        <option value="EH"
                                                                            {{ ($billingParty->country ?? '') == 'EH' ? 'selected' : '' }}>
                                                                            Western Sahara</option>
                                                                        <option value="YE"
                                                                            {{ ($billingParty->country ?? '') == 'YE' ? 'selected' : '' }}>
                                                                            Yemen</option>
                                                                        <option value="ZM"
                                                                            {{ ($billingParty->country ?? '') == 'ZM' ? 'selected' : '' }}>
                                                                            Zambia</option>
                                                                        <option value="ZW"
                                                                            {{ ($billingParty->country ?? '') == 'ZW' ? 'selected' : '' }}>
                                                                            Zimbabwe</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Phone<span class="text-danger">*</span></label>
                                                                    <input type="text" class="form-control"
                                                                        id="phone1" name="phone1"
                                                                        value="{{ $billingParty->phone1 ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Mobile</label>
                                                                    <input type="text" class="form-control"
                                                                        id="mobile" name="mobile"
                                                                        value="{{ $billingParty->mobile ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Fax 1</label>
                                                                    <input type="text" class="form-control"
                                                                        id="fax1" name="fax1"
                                                                        value="{{ $billingParty->fax1 ?? '' }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Fax 2</label>
                                                                    <input type="text" class="form-control"
                                                                        id="fax2" name="fax2"
                                                                        value="{{ $billingParty->fax2 ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-6">
                                                                <div class="form-group">
                                                                    <label>Email</label>
                                                                    <input type="email" class="form-control"
                                                                        id="email" name="email"
                                                                        value="{{ $billingParty->email ?? '' }}">
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-12">
                                                                <button type="button" class="btn btn-{{ $billingParty->sent_to_sql == 1 ? 'danger' : 'primary' }}"
                                                                    onclick="updateBillToInfo()" {{ $billingParty->sent_to_sql == 1 ? 'disabled' : '' }} >
                                                                    {{ $billingParty->sent_to_sql == 1 ? 'Records alredy sent to SQL' : 'Save Changes' }}</button>
                                                                
                                                                <button type="button" class="btn btn-info ml-2" onclick="generateClientLink()">
                                                                    <i class="cil-link"></i> Get Client Link
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <!-- /.box-body -->
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
        function updateBillToInfo() {
            var formData = {
                customer_name: $('#customer_name').val(),
                customer_code: $('#customer_code').val(),
                customer_category: $('#customer_category').val(),
                tin: $('#tin').val(),
                brn: $('#brn').val(),
                brn2: $('#brn2').val(),
                sales_tax_no: $('#sales_tax_no').val(),
                service_tax_no: $('#service_tax_no').val(),
                id_type: $('#id_type').val(),
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
                fax2: $('#fax2').val(),
                email: $('#email').val()
            };

            $.ajax({
                url: '/einvoice-billto/update/{{ $billingParty->id }}',
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 1) {
                        toastController('Billing party information updated successfully');
                    } else {
                        toastController('Error updating billing party information');
                    }
                },
                error: function(xhr) {
                    toastController('Error updating billing party information');
                }
            });
        }

        function generateClientLink() {
            $.ajax({
                url: '/einvoice-billto/generate-client-link/{{ $billingParty->id }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status == 1) {
                        // Create a simple dialog instead of Bootstrap modal
                        var clientLink = response.client_link;
                        var dialogHtml = `
                            <div id="clientLinkDialog" style="
                                position: fixed;
                                top: 0;
                                left: 0;
                                width: 100%;
                                height: 100%;
                                background: rgba(0,0,0,0.5);
                                z-index: 9999;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                            ">
                                <div style="
                                    background: white;
                                    padding: 30px;
                                    border-radius: 10px;
                                    max-width: 500px;
                                    width: 90%;
                                    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
                                ">
                                    <div style="
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        margin-bottom: 20px;
                                        border-bottom: 1px solid #eee;
                                        padding-bottom: 10px;
                                    ">
                                        <h4 style="margin: 0; color: #333;">
                                            <i class="cil-link"></i> Client Link Generated
                                        </h4>
                                        <button onclick="closeClientLinkDialog()" style="
                                            background: none;
                                            border: none;
                                            font-size: 24px;
                                            cursor: pointer;
                                            color: #666;
                                        ">&times;</button>
                                    </div>
                                    
                                    <p style="margin-bottom: 15px; font-weight: 600; color: #333;">Client Link:</p>
                                    <div style="
                                        display: flex;
                                        border: 1px solid #ddd;
                                        border-radius: 5px;
                                        overflow: hidden;
                                    ">
                                        <input type="text" id="clientLinkInput" value="${clientLink}" readonly style="
                                            flex: 1;
                                            padding: 10px;
                                            border: none;
                                            outline: none;
                                            font-family: monospace;
                                            font-size: 12px;
                                        ">
                                        <button onclick="copyToClipboard()" style="
                                            background: #007bff;
                                            color: white;
                                            border: none;
                                            padding: 10px 15px;
                                            cursor: pointer;
                                            font-size: 12px;
                                        ">
                                            <i class="cil-copy"></i> Copy
                                        </button>
                                    </div>
                                    
                                    <p style="
                                        margin-top: 15px;
                                        font-size: 12px;
                                        color: #666;
                                        line-height: 1.4;
                                    ">
                                        <i class="cil-info"></i> Share this link with your client to allow them to access their e-invoice data.
                                    </p>
                                    
                                    <div style="
                                        text-align: right;
                                        margin-top: 20px;
                                        padding-top: 15px;
                                        border-top: 1px solid #eee;
                                    ">
                                        <button onclick="closeClientLinkDialog()" style="
                                            background: #6c757d;
                                            color: white;
                                            border: none;
                                            padding: 8px 16px;
                                            border-radius: 4px;
                                            cursor: pointer;
                                        ">Close</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        // Remove existing dialog if any
                        $('#clientLinkDialog').remove();
                        
                        // Append new dialog to body
                        $('body').append(dialogHtml);
                        
                        toastController('Client link generated successfully');
                    } else {
                        toastController('Error generating client link: ' + response.message);
                    }
                },
                error: function(xhr) {
                    toastController('Error generating client link');
                }
            });
        }

        function closeClientLinkDialog() {
            $('#clientLinkDialog').remove();
        }

        function copyToClipboard() {
            var copyText = document.getElementById("clientLinkInput");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            
            try {
                // Try modern clipboard API first
                navigator.clipboard.writeText(copyText.value).then(function() {
                    toastController('Link copied to clipboard!');
                }).catch(function() {
                    // Fallback for older browsers
                    document.execCommand("copy");
                    toastController('Link copied to clipboard!');
                });
            } catch (err) {
                // Final fallback
                document.execCommand("copy");
                toastController('Link copied to clipboard!');
            }
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
            $('#id_type').on('change', function() {
                var idType = $(this).val();
                var defaultIdNo = getDefaultIdNoByType(idType);
                $('#tin').val(defaultIdNo);
            });
        });
    </script>
@endsection
