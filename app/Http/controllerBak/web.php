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

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AdjudicationController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\CaseArchieveController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\ChecklistItemsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DispatchController;
use App\Http\Controllers\DocTemplateFilev2Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QuotationGeneratorController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SummaryReportController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['get.menu']], function () {
    Route::get('/', function () {
        return view('auth.login');
    });

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

        Route::group(['middleware' => ['role:admin|management|lawyer|sales|account|clerk|receptionist|chambering']], function () {
            // Route::resource('todolist',    'TodoController');
            Route::resource('loan',    'CaseController');
            Route::resource('todolist',    'TodoController');
            Route::resource('mytodo',    'MyTodoController');
            Route::resource('case',    'CaseController');
            Route::resource('couriers',    'CourierController');
            Route::resource('dispatch',    'DispatchController');
            // Route::resource('voucher',    'BillController');
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
            Route::resource('account',    'AccountController');
            Route::resource('account-item',    'AccountItemController');
            Route::resource('account-template',    'AccountTemplateController');
            Route::resource('checklist-template',    'ChecklistTemplateController');
            Route::resource('checklist-item',    'ChecklistItemsController');
            Route::resource('masterlist',    'MasterlistController');
            Route::resource('referral',    'ReferralController');
            Route::resource('quotation',    'QuotationController');
            Route::resource('summary-report',    'SummaryReportController');

            
            Route::resource('email',    'EmailController');

            Route::post('update_page',        'DocTemplateController@updatePage');
            Route::post('update_lock',        'DocTemplateController@updateLockStatus');
            Route::post('check_lock',        'DocTemplateController@checkPageLocked');

            
            Route::post('update_checklist',        'CaseController@updateCheckList');
            Route::post('create_dispatch',        'CaseController@createDispatch');
            Route::post('upload_file',        'CaseController@UploadFile');
            Route::post('update_masterlist/{paramter1}',        'CaseController@updateMasterList');
            Route::post('request_voucher/{paramter1}',        'CaseController@requestVoucher');
            Route::post('receive_bill_payment/{paramter1}/{paramter2}',        'CaseController@receiveBillPayment');
            Route::post('update_voucher_status/{paramter1}',        'VoucherController@updateVoucherStatus');

            Route::get('document/{paramter1}/{paramter2}', 'CaseController@document');

            
            Route::resource('clients',    'CustomerController');
            Route::post('view_voucher/{paramter1}',        'AccountController@view');
            Route::post('update_voucher',        'AccountController@update');
            Route::post('trust_entry/{paramter1}',        'CaseController@trustEntry');
            Route::post('bill_entry',        'CaseController@billEntry');

            
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
            Route::resource('dashboard',        'DashboardController');
            Route::resource('users',        'UsersController');
            Route::post('filter',        'UsersController@filter');
            Route::resource('office-bank-account',        'OfficeBankAccountController');

            
            Route::post('load_case_template',        'CaseController@loadCaseTemplate');

            Route::post('gen_file',        'DocTemplateFileController@getFile');


            
            Route::post('set_file_active',        'DocTemplateFilev2Controller@setFileActive');
            Route::post('update_file_template_info/{paramter1}',        'DocTemplateFilev2Controller@updateFileTemplateInfo');
            Route::post('upload_file_template/{paramter1}',        'DocTemplateFilev2Controller@UploadFileTemplate');

            
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
            Route::get('voucher_main/list/', [VoucherController::class, 'getVoucherList'])->name('voucher.list');
            Route::get('ledger/list/{parameter1}', [CaseController::class, 'getLedger'])->name('ledger.list');
            Route::get('checklistdetails/list/', [ChecklistItemsController::class, 'getChecklist'])->name('checklistdetails.list');
            Route::post('delete_checklist/{parameter1}', [ChecklistItemsController::class, 'deleteChecklist']);
            Route::get('generate-pdf', [VoucherController::class, 'generatePDF']);

            
            Route::post('reorder_sequence_checklist_template', [ChecklistItemsController::class, 'reorderSequenceChecklistTemplate']);

            Route::post('upload_account_file/{parameter1}', [VoucherController::class, 'uploadAccountFile']);
            Route::post('generate_receipt/{parameter1}/{parameter2}', [CaseController::class, 'generateReceipt']);
            Route::post('get_trust_value/{parameter1}', [CaseController::class, 'getTrustValue']);
            Route::post('update_trust_value/{parameter1}', [CaseController::class, 'updateTrustValueV2']);
            Route::post('delete_receipt_file', [CaseController::class, 'deleteReceiptFile']);
            Route::post('update_loan_case_trust_main/{parameter1}', [CaseController::class, 'updateLoanCaseTrustMain']);
            Route::post('generate_trust_receipt/{parameter1}', [CaseController::class, 'generateTrustReceipt']);
            Route::post('requestTrustDisbusement/{parameter1}', [CaseController::class, 'requestTrustDisbusement']);
            Route::post('receiveTrustDisbusement/{parameter1}', [CaseController::class, 'receiveTrustDisbusement']);

            
            Route::post('create_folder', [DocTemplateFilev2Controller::class, 'createFolder']);
            Route::post('move_file_folder/{parameter1}', [DocTemplateFilev2Controller::class, 'moveFileFolder']);
            Route::post('retriveNotification', [NotificationController::class, 'retriveNotification']);
            Route::post('openNotification/{parameter1}', [NotificationController::class, 'openNotification']);
            Route::post('generateBillReceipt/{parameter1}/{parameter2}', [CaseController::class, 'generateBillReceipt']);
            Route::post('get_bill_receive_value/{parameter1}', [CaseController::class, 'getBillReceive']);
            Route::post('update_bill_receove_value/{parameter1}/{parameter2}', [CaseController::class, 'updateBillReceiveValue']);
            Route::post('updateBillSummary/{parameter1}', [CaseController::class, 'updateBillSummary']);

            
            Route::post('resubmitVoucher/{parameter1}', [VoucherController::class, 'resubmitVoucher']);
            Route::post('updateVoucherValue', [VoucherController::class, 'updateVoucherValue']);

            Route::post('deleteUploadedFile/{parameter1}/{parameter2}', [DocTemplateFilev2Controller::class, 'deleteUploadedFile']);
            Route::post('submitNotes/{parameter1}', [CaseController::class, 'submitNotes']);

            Route::post('uploadMarketingBill', [CaseController::class, 'uploadMarketingBill']);
            Route::post('deleteMarketingBill/{parameter1}', [CaseController::class, 'deleteMarketingBill']);

            
            Route::get('voucher-report', [SummaryReportController::class, 'voucherReport']);
            
            Route::get('ledger', [AccountController::class, 'bankRecon']);
            
            // Route::resource('ledger',    'AccountController');
            Route::post('update_bill_print_details/{parameter1}', [CaseController::class, 'updateBillPrintDetail']);

            
            Route::post('generateBillLumdReceipt/{parameter1}/{parameter2}', [CaseController::class, 'generateBillLumdReceipt']);
            
            Route::get('voucher-archive', [VoucherController::class, 'voucherArchieve']);
            Route::get('quotation-generator', [QuotationGeneratorController::class, 'quotationGenerator']);
            Route::post('load_quotation_template_generator/{parameter1}', [QuotationGeneratorController::class, 'loadQuotationTemplateGenerator']);
            Route::post('logPrintedQuotation', [QuotationGeneratorController::class, 'logPrintedQuotation']);
            Route::post('convertToInvoice/{parameter1}', [CaseController::class, 'convertQuotationToInvoice']);

            
            Route::post('update_quotation_bill_by_admin', [CaseController::class, 'updateQuotationBillByAdmin']);
            Route::post('SaveSummaryInfo/{parameter1}', [CaseController::class, 'SaveSummaryInfo']);
            Route::post('updateQuotationValue', [CaseController::class, 'updateQuotationValue']);
            Route::post('addQuotationItem/{parameter1}', [CaseController::class, 'addQuotationItem']);
            Route::post('deleteQuotationItem/{parameter1}', [CaseController::class, 'deleteQuotationItem']);
            // Route::post('adminUpdateValue', [CaseController::class, 'updateBillSummaryAllByAdmin']);   
            Route::post('adminUpdateValue', [CaseController::class, 'adminUpdateValue']);   
            Route::post('saveQuotationTemplate', [QuotationGeneratorController::class, 'saveQuotationTemplate']);

            
            Route::post('updateCheckListBulk/{parameter1}', [CaseController::class, 'updateCheckListBulk']);
            Route::post('deleteNotes/{parameter1}', [CaseController::class, 'deleteNotes']);

            
            Route::post('updateAllcheckListDate', [CaseController::class, 'updateAllcheckListDate']);
            Route::post('loadQuotationToInvoice/{parameter1}', [CaseController::class, 'loadQuotationToInvoice']);
            Route::post('updateInvoiceValue', [CaseController::class, 'updateInvoiceValue']);
            Route::post('addInvoiceItem/{parameter1}', [CaseController::class, 'addInvoiceItem']);
            Route::post('deleteInvoiceItem/{parameter1}', [CaseController::class, 'deleteInvoiceItem']);
            Route::post('convertToSST/{parameter1}', [CaseController::class, 'convertToSST']);
            Route::post('deleteBill/{parameter1}', [CaseController::class, 'deleteBill']);
            
            // Route::get('referral/list/', [ReferralController::class, 'getReferralList'])->name('referral.list');


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
            Route::get('dispatch_list/list/', [DispatchController::class, 'getDispatchList'])->name('dispatch_list.list');

            
            Route::get('report-invoice', [ReportController::class, 'reportInvoice']);
            Route::get('bank-recon', [AccountController::class, 'bankRecon']);

            
            Route::resource('cases-archieve',    'CaseArchieveController');
            // Route::get('case_archieve_list/list/{parameter1}', [CaseArchieveController::class, 'getCaseList'])->name('case_archieve.list');
            Route::get('case_archieve_list/list/{parameter1}/{parameter2}', [CaseArchieveController::class, 'getOpenCaseList'])->name('case_archieve.list');
            Route::get('case_archieve_closed_list/list/{parameter1}/{parameter2}', [CaseArchieveController::class, 'getClosedCaseList'])->name('case_archieve_closed.list');
            // Route::get('case_archieve_closed_list/list/{parameter1/{parameter2}', [CaseArchieveController::class, 'getClosedCaseList'])->name('case_archieve_closed.list');
            Route::post('updateArchieveCaseRemark/{parameter1}', [CaseArchieveController::class, 'updateArchieveCaseRemark']);
            Route::post('TransferCase/{parameter1}', [CaseArchieveController::class, 'TransferCase']);
            Route::post('closeArchieveCase/{parameter1}', [CaseArchieveController::class, 'closeArchieveCase']);
            Route::post('updateArchieveCaseCompletionDate/{parameter1}', [CaseArchieveController::class, 'updateArchieveCaseCompletionDate']);
            Route::get('closed-2022-list', [CaseArchieveController::class, 'closedList']);

            
            
            
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
    // });
});

Route::get('locale', 'LocaleController@locale');
