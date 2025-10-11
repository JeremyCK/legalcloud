<?php

namespace App\Http\Controllers;

use App\Models\MenuLangList;
use App\Models\TodoList;
use App\Models\Users;
use App\Models\Banks;
use App\Models\BanksUsersRel;
use App\Models\Customer;
use App\Models\Parameter;
use App\Models\caseTemplate;
use App\Models\LoanCase;
use App\Models\LoanCaseDetails;
use App\Models\CaseMasterListCategory;
use App\Models\CaseMasterListField;
use App\Models\perm;
use Illuminate\Http\Request;
use App\Models\MenusLang;
use App\Http\Helper\Helper;
use App\Models\BonusRequestHistory;
use App\Models\BonusRequestList;
use App\Models\BonusRequestRecords;
use App\Models\ClaimRequest;
use App\Models\LoanCaseBillMain;
use App\Models\OfficeBankAccount;
use App\Models\User;
use App\Models\UserAccessControl;
use App\Models\VoucherDetails;
use App\Models\VoucherMain;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public static function getAccessCode()
    {
        return 'ClaimsView';
    }

    public function getEditPermission()
    {
        return 'ClaimsApproval';
    }

    public static function getTransferCasePermission()
    {
        return 'TransferCase';
    }

    public static function getSetCasePendingClosePermission()
    {
        return 'SetCasePendingClose';
    }

    public static function getSetCaseReviewingPermission()
    {
        return 'SetCaseReviewing';
    }

    public static function SetCaseReopenPermission()
    {
        return 'SetCaseReopen';
    }

    public static function CreateCasePermission()
    {
        return 'CreateCasePermission';
    }

    public static function TransferCasePermission()
    {
        return 'TransferCasePermission';
    }

    public static function EditClientPermission()
    {
        return 'EditClientPermission';
    }

    public static function TransferSalesPermission()
    {
        return 'TransferSalesPermission';
    }

    public static function CaseReportPermission()
    {
        return 'CaseReportPermission';
    }

    public static function StaffReportPermission()
    {
        return 'StaffReportPermission';
    }

    public static function QuotationGeneratorPermission()
    {
        return 'QuotationGeneratorPermission';
    }

    public static function ConvertInvoicePermission()
    {
        return 'ConvertInvoicePermission';
    }

    public static function MarketingNotePermission()
    {
        return 'MarketingNotePermission';
    }

    public static function LedgerPermission()
    {
        return 'LedgerPermission';
    }

    public static function MarketingBillPermission()
    {
        return 'MarketingBillPermission';
    }

    public static function ClaimsPermission()
    {
        return 'ClaimsPermission';
    }

    public static function EditCloseCasePermission()
    {
        return 'EditCloseCasePermission';
    }

    public static function SubmitReviewPermission()
    {
        return 'SubmitReviewPermission';
    }

    public static function PendingClosePermission()
    {
        return 'PendingClosePermission';
    }

    public static function AbortCasePermission()
    {
        return 'AbortCasePermission';
    }

    public static function CloseCasePermission()
    {
        return 'CloseCasePermission';
    }

    public static function BillSummaryReportPermission()
    {
        return 'BillSummaryReportPermission';
    }

    public static function AccessInvoicePermission()
    {
        return 'AccessInvoicePermission';
    }

    public static function BillBalancePermission()
    {
        return 'BillBalancePermission';
    }

    public static function UpdateSSTRatePermission()
    {
        return 'UpdateSSTRatePermission';
    }

    public static function AccountOverwritePermission()
    {
        return 'AccountOverwritePermission';
    }

    public static function SelectSalesPermission()
    {
        return 'SelectSalesPermission';
    }

    public static function ManageUserPermission()
    {
        return 'ManageUserPermission';
    }

    public static function BonusRequestListPermission()
    {
        return 'BonusRequestListPermission';
    }

    public static function AdvanceReportPermission()
    {
        return 'AdvanceReportPermission';
    }

    public static function MonthlyReportPermission()
    {
        return 'MonthlyReportPermission';
    }

    public static function InvoiceReportPermission()
    {
        return 'InvoiceReportPermission';
    }

    public static function ReferralReportPermission()
    {
        return 'ReferralReportPermission';
    }

    public static function ReconReportPermission()
    {
        return 'ReconReportPermission';
    }

    public static function BonusReportPermission()
    {
        return 'BonusReportPermission';
    }

    public static function BonusEstimateReportPermission()
    {
        return 'BonusEstimateReportPermission';
    }

    public static function QuotationReportPermission()
    {
        return 'QuotationReportPermission';
    }

    public static function StaffCaseReportPermission()
    {
        return 'StaffCaseReportPermission';
    }

    public static function AccountCodeSettingPermission()
    {
        return 'AccountCodeSettingPermission';
    }
    
    public static function AccountItemSettingPermission()
    {
        return 'AccountItemSettingPermission';
    }

    public static function BankSettingPermission()
    {
        return 'BankSettingPermission';
    }

    public static function CaseTypeSettingPermission()
    {
        return 'CaseTypeSettingPermission';
    }

    public static function CourierSettingPermission()
    {
        return 'CourierSettingPermission';
    }

    public static function OfficeAccountSettingPermission()
    {
        return 'OfficeAccountSettingPermission';
    }

    public static function ClientManagementPermission()
    {
        return 'ClientManagementPermission';
    }

    public static function ReferralManagementPermission()
    {
        return 'ReferralManagementPermission';
    }
    
    public static function BankReconPermission()
    {
        return 'BankReconPermission';
    }

    public static function BankLedgerPermission()
    {
        return 'BankLedgerPermission';
    }

    public static function JournalEntryPermission()
    {
        return 'JournalEntryPermission';
    }

    public static function TransferFeePermission()
    {
        return 'TransferFeePermission';
    }

    public static function EInvoicePermission()
    {
        return 'EInvoicePermission';
    }

    public static function SSTPermission()
    {
        return 'SSTPermission';
    }

    public static function ClientAccountBalancePermission()
    {
        return 'ClientAccountBalancePermission';
    }

    public static function DocumentTemplatePermission()
    {
        return 'DocumentTemplatePermission';
    }

    public static function QuotationTemplatePermission()
    {
        return 'QuotationTemplatePermission';
    }

    public static function AttachmentPermission()
    {
        return 'AttachmentPermission';
    }
   
    public static function FileBefore2022Permission()
    {
        return 'FileBefore2022Permission';
    }

    public static function LetterHeadTemplatePermission()
    {
        return 'LetterHeadTemplatePermission';
    }

    public static function MoveBillPermission()
    {
        return 'MoveBillPermission';
    }

}
