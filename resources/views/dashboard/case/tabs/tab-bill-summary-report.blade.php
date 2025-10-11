<div id="div_case_referral">
    <div class="row">
        <div class="col-12">
            <div class="box-tools">

                @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillSummaryReportPermission()) == true)
                    <h4>Summary report </h4>
                    <hr/>

                    @if(isset($loanCaseBillMain))
                        <div class="row" style="">
                            
                            <div class="col-xl-6 col-lg-6 col-xs-12">
                                <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                    <tbody>
                                        <tr>
                                            <td class="text-left" style="background-color: #d8dbe0;border-top: 1px solid black !important;">Pfee 1</td>
                                            <td class="text-right" style="background-color: white;border-top: 1px solid black !important;">RM {{  number_format($loanCaseBillMain->pfee1, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="background-color: #d8dbe0;">Pfee 2</td>
                                            <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->pfee2, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="background-color: #d8dbe0">SST</td>
                                            <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->sst, 2, '.', ',') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="col-xl-6 col-lg-6 col-xs-12">
                                <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                    <tbody>
                                        <tr>
                                            <td class="text-left" style="background-color: #d8dbe0;border-top: 1px solid black !important;">
                                                {{-- <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" data-toggle="modal" onclick="accountSummaryInputController('disb')"
                                                data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                    <i class="cil-pencil"></i> --}}
                                                </a> Disb </td>
                                            <td class="text-right" style="background-color: white;border-top: 1px solid black !important;">RM {{  number_format($loanCaseBillMain->disb, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="background-color: #d8dbe0">
                                                {{-- <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" 
                                                onclick="accountSummaryInputController('Collected', '', '{{  $loanCaseBillMain->collected_amt }}', '', '','')"
                                                data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                    <i class="cil-pencil"></i> --}}
                                                </a> Collected</td>
                                            <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->collected_amt, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-left" style="background-color: #d8dbe0">
                                                <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" 
                                                onclick="accountSummaryInputController('Uncollected', '', '{{  $loanCaseBillMain->uncollected }}', '', '','')"
                                                data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                    <i class="cil-pencil"></i>
                                                </a> Uncollected</td>
                                            <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->uncollected, 2, '.', ',') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> 
 
                            @if(App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::BillSummaryReportPermission()) == true)
                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;">
                                                    @if(in_array($loanCaseBillMain->referral_a1_trx_id, ['', null]) || 
                                                    App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccountOverwritePermission()) == true)
                                                        @php
                                                            $name = str_replace('\'', "\'", $loanCaseBillMain->referral_a1_id);
                                                        @endphp
                                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" 
                                                        onclick="accountSummaryInputController('Referral A1', '{{ $loanCaseBillMain->referral_a1_ref_id }}', 
                                                        '{{  $loanCaseBillMain->referral_a1 }}', '{{  $name }}',
                                                        '{{  $loanCaseBillMain->referral_a1_trx_id }}','{{ $loanCaseBillMain->referral_a1_payment_date }}')"
                                                        data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                            <i class="cil-pencil"></i>
                                                        </a>
                                                        
                                                        @if(!in_array($loanCaseBillMain->referral_a1_ref_id, ['', null]))
                                                            <a href="javascript:void(0)" 
                                                            onclick="clearReferral('Referral A1')" class="btn btn-xs btn-warning float-right">
                                                                Clear Referral
                                                            </a>
                                                        @endif
                                                    @endif
                                                    
                                                    Referral (A1) 

                                                   
                                                     
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Referral 
                                                </td>
                                                <td class="text-right" style="background-color: white">
                                                    @if($loanCaseBillMain->referral_a1_id != 0  && $loanCaseBillMain->referral_a1_id != '')
                                                    <b>[{{  $loanCaseBillMain->referral_a1_id }}]</b>
                                                    @endif 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Amount
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->referral_a1, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Transaction ID
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->referral_a1_trx_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->referral_a1_payment_date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Account No
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->r1_bank_account }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;"> 
                                                    @if(in_array($loanCaseBillMain->referral_a2_trx_id, ['', null])  || 
                                                    App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccountOverwritePermission()) == true)
                                                       @php
                                                        $name = str_replace('\'', "\'", $loanCaseBillMain->referral_a2_id);
                                                    @endphp
                                                       <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false"
                                                        onclick="accountSummaryInputController('Referral A2', '{{ $loanCaseBillMain->referral_a2_ref_id }}', 
                                                        '{{  $loanCaseBillMain->referral_a2 }}', '{{  $name }}',
                                                        '{{  $loanCaseBillMain->referral_a2_trx_id }}','{{ $loanCaseBillMain->referral_a2_payment_date }}')"
                                                        data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                            <i class="cil-pencil"></i>
                                                        </a>

                                                        @if(!in_array($loanCaseBillMain->referral_a2_ref_id, ['', null]))
                                                            <a href="javascript:void(0)" 
                                                            onclick="clearReferral('Referral A2')" class="btn btn-xs btn-warning float-right">
                                                                Clear Referral
                                                            </a>
                                                        @endif
                                                    @endif
                                                   
                                                    Referral (A2) 

                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Referral 
                                                </td>
                                                <td class="text-right" style="background-color: white">
                                                    @if($loanCaseBillMain->referral_a2_id != 0 && $loanCaseBillMain->referral_a2_id != '')
                                                    <b>[{{  $loanCaseBillMain->referral_a2_id }}]</b>
                                                    @endif 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Amount
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->referral_a2, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Transaction ID
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->referral_a2_trx_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->referral_a2_payment_date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Account No
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->r2_bank_account }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;">
                                                    @if(in_array($loanCaseBillMain->referral_a3_trx_id, ['', null]) || 
                                                    App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccountOverwritePermission()) == true)
                                                        @php
                                                            $name = str_replace('\'', "\'", $loanCaseBillMain->referral_a3_id);
                                                        @endphp
                                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" 
                                                        onclick="accountSummaryInputController('Referral A3', '{{ $loanCaseBillMain->referral_a3_ref_id }}', 
                                                        '{{  $loanCaseBillMain->referral_a3 }}', '{{  $name }}',
                                                        '{{  $loanCaseBillMain->referral_a3_trx_id }}','{{ $loanCaseBillMain->referral_a3_payment_date }}')"
                                                        data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                            <i class="cil-pencil"></i>
                                                        </a>

                                                        @if(!in_array($loanCaseBillMain->referral_a3_ref_id, ['', null]))
                                                            <a href="javascript:void(0)" 
                                                            onclick="clearReferral('Referral A3')" class="btn btn-xs btn-warning float-right">
                                                                Clear Referral
                                                            </a>
                                                        @endif
                                                    @endif
                                                    
                                                    Referral (A3)
                                                    
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Referral 
                                                </td>
                                                <td class="text-right" style="background-color: white">
                                                    @if($loanCaseBillMain->referral_a3_id != 0  && $loanCaseBillMain->referral_a3_id != '')
                                                    <b>[{{  $loanCaseBillMain->referral_a3_id }}]</b>
                                                    @endif     
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Amount
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->referral_a3, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Transaction ID
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->referral_a3_trx_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->referral_a3_payment_date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Account No
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->r3_bank_account }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                @if( App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccountOverwritePermission()) == true)
                               
                               
                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;">
                                                    
                                                    @if(in_array($loanCaseBillMain->referral_a4_trx_id, ['', null]) || 
                                                    App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccountOverwritePermission()) == true)
                                                        @php
                                                            $name = str_replace('\'', "\'", $loanCaseBillMain->referral_a4_id);
                                                        @endphp
                                                       <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" 
                                                        onclick="accountSummaryInputController('Referral A4', '{{ $loanCaseBillMain->referral_a4_ref_id }}', 
                                                        '{{  $loanCaseBillMain->referral_a4 }}', '{{  $name }}',
                                                        '{{  $loanCaseBillMain->referral_a4_trx_id }}','{{ $loanCaseBillMain->referral_a4_payment_date }}')"
                                                        data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                            <i class="cil-pencil"></i>
                                                        </a>

                                                        @if(!in_array($loanCaseBillMain->referral_a4_ref_id, ['', null]))
                                                            <a href="javascript:void(0)" 
                                                            onclick="clearReferral('Referral A4')" class="btn btn-xs btn-warning float-right">
                                                                Clear Referral
                                                            </a>
                                                        @endif
                                                    @endif
                                                  
                                                    {{-- Referral (A4) --}}
                                                    Misc
                                                    
                                                </td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Referral 
                                                </td>
                                                <td class="text-right" style="background-color: white">
                                                
                                                    @if($loanCaseBillMain->referral_a4_id != 0  && $loanCaseBillMain->referral_a4_id != '')
                                                    <b>[{{  $loanCaseBillMain->referral_a4_id }}]</b>
                                                    @endif     
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Amount
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->referral_a4, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Transaction ID
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->referral_a4_trx_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->referral_a4_payment_date }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Account No
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->r4_bank_account }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                
                                @endif

                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;">
                                                    @if(in_array($loanCaseBillMain->marketing_trx_id, ['', null]) || 
                                                    App\Http\Controllers\AccessController::UserAccessPermissionController(App\Http\Controllers\PermissionController::AccountOverwritePermission()) == true)
                                                        <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" 
                                                        onclick="accountSummaryInputController('Marketing', '{{ $loanCaseBillMain->marketing_id }}', 
                                                        '{{  $loanCaseBillMain->marketing }}', '', '{{  $loanCaseBillMain->marketing_trx_id }}','{{ $loanCaseBillMain->marketing_payment_date }}')"
                                                        data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                            <i class="cil-pencil"></i>
                                                        </a>
                                                    @endif
                                                    
    
                                                    Marketing
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                     Amount
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->marketing, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                     Transaction ID
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->marketing_trx_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->marketing_payment_date }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;">
                                                    <a href="javascript:void(0)" data-backd rop="static" data-keyboard="false" 
                                                    onclick="accountSummaryInputController('Financed', '', '', '', '','{{  $loanCaseBillMain->payment_date }}', 
                                                    '{{  $loanCaseBillMain->financed_fee }}','{{  $loanCaseBillMain->financed_sum }}')"
                                                    data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                        <i class="cil-pencil"></i>
                                                    </a>
                                                    Financed
                                                     
                                                   
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                     Financed Fee
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  number_format($loanCaseBillMain->financed_fee, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Financed Sum
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->financed_sum, 2, '.', ',') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->payment_date }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            @if (in_array($current_user->menuroles, ['account', 'admin', 'management', 'maker']))
                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;">
                                                    <a href="javascript:void(0)" data-backdrop="static" data-keyboard="false" 
                                                    onclick="accountSummaryInputController('Other', '', 
                                                    '{{  $loanCaseBillMain->other_amt }}', '{{ $loanCaseBillMain->other_name }}', '{{  $loanCaseBillMain->other_trx_id }}',
                                                    '{{ $loanCaseBillMain->other_payment_date }}')"
                                                    data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                        <i class="cil-pencil"></i>
                                                    </a>
                                                    Other
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Desc
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->other_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Amount
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->other_amt, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Transaction ID
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->other_trx_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->other_payment_date }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="col-xl-6 col-lg-6 col-xs-12">
                                    <table class="table table-striped col-12" style="border: 1px solid black !important;border-top: 1px solid black !important;">
                                        <tbody>
                                            <tr>
                                                <td colspan="2"  style="border-top: 1px solid black !important;">
                                                    <a href="javascript:void(0)" data-backd rop="static" data-keyboard="false" 
                                                    onclick="accountSummaryInputController('Disb Manual', '', 
                                                    '{{  $loanCaseBillMain->disb_amt_manual }}', '{{ $loanCaseBillMain->disb_name }}', '{{  $loanCaseBillMain->disb_trx_id }}',
                                                    '{{ $loanCaseBillMain->disb_payment_date }}')"
                                                    data-toggle="modal" data-target="#modalAccountSummaryInput" class="btn btn-xs btn-primary">
                                                        <i class="cil-pencil"></i>
                                                    </a>
                                                    Disb (Manual)
                                                
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Desc
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->disb_name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Amount
                                                </td>
                                                <td class="text-right" style="background-color: white">RM {{  number_format($loanCaseBillMain->disb_amt_manual, 2, '.', ',') }}</td>
                                            </tr>
                                            
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Transaction ID
                                                </td>
                                                <td class="text-right" style="background-color: white">{{  $loanCaseBillMain->disb_trx_id }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="background-color: #d8dbe0">
                                                    Payment Date
                                                </td>
                                                <td class="text-right" style="background-color: white">{{ $loanCaseBillMain->disb_payment_date }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif

                            
                        </div>
                    @endif

                @endif
            </div>
        </div>
    </div>

</div>