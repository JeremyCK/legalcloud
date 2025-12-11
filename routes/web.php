<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use App\Http\Controllers\AccountCodeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdjudicationController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\CaseArchieveController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\CasesV2Controller;
use App\Http\Controllers\ChecklistItemsController;
use App\Http\Controllers\CHKTController;
use App\Http\Controllers\ClaimsController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardV2Controller;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DocTemplateFilev2Controller;
use App\Http\Controllers\EInvoiceContoller;
use App\Http\Controllers\EInvoiceContollerV2;
use App\Http\Controllers\EInvoiceController;
use App\Http\Controllers\TransferFeeV2Controller;
use App\Http\Controllers\TransferFeeV3Controller;
use App\Http\Controllers\DataRepairController;
use App\Http\Controllers\InvoiceFixController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\LandOfficeController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\PrepareDocsController;
use App\Http\Controllers\QuotationGeneratorController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnCallController;
use App\Http\Controllers\SafeKeepingController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SummaryReportController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\VoucherControllerV2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;


Route::get('/client-einvoice-data/{token}', [EInvoiceContoller::class, 'clientEinvoiceData']);
Route::post('/client-einvoice-data/update/{token}', [EInvoiceContoller::class, 'updateClientEinvoiceData']);

            // API routes for billing party search
            Route::get('/api/billing-parties/search', [EInvoiceContoller::class, 'searchBillingParties']);
            Route::get('/api/billing-parties/{id}', [EInvoiceContoller::class, 'getBillingParty']);
            Route::post('/api/billing-parties/create', [EInvoiceContoller::class, 'createBillingParty']);
            Route::post('/api/billing-parties/check-duplicate', [EInvoiceContoller::class, 'checkDuplicateBillingParty']);

// API routes for client search and data loading
Route::get('/api/clients/search', [EInvoiceContoller::class, 'searchClients']);
Route::get('/api/clients/{id}/billing-party-data', [EInvoiceContoller::class, 'getClientBillingPartyData']);

// API routes for combined party search (clients + masterlist)
Route::get('/api/parties/search', [EInvoiceContoller::class, 'searchParties']);
Route::get('/api/masterlist-parties/{caseId}/{partyType}/{partyCategory}/billing-party-data', [EInvoiceContoller::class, 'getMasterlistPartyData']);

// API routes for access-controlled party search (NEW)
Route::get('/api/parties/search-with-access', [EInvoiceContoller::class, 'searchPartiesWithAccess']);
Route::get('/api/clients/{id}/billing-party-data-with-access', [EInvoiceContoller::class, 'getClientBillingPartyDataWithAccess']);
Route::get('/api/masterlist-parties/{caseId}/{partyType}/{partyCategory}/billing-party-data-with-access', [EInvoiceContoller::class, 'getMasterlistPartyDataWithAccess']);

// Test route for debugging transfer list
Route::match(['GET', 'POST'], '/test-transfer-list', [AccountController::class, 'testTransferList']);

// Route for getting invoice data for transfer fee
Route::post('/get-transfer-fee-invoice-data', [AccountController::class, 'getTransferFeeInvoiceData'])->name('transferFeeInvoiceData.list');

Route::group(['middleware' => ['get.menu']], function () {

    


    Route::get('/', function () {
        return view('auth.login');
    });

    // Export routes without middleware to avoid HTML response issues
    Route::get('export-sst-excel/{id}', [App\Http\Controllers\SSTV2Controller::class, 'exportSSTV2ExcelSimple']);

    Route::group(['middleware' => ['role:user']], function () {
        Route::get('/colors', function () {
            return view('dashboard.colors');
        });
        Route::get('/typography', function () {
            return view('dashboard.typography');
        });
        Route::get('/charts', function () {
            return view('dashboard.charts');
        });
        Route::get('/widgets', function () {
            return view('dashboard.widgets');
        });
        Route::get('/google-maps', function () {
            return view('dashboard.googlemaps');
        });
        Route::get('/404', function () {
            return view('dashboard.404');
        });
        Route::get('/500', function () {
            return view('dashboard.500');
        });
        Route::prefix('base')->group(function () {
            Route::get('/breadcrumb', function () {
                return view('dashboard.base.breadcrumb');
            });
            Route::get('/cards', function () {
                return view('dashboard.base.cards');
            });
            Route::get('/carousel', function () {
                return view('dashboard.base.carousel');
            });
            Route::get('/collapse', function () {
                return view('dashboard.base.collapse');
            });

            Route::get('/jumbotron', function () {
                return view('dashboard.base.jumbotron');
            });
            Route::get('/list-group', function () {
                return view('dashboard.base.list-group');
            });
            Route::get('/navs', function () {
                return view('dashboard.base.navs');
            });
            Route::get('/pagination', function () {
                return view('dashboard.base.pagination');
            });

            Route::get('/popovers', function () {
                return view('dashboard.base.popovers');
            });
            Route::get('/progress', function () {
                return view('dashboard.base.progress');
            });
            Route::get('/scrollspy', function () {
                return view('dashboard.base.scrollspy');
            });
            Route::get('/switches', function () {
                return view('dashboard.base.switches');
            });

            Route::get('/tabs', function () {
                return view('dashboard.base.tabs');
            });
            Route::get('/tooltips', function () {
                return view('dashboard.base.tooltips');
            });
        });
        Route::prefix('buttons')->group(function () {
            Route::get('/buttons', function () {
                return view('dashboard.buttons.buttons');
            });
            Route::get('/button-group', function () {
                return view('dashboard.buttons.button-group');
            });
            Route::get('/dropdowns', function () {
                return view('dashboard.buttons.dropdowns');
            });
            Route::get('/brand-buttons', function () {
                return view('dashboard.buttons.brand-buttons');
            });
            Route::get('/loading-buttons', function () {
                return view('dashboard.buttons.loading-buttons');
            });
        });
        Route::prefix('editors')->group(function () {
            Route::get('/code-editor', function () {
                return view('dashboard.editors.code-editor');
            });
            Route::get('/markdown-editor', function () {
                return view('dashboard.editors.markdown-editor');
            });
            Route::get('/text-editor', function () {
                return view('dashboard.editors.text-editor');
            });
        });

        Route::prefix('forms')->group(function () {
            Route::get('/basic-forms', function () {
                return view('dashboard.forms.basic-forms');
            });
            Route::get('/advanced-forms', function () {
                return view('dashboard.forms.advanced-forms');
            });
            Route::get('/validation', function () {
                return view('dashboard.forms.validation');
            });
        });

        Route::prefix('icon')->group(function () {  // word: "icons" - not working as part of adress
            Route::get('/coreui-icons', function () {
                return view('dashboard.icons.coreui-icons');
            });
            Route::get('/flags', function () {
                return view('dashboard.icons.flags');
            });
            Route::get('/brands', function () {
                return view('dashboard.icons.brands');
            });
        });
        Route::prefix('notifications')->group(function () {
            Route::get('/alerts', function () {
                return view('dashboard.notifications.alerts');
            });
            Route::get('/badge', function () {
                return view('dashboard.notifications.badge');
            });
            Route::get('/modals', function () {
                return view('dashboard.notifications.modals');
            });
            Route::get('/toastr', function () {
                return view('dashboard.notifications.toastr');
            });
        });
        Route::prefix('plugins')->group(function () {
            Route::get('/calendar', function () {
                return view('dashboard.plugins.calendar');
            });
            Route::get('/draggable-cards', function () {
                return view('dashboard.plugins.draggable-cards');
            });
            Route::get('/spinners', function () {
                return view('dashboard.plugins.spinners');
            });
        });
        Route::prefix('tables')->group(function () {
            Route::get('/tables', function () {
                return view('dashboard.tables.tables');
            });
            Route::get('/datatables', function () {
                return view('dashboard.tables.datatables');
            });
        });

        Route::prefix('apps')->group(function () {
            Route::prefix('invoicing')->group(function () {
                Route::get('/invoice', function () {
                    return view('dashboard.apps.invoicing.invoice');
                });
            });
            Route::prefix('email')->group(function () {
                Route::get('/inbox', function () {
                    return view('dashboard.apps.email.inbox');
                });
                Route::get('/message', function () {
                    return view('dashboard.apps.email.message');
                });
                Route::get('/compose', function () {
                    return view('dashboard.apps.email.compose');
                });
            });
        });
        Route::resource('notes', 'NotesController');
    });


    Auth::routes();

    Route::resource('resource/{table}/resource', 'ResourceController')->names([
        'index'     => 'resource.index',
        'create'    => 'resource.create',
        'store'     => 'resource.store',
        'show'      => 'resource.show',
        'edit'      => 'resource.edit',
        'update'    => 'resource.update',
        'destroy'   => 'resource.destroy'
    ]);

    Route::group(['middleware' => ['auth']], function () {

        Route::resource('bread',  'BreadController');   //create BREAD (resource)

        Route::group(
            ['prefix' => 'case'],
            function () {
            }
        );

        Route::group(['middleware' => ['role:admin|management|lawyer|sales|account|clerk|receptionist|chambering|maker|jr_account']], function () {
            Route::resource('loan',    'CaseController');
            Route::resource('todolist',    'TodoController');
            Route::resource('mytodo',    'MyTodoController');
            Route::resource('case',    'CaseController');
            Route::resource('couriers',    'CourierController');
            // Route::resource('dispatch',    'DispatchController');
            Route::resource('bill',    'BillController');
            Route::resource('voucher',    'VoucherController');
            Route::resource('todolist.masterlist',    'TodoController@masterlist');
            Route::get('todolist/masterlist',      'TodoController@masterlist');
            Route::resource('case-template',    'CaseTemplateController');
            Route::resource('email-template',    'EmailTemplateController');
            Route::resource('document-template',    'DocTemplateController');
            Route::resource('audit-log',    'AuditLogController');
            Route::resource('activity-log',    'ActivityLogController');
            Route::resource('account-cat',    'AccountCategoryController');
            Route::resource('account',    AccountController::class);
            Route::resource('account-item',    'AccountItemController');
            Route::resource('account-template',    'AccountTemplateController');
            Route::resource('checklist-template',    'ChecklistTemplateController');
            Route::resource('checklist-item',    'ChecklistItemsController');
            Route::resource('masterlist',    'MasterlistController');
            Route::resource('referral',    'ReferralController');
            Route::resource('quotation',    'QuotationController');
            // Route::resource('summary-report',    'SummaryReportController');

            
            Route::resource('email',    'EmailController');

            Route::post('update_page',        'DocTemplateController@updatePage');
            Route::post('update_lock',        'DocTemplateController@updateLockStatus');
            Route::post('check_lock',        'DocTemplateController@checkPageLocked');

            
            Route::post('update_checklist',        'CaseController@updateCheckList');
            Route::post('create_dispatch',        'CaseController@createDispatch');
            Route::post('upload_file',        'CaseController@UploadFile');
            Route::post('update_masterlist/{paramter1}',        'CaseController@updateMasterList');
            
            // Route::post('request_voucher/{paramter1}',        'CaseController@requestVoucher');
            // Route::post('receive_bill_payment/{paramter1}/{paramter2}',        'CaseController@receiveBillPayment');
            // Route::post('requestTrustDisbusement/{parameter1}', [CaseController::class, 'requestTrustDisbusement']);
            // Route::post('receiveTrustDisbusement/{parameter1}', [CaseController::class, 'receiveTrustDisbusement']);
            // Route::post('update_trust_value/{parameter1}', [CaseController::class, 'updateTrustValueV2']);
            // Route::post('updateVoucherValue/{parameter1}', [VoucherController::class, 'updateVoucherValue']);
            
            Route::post('deleteReceivedBill/{parameter1}/{parameter2}', [CaseController::class, 'deleteReceivedBill']);
            Route::post('deleteReceivedTrust/{parameter1}', [CaseController::class, 'deleteReceivedTrust']);
            Route::post('deleteDisburseTrust/{parameter1}', [CaseController::class, 'deleteDisburseTrust']);

            Route::post('request_voucher/{paramter1}', [VoucherControllerV2::class, 'requestBillDisb']);
            Route::post('receive_bill_payment/{paramter1}', [VoucherControllerV2::class, 'receiveBillPayment']);
            Route::post('requestTrustDisbusement/{paramter1}', [VoucherControllerV2::class, 'requestTrustDisb']);
            Route::post('receiveTrustDisbusement/{parameter1}', [VoucherControllerV2::class, 'receiveTrustFund']);
            Route::post('updateVoucher/{parameter1}', [VoucherControllerV2::class, 'updateVoucher']);
            Route::post('updateVoucherValue/{parameter1}', [VoucherControllerV2::class, 'updateVoucherBillDisbAmt']);
            Route::post('deleteVoucherRecord/{parameter1}', [VoucherControllerV2::class, 'deleteVoucher'])->name('voucher.delete');

            Route::post('update_voucher_status/{paramter1}',        'VoucherController@updateVoucherStatus');
            Route::post('update_voucher_status_v2/{paramter1}',        'VoucherController@updateVoucherStatusV2');

            Route::get('document/{paramter1}/{paramter2}', 'CaseController@document');

            
            Route::resource('clients',    'ClientsController');
            Route::post('view_voucher/{paramter1}',        [AccountController::class, 'view']);
            Route::post('update_voucher',        [AccountController::class, 'update']);

            
            Route::post('update_mytodo_status/{paramter1}',        'MyTodoController@updateMyTodoStatus');
            Route::get('getBillTemplate/{paramter1}',        'CaseController@getBillTemplate');

            Route::get('change_password', 'ProfileController@changePasswordView');
            Route::post('update_password', 'ProfileController@changePassword');
            

            
            Route::post('update_bill_transaction', 'MyTodoController@updateBillTransction');

            Route::post('update_checklist_template/{paramter1}',        'CaseTemplateController@updateCheckListTemplate');
            Route::post('add_checklist_template/{paramter1}',        'CaseTemplateController@addNewCheckListTemplate');
            Route::post('create_checklist_template',        'CaseTemplateController@createChecklistTemplate');
            Route::post('update_template_details/{paramter1}',        'CaseTemplateController@updateTemplateDetails');

            
            Route::get('get_email_content/{paramter1}',        'CaseTemplateController@updateTemplateDetails');

            
            Route::resource('teams',        'TeamController');
            Route::resource('banks',        'BankController');
            Route::resource('genfile',        'DocTemplateFileController');
            Route::resource('document-file',        'DocTemplateFilev2Controller'); 
            Route::resource('portfolio',        'PortfolioController');
            // Main dashboard route now uses V2 (optimized version)
            Route::get('dashboard', [DashboardV2Controller::class, 'index'])->name('dashboard.index');
            
            // Keep original dashboard accessible at dashboard-original for backup
            Route::get('dashboard-original', [DashboardController::class, 'index'])->name('dashboard.original');
            
            // Keep other dashboard routes for compatibility
            Route::post('dashboard/{action}', [DashboardController::class, '{action}'])->where('action', 'create|store|update|destroy');
            Route::resource('users',        'UsersController');
            Route::post('filter',        'UsersController@filter');
            Route::resource('office-bank-account',        'OfficeBankAccountController');

            
            Route::post('load_case_template',        'CaseController@loadCaseTemplate');

            Route::post('gen_file',        'DocTemplateFileController@getFile');


            
            Route::post('set_file_active',        'DocTemplateFilev2Controller@setFileActive');
            Route::post('update_file_template_info/{paramter1}',        'DocTemplateFilev2Controller@updateFileTemplateInfo');
            Route::post('upload_file_template/{paramter1}',        'DocTemplateFilev2Controller@UploadFileTemplate');

            
            Route::post('getDocumentFileMainList/{parameter1}', [DocTemplateFilev2Controller::class, 'getDocumentFileMainList']);
            Route::get('getDocumentFileMainList', [DocTemplateFilev2Controller::class, 'getDocumentFileMainList'])->name('documentFileMainList.list');
            Route::post('deleteFolder/{parameter1}', [DocTemplateFilev2Controller::class, 'deleteFolder']);
            
            Route::post('generate_file_from_template/{paramter1}',        'CaseController@generateFilesFromTemplate');
            Route::post('set_kiv/{paramter1}',        'CaseController@setKIV');
            Route::post('save_team_member/{paramter1}',        'TeamController@setTeamMember');
            Route::post('save_team_portfolio/{paramter1}',        'TeamController@setTeamPortfolio');

            Route::post('get_file',        'DocTemplateFilev2Controller@getFile');
            Route::post('delete_file/{parameter1}',        'DocTemplateFilev2Controller@deleteFile');

            Route::post('add_checklist_step',        'ChecklistItemsController@AddNewChecklistStep');
            Route::post('update_checklist_step/{parameter1}',        'ChecklistItemsController@updateChecklistSteps');
            Route::post('add_checklist_item/{parameter1}',        'ChecklistItemsController@AddCheckListItem');
            Route::post('update_checklist_item/{parameter1}',        'ChecklistItemsController@updateCheckListItem');
            
            Route::post('add_checklist_template_step/{parameter1}',        'ChecklistTemplateController@AddCheckListTemplateStep');
            Route::post('update_checklist_template_step/{parameter1}',        'ChecklistTemplateController@UpdateCheckListTemplateStep');
            Route::post('delete_checklist_template_step/{parameter1}',        'ChecklistTemplateController@DeleteCheckListTemplateStep');


            
            Route::post('create_case',        'CaseController@createCase');
            Route::post('accept_case/{parameter1}',        'CaseController@acceptCase');
            Route::post('close_case/{parameter1}/{parameter2}',        'CaseController@closeCase');

            
            Route::get('case_details/{parameter1}', [CaseController::class, 'caseDetails'])->name('case.details');
            Route::post('loadMainBillTable/{parameter1}', [CaseController::class, 'loadMainBillTable']);
            Route::post('loadMasterlistPrintInfo/{parameter1}', [CaseController::class, 'loadMasterlistPrintInfo']);

            
            Route::post('filter_case',        'CaseController@filterCase');
            Route::post('filter_case_by_role',        'CaseController@filterCaseByRole');
            Route::post('filter_case_by_branch',        'CaseController@filterCaseByBranch');

            Route::post('search_case',        'DashboardController@searchCase');

            
            Route::post('load_quotation_template/{parameter1}',        'CaseController@loadQuotationTemplate');
            Route::post('create_bill/{parameter1}',        'CaseController@createBill');
            Route::post('load_case_bill/{parameter1}',        'CaseController@loadCaseBill');

            Route::get('autocomplete', [ReferralController::class, 'autocomplete'])->name('autocomplete');

            Route::post('create_referral',        'ReferralController@createReferral');

            Route::get('case_file/list/{parameter1}', [CaseController::class, 'getCaseFile'])->name('caseFile.list');
            Route::post('delete_case_file/{parameter1}',        'CaseController@deleteFile');

            
            Route::post('update_quotation_bill/{parameter1}',        'QuotationController@updateQuotationBill');
            Route::post('add_account_item_to_quotation/{parameter1}',        'QuotationController@addAccountIntoQuotation');
            Route::post('delete_account_item_from_quotation/{parameter1}',        'QuotationController@deleteAccountIntoQuotation');
            Route::get('bill_main/list/', [BillController::class, 'getBillList'])->name('bill.list');
            Route::get('voucher_main/list/', [VoucherController::class, 'getVoucherListV2'])->name('voucher.list');
            Route::get('ledger/list/{parameter1}', [CaseController::class, 'getLedger'])->name('ledger.list');
            Route::get('checklistdetails/list/', [ChecklistItemsController::class, 'getChecklist'])->name('checklistdetails.list');
            Route::post('delete_checklist/{parameter1}', [ChecklistItemsController::class, 'deleteChecklist']);
            Route::get('generate-pdf', [VoucherController::class, 'generatePDF']);

            
            Route::post('reorder_sequence_checklist_template', [ChecklistItemsController::class, 'reorderSequenceChecklistTemplate']);

            Route::post('upload_account_file/{parameter1}', [VoucherController::class, 'uploadAccountFile']);
            Route::post('upload_account_file2/{parameter1}', [VoucherController::class, 'uploadAccountFile2']);
            Route::post('generate_receipt/{parameter1}/{parameter2}', [CaseController::class, 'generateReceipt']);
            Route::post('get_trust_value/{parameter1}', [CaseController::class, 'getTrustValue']);
            Route::post('delete_receipt_file', [CaseController::class, 'deleteReceiptFile']);
            Route::post('update_loan_case_trust_main/{parameter1}', [CaseController::class, 'updateLoanCaseTrustMain']);
            Route::post('generate_trust_receipt/{parameter1}', [CaseController::class, 'generateTrustReceipt']);

            
            Route::post('create_folder', [DocTemplateFilev2Controller::class, 'createFolder']);
            Route::post('edit_folder', [DocTemplateFilev2Controller::class, 'editFolder']);
            Route::post('move_file_folder/{parameter1}', [DocTemplateFilev2Controller::class, 'moveFileFolder']);
            Route::post('retriveNotification', [NotificationController::class, 'retriveNotification']);
            Route::post('openNotification/{parameter1}', [NotificationController::class, 'openNotification']);
            Route::post('generateBillReceipt/{parameter1}/{parameter2}', [CaseController::class, 'generateBillReceipt']);
            Route::post('get_bill_receive_value/{parameter1}', [CaseController::class, 'getBillReceive']);
            Route::post('update_bill_receove_value/{parameter1}/{parameter2}', [CaseController::class, 'updateBillReceiveValue']);
            Route::post('updateBillSummary/{parameter1}', [CaseController::class, 'updateBillSummary']);

            
            Route::post('resubmitVoucher/{parameter1}', [VoucherController::class, 'resubmitVoucher']);

            Route::post('deleteUploadedFile/{parameter1}/{parameter2}', [DocTemplateFilev2Controller::class, 'deleteUploadedFile']);
            Route::post('submitNotes/{parameter1}', [CaseController::class, 'submitNotes']);
            Route::post('submitEditNotes/{parameter1}', [CaseController::class, 'submitEditNotes']);
            Route::post('submitEditPncNotes/{parameter1}', [CaseController::class, 'submitEditPncNotes']);
            Route::post('submitEditMarketingNotes/{parameter1}', [CaseController::class, 'submitEditMarketingNotes']);

            Route::post('uploadMarketingBill', [CaseController::class, 'uploadMarketingBill']);
            Route::post('deleteMarketingBill/{parameter1}', [CaseController::class, 'deleteMarketingBill']);

            
            Route::get('voucher-report', [SummaryReportController::class, 'voucherReport']);
            
            Route::get('ledger', [AccountController::class, 'bankRecon']);
        
            Route::post('update_bill_print_details/{parameter1}', [CaseController::class, 'updateBillPrintDetail']);

            
            Route::post('generateBillLumdReceipt/{parameter1}/{parameter2}', [CaseController::class, 'generateBillLumdReceipt']);
            
            Route::get('voucher-archive', [VoucherController::class, 'voucherArchieve']);
            Route::get('voucher-inprogress', [VoucherController::class, 'voucherInprogress']);
            Route::get('payment-received', [VoucherController::class, 'paymentReceived']);
            Route::get('quotation-generator', [QuotationGeneratorController::class, 'quotationGenerator']);
            Route::post('load_quotation_template_generator/{parameter1}', [QuotationGeneratorController::class, 'loadQuotationTemplateGenerator']);
            Route::post('logPrintedQuotation', [QuotationGeneratorController::class, 'logPrintedQuotation']);
            Route::post('convertToInvoice/{parameter1}', [CaseController::class, 'convertQuotationToInvoice']);
            Route::get('quotation-generator-create', [QuotationGeneratorController::class, 'quotationGeneratorCreate']);
            Route::get('quotation-generator-edit/{parameter1}', [QuotationGeneratorController::class, 'quotationGeneratorEdit']);
            Route::post('generateQuotationPrint', [QuotationGeneratorController::class, 'generateQuotationPrint']);
            Route::post('quotationGenAddAccountItem/{parameter1}', [QuotationGeneratorController::class, 'quotationGenAddAccountItem']);
            Route::get('copyTemplate/{parameter1}', [QuotationGeneratorController::class, 'copyTemplate']);
            
            Route::get('getQuotationGeneratorList', [QuotationGeneratorController::class, 'getQuotationGeneratorList'])->name('quotationGeneratorList.list');

            
            Route::post('update_quotation_bill_by_admin', [CaseController::class, 'updateQuotationBillByAdmin']);
            Route::post('SaveSummaryInfo/{parameter1}', [CaseController::class, 'SaveSummaryInfo']);
            
            Route::post('SaveAccountSummary/{parameter1}', [CaseController::class, 'SaveAccountSummary']);
            Route::post('clearReferral/{parameter1}', [CaseController::class, 'clearReferral']);
            Route::post('updateQuotationValue', [CaseController::class, 'updateQuotationValue']);
            Route::post('addQuotationItem/{parameter1}', [CaseController::class, 'addQuotationItem']);
            Route::post('deleteQuotationItem/{parameter1}', [CaseController::class, 'deleteQuotationItem']);
            // Route::post('adminUpdateValue', [CaseController::class, 'updateBillSummaryAllByAdmin']);      
            Route::post('adminUpdateValue', [CaseController::class, 'adminUpdateValue']);               
            Route::post('adminUpdateBillSum', [CaseController::class, 'adminUpdateBillSum']);            
            Route::post('adminMigrateLedger', [CaseController::class, 'adminMigrateLedger']);        
            Route::post('adminBulkTransferCase', [CaseController::class, 'adminBulkTransferCase']);         
            Route::post('adminUploadExcelFile', [CaseController::class, 'adminUploadExcelFile']);      
            Route::post('adminBonusCalculation', [CaseController::class, 'adminBonusCalculation']);  
            // Route::post('adminBonusCalculation', [CaseController::class, 'calculateEstimateBonus']);  
            Route::post('saveQuotationTemplate', [QuotationGeneratorController::class, 'saveQuotationTemplate']);

            
            Route::post('updateCheckListBulk/{parameter1}', [CaseController::class, 'updateCheckListBulk']);
            Route::post('updateCheckListBulkV2/{parameter1}', [CaseController::class, 'updateCheckListBulkV2']);
            Route::post('deleteNotes/{parameter1}', [CaseController::class, 'deleteNotes']);
            Route::post('deletePncNotes/{parameter1}', [CaseController::class, 'deletePncNotes']);

            
            Route::post('deleteMarketingNotes/{parameter1}', [CaseController::class, 'deleteMarketingNotes']);

            
            Route::post('updateAllcheckListDate', [CaseController::class, 'updateAllcheckListDate']);
            Route::post('loadQuotationToInvoice/{parameter1}', [CaseController::class, 'loadQuotationToInvoice']);
            Route::post('updateInvoiceValue', [CaseController::class, 'updateInvoiceValue']);
            Route::post('addInvoiceItem/{parameter1}', [CaseController::class, 'addInvoiceItem']);
            Route::post('deleteInvoiceItem/{parameter1}', [CaseController::class, 'deleteInvoiceItem']);
            Route::get('debug-invoice-calculation/{invoice_no}', [CaseController::class, 'debugInvoiceCalculation']);
            Route::get('debug-multi-invoice/{bill_id}', [CaseController::class, 'debugMultiInvoiceScenario']);
            Route::post('convertToSST/{parameter1}', [CaseController::class, 'convertToSST']);
            Route::post('deleteBill/{parameter1}', [CaseController::class, 'deleteBill']);
            
            Route::get('referral_main/list/', [ReferralController::class, 'getReferralList'])->name('referral_main.list');
            Route::post('loadSavedQuotationTemplateGenerator/{parameter1}', [QuotationGeneratorController::class, 'loadSavedQuotationTemplateGenerator']);
            Route::post('updateQuotationTemplate/{parameter1}', [QuotationGeneratorController::class, 'updateQuotationTemplate']);
            Route::post('deleteSavedQuotation/{parameter1}', [QuotationGeneratorController::class, 'deleteSavedQuotation']);

            
            Route::post('deleteVoucher/{parameter1}', [VoucherController::class, 'deleteVoucher']);
            
            Route::get('user_kpi_list/list/{parameter1}', [UsersController::class, 'getUserKPIList'])->name('user_kpi_list.list');

            
            Route::get('kpi_list/{parameter1}', [UsersController::class, 'kpiList']);

            Route::get('to_do_list/list/', [DashboardController::class, 'getTodoList'])->name('user_todo_list.list');
            
            Route::resource('adjudication',    'AdjudicationController');
            Route::get('adjudication_list/list/', [AdjudicationController::class, 'getAdjudicationList'])->name('adjudication_list.list');
            Route::get('dispatch_list/list/{parameter1}/{parameter2}/{parameter3}', [DispatchController::class, 'getDispatchList'])->name('dispatch_list.list');

            
            Route::get('report-invoice', [ReportController::class, 'reportInvoice'])->name('report.invoice');
            Route::get('report-sst', [ReportController::class, 'reportSST']);
            Route::get('report-referral', [ReportController::class, 'reportReferral'])->name('report.referral');

            Route::resource('cases-archieve',    'CaseArchieveController');
            Route::get('case_archieve_list/list/{parameter1}/{parameter2}/{parameter3}', [CaseArchieveController::class, 'getOpenCaseList'])->name('case_archieve.list');
            Route::get('case_archieve_closed_list/list/{parameter1}/{parameter2}', [CaseArchieveController::class, 'getClosedCaseList'])->name('case_archieve_closed.list');
            Route::get('case_archieve_pending_closed_list/list', [CaseArchieveController::class, 'getPendingCloseCaseList'])->name('case_archieve_pending.list');
            Route::post('updateArchieveCaseRemark/{parameter1}', [CaseArchieveController::class, 'updateArchieveCaseRemark']);
            Route::post('TransferCase/{parameter1}', [CaseArchieveController::class, 'TransferCase']);
            Route::post('closeArchieveCase/{parameter1}', [CaseArchieveController::class, 'closeArchieveCase']);
            Route::post('pendingCloseArchieveCase/{parameter1}', [CaseArchieveController::class, 'pendingCloseArchieveCase']);
            Route::post('updateArchieveCaseCompletionDate/{parameter1}', [CaseArchieveController::class, 'updateArchieveCaseCompletionDate']);
            Route::get('closed-2022-list', [CaseArchieveController::class, 'closedList']);
            Route::get('pending-close-2022-list', [CaseArchieveController::class, 'pendingClosedList']);
            Route::post('AssignSales/{parameter1}', [CaseArchieveController::class, 'AssignSales']);
            Route::post('AssignLawyer/{parameter1}', [CaseArchieveController::class, 'AssignLawyer']);
            Route::post('reopenArchieveCase/{parameter1}', [CaseArchieveController::class, 'reopenArchieveCase']);
            Route::post('deleteDispatch/{parameter1}', [DispatchController::class, 'deleteDispatch']);
            
            Route::post('moveCaseToPerfectionCase/{parameter1}', [CaseArchieveController::class, 'moveCaseToPerfectionCase']);

            
            Route::get('perfection-case', [CasesV2Controller::class, 'perfectionCase'])->name('perfectionCase');
            Route::get('add-case', [CasesV2Controller::class, 'addCase'])->name('addCase');
            Route::get('perfection/list', [CasesV2Controller::class, 'getCasesList'])->name('perfection.list');
            Route::get('case-details/{parameter1}', [CasesV2Controller::class, 'caseDetails']);
            Route::post('addCaseNotes/{parameter1}', [CasesV2Controller::class, 'addCaseNotes']);
            Route::post('editCaseNotes/{parameter1}', [CasesV2Controller::class, 'editCaseNotes']);
            Route::post('deleteCaseNote/{parameter1}', [CasesV2Controller::class, 'deleteCaseNote']);
            Route::post('updateCaseDetails/{parameter1}', [CasesV2Controller::class, 'updateCaseDetails']);
            Route::post('assignPIC/{parameter1}', [CasesV2Controller::class, 'assignPIC']);
            Route::post('removePIC/{parameter1}', [CasesV2Controller::class, 'removePIC']);
            Route::post('createCaseOther', [CasesV2Controller::class, 'createCaseOther']);

            
            Route::post('checkCloseFileBalance/{parameter1}', [CaseController::class, 'checkCloseFileBalance']);
            Route::post('closeFile/{parameter1}', [CaseController::class, 'closeFile']);
            Route::post('updateCloseFileDate/{parameter1}', [CaseController::class, 'updateCloseFileDate']);

            

            
            Route::resource('dispatch',    'DispatchController');

            
            Route::get('dispatch-incoming', [DispatchController::class, 'dispatchIncoming']);
            // Route::get('dispatch-out', [DispatchController::class, 'dispatchOutgoing']);
            Route::get('createincoming', [DispatchController::class, 'createIncoming']);


             
            Route::get('invoice_report/list/{parameter1}/{parameter2}', [ReportController::class, 'getInvoiceReport'])->name('invoice_report.list');
            Route::get('getInvoiceReport', [ReportController::class, 'getInvoiceReportV2'])->name('invoiceReport.list');

            
            Route::post('filterInvoiceReport', [ReportController::class, 'filterInvoiceReport']);
            Route::post('filterSSTReport', [ReportController::class, 'filterSSTReport']);
            Route::post('updatePaidStatus', [ReportController::class, 'updatePaidStatus']);

            
            Route::post('bulk_update_voucher_status', [VoucherController::class, 'bulkUpdateVoucherStatus']);
            Route::post('approveVoucher/{parameter1}', [VoucherController::class, 'approveVoucher']);
            Route::post('unapproveVoucher/{parameter1}', [VoucherController::class, 'unapproveVoucher']);

            Route::resource('safe-keeping',    'SafeKeepingController');
            Route::get('safe_keeping_list/list/', [SafeKeepingController::class, 'getSafeKeepingList'])->name('safe_keeping_list.list');

            
            Route::resource('land-office',    'LandOfficeController');
            Route::get('land_office_list/list/', [LandOfficeController::class, 'getLandOfficeList'])->name('land_office_list.list');
            
            Route::resource('return-call',    'ReturnCallController');
            Route::get('return_call_list/list/', [ReturnCallController::class, 'getReturnCallList'])->name('return_call_list.list');

            Route::resource('prepare-docs',    'PrepareDocsController');
            Route::get('prepare_docs_list/list/', [PrepareDocsController::class, 'getPrepareDocsList'])->name('prepare_docs_list.list');

            
            Route::post('clientProfileCheck', [CaseController::class, 'clientProfileCheck']);
            // Route::post('clientProfileCheck', [CaseController::class, 'clientProfileCheckV2']);
            Route::post('createCaseWithSameTeam', [CaseController::class, 'createCaseWithSameTeam']);

            
            Route::get('case_list/list/', [CaseController::class, 'getCaseList'])->name('case_list.list');
            Route::get('search_case_list/list/', [CaseController::class, 'getSearchCaseList'])->name('search_case_list.list');

            
            Route::post('getSearchCase', [CaseController::class, 'getSearchCase']);

            
            Route::get('referral_report/list', [ReportController::class, 'getReferralReport'])->name('referral_report.list');
            Route::post('updateCaseStatus/{parameter1}', [CaseController::class, 'updateCaseStatus']);

            
            Route::get('close-case', [CaseController::class, 'closeCaseList']);
            Route::get('abort-case', [CaseController::class, 'abortCaseList']);
            Route::get('pending-close-case', [CaseController::class, 'pendingCloseCaseList']);
            Route::get('reviewing-case', [CaseController::class, 'reviewCaseList']);

        
            Route::get('cases/{parameter1}', [CaseController::class, 'Cases'])->name('cases.list');

            
            Route::get('download-referral', [ReportController::class, 'downloadReferral'])->name('download-referral');

            Route::resource('chkt',    'CHKTController');
            Route::get('chkt_list/list/', [CHKTController::class, 'getCHKTList'])->name('chkt_list.list');

            Route::prefix('referral-comm')->group(function () {
                Route::get('/',  [SettingsController::class, 'referralCommList']);
                Route::get('{parameter1}/edit',  [SettingsController::class, 'referralCommEdit']);
                Route::get('list/{parameter1}', [SettingsController::class, 'getFormulaReferralList'])->name('referral-comm.list');
                Route::get('referral_list/{parameter1}', [SettingsController::class, 'getReferralList'])->name('referral-comm.referral_list');
                Route::post('saveReferralIntoCommGroup/{parameter1}', [SettingsController::class, 'saveReferralIntoCommGroup']);
                Route::post('removeReferralFromCommGroup', [SettingsController::class, 'removeReferralFromCommGroup']);
            });
        
            Route::get('bank-recon', [AccountController::class, 'bankRecon'])->name('bank.recon');
            // Route::get('recon_list/list/', [AccountController::class, 'getBankReconList'])->name('recon_list.list');
            Route::get('recon_list/list/', [AccountController::class, 'getBankReconListV2'])->name('recon_list.list');
            // Route::get('recon_list/list/', [AccountController::class, 'getBankReconListV3'])->name('recon_list.list');
            Route::post('getBankReconTotal', [AccountController::class, 'getBankReconTotal']);
            // Route::post('updateRecon', [AccountController::class, 'updateRecon']); 
            Route::post('updateRecon', [AccountController::class, 'updateReconV2']);
            Route::post('getMonthRecon', [AccountController::class, 'getMonthRecon']);

            
            Route::get('transfer-fee-list', [AccountController::class, 'transferFeeList'])->name('transfer-fee-list');
Route::get('transfer-fee-old', [AccountController::class, 'transferFeeList'])->name('transfer-fee-old.index');
            Route::get('transfer-fee-list-v2', [AccountController::class, 'transferFeeListV2'])->name('transfer-fee-list-v2');
            Route::get('transfer-fee/{parameter1}', [AccountController::class, 'transferFeeView']);
Route::get('transfer-fee/{parameter1}/edit', [AccountController::class, 'transferFeeEdit'])->name('transfer-fee.edit');
Route::get('transfer-fee-create', [AccountController::class, 'transferFeeCreate']);
            Route::post('createNewTranferFee', [AccountController::class, 'createNewTranferFee']);
            Route::post('updateTranferFee/{parameter1}', [AccountController::class, 'updateTranferFee']);
            Route::post('deleteTransferFee/{parameter1}', [AccountController::class, 'deleteTransferFee']);
            Route::post('reconTransferFee/{parameter1}', [AccountController::class, 'reconTransferFee']);

            
            Route::get('einvoice-list', [EInvoiceContoller::class, 'EInvoiceList'])->name('einvoice-list');
            Route::get('getEInvoiceMainList', [EInvoiceContoller::class, 'getEInvoiceMainList'])->name('EInvoiceMain.list');
            Route::get('transfer-fee/{parameter1}', [AccountController::class, 'transferFeeView']);
            Route::get('einvoice-create', [EInvoiceContoller::class, 'einvoiceCreate']);
            
            Route::get('getEInvoiceSentList', [EInvoiceContoller::class, 'getEInvoiceSentList'])->name('EInvoiceSent.list');
            Route::get('getTransferFeeBillAddList', [AccountController::class, 'getTransferFeeAddBillList'])->name('transferFeeBillAddList.list');


            
            Route::get('einvoice-createv2', [EInvoiceContollerV2::class, 'einvoiceCreate']);
            // Route::post('SaveNewEInvoice', [EInvoiceController::class, 'SaveNewEInvoice']);
            Route::post('SaveNewEInvoice', [EInvoiceContoller::class, 'saveEinvoice']);
            Route::get('einvoice/{parameter1}', [EInvoiceContoller::class, 'einvoiceView']);
            Route::post('AddInvoiceIntoEInvoice/{parameter1}', [EInvoiceContoller::class, 'AddInvoiceIntoEInvoice']);
            Route::post('DeleteInvoiceFromEInvoice/{parameter1}', [EInvoiceContoller::class, 'DeleteInvoiceFromEInvoice']);
            Route::post('generateSQLExcelTemplate/{parameter1}', [EInvoiceContollerV2::class, 'generateSQLExcelTemplate']);
            Route::post('generateSQLCustomerTemplate/{parameter1}', [EInvoiceContollerV2::class, 'generateSQLCustomerTemplate']);

            
            Route::get('report-bank-recon', [ReportController::class, 'ReportBankRecon']);
            // Route::post('get_bank_recon_report', [ReportController::class, 'getBankReconReport']);
            Route::post('get_bank_recon_report', [ReportController::class, 'getBankReconReportV2']);
            Route::get('summary-report', [ReportController::class, 'ReportSummary']);
            Route::get('quotation-report', [ReportController::class, 'ReportQuotation']);
            Route::post('getSummaryReport', [ReportController::class, 'getSummaryReport']);
            Route::post('getQuotationReport', [ReportController::class, 'getQuotationReport']);
            Route::get('summary_report_list', [ReportController::class, 'getSummaryReportList'])->name('summary-report.list');

            
            Route::get('advance-report', [ReportController::class, 'ReportAdvance']);
            Route::post('getAdvanceReport', [ReportController::class, 'getAdvanceReport']);
            Route::get('staff-report', [ReportController::class, 'ReportStaffCases']);
            Route::post('getStaffCaseReport', [ReportController::class, 'getStaffCaseReport']);
            Route::get('case-report', [ReportController::class, 'ReportCases']);
            Route::post('getCaseReport', [ReportController::class, 'getCaseReport']);
            Route::get('staff-detail-report', [ReportController::class, 'ReportStaffDetails']);
            Route::post('get-staff-details=report', [ReportController::class, 'getStaffDetailsReport']);
            
            Route::get('download-advance', [ReportController::class, 'downloadAdvance'])->name('download-advance');

            
            Route::post('updateSummarySum', [ReportController::class, 'updateSummarySum']);

            Route::post('searchCase', [CaseController::class, 'searchCase']);
            Route::post('transferSystemCase/{parameter1}', [CaseController::class, 'transferSystemCase']);
            Route::post('adminUpdateOperation', [CaseController::class, 'adminUpdateOperation']);
            Route::post('adminUpdateTransferAmount', [CaseController::class, 'adminUpdateLedgerV2']);
            
            Route::post('adminUpdateInvoiceBranch', [CaseController::class, 'adminUpdateInvoiceBranch']);
            Route::post('adminUpdateCaseCount', [CaseController::class, 'adminUpdateCaseCount']);
            Route::post('adminResizeImage', [CaseController::class, 'adminResizeImage']);
            Route::post('getDashboardCaseCount', [DashboardController::class, 'getDashboardCaseCount']);
            Route::post('getPrevYeraDashboardCaseCount', [DashboardController::class, 'getPrevYeraDashboardCaseCount']);

            
            Route::post('getDashboardCaseChart', [DashboardController::class, 'getDashboardCaseChart']);
            Route::post('getDashboardCaseChartByBranch', [DashboardController::class, 'getDashboardCaseChartByBranch']);
            Route::post('getDashboardCaseChartByStaff', [DashboardController::class, 'getDashboardCaseChartByStaff']);
            Route::post('getDashboardCaseChartBySales', [DashboardController::class, 'getDashboardCaseChartBySales']);
            Route::post('getDashboardReport', [DashboardController::class, 'getDashboardReport']);

            // Dashboard V2 - Optimized AJAX endpoints for lazy loading
            Route::post('dashboard/load-counts', [DashboardController::class, 'loadDashboardCounts']);
            Route::post('dashboard/load-notes', [DashboardController::class, 'loadNotesData']);
            Route::post('dashboard/load-case-files', [DashboardController::class, 'loadCaseFiles']);
            Route::post('dashboard/load-b2022-cases', [DashboardController::class, 'loadB2022Cases']);

            // Dashboard V2 Routes
            Route::get('dashboard-v2', [DashboardV2Controller::class, 'index'])->name('dashboard.v2');
            Route::post('dashboard-v2/chart-data', [DashboardV2Controller::class, 'getChartData']);
            Route::post('dashboard-v2/clear-cache', [DashboardV2Controller::class, 'clearCache']);
            
            // Dashboard V2 - Lazy Loading AJAX Endpoints
            Route::post('dashboard-v2/load-all-notes', [DashboardV2Controller::class, 'loadAllNotes']);
            Route::post('dashboard-v2/load-summary', [DashboardV2Controller::class, 'loadDashboardSummary']);
            Route::post('dashboard-v2/load-activities', [DashboardV2Controller::class, 'loadRecentActivities']);
            Route::post('dashboard-v2/load-metrics', [DashboardV2Controller::class, 'loadPerformanceMetrics']);
            
            // Dashboard V2 - Original Dashboard Methods
            Route::post('dashboard-v2/getDashboardCaseCount', [DashboardV2Controller::class, 'getDashboardCaseCount']);
            Route::post('dashboard-v2/getDashboardCaseChart', [DashboardV2Controller::class, 'getDashboardCaseChart']);
            Route::post('dashboard-v2/getDashboardCaseChartByBranch', [DashboardV2Controller::class, 'getDashboardCaseChartByBranch']);
            Route::post('dashboard-v2/getDashboardCaseChartByStaff', [DashboardV2Controller::class, 'getDashboardCaseChartByStaff']);
            Route::post('dashboard-v2/getDashboardCaseChartBySales', [DashboardV2Controller::class, 'getDashboardCaseChartBySales']);


            Route::get('download-summary', [ReportController::class, 'downloadSummary'])->name('download-summary');

            
            Route::post('updateVoucherAccountItem', [VoucherController::class, 'updateVoucherAccountItem']);
            Route::post('RevertInvoiceBacktoQuotation/{parameter1}', [CaseController::class, 'RevertInvoiceBacktoQuotation']);

            
            Route::get('report-bonus', [ReportController::class, 'ReportBonus']);
            Route::post('getBonusReport/{parameter1}', [ReportController::class, 'getStaffBonusReport']);
            Route::post('loadCaseQuotation/{parameter1}', [ReportController::class, 'loadCaseQuotation']);
            Route::post('loadBonusDetails/{parameter1}', [ReportController::class, 'loadBonusDetails']);

            
            Route::get('report-bonus-estimate', [ReportController::class, 'ReportBonusEstimate']);
            Route::post('getBonusReportEstimate/{parameter1}', [ReportController::class, 'getBonusReportEstimate']);
            
            Route::get('account-log', [LogsController::class, 'accountLog']);
            Route::get('getAccountLog', [LogsController::class, 'getAccountLog']);
            Route::get('account_log_list', [LogsController::class, 'getAccountLog'])->name('account_log.list');

            
            Route::post('deleteVoucherAttachment/{parameter1}', [VoucherController::class, 'deleteVoucherAttachment']);
            Route::get('client_list', [ClientsController::class, 'getClientList'])->name('client.list');

            
            Route::post('updateCaseSummary/{parameter1}', [CaseController::class, 'updateCaseSummary']);
            Route::get('getReferralCaseList', [ReferralController::class, 'getReferralCaseList'])->name('referralcase.list');

            
            Route::post('setVoucherReceiptIssue/{parameter1}', [VoucherController::class, 'setVoucherReceiptIssue']);

            
            Route::resource('account-code', AccountCodeController::class);
            // Route::resource('bonus', BonusController::class);

            
            Route::resource('bonus',    'BonusController');

            
            Route::post('getTransferList', [AccountController::class, 'getTransferList']);
            Route::get('getTransferFeeBillList', [AccountController::class, 'getTransferFeeBillList'])->name('transferFeeBillList.list');
            Route::get('getTransferFeeBillAddList', [AccountController::class, 'getTransferFeeAddBillList'])->name('transferFeeBillAddList.list');

            
            Route::post('getTransferFeeBillListV2', [AccountController::class, 'getTransferFeeBillListV2']);

            
            Route::get('getTransferMainList', [AccountController::class, 'getTransferMainList'])->name('transferFeeMainList.list');
            Route::get('getTransferMainListV2', [AccountController::class, 'getTransferMainListV2'])->name('transferFeeMainListV2.list');
            Route::get('transfer-fee-statistics', [AccountController::class, 'getTransferFeeStatistics'])->name('transfer-fee-statistics');

            
            Route::post('submitBonusReview/{parameter1}', [CaseController::class, 'submitBonusReview']);
            Route::post('rejectBonus/{parameter1}', [BonusController::class, 'rejectBonus']);
            Route::post('approveBonus/{parameter1}', [BonusController::class, 'approveBonus']);
            Route::post('revertBonus/{parameter1}', [BonusController::class, 'revertBonus']);

            
            Route::get('bonus-request-list', [BonusController::class, 'BonusReviewList']);
            Route::get('bonusrequest/list/', [BonusController::class, 'getBonusReviewList'])->name('bonusrequest.list');
            Route::get('bonus-request-details/{parameter1}', [BonusController::class, 'BonusReviewDetails']);

            
            Route::get('staff-bonus-request-list', [BonusController::class, 'StaffBonusReviewList']);
            Route::get('staffbonusrequest/list/', [BonusController::class, 'getStaffBonusReviewList'])->name('Staffbonusrequest.list');


            // Original SST Routes - Redirected to SST V2 (Invoice Based)
            Route::get('sst-list', [App\Http\Controllers\SSTV2Controller::class, 'sstListV2'])->name('sst.list');
            Route::get('sst/{parameter1}', [App\Http\Controllers\SSTV2Controller::class, 'sstShowV2']);
            
            Route::get('getSSTMainList', [App\Http\Controllers\SSTV2Controller::class, 'getSSTMainListV2'])->name('sstMainList.list');
            Route::get('sst-create', [App\Http\Controllers\SSTV2Controller::class, 'sstCreateV2']);
            Route::get('getInvoiceList', [App\Http\Controllers\SSTV2Controller::class, 'getInvoiceListV2'])->name('sstInvoiceList.list');
            Route::get('getInvoiceADDList', [App\Http\Controllers\SSTV2Controller::class, 'getInvoiceAddListV2'])->name('sstInvoiceAddList.list');
            Route::post('createNewSSTRecord', [App\Http\Controllers\SSTV2Controller::class, 'createNewSSTRecordV2']);
            Route::get('sst/{parameter1}', [App\Http\Controllers\SSTV2Controller::class, 'sstShowV2']);
            Route::post('updateSST/{parameter1}', [App\Http\Controllers\SSTV2Controller::class, 'updateSSTV2']);
            Route::post('deleteSST/{parameter1}', [App\Http\Controllers\SSTV2Controller::class, 'deleteSSTV2']);

            // SST V2 Routes (Invoice Based)
            Route::get('sst-v2-list', [App\Http\Controllers\SSTV2Controller::class, 'sstListV2'])->name('sst-v2.list');
            Route::get('sst-v2-create', [App\Http\Controllers\SSTV2Controller::class, 'sstCreateV2'])->name('sst-v2.create');
            Route::get('sst-v2-edit/{id}', [App\Http\Controllers\SSTV2Controller::class, 'sstEditV2'])->name('sst-v2.edit');
            Route::get('sst-v2-show/{id}', [App\Http\Controllers\SSTV2Controller::class, 'sstShowV2'])->name('sst-v2.show');
            Route::get('getSSTMainListV2', [App\Http\Controllers\SSTV2Controller::class, 'getSSTMainListV2'])->name('sst-v2.main-list');
            Route::get('getInvoiceListV2', [App\Http\Controllers\SSTV2Controller::class, 'getInvoiceListV2'])->name('sst-v2.invoice-list');
            Route::get('getInvoiceAddListV2', [App\Http\Controllers\SSTV2Controller::class, 'getInvoiceAddListV2'])->name('sst-v2.invoice-add-list');
            Route::post('createNewSSTRecordV2', [App\Http\Controllers\SSTV2Controller::class, 'createNewSSTRecordV2']);
            Route::post('updateSSTV2/{id}', [App\Http\Controllers\SSTV2Controller::class, 'updateSSTV2']);
            Route::post('deleteSSTV2/{id}', [App\Http\Controllers\SSTV2Controller::class, 'deleteSSTV2']);
            Route::post('deleteSSTDetail', [App\Http\Controllers\SSTV2Controller::class, 'deleteSSTDetail']);
            Route::get('exportSSTV2Excel/{id}', [App\Http\Controllers\SSTV2Controller::class, 'exportSSTV2Excel']);
            Route::get('exportSSTV2ExcelDirect/{id}', [App\Http\Controllers\SSTV2Controller::class, 'exportSSTV2ExcelDirect']);
            Route::get('exportSSTV2PDF/{id}', [App\Http\Controllers\SSTV2Controller::class, 'exportSSTV2PDF']);

            
            Route::get('bank-ledger', [BankController::class, 'BankLedger'])->name('bank.ledger');
            Route::get('getBankLedgerList', [BankController::class, 'getBankLedgerList'])->name('bankLedgerList.list');
            Route::post('getBankLedger', [BankController::class, 'getBankLedger']);

            Route::get('client-ledger', [AccountController::class, 'clientLedger']);
            Route::post('getClientLedger', [AccountController::class, 'getClientLedger']);
            Route::post('exportClientLedger', [AccountController::class, 'exportClientLedger']);

            Route::get('office-account-ledger', [AccountController::class, 'officeAccountLedger']);
            Route::post('getOfficeAccountLedger', [AccountController::class, 'getOfficeAccountLedger']);
            Route::post('exportOfficeAccountLedger', [AccountController::class, 'exportOfficeAccountLedger']);
            Route::get('office-account-ledger-details/{bank_id}', [AccountController::class, 'officeAccountLedgerDetails'])->name('office-account-ledger-details');
            Route::post('getOfficeAccountLedgerDetails', [AccountController::class, 'getOfficeAccountLedgerDetails']);

            
            Route::resource('files',    'FileController');
            Route::get('getFileList', [FileController::class, 'getFileList'])->name('files.list');

            
            Route::post('updateReceiptDone', [FileController::class, 'updateReceiptDone']);
            Route::post('updateFileType', [FileController::class, 'updateFileType']);
            Route::post('saveInvoiceDate/{parameter1}', [CaseController::class, 'saveInvoiceDate']);
            Route::post('saveSSTRate/{parameter1}', [CaseController::class, 'saveSSTRate']);;

            
            Route::post('reopenCase/{parameter1}', [CaseController::class, 'reopenCase']);

            Route::get('journal-entry-list', [AccountController::class, 'journalEntryList'])->name('journal.list');;
            Route::get('journal-entry/{parameter1}', [AccountController::class, 'journalEntryView']);
            Route::get('journal-entry-create', [AccountController::class, 'journalEntryCreate']);
            Route::post('saveJournalEntry', [AccountController::class, 'saveJournalEntry']);
            Route::post('updateJournalEntry/{parameter1}', [AccountController::class, 'updateJournalEntry']);
            Route::post('deleteJournalEntry/{parameter1}', [AccountController::class, 'deleteJournalEntry']);
            Route::post('lockJournal/{parameter1}', [AccountController::class, 'lockJournal']);
            Route::post('unlockJournal/{parameter1}', [AccountController::class, 'unlockJournal']);
            Route::post('deleteJournal/{parameter1}', [AccountController::class, 'deleteJournal']);

            Route::get('getJournalEntryList', [AccountController::class, 'getJournalEntryList'])->name('journalEntrytMainList.list');

            Route::post('/getFileFromS3', function (Request $request) {
                $temporarySignedUrl = Storage::disk('Wasabi')->temporaryUrl($request->input('filename'), now()->addMinutes(30));
                return $temporarySignedUrl;
            });

            Route::post('deleteOperation', [AdjudicationController::class, 'deleteOperation']);
            Route::post('generateReceiptController/{parameter1}', [CaseController::class, 'generateReceiptController']);

            
            Route::post('sumStaffBonus', [BonusController::class, 'sumStaffBonus']);

            
            Route::post('submitClaimsRequest/{parameter1}', [CaseController::class, 'submitClaimsRequest']);
            Route::get('claims-request-list', [ClaimsController::class, 'ClaimsReqList']);
            Route::get('claimsRequest/list/', [ClaimsController::class, 'getClaimsList'])->name('claimsRequest.list');
            Route::get('claims-request-details/{parameter1}', [ClaimsController::class, 'ClaimsReviewDetails']);
            
            Route::post('rejectClaim/{parameter1}', [ClaimsController::class, 'rejectClaim']);
            Route::post('approveClaim/{parameter1}', [ClaimsController::class, 'approveClaim']);
            Route::post('editClaim/{parameter1}', [ClaimsController::class, 'editClaim']);
            Route::post('getClaimSum', [ClaimsController::class, 'getClaimSum']);

            
            Route::get('getTransferFeeBillList', [AccountController::class, 'getTransferFeeBillList'])->name('transferFeeBillList.list');
            Route::get('getStaffList', [UsersController::class, 'getStaffList'])->name('staffList.list');
            Route::get('reset-password/{parameter1}', [UsersController::class, 'resetUserPasswordView'])->name('resetpassword');
            Route::post('reset-password/{parameter1}', [UsersController::class, 'resetUserPassword'])->name('resetpassword');

            
            Route::post('uploadSafeKeepingFile', [SafeKeepingController::class, 'uploadSafeKeepingFile']);
            
            Route::post('operationUpload', [UploadController::class, 'operationUpload']);
            Route::post('storeSafeKeepingRecord', [SafeKeepingController::class, 'storeRecords']);
            Route::post('storeRecords', [OperationController::class, 'storeRecords']);
            Route::post('updateRecords/{parameter1}', [OperationController::class, 'updateRecords']);
            Route::post('deleteOperations/{parameter1}', [OperationController::class, 'deleteOperations']);
            Route::post('deleteOperationAttachment/{parameter1}', [OperationController::class, 'deleteOperationAttachment']);

            
            Route::post('updateQuotationBillTo/{parameter1}', [CaseController::class, 'updateQuotationBillTo']);
            Route::post('updateInvoiceTo/{parameter1}', [CaseController::class, 'updateInvoiceTo']);
            Route::post('changeClient/{parameter1}', [CaseController::class, 'changeClient']);
            Route::post('changeReferral/{parameter1}', [CaseController::class, 'changeReferral']);

            Route::post('updateCaseCountPerBranch', [CaseController::class, 'updateCaseCountPerBranch']);
            Route::post('CaseFileUpload', [UploadController::class, 'CaseFileUpload']);

            
            Route::post('loadMyQuotationTemplate/{parameter1}', [CaseController::class, 'loadMyQuotationTemplate']);

            Route::get('letter-head', [SettingsController::class, 'letterHeadList']);
            Route::get('letter-head-lawyer', [SettingsController::class, 'letterHeadLawyerList']);
            Route::post('letterHeadList', [SettingsController::class, 'letterHeadList']);
            Route::post('SaveLetterHead', [SettingsController::class, 'SaveLetterHead']);
            Route::post('updateLetterHead', [SettingsController::class, 'updateLetterHead']);
            Route::post('deleteLetterHead/{parameter1}', [SettingsController::class, 'deleteLetterHead']);

            Route::post('loadBillDisb', [CaseController::class, 'loadBillDisb']);
            Route::post('MoveBill', [CaseController::class, 'MoveBill']);

            Route::get('casesearch/list', [CaseController::class, 'getCaseListReq'])->name('casesearch.list');
            Route::get('billsearch/list', [CaseController::class, 'getBillListReq'])->name('billsearch.list');


            // E-invoice implementation
            
            Route::post('AddBilltoInvoice/{parameter1}', [EInvoiceContoller::class, 'AddBilltoInvoice']);
            Route::post('loadBillToInv/{parameter1}', [EInvoiceContoller::class, 'loadBillToInv']);
            Route::post('UpdateBillToInfo/{parameter1}', [EInvoiceContoller::class, 'updateBillToInfo']);
            Route::get('getSplitInvoiceDetails/{invoice_id}', [EInvoiceContoller::class, 'getSplitInvoiceDetails']);
            Route::post('updateSplitInvoiceDetail', [EInvoiceContoller::class, 'updateSplitInvoiceDetail']);
            Route::get('getInvoiceDate/{invoice_id}', [EInvoiceContoller::class, 'getInvoiceDate']);
            Route::post('updateInvoiceDate', [EInvoiceContoller::class, 'updateInvoiceDate']);
            Route::post('removeBillto/{parameter1}', [EInvoiceContoller::class, 'removeBillto']);
            Route::post('loadBillToInvWIthInvoice/{parameter1}', [EInvoiceContoller::class, 'loadBillToInvWIthInvoice']);

            

            Route::get('einvoice-client', [EInvoiceContoller::class, 'einvoice_billto'])->name('einvoice-client.index');
            Route::get('einvoice-client-list', [EInvoiceContoller::class, 'getEInvoiceClientList'])->name('einvoice-client-list');
            
            Route::get('einvoice-client-edit/{parameter1}', [EInvoiceContoller::class, 'einvoiceBilltoEdit']);

            
            Route::post('splitInvoice/{parameter1}', [EInvoiceContoller::class, 'splitInvoice']);
            Route::post('removeInvoice/{parameter1}', [EInvoiceContoller::class, 'removeInvoice']);
            Route::post('deleteEinvoiceMainRecords/{parameter1}', [EInvoiceContoller::class, 'deleteEinvoiceMainRecords']);
            
            // Route::post('createNewTranferFee', [AccountController::class, 'createNewTranferFee']);
            // Route::post('updateTranferFee/{parameter1}', [AccountController::class, 'updateTranferFee']);
            // Route::post('deleteTransferFee/{parameter1}', [AccountController::class, 'deleteTransferFee']);

            // Route::get('account-code', [SettingsController::class, 'AccountCodeSetting']);

            
            // Route::get("/main", 'Controller@main')->name("main");
            // Route::get('safe_keeping_list/list/', [SafeKeepingController::class, 'getSafeKeepingList'])->name('safe_keeping_list.list');

            
            
            
            // Route::get('/', 'DocTemplateController@show');
            // Route::get('document-template/test',       'DocTemplateController@test1');

            // Route::group(['prefix' => 'document-template'], function () {
            //     Route::resource('/', 'DocTemplateController');
            //     Route::post('/create', 'DocTemplateController@create');
            //     // Route::post('/preview', 'DesignController@storePreviewImage');
            //     //   Route::post('/logo', 'DesignController@storeLogo');
            //   });

            

            // Route::prefix('document-template')->group(function () {

            //     Route::get('/',             'MenuElementController@index')->name('menu.index');
            //     // Route::resource('/',             'DocTemplateController');
            //     Route::get('{id}', 'DocTemplateController@show');
            //     Route::get('/create',       'DocTemplateController@show');
            //     Route::get('{id}/edit', 'DocTemplateController@edit');
            //     // Route::get('{id}', 'DocTemplateController@show');       
            // Route::get('/test',       'DocTemplateController@test1'); 
            //     // Route::resource('document-template',    'DocTemplateController');
            // });
        });

        // Route::group(['middleware' => ['auth'], 'prefix' => 'account'], function () {
        //     Route::get('/', 'AccountController@index');
        //     Route::post('/profile', 'AccountController@updateProfile');
        //     Route::post('/password', 'AccountController@UpdatePassword');
          
        //     // My Address Book
        //     Route::resource('address-book', 'AddressController');
          
        //     // My Orders
        //     Route::get('orders', 'OrderController@index');
        //     Route::get('orders/{id}', 'OrderController@show');
        //   });

        


        // Route::prefix('users')->group(function () {
        //     // Route::resource('/',             'UsersController@index');
        //     Route::get('/filter',      'MenuElementController@filter');
        //     // Route::get('/move-down',    'MenuElementController@moveDown')->name('menu.down');
        //     // Route::get('/create',       'MenuElementController@create')->name('menu.create');
        //     // Route::post('/store',       'MenuElementController@store')->name('menu.store');
        //     // Route::get('/get-parents',  'MenuElementController@getParents');
        //     // Route::get('/edit',         'MenuElementController@edit')->name('menu.edit');
        //     // Route::post('/update',      'MenuElementController@update')->name('menu.update');
        //     // Route::get('/show',         'MenuElementController@show')->name('menu.show');
        //     // Route::get('/delete',       'MenuElementController@delete')->name('menu.delete');
        // });

        Route::resource('languages',    'LanguageController');
        Route::resource('mail',        'MailController');
        Route::get('prepareSend/{id}',        'MailController@prepareSend')->name('prepareSend');
        Route::post('mailSend/{id}',        'MailController@send')->name('mailSend');
        Route::resource('roles',        'RolesController');
        Route::get('/roles/move/move-up',      'RolesController@moveUp')->name('roles.up');
        Route::get('/roles/move/move-down',    'RolesController@moveDown')->name('roles.down');
        Route::prefix('menu/element')->group(function () {
            Route::get('/',             'MenuElementController@index')->name('menu.index');
            Route::get('/move-up',      'MenuElementController@moveUp')->name('menu.up');
            Route::get('/move-down',    'MenuElementController@moveDown')->name('menu.down');
            Route::get('/create',       'MenuElementController@create')->name('menu.create');
            Route::post('/store',       'MenuElementController@store')->name('menu.store');
            Route::get('/get-parents',  'MenuElementController@getParents');
            Route::get('/edit',         'MenuElementController@edit')->name('menu.edit');
            Route::post('/update',      'MenuElementController@update')->name('menu.update');
            Route::get('/show',         'MenuElementController@show')->name('menu.show');
            Route::get('/delete',       'MenuElementController@delete')->name('menu.delete');
        });
        Route::prefix('menu/menu')->group(function () {
            Route::get('/',         'MenuController@index')->name('menu.menu.index');
            Route::get('/create',   'MenuController@create')->name('menu.menu.create');
            Route::post('/store',   'MenuController@store')->name('menu.menu.store');
            Route::get('/edit',     'MenuController@edit')->name('menu.menu.edit');
            Route::post('/update',  'MenuController@update')->name('menu.menu.update');
            //Route::get('/show',     'MenuController@show')->name('menu.menu.show');
            Route::get('/delete',   'MenuController@delete')->name('menu.menu.delete');
        });
        Route::prefix('media')->group(function () {
            Route::get('/',                 'MediaController@index')->name('media.folder.index');
            Route::get('/folder/store',     'MediaController@folderAdd')->name('media.folder.add');
            Route::post('/folder/update',   'MediaController@folderUpdate')->name('media.folder.update');
            Route::get('/folder',           'MediaController@folder')->name('media.folder');
            Route::post('/folder/move',     'MediaController@folderMove')->name('media.folder.move');
            Route::post('/folder/delete',   'MediaController@folderDelete')->name('media.folder.delete');;

            Route::post('/file/store',      'MediaController@fileAdd')->name('media.file.add');
            Route::get('/file',             'MediaController@file');
            Route::post('/file/delete',      'MediaController@fileDelete')->name('media.file.delete');
            Route::post('/file/update',     'MediaController@fileUpdate')->name('media.file.update');
            Route::post('/file/move',       'MediaController@fileMove')->name('media.file.move');
            Route::post('/file/cropp',      'MediaController@cropp');
            Route::get('/file/copy',        'MediaController@fileCopy')->name('media.file.copy');
        });
    });


    // Route::group(['middleware' => ['role:lawyer']], function () {

    //     Route::resource('bread',  'BreadController');   //create BREAD (resource)

    //     Route::resource('users',        'UsersController');
    //     Route::resource('languages',    'LanguageController');
    //     Route::resource('mail',        'MailController');
    //     Route::get('prepareSend/{id}',        'MailController@prepareSend')->name('prepareSend');
    //     Route::post('mailSend/{id}',        'MailController@send')->name('mailSend');
    //     // Route::resource('roles',        'RolesController');
    //     // Route::get('/roles/move/move-up',      'RolesController@moveUp')->name('roles.up');
    //     // Route::get('/roles/move/move-down',    'RolesController@moveDown')->name('roles.down');
    //     Route::prefix('menu/element')->group(function () { 
    //         Route::get('/',             'MenuElementController@index')->name('menu.index');
    //         Route::get('/move-up',      'MenuElementController@moveUp')->name('menu.up');
    //         Route::get('/move-down',    'MenuElementController@moveDown')->name('menu.down');
    //         Route::get('/create',       'MenuElementController@create')->name('menu.create');
    //         Route::post('/store',       'MenuElementController@store')->name('menu.store');
    //         Route::get('/get-parents',  'MenuElementController@getParents');
    //         Route::get('/edit',         'MenuElementController@edit')->name('menu.edit');
    //         Route::post('/update',      'MenuElementController@update')->name('menu.update');
    //         Route::get('/show',         'MenuElementController@show')->name('menu.show');
    //         Route::get('/delete',       'MenuElementController@delete')->name('menu.delete');
    //     });
    //     Route::prefix('menu/menu')->group(function () { 
    //         Route::get('/',         'MenuController@index')->name('menu.menu.index');
    //         Route::get('/create',   'MenuController@create')->name('menu.menu.create');
    //         Route::post('/store',   'MenuController@store')->name('menu.menu.store');
    //         Route::get('/edit',     'MenuController@edit')->name('menu.menu.edit');
    //         Route::post('/update',  'MenuController@update')->name('menu.menu.update');
    //         //Route::get('/show',     'MenuController@show')->name('menu.menu.show');
    //         Route::get('/delete',   'MenuController@delete')->name('menu.menu.delete');
    //     });
    //     Route::prefix('media')->group(function () {
    //         Route::get('/',                 'MediaController@index')->name('media.folder.index');
    //         Route::get('/folder/store',     'MediaController@folderAdd')->name('media.folder.add');
    //         Route::post('/folder/update',   'MediaController@folderUpdate')->name('media.folder.update');
    //         Route::get('/folder',           'MediaController@folder')->name('media.folder');
    //         Route::post('/folder/move',     'MediaController@folderMove')->name('media.folder.move');
    //         Route::post('/folder/delete',   'MediaController@folderDelete')->name('media.folder.delete');;

    //         Route::post('/file/store',      'MediaController@fileAdd')->name('media.file.add');
    //         Route::get('/file',             'MediaController@file');
    //         Route::post('/file/delete',      'MediaController@fileDelete')->name('media.file.delete');
    //         Route::post('/file/update',     'MediaController@fileUpdate')->name('media.file.update');
    //         Route::post('/file/move',       'MediaController@fileMove')->name('media.file.move');
    //         Route::post('/file/cropp',      'MediaController@cropp');
    //         Route::get('/file/copy',        'MediaController@fileCopy')->name('media.file.copy');
    //     });
    //     });

    // Transfer Fee V2 Routes
    Route::prefix('transfer-fee-v2')->group(function () {
                    Route::get('/', [TransferFeeV2Controller::class, 'transferFeeListV2'])->name('transfer-fee-v2.index');
            Route::get('/simple', [TransferFeeV2Controller::class, 'transferFeeListSimpleV2'])->name('transfer-fee-v2.simple');
        Route::get('/create', [TransferFeeV2Controller::class, 'transferFeeCreateV2'])->name('transfer-fee-v2.create');
        Route::post('/store', [TransferFeeV2Controller::class, 'createNewTransferFeeV2'])->name('transfer-fee-v2.store');
        Route::get('/{id}', [TransferFeeV2Controller::class, 'transferFeeViewV2'])->name('transfer-fee-v2.show');
        Route::get('/{id}/edit', [TransferFeeV2Controller::class, 'transferFeeEditV2'])->name('transfer-fee-v2.edit');
        Route::put('/{id}', [TransferFeeV2Controller::class, 'transferFeeUpdateV2'])->name('transfer-fee-v2.update');
        Route::delete('/{id}', [TransferFeeV2Controller::class, 'transferFeeDeleteV2'])->name('transfer-fee-v2.destroy');
        
        // AJAX endpoints
        Route::get('/getTransferInvoiceListV2', [TransferFeeV2Controller::class, 'getTransferInvoiceListV2'])->name('transfer-fee-v2.invoice-list');
                    Route::get('/getTransferMainRecordsV2', [TransferFeeV2Controller::class, 'getTransferMainRecordsV2'])->name('transfer-fee-v2.main-records');
            Route::get('/getTransferRecordsSimpleV2', [TransferFeeV2Controller::class, 'getTransferRecordsSimpleV2'])->name('transfer-fee-v2.simple-records');
            Route::get('/create-test-record', [TransferFeeV2Controller::class, 'createTestRecordV2'])->name('transfer-fee-v2.create-test');
        });

        
        

        Route::get('/getTransferInvoiceListV3', [TransferFeeV3Controller::class, 'getTransferInvoiceListV3'])->name('transferfee.invoice-list');
        Route::get('/getCurrentInvoices/{id}', [TransferFeeV3Controller::class, 'getCurrentInvoices'])->name('transferfee.current-invoices');


        // Transfer Fee Routes
        Route::prefix('transferfee')->group(function () {
            Route::get('/', [TransferFeeV3Controller::class, 'transferFeeListV3'])->name('transferfee.index');
            Route::get('/create', [TransferFeeV3Controller::class, 'transferFeeCreateV3'])->name('transferfee.create');
            Route::post('/store', [TransferFeeV3Controller::class, 'createNewTransferFeeV3'])->name('transferfee.store');
            Route::get('/{id}', [TransferFeeV3Controller::class, 'transferFeeShowV3'])->name('transferfee.show');
    
                    Route::get('/{id}/edit', [TransferFeeV3Controller::class, 'transferFeeEditV3'])->name('transferfee.edit');
        Route::put('/{id}', [TransferFeeV3Controller::class, 'transferFeeUpdateV3'])->name('transferfee.update');
        Route::post('/update-total-amt/{detailId}', [TransferFeeV3Controller::class, 'updateTotalAmtV3'])->name('transferfee.updateTotalAmt');
        Route::delete('/{id}', [TransferFeeV3Controller::class, 'transferFeeDeleteV3'])->name('transferfee.destroy');
            Route::delete('/{id}/delete-detail', [TransferFeeV3Controller::class, 'deleteTransferFeeDetailV3'])->name('transferfee.delete-detail');
    Route::post('/{id}/reconcile', [TransferFeeV3Controller::class, 'reconTransferFeeV3'])->name('transferfee.reconcile');
    Route::post('/{id}/revert-recon', [TransferFeeV3Controller::class, 'revertReconTransferFeeV3'])->name('transferfee.revert-recon');
    Route::post('/export', [TransferFeeV3Controller::class, 'exportTransferFeeInvoices'])->name('transferfee.export');
    Route::post('/fix-transferred-amounts', [TransferFeeV3Controller::class, 'fixExistingTransferredAmounts'])->name('transferfee.fix-amounts');
            
                            // AJAX endpoints
                // Route::get('/getTransferInvoiceListV3', [TransferFeeV3Controller::class, 'getTransferInvoiceListV3'])->name('transferfee.invoice-list');


    });
});

Route::get('locale', 'LocaleController@locale');

Route::get('/einvoice/list', [EInvoiceContollerV2::class, 'getEInvoiceMainList'])->name('einvoice.list');
Route::post('/einvoice/delete', [EInvoiceContollerV2::class, 'deleteInvoices'])->name('einvoice.delete');
Route::get('/generate-sql-excel-template/{id}', [EInvoiceContollerV2::class, 'generateSQLExcelTemplate'])->name('generate.sql.excel.template');
Route::get('/generate-sql-customer-template/{id}', [EInvoiceContollerV2::class, 'generateSQLCustomerTemplate'])->name('generate.sql.customer.template');


Route::post('/einvoice-billto/update/{id}', [EInvoiceContoller::class, 'updateBillToInfo']);

Route::post('/einvoice-billto/generate-client-link/{id}', [EInvoiceContoller::class, 'generateClientLink']);

Route::post('/einvoice/send-to-sql', [EInvoiceContoller::class, 'sendInvoicesToSQL']);
Route::post('/einvoice/send-to-lhdn', [EInvoiceContoller::class, 'sendInvoicesToLHDN']);
Route::put('/einvoice/{id}', [EInvoiceContoller::class, 'updateEinvoice']);

// Data Repair Routes (protected)
Route::middleware(['auth'])->group(function () {
    Route::get('/data-repair', [DataRepairController::class, 'index'])->name('data-repair.index');
    Route::get('/data-repair/get-missing-entries', [DataRepairController::class, 'getMissingEntries'])->name('data-repair.get-missing-entries');
    Route::post('/data-repair/fix-single-entry', [DataRepairController::class, 'fixSingleEntry'])->name('data-repair.fix-single-entry');
    Route::post('/data-repair/fix-all-entries', [DataRepairController::class, 'fixAllEntries'])->name('data-repair.fix-all-entries');
    
    // Invoice Fix Routes (protected)
    Route::get('/invoice-fix', [InvoiceFixController::class, 'index'])->name('invoice-fix.index');
    Route::get('/account-tool', [InvoiceFixController::class, 'index'])->name('account-tool.index');
    Route::get('/invoice-fix/get-wrong-invoices', [InvoiceFixController::class, 'getWrongInvoices'])->name('invoice-fix.get-wrong-invoices');
    Route::post('/invoice-fix/fix-single', [InvoiceFixController::class, 'fixSingleInvoice'])->name('invoice-fix.fix-single');
    Route::post('/invoice-fix/fix-multiple', [InvoiceFixController::class, 'fixMultipleInvoices'])->name('invoice-fix.fix-multiple');
    Route::post('/invoice-fix/fix-all', [InvoiceFixController::class, 'fixAllInvoices'])->name('invoice-fix.fix-all');
    Route::get('/invoice-fix/search-bills', [InvoiceFixController::class, 'searchBills'])->name('invoice-fix.search-bills');
    Route::get('/invoice-fix/bill-details/{billId}', [InvoiceFixController::class, 'getBillDetailsForConversion'])->name('invoice-fix.bill-details');
    Route::post('/invoice-fix/convert-reimbursement', [InvoiceFixController::class, 'convertReimbursement'])->name('invoice-fix.convert-reimbursement');
});
