<div class="row" style="border-bottom: 1px solid black">
    <input type="hidden" id="receipt_name" value="{{ $file_name }}" />
    <div class="col-6">
        <address class="print-formal">
            <h2 style="font-weight:600;margin-bottom:0px">{{ $Branch->office_name }}
            </h2>
            Advocates & Solicitors<br>
        </address>
    </div>

    <div class="col-4">
        <address class="print-formal" style="margin-bottom:0.5rem">
            {!! $Branch->address !!}<br>
            <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
            <b>Email</b>: {{ $Branch->email }}
        </address>
    </div>

</div>

<div class="row" style="margin-top: 10px">
    <div class="col-6">
        <table class="table print-table-receipt print-receipt mb-0">
            <tbody>
                <tr>
                    <td style="border: 1px solid black; "><b>Ref No</b></td>
                    <td>{{ $case->case_ref_no }}</td>
                </tr>
                <tr>
                    <td class="print-table-receipt"><b>Receipt No</b></td>
                    <td>{{ $receipt_no }}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>Acc No</b></td>
                    <td>{{ $account_no }}</td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="col-6">
        <address class="print-formal float-right">
            <b style="font-size: 1.714rem">Official Receipt</b> <br>
                <strong>Date: {{ $obj->payment_date }}</strong>
        </address>
    </div>
</div>

<div class="row" style="margin-top: 10px">
    <div class="col-12">
        <table class="table print-receipt mb-0">
            <tbody>
                <tr>
                    <td style="width: 15%"><b>Received from</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $obj->payee }}</td>
                </tr>
                <tr>
                    <td style="width: 15%"><b>The sum of RM</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $amount_in_en }}</td>
                </tr>
                <tr>
                    <td style="width: 15%"><b>Being payment of</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $obj->remark }}</td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

{{-- <div class="row" style="border-bottom: 2px dashed black; padding-bottom:10px;padding-top:10px"> --}}
<div class="row" style="padding-bottom:10px;padding-top:10px">
    <div class="col-6">
        <table class="table print-table-receipt print-receipt mb-0">
            <tbody>
                <tr>
                    <td><b style="font-size: 1.5rem">RM</b></td>
                    <td style="font-size: 1.5rem">{{ number_format($amount, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><b>Cash/Cheque No</b></td>
                    <td>{{ $transaction_id }}</td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="col-6" style="margin-top: 30px">
        <b >Note: This Official Receipt is only valid upon clearance of the cheque</b><br/>
        <b >Note: This is a computer-generated document. No signature is required.</b>
    </div>

</div>


{{--======================================== File Copy==================================== --}}

{{-- <div class="row"  style="margin-top:20px;border-bottom: 1px solid black">

    <div class="col-6">
        <address class="print-formal">
            <h2 style="font-weight:600;margin-bottom:0px">{{ $Branch->office_name }}
            </h2>
            Advocates & Solicitors<br>
        </address>
    </div>

    <div class="col-4">
        <address class="print-formal" style="margin-bottom:0.5rem">
            {!! $Branch->address !!}<br>
            <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
            <b>Email</b>: {{ $Branch->email }}
        </address>
    </div>

</div>

<div class="row" style="margin-top: 10px">
    <div class="col-6">
        <table class="table print-table-receipt print-receipt mb-0">
            <tbody>
                <tr>
                    <td style="border: 1px solid black; "><b>Ref No</b></td>
                    <td>{{ $case->case_ref_no }}</td>
                </tr>
                <tr>
                    <td class="print-table-receipt"><b>Receipt No</b></td>
                    <td>{{ $receipt_no }}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>Acc No</b></td>
                    <td>{{ $account_no }}</td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="col-6">
        <address class="print-formal float-right">
            <b style="font-size: 1.714rem">Official Receipt - </b> <b
                style="font-size: 1.2rem">File Copy</b> <br>
                <strong>Date: {{ $obj->payment_date }}</strong>
        </address>
    </div>
</div>

<div class="row" style="margin-top: 10px">
    <div class="col-12">
        <table class="table print-receipt mb-0">
            <tbody>
                <tr>
                    <td style="width: 15%"><b>Received from</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $obj->payee }}</td>
                </tr>
                <tr>
                    <td style="width: 15%"><b>The sum of RM</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $amount_in_en }}</td>
                </tr>
                <tr>
                    <td style="width: 15%"><b>Being payment of</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $obj->remark }}</td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

<div class="row" style="border-bottom: 2px dashed black; padding-bottom:10px;padding-top:10px">
    <div class="col-6">
        <table class="table print-table-receipt print-receipt mb-0">
            <tbody>
                <tr>
                    <td><b style="font-size: 1.5rem">RM</b></td>
                    <td style="font-size: 1.5rem">{{ number_format($amount, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><b>Cash/Cheque No</b></td>
                    <td>{{ $transaction_id }}</td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="col-6" style="margin-top: 30px">
        <b >Note: This Official Receipt is only valid upon clearance of the cheque</b><br/>
        <b >Note: This is a computer-generated document. No signature is required.</b>
    </div>

</div> --}}


{{--======================================== Account Copy==================================== --}}

{{-- <div class="row" style="margin-top:20px;border-bottom: 1px solid black">

    <div class="col-6">
        <address class="print-formal">
            <h2 style="font-weight:600;margin-bottom:0px">{{ $Branch->office_name }}
            </h2>
            Advocates & Solicitors<br>
        </address>
    </div>

    <div class="col-4">
        <address class="print-formal" style="margin-bottom:0.5rem">
            {!! $Branch->address !!}<br>
            <b>Phone</b>: {{ $Branch->tel_no }} <b>Fax</b>: {{ $Branch->fax }}<br>
            <b>Email</b>: {{ $Branch->email }}
        </address>
    </div>

</div>

<div class="row" style="margin-top: 10px">
    <div class="col-6">
        <table class="table print-table-receipt print-receipt mb-0">
            <tbody>
                <tr>
                    <td style="border: 1px solid black; "><b>Ref No</b></td>
                    <td>{{ $case->case_ref_no }}</td>
                </tr>
                <tr>
                    <td class="print-table-receipt"><b>Receipt No</b></td>
                    <td>{{ $receipt_no }}</td>
                </tr>
                <tr>
                    <td class="fw-medium"><b>Acc No</b></td>
                    <td>{{ $account_no }}</td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="col-6">
        <address class="print-formal float-right">
            <b style="font-size: 1.714rem">Official Receipt - </b> <b
                style="font-size: 1.2rem">Account Copy</b> <br>
            <strong>Date: {{ $obj->payment_date }}</strong>
        </address>
    </div>
</div>

<div class="row" style="margin-top: 10px">
    <div class="col-12">
        <table class="table print-receipt mb-0">
            <tbody>
                <tr>
                    <td style="width: 15%"><b>Received from</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $obj->payee }}</td>
                </tr>
                <tr>
                    <td style="width: 15%"><b>The sum of RM</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $amount_in_en }}</td>
                </tr>
                <tr>
                    <td style="width: 15%"><b>Being payment of</b></td>
                    <td style="border-bottom: 1px solid black; ">{{ $obj->remark }}</td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

<div class="row" style="border-bottom: 2px dashed black; padding-bottom:10px;padding-top:10px">
    <div class="col-6">
        <table class="table print-table-receipt print-receipt mb-0">
            <tbody>
                <tr>
                    <td><b style="font-size: 1.5rem">RM</b></td>
                    <td style="font-size: 1.5rem">{{ number_format($amount, 2, '.', ',') }}</td>
                </tr>
                <tr>
                    <td><b>Cash/Cheque No</b></td>
                    <td>{{ $transaction_id }}</td>
                </tr>

            </tbody>
        </table>
    </div>

    <div class="col-6" style="margin-top: 30px">
        <b >Note: This Official Receipt is only valid upon clearance of the cheque</b><br/>
        <b >Note: This is a computer-generated document. No signature is required.</b>
    </div>

</div> --}}