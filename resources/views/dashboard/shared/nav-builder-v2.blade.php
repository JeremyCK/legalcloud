@php
    // use App\Http\Controllers;
    namespace App\Http\Controllers;
@endphp
<div class="c-sidebar-brand">
    <img class="c-sidebar-brand-full" src="{{ url('/assets/brand/logo-lhyeo.jpeg') }}" width="auto" height="60"
        alt="CoreUI Logo">
    <img class="c-sidebar-brand-minimized" src="{{ url('/assets/brand/logo-lhyeo.jpeg') }}" width="118" height="46"
        alt="CoreUI Logo">
</div>
<ul class="c-sidebar-nav ps ps--active-y">
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ route('dashboard.index') }}">
            <i class="cil-speedometer c-sidebar-nav-icon"></i>
            Dashboard
        </a>
    </li>
    @if (AccessController::UserAccessPermissionController(PermissionController::ManageUserPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::ClientManagementPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::ReferralManagementPermission()) == true)
        <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                    class="cil-user c-sidebar-nav-icon"></i>Users Management <div id="side_Users Management"></div></a>
            <ul class="c-sidebar-nav-dropdown-items">
                @if (AccessController::UserAccessPermissionController(PermissionController::ManageUserPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('users.index') }}"><span
                                class="c-sidebar-nav-icon"></span>Staffs<div id="side_3"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::ClientManagementPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ route('clients.index') }}"><span class="c-sidebar-nav-icon"></span>Clients<div
                                id="side_4"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::ReferralManagementPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ route('referral.index') }}"><span class="c-sidebar-nav-icon"></span>Referrals<div
                                id="side_35"></div></a></li>
                @endif

                
                 @if (AccessController::UserAccessPermissionController(PermissionController::EInvoicePermission()) == true)
                   <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ route('einvoice-client.index') }}"><span class="c-sidebar-nav-icon"></span>E-invoice Client<div
                                id="side_35"></div></a></li>
                @endif


                                  


            </ul>
        </li>
    @endif

    <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                class="cil-list c-sidebar-nav-icon"></i>Cases <div id="side_Cases"></div></a>
        <ul class="c-sidebar-nav-dropdown-items">
            @if (AccessController::UserAccessPermissionController(PermissionController::CreateCasePermission()) == true)
                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('case.create') }}"><span
                            class="c-sidebar-nav-icon"></span>Create Case<div id="side_91"></div></a></li>
            @endif

            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="{{ route('cases.list', 'active') }}"><span class="c-sidebar-nav-icon"></span>Active Case<div
                        id="side_62"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('cases.list', 'reviewing') }}">
                    <span class="c-sidebar-nav-icon"></span>Reviewing Case
                    <div id="side_90"></div>
                </a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="{{ route('cases.list', 'pendingclose') }}"><span class="c-sidebar-nav-icon"></span>Pending
                    Close<div id="side_71"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="{{ route('cases.list', 'closed') }}"><span class="c-sidebar-nav-icon"></span>Closed Case<div
                        id="side_63"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="{{ route('cases.list', 'aborted') }}"><span class="c-sidebar-nav-icon"></span>Aborted Case
                    <div id="side_64"></div>
                </a></li>
        </ul>
    </li>
    @if (AccessController::UserAccessPermissionController(PermissionController::BankReconPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::BankLedgerPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::JournalEntryPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::ClientAccountBalancePermission()) ==
                true ||
            AccessController::UserAccessPermissionController(PermissionController::OfficeAccountBalancePermission()) ==
                true)

        <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                    class="cil-money c-sidebar-nav-icon"></i>Account <div id="side_Account"></div></a>
            <ul class="c-sidebar-nav-dropdown-items">
                @if (AccessController::UserAccessPermissionController(PermissionController::BankReconPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('bank.recon') }}">
                            <span class="c-sidebar-nav-icon"></span>Bank Recon<div id="side_72"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::BankLedgerPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ route('bank.ledger') }}">
                            <span class="c-sidebar-nav-icon"></span>Bank Ledger<div id="side_77"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::JournalEntryPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('journal-entry-list') }}">
                            <span class="c-sidebar-nav-icon"></span>Journal Entry<div id="side_83"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::TransferFeePermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('transferfee') }}">
                            <span class="c-sidebar-nav-icon"></span>Transfer Fee<div id="side_75"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::SSTPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('sst-list') }}">
                            <span class="c-sidebar-nav-icon"></span>SST<div id="side_78"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::ClientAccountBalancePermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('client-ledger') }}">
                            <span class="c-sidebar-nav-icon"></span>Client Account Balance<div id="side_86"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::OfficeAccountBalancePermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('office-account-ledger') }}">
                            <span class="c-sidebar-nav-icon"></span>Office Account Balance<div id="side_87"></div>
                        </a></li>
                @endif
                 @if (AccessController::UserAccessPermissionController(PermissionController::EInvoicePermission()) == true)
                     <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('einvoice-list') }}"><span class="c-sidebar-nav-icon"></span> E-Invoice</a></li>
                @endif

               
            </ul>
        </li>
    @endif

    @if (AccessController::UserAccessPermissionController(PermissionController::DocumentTemplatePermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::QuotationTemplatePermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::LetterHeadTemplatePermission()) == true)
        <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                    class="cil-file c-sidebar-nav-icon"></i>Template <div id="side_Template"></div></a>
            <ul class="c-sidebar-nav-dropdown-items">


                @if (AccessController::UserAccessPermissionController(PermissionController::DocumentTemplatePermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('document-file') }}"><span class="c-sidebar-nav-icon"></span>Document files
                            <div id="side_12"></div>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::QuotationTemplatePermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('quotation') }}"><span
                                class="c-sidebar-nav-icon"></span>Quotation<div id="side_36"></div></a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::LetterHeadTemplatePermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('letter-head-lawyer') }}"><span
                                class="c-sidebar-nav-icon"></span>Lawyer Letter Head<div id="side_36"></div></a></li>
                @endif


                {{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="http://127.0.0.1:8000/checklist-template"><span class="c-sidebar-nav-icon"></span>Case<div
                            id="side_10"></div></a></li> --}}
                {{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="http://127.0.0.1:8000/email-template"><span class="c-sidebar-nav-icon"></span>Email<div
                            id="side_11"></div></a></li>
            
                    </a></li>
                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="http://127.0.0.1:8000/account-template"><span class="c-sidebar-nav-icon"></span>Account<div
                            id="side_13"></div></a></li> --}}

            </ul>
        </li>
    @endif
    {{-- <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="http://127.0.0.1:8000/bonus">
            <i class="cil-star c-sidebar-nav-icon"></i>

            Bonus
        </a>
    </li> --}}
    @if (AccessController::UserAccessPermissionController(PermissionController::BonusRequestListPermission()) == true)
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('bonus-request-list') }}">
                <i class="cil-star c-sidebar-nav-icon"></i>

                Bonus Request
            </a>
        </li>
    @endif

    @if (AccessController::UserAccessPermissionController(PermissionController::ClaimsPermission()) == true)
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('claims-request-list') }}">
                <i class="cil-star c-sidebar-nav-icon"></i>

                Claims
            </a>
        </li>
    @endif

    @if (AccessController::UserAccessPermissionController(PermissionController::AccountCodeSettingPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::AccountItemSettingPermission()) ==
                true ||
            AccessController::UserAccessPermissionController(PermissionController::BankSettingPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::CourierSettingPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::CaseTypeSettingPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::OfficeAccountSettingPermission()) ==
                true)

        <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                    class="cil-cog c-sidebar-nav-icon"></i>Settings <div id="side_Settings"></div></a>
            <ul class="c-sidebar-nav-dropdown-items">
                @if (AccessController::UserAccessPermissionController(PermissionController::AccountCodeSettingPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('account-code') }}"><span class="c-sidebar-nav-icon"></span>Account Code<div
                                id="side_20"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::AccountItemSettingPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('account-item') }}"><span class="c-sidebar-nav-icon"></span>Account Item<div
                                id="side_37"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::BankSettingPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('banks') }}s"><span
                                class="c-sidebar-nav-icon"></span>Bank<div id="side_16"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::CaseTypeSettingPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('portfolio') }}"><span
                                class="c-sidebar-nav-icon"></span>Case categories<div id="side_17"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::CourierSettingPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('couriers') }}"><span
                                class="c-sidebar-nav-icon"></span>Courier<div id="side_18"></div>
                        </a></li>
                @endif


                @if (AccessController::UserAccessPermissionController(PermissionController::OfficeAccountSettingPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('office-bank-account') }}"><span class="c-sidebar-nav-icon"></span>Office
                            Account<div id="side_38"></div></a></li>
                @endif

                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="{{ route('data-repair.index') }}"><span class="c-sidebar-nav-icon"></span>Data Repair<div id="side_39"></div></a></li>







                {{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="http://127.0.0.1:8000/teams"><span
                        class="c-sidebar-nav-icon"></span>Teams<div id="side_15"></div></a></li> --}}
                {{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="http://127.0.0.1:8000/account-cat"><span class="c-sidebar-nav-icon"></span>Account Category
                    <div id="side_19"></div>
                </a></li> --}}
                {{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="http://127.0.0.1:8000/referral-comm"><span class="c-sidebar-nav-icon"></span>Referral
                    Commission<div id="side_67"></div></a></li> --}}

                {{-- <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="http://127.0.0.1:8000/checklist-item"><span class="c-sidebar-nav-icon"></span>Checklist<div
                        id="side_21"></div></a></li> --}}
            </ul>
        </li>
    @endif
    @if (AccessController::UserAccessPermissionController(PermissionController::AttachmentPermission()) == true)
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('files') }}">
                <i class="cil-folder c-sidebar-nav-icon"></i>
                Attachments
            </a>
        </li>
    @endif

    <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                class="cil-featured-playlist c-sidebar-nav-icon"></i>Voucher <div id="side_Voucher"></div></a>
        <ul class="c-sidebar-nav-dropdown-items">
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('voucher') }}"><span
                        class="c-sidebar-nav-icon"></span>Pending<div id="side_53"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="{{ url('voucher-inprogress') }}"><span class="c-sidebar-nav-icon"></span>In
                    Progress<div id="side_54"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('voucher-archive') }}"><span
                        class="c-sidebar-nav-icon"></span>Archived<div id="side_55"></div></a></li>
        </ul>
    </li>
    <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                class="cil-bike c-sidebar-nav-icon"></i>Dispatch <div id="side_Dispatch"></div></a>
        <ul class="c-sidebar-nav-dropdown-items">
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('dispatch') }}"><span
                        class="c-sidebar-nav-icon"></span>Outgoing<div id="side_49"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('dispatch-incoming') }}"><span
                        class="c-sidebar-nav-icon"></span>Incoming
                    <div id="side_50"></div>
                </a></li>
        </ul>
    </li>
    <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                class="cil-vector c-sidebar-nav-icon"></i>Operation <div id="side_Operation"></div></a>
        <ul class="c-sidebar-nav-dropdown-items">
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('adjudication') }}"><span
                        class="c-sidebar-nav-icon"></span>Adjudication<div id="side_42"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('safe-keeping') }}"><span
                        class="c-sidebar-nav-icon"></span>Safe Keeping<div id="side_57"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('repare-docs') }}"><span
                        class="c-sidebar-nav-icon"></span>Prepare Docs<div id="side_58"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('return-call') }}"><span
                        class="c-sidebar-nav-icon"></span>Return Call<div id="side_59"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('land-office') }}"><span
                        class="c-sidebar-nav-icon"></span>Land Office<div id="side_60"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="{{ url('chkt') }}"><span
                        class="c-sidebar-nav-icon"></span>CHKT<div id="side_66"></div></a></li>
        </ul>
    </li>
    {{-- <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                class="cil-bank c-sidebar-nav-icon"></i>Logs <div id="side_Logs"></div></a>
        <ul class="c-sidebar-nav-dropdown-items">
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link" href="http://127.0.0.1:8000/audit-log"><span
                        class="c-sidebar-nav-icon"></span>Audit Log<div id="side_24"></div></a></li>
            <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                    href="http://127.0.0.1:8000/activity-log"><span class="c-sidebar-nav-icon"></span>Activities Log
                    <div id="side_25"></div>
                </a></li>
        </ul>
    </li> --}}
    @if (AccessController::UserAccessPermissionController(PermissionController::QuotationGeneratorPermission()) == true)
        <li class="c-sidebar-nav-item">
            <a class="c-sidebar-nav-link" href="{{ url('quotation-generator') }}">
                <i class="cil-description c-sidebar-nav-icon"></i>
                Quotation Generator
            </a>
        </li>
    @endif

    @if (AccessController::UserAccessPermissionController(PermissionController::FileBefore2022Permission()) == true)
        <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                    class="cil-list c-sidebar-nav-icon"></i>Files before 2022 <div id="side_Files before 2022"></div>
                </a>
            <ul class="c-sidebar-nav-dropdown-items">
                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="{{ url('cases-archieve') }}"><span class="c-sidebar-nav-icon"></span>Open case
                        <div id="side_46"></div></a></li>
                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="{{ url('closed-2022-list') }}"><span class="c-sidebar-nav-icon"></span>Closed
                        case
                        <div id="side_47"></div>
                    </a></li>
                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="{{ url('pending-close-2022-list') }}"><span
                            class="c-sidebar-nav-icon"></span>Pending Close Case<div id="side_68"></div></a></li>
                <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="{{ url('perfection-case') }}"><span
                            class="c-sidebar-nav-icon"></span>Perfection
                        Case<div id="side_84"></div></a></li>
            </ul>
        </li>
    @endif



    @if (AccessController::UserAccessPermissionController(PermissionController::AdvanceReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::MonthlyReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::InvoiceReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::ReferralReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::ReconReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::BonusReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::BonusEstimateReportPermission()) ==
                true ||
            AccessController::UserAccessPermissionController(PermissionController::QuotationReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::CaseReportPermission()) == true ||
            AccessController::UserAccessPermissionController(PermissionController::StaffCaseReportPermission()) == true)


        <li class="c-sidebar-nav-dropdown"><a class="c-sidebar-nav-dropdown-toggle" href="#"><i
                    class="cil-bar-chart c-sidebar-nav-icon"></i>Reporting <div id="side_Reporting"></div></a>
            <ul class="c-sidebar-nav-dropdown-items">
                @if (AccessController::UserAccessPermissionController(PermissionController::AdvanceReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('advance-report') }}"><span class="c-sidebar-nav-icon"></span>Advance Report
                            <div id="side_81"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::MonthlyReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('summary-report') }}"><span class="c-sidebar-nav-icon"></span>Monthly Report
                            <div id="side_39"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::InvoiceReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ route('report.invoice') }}"><span class="c-sidebar-nav-icon"></span>Invoice
                            Report
                            <div id="side_43"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::ReferralReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ route('report.referral') }}"><span class="c-sidebar-nav-icon"></span>Referral
                            Report
                            <div id="side_65"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::ReconReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('report-bank-recon') }}"><span class="c-sidebar-nav-icon"></span>Bank Recon
                            Report
                            <div id="side_73"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::BonusReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('report-bonus') }}"><span class="c-sidebar-nav-icon"></span>Bonus Report
                            <div id="side_70"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::BonusEstimateReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('report-bonus-estimate') }}"><span class="c-sidebar-nav-icon"></span>Bonus
                            Estimation Report<div id="side_82"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::QuotationReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('quotation-report') }}"><span class="c-sidebar-nav-icon"></span>Quotation
                            Report
                            <div id="side_87"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::CaseReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('case-report') }}"><span class="c-sidebar-nav-icon"></span>Cases Report<div id="side_85"></div>
                        </a></li>
                @endif

                @if (AccessController::UserAccessPermissionController(PermissionController::StaffCaseReportPermission()) == true)
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                            href="{{ url('staff-report') }}"><span class="c-sidebar-nav-icon"></span>Staff Case's
                            Report<div id="side_85"></div>
                        </a></li>
                        
                    <li class="c-sidebar-nav-item"><a class="c-sidebar-nav-link"
                        href="{{ url('staff-detail-report') }}"><span class="c-sidebar-nav-icon"></span>Staff Performance Report<div id="side_86"></div>
                    </a></li>
                @endif

            </ul>
    @endif
    </li>
    <div class="ps__rail-x" style="left: 0px; bottom: 0px;">
        <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
    </div>
    <div class="ps__rail-y" style="top: 0px; height: 620px; right: 0px;">
        <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 471px;"></div>
    </div>
</ul>
<button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent"
    data-class="c-sidebar-unfoldable"></button>
</div>
