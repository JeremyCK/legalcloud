<div id="dQuotationInvoice-p" class="div2 invoice printableArea d_operation "
    style="display: none;padding:50px;background-color:white !important; border:1px solid black">

    <div class="row no-print" style="margin-bottom:30px;">

        <div class="col-12 ">
            <div class="row">
                <div class="col-6">
                    <div class="form-group row">
                        <div class="col">
                            <label>Bill To</label>
                            <select class="form-control ddl-party" id="ddl_party" name="ddl_party">
                                
                            {{-- @include('dashboard.case.section.d-billto-party-option') --}}
                            </select>
                        </div>
                    </div>

                </div>
                <div class="col-6">
                    <div class="form-group row">
                        <div class="col">
                            <label>Formal</label>
                            <select class="form-control" id="ddlPrintFormal" name="ddlPrintFormal">
                                <option value="1">Formal</option>
                                <option value="0">Non Formal</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        
        <div class="col-12">
            <a href="javascript:void(0);" onclick="updateQuotationBillTo()"
            class="btn btn-info float-right no-print">Update Bill To</a>

            <a class="btn btn-success float-left no-print" href="javascript:void(0)" 
            style="color:white;margin:0" data-backdrop="static" data-keyboard="false"  data-target="#modalCloseFileUpdate"  data-toggle="modal" >
            <i style="margin-right: 10px;" class="cil-list"></i>Quick Update Master List</a>
        </div>

        
        
        <div class="col-12">
            <hr/>
        </div>
        <div class="col-12">
            <button type="button" class="btn btn-warning pull-left " onclick="printlo()" style="margin-right: 5px;">
                <span><i class="fa fa-print"></i> Print</span>
            </button>
            <a href="javascript:void(0);" onclick="cancelQuotationPrintMode()"
                class="btn btn-danger float-right no-print">Cancel</a>

        </div>

    </div>


    <div class="row" style="border-bottom: 1px solid #0066CC ">

        <div class="col-4">
            <address class="print-formal">
                <strong style="color: #2d659d">{{ $Branch->office_name }}</strong><br>

                @if ($Branch->sst_no)
                    <b>SST No:</b> {{ $Branch->sst_no }}<br>
                @endif

                Advocates & Solicitors<br>
                {!! $Branch->address !!}<br>
                <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
                <b>Email</b>: {{ $Branch->email }}
            </address>
        </div>

        <div class="col-4">

            <h2 class="text-center" style="position:absolute;bottom:0;left:40%;font-weight:bold">
                Quotation
            </h2>

        </div>

        <div class="col-4">
            <h2 class="text-center" style="position:absolute;bottom:0;right:0">
                <small class="pull-right">Date: <span id="print_payment_date"></span> </small>
            </h2>
        </div>

    </div>

    <div class="row invoice-info" style="margin-top:20px;">
        <div class="col-sm-12 invoice-col">
            <address>
                <b>To</b> <strong class="text-blue"><span id="p-quo-client-name"></span></strong><br>
            </address>
        </div>

        <div class="col-sm-12 invoice-col div-info-print-section">
            @include('dashboard.case.section.d-print-info')
        </div>

        <!-- /.col -->
        <div class="col-sm-12 invoice-col">
            <div class="invoice-details row no-margin">
                <div class="col-6"><b>Case Ref No: </b>#{{ $case->case_ref_no }} </div>
                <div class="col-6 pull-right">
                    <span class="pull-right"><b>Quotation No: <span id="quotation_no"></span></b></span>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <?php $total_amt = 0; ?>
        <div class="col-12 table-responsive">
            <table class="table table-border" id="tbl-print-quotation">
            </table>
        </div>
    </div>

    <div class="row print-formal">
        <div class="col-12" style="border: 1px solid black;">
            @php
                $firm_name = 'L H YEO & CO';
                if ($case->branch_id == '4') {
                    $firm_name = 'Ramakrishnan & Co';
                } elseif ($case->branch_id == '6') {
                    $firm_name = 'ISMAIL & LIM';
                }
            @endphp

            @if ($case->branch_id == 1)
                Please note that payment can be made by way of cheque / bank draft / online transfer to our firm details as follow :<br/>
                Name : {{ $firm_name }}<br/>
                Bank : PUBLIC BANK BERHAD<br/>
                Account No. 3212995518 <br/><br/>
                <b>We DO NOT accept payment by cash</b> <br/><br/>
                <i>* Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration of one (1) month from the date of the bill until the date of the actual payment in accordance with clause 6 of the Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976. E & OE *</i>
            @else
                Please Note:
                1. Please issue cheque/bank draft in favour of "{{ $firm_name }}" or deposit to:
                <br />
                Interest at 8% per annum on the aforesaid amount shall be charged with effect from the expiration of one (1)
                month from the date of the bill until the date of the actual payment in accordance with clause 6 of the
                Solicitors’ Remuneration Order 1991 made to the Legal Profession Act 1976.
                E & OE<br />

                2. Payment by cheque should be crossed and made payable to "{{ $firm_name }}" or transfer to
                @if ($case->branch_id == '1')
                    Public Bank Berhad 3212995518.
                @elseif($case->branch_id == '2')
                    @if($case->sales_user_id == 13)
                    CIMB Bank Berhad 8605606123
                    @else
                    Standard Chartered Saadiq Berhad 620409971106.
                    @endif
                @elseif($case->branch_id == '3')
                    PBB CLIENT ACC 3230791418
                @elseif($case->branch_id == '4')
                    PBB CLIENT ACC 3231832819
                @elseif($case->branch_id == '5')
                    PBB CLIENT ACC 3232880915
                @elseif($case->branch_id == '6')
                    CIMB Bank Berhad 8605514414
                @endif
            @endif

           
        </div>
    </div>

</div>
