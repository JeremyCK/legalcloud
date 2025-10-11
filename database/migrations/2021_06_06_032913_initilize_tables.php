    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    class InitilizeTables extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {

            //to avoid conflict
            Schema::dropIfExists('case_email_schedule');
            Schema::dropIfExists('case_email_sent_log');
            Schema::dropIfExists('handle_group');
            Schema::dropIfExists('parameter');
            Schema::dropIfExists('client');
            Schema::dropIfExists('referral');
            Schema::dropIfExists('voucher');
            Schema::dropIfExists('voucher_main');
            Schema::dropIfExists('voucher_details');
            Schema::dropIfExists('activity_log');
            Schema::dropIfExists('transaction');
            Schema::dropIfExists('case_account_transaction');
            Schema::dropIfExists('loan_case_files');
            Schema::dropIfExists('loan_case_trust');
            Schema::dropIfExists('loan_case_account');
            Schema::dropIfExists('loan_case_dispatch');
            Schema::dropIfExists('loan_case_document_version');
            Schema::dropIfExists('loan_case_document_page');
            Schema::dropIfExists('loan_case_masterlist');
            Schema::dropIfExists('loan_attachment');
            Schema::dropIfExists('loan_case_notes');
            Schema::dropIfExists('loan_case_checklist');
            Schema::dropIfExists('loan_case_checklist_details');
            Schema::dropIfExists('loan_case_checklist_main');
            Schema::dropIfExists('loan_case');
            Schema::dropIfExists('customer_case_expense');
            Schema::dropIfExists('internal_case_expense');
            Schema::dropIfExists('case_todo');
            Schema::dropIfExists('user_kpi_history');
            Schema::dropIfExists('checklist_template_main_step_rel');
            Schema::dropIfExists('checklist_template_categories');
            Schema::dropIfExists('checklist_template_item');
            Schema::dropIfExists('checklist_template_steps');
            Schema::dropIfExists('checklist_template_main');
            Schema::dropIfExists('case_checklist_template_details');
            Schema::dropIfExists('case_checklist_template_main');
            Schema::dropIfExists('checklist_case_category');
            Schema::dropIfExists('case_masterlist_field');
            Schema::dropIfExists('case_masterlist_value');
            Schema::dropIfExists('case_masterlist_field_category');
            Schema::dropIfExists('document_template_pages');
            Schema::dropIfExists('document_template_details');
            Schema::dropIfExists('document_template_main');
            Schema::dropIfExists('document_template_file_details');
            Schema::dropIfExists('document_template_file_main');
            Schema::dropIfExists('document_template_file');
            Schema::dropIfExists('email_template_details');
            Schema::dropIfExists('email_template_main');
            Schema::dropIfExists('account_category');
            Schema::dropIfExists('group_portfolio');
            Schema::dropIfExists('bank_user_rel');
            Schema::dropIfExists('portfolio');
            Schema::dropIfExists('banks');
            Schema::dropIfExists('courier');
            Schema::dropIfExists('team_portfolio');
            Schema::dropIfExists('team_member');
            Schema::dropIfExists('team_main');
            Schema::dropIfExists('team_members');
            Schema::dropIfExists('member_portfolio');
            Schema::dropIfExists('teams');
            Schema::dropIfExists('audit_log');
            Schema::dropIfExists('account_template_details');
            Schema::dropIfExists('account_template_main');
            Schema::dropIfExists('loan_case_bill_main');
            Schema::dropIfExists('loan_case_bill_details');
            Schema::dropIfExists('quotation_template_details');
            Schema::dropIfExists('quotation_template_main');
            Schema::dropIfExists('account_item');
            Schema::dropIfExists('account');
            Schema::dropIfExists('account_main_cat');
            Schema::dropIfExists('case_type');



            // this group is assign to process the loan case
            // if assign group didn't response in set time, system will reassign to new group
            Schema::create('handle_group', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('group_name');
                $table->integer('laywer_id')->unsigned()->nullable();
                $table->integer('clerk_id')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('parameter', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('parameter_type');
                $table->string('parameter_value_1');
                $table->string('parameter_value_2');
                $table->string('parameter_value_3')->nullable(); // label
                $table->string('parameter_value_4')->nullable();
                $table->string('status');
                $table->timestamps();
            });


            Schema::create('case_type', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->text('name');
                $table->integer('is_bank_required');
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('client', function (Blueprint $table) {
                $table->bigIncrements('id');
                // $table->string('case_ref_no');

                // 1 case 1 account & 1 customer
                $table->string('account_no')->nullable();
                $table->string('name');
                $table->string('ic_no')->nullable();
                $table->string('company_ref_no')->nullable();
                $table->integer('client_type')->nullable();
                $table->string('passport_no')->nullable();
                $table->string('race')->nullable();
                $table->string('phone_no');
                $table->string('email');
                $table->string('gender')->nullable();
                $table->string('income_tax_no')->nullable();

                $table->string('address')->nullable();
                $table->string('mailing_state')->nullable();
                $table->string('mailing_postcode')->nullable();
                $table->string('mailing_country')->nullable();

                $table->string('billing_address')->nullable();
                $table->string('billing_state')->nullable();
                $table->string('billing_postcode')->nullable();
                $table->string('billing_country')->nullable();
                $table->integer('case_count')->nullable();

                //additional field for add in

                $table->string('status');
                $table->timestamps();
            });

            Schema::create('referral', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('phone_no')->nullable();
                $table->string('email')->nullable();
                $table->string('race')->nullable();
                $table->string('ic_no')->nullable();
                $table->integer('referral_count')->default(0);
                $table->string('company')->nullable();
                $table->string('gender')->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('case_ref_no');
                $table->integer('customer_id')->unsigned()->nullable();
                $table->string('property_address');
                $table->integer('referral_id')->unsigned()->nullable();
                $table->string('referral_name')->nullable();
                $table->string('referral_phone_no')->nullable();
                $table->string('referral_email')->nullable();
                $table->decimal('purchase_price', 20, 2)->default(0);
                $table->decimal('loan_sum', 20, 2)->default(0);
                $table->decimal('targeted_bill', 20, 2)->default(0);
                $table->decimal('collected_bill', 20, 2)->default(0);
                $table->decimal('total_bill', 20, 2)->default(0);
                $table->decimal('targeted_trust', 20, 2)->default(0);
                $table->decimal('collected_trust', 20, 2)->default(0);
                $table->decimal('total_trust', 20, 2)->default(0);
                $table->string('remark')->nullable();

                $table->timestamp('target_close_date')->nullable();
                $table->timestamp('case_accept_date')->nullable();
                $table->decimal('percentage', 5, 2)->default(0);
                $table->boolean('is_handle')->nullable();
                $table->timestamp('handle_expired')->nullable();

                $table->integer('sales_user_id')->unsigned()->nullable();
                $table->integer('handle_group_id')->unsigned()->nullable();
                $table->integer('bill_ready')->default(0);

                //idea of not handle pair work by group assign
                $table->integer('bank_id')->unsigned()->nullable();
                $table->integer('lawyer_id')->unsigned()->nullable();
                $table->integer('account_id')->default(0);
                $table->integer('clerk_id')->unsigned()->nullable();
                $table->integer('runner_user_id')->unsigned()->nullable();
                $table->integer('case_type_id')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case_files', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->string('name');
                $table->string('path');
                $table->string('type')->nullable();;
                $table->string('remarks')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case_checklist_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->string('name');
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case_checklist_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->bigInteger('loan_case_main_id')->unsigned()->nullable();
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('order');
                $table->string('roles')->nullable();
                $table->decimal('kpi', 5, 2);
                $table->decimal('days', 5, 2);
                $table->bigInteger('start');
                $table->decimal('duration', 5, 2);

                $table->string('pic_id')->nullable();

                $table->integer('need_attachment')->default(0);
                $table->bigInteger('auto_dispatch')->default(0);
                $table->bigInteger('auto_receipt')->default(0);
                $table->integer('open_case')->default(0);
                $table->integer('close_case')->default(0);
                $table->bigInteger('email_template_id')->default(0);
                $table->integer('email_recurring')->default(0);
                $table->timestamp('target_close_date')->nullable();
                $table->text('remark')->nullable();

                $table->string('status');
                $table->timestamps();

                $table->foreign('case_id')->references('id')->on('loan_case');
                // $table->foreign('loan_case_main_id')->references('id')->on('loan_case_checklist_main');
            });

            Schema::create('loan_case_checklist', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();

                //? Can use id for sequence, but just incase in future need to sort or reorder checklist
                $table->integer('process_number');
                $table->string('checklist_name');

                $table->timestamp('target_date')->nullable();
                $table->timestamp('target_close_date')->nullable();
                $table->timestamp('completion_date')->nullable();

                $table->integer('need_attachment');
                $table->string('bln_gen_doc')->default(0);
                
                $table->bigInteger('auto_dispatch')->default(0);

                //! Can be zero, the check point
                $table->integer('check_point');
                $table->decimal('kpi', 5, 2);

                $table->string('status');
                $table->integer('is_checkbox');
                $table->text('remarks')->nullable();

                //additional field for add in
                $table->bigInteger('email_template_id')->unsigned()->default(0);
                $table->integer('email_recurring')->default(0);

                $table->integer('sales_user_id')->unsigned()->nullable();
                $table->integer('handle_group_id')->unsigned()->nullable();
                $table->integer('runner_user_id')->unsigned()->nullable();
                $table->string('role');

                $table->timestamps();
                $table->foreign('case_id')->references('id')->on('loan_case');

                // -> cannot use here as it might have multiple attachment
                //$table->foreign('attachment_id')->references('id')->on('attachments');
            });

            Schema::create('loan_case_notes', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->text('notes');
                $table->string('label');

                $table->timestamps();
                $table->foreign('case_id')->references('id')->on('loan_case');

            });

            Schema::create('loan_case_masterlist', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('masterlist_field_id')->unsigned();
                $table->text('value');

                $table->timestamps();
            });

            Schema::create('loan_case_document_version', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('checklist_id')->unsigned();
                $table->string('version_name');
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case_document_page', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('loancase_document_version_id')->unsigned();
                $table->integer('page');
                $table->longText('content');
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case_account', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->string('item_code')->nullable();
                $table->string('item_name');
                $table->bigInteger('account_details_id')->unsigned()->nullable();
                $table->integer('account_cat_id')->unsigned()->nullable();
                $table->decimal('amount', 18, 2);
                $table->integer('need_approval')->default(0);
                $table->string('formula')->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case_bill_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->string('bill_no')->nullable();
                $table->string('name');
                $table->decimal('total_amt', 20, 2)->default(0);
                $table->decimal('collected_amt', 20, 2)->default(0);
                $table->decimal('used_amt', 20, 2)->default(0);
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_case_bill_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('loan_case_main_bill_id')->unsigned()->nullable();
                $table->integer('account_item_id')->unsigned()->nullable();
                $table->decimal('min', 18, 2)->default(0);
                $table->decimal('max', 18, 2)->default(0);
                $table->integer('need_approval')->default(0);
                $table->decimal('amount', 18, 2)->default(0);
                $table->string('remark')->nullable();
                $table->integer('approved_by')->unsigned()->nullable();
                $table->integer('created_by')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });
            

            Schema::create('loan_case_dispatch', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('courier_id')->unsigned();
                $table->string('package_name');
                $table->string('departure_address');
                $table->string('destination_address');
                $table->string('departure_time')->nullable();
                $table->string('delivered_time')->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            // Schema::create('loan_case_customer_account', function (Blueprint $table) {
            //     $table->bigIncrements('id');
            //     $table->bigInteger('case_id')->unsigned();
            //     $table->integer('payment_type')->nullable();
            //     $table->string('voucher_detail_id')->unsigned()->nullable();
            //     $table->string('transaction_type')->nullable();
            //     $table->decimal('amount', 18, 2);
            //     $table->string('remark')->nullable();
            //     $table->string('status')->nullable();
            //     $table->timestamps();
            // });

            Schema::create('loan_case_trust', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->integer('payment_type')->nullable();
                $table->integer('movement_type')->nullable();
                $table->string('transaction_type')->nullable();
                $table->string('voucher_no')->nullable();
                $table->string('cheque_no')->nullable();
                $table->bigInteger('bank_id')->nullable();
                $table->string('bank_account')->nullable();
                $table->date('payment_date')->nullable();
                $table->string('item_code')->nullable();
                $table->string('item_name')->nullable();
                $table->integer('account_cat_id')->unsigned()->nullable();
                $table->decimal('amount', 18, 2);
                $table->string('formula')->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('loan_attachment', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('checklist_id')->unsigned();
                $table->string('case_ref_no')->nullable();
                $table->string('display_name');
                $table->string('filename');
                $table->string('type');
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();

                $table->foreign('case_id')->references('id')->on('loan_case');
                // $table->foreign('checklist_id')->references('id')->on('loan_case_checklist');
            });

            Schema::create('account_main_cat', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code');
                $table->string('category');
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('quotation_template_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('quotation_template_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('acc_main_template_id')->unsigned()->nullable();
                $table->integer('account_item_id')->unsigned()->nullable();
                $table->decimal('min', 18, 2)->default(0);
                $table->decimal('max', 18, 2)->default(0);
                $table->integer('need_approval')->default(0);
                $table->decimal('amount', 18, 2)->default(0);
                $table->string('formula')->nullable();
                $table->string('remark')->nullable();
                $table->integer('approved_by')->unsigned()->nullable();
                $table->integer('created_by')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('account_item', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('account_code')->nullable();
                $table->string('name');
                $table->integer('account_cat_id')->unsigned()->nullable();
                $table->integer('need_approval')->default(0);
                $table->decimal('amount', 18, 2)->default(0);
                $table->decimal('min', 18, 2)->default(0);
                $table->decimal('max', 18, 2)->default(0);
                $table->string('formula')->nullable();
                $table->string('remark')->nullable();
                $table->integer('approved_by')->unsigned()->nullable();
                $table->integer('created_by')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('account_template_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });


            // temporary use others voucher,main and details
            Schema::create('voucher', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('case_id')->unsigned();
                $table->integer('payment_type');
                $table->string('cheque_no')->nullable();
                $table->bigInteger('account_details_id')->unsigned()->nullable();
                $table->decimal('amount', 18, 2);
                $table->bigInteger('approval_id')->unsigned()->default(0);
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('voucher_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('case_id')->unsigned();
                $table->integer('payment_type');
                $table->string('voucher_no')->nullable();
                $table->string('cheque_no')->nullable();
                $table->string('credit_card_no')->nullable();
                $table->bigInteger('bank_id')->nullable();
                $table->string('bank_account')->nullable();
                $table->date('payment_date');
                $table->decimal('total_amount', 18, 2);
                $table->bigInteger('approval_id')->unsigned()->default(0);
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('voucher_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('voucher_main_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('account_details_id')->unsigned()->nullable();
                $table->integer('payment_type');
                $table->string('voucher_no')->nullable();
                $table->string('cheque_no')->nullable();
                $table->string('credit_card_no')->nullable();
                $table->bigInteger('bank_id')->nullable();
                $table->string('bank_account')->nullable();
                $table->string('transaction_id')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_display_name')->nullable();
                $table->bigInteger('approval_id')->unsigned()->default(0);
                $table->decimal('amount', 18, 2);
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('case_account_transaction', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('account_details_id')->unsigned()->nullable();
                $table->decimal('debit', 18, 2);
                $table->decimal('credit', 18, 2);
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('account_template_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('acc_main_template_id')->unsigned()->nullable();
                $table->string('item_code')->nullable();
                $table->string('item_name');
                $table->integer('account_cat_id')->unsigned()->nullable();
                $table->integer('need_approval')->default(0);
                $table->decimal('amount', 18, 2);
                $table->string('formula')->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('account_category', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code');
                $table->string('type')->nullable(); //debit or credit
                $table->string('category');
                $table->integer('taxable');
                $table->decimal('percentage', 18, 2);
                $table->string('account_no')->nullable();
                $table->bigInteger('ref_acc_cat_id')->unsigned()->nullable(); //refer to parent account category
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('account', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('account_category_id')->unsigned()->nullable();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('type')->nullable(); //debit or credit
                $table->integer('taxable')->default(0);
                $table->integer('approval')->default(0);
                $table->decimal('percentage', 18, 2)->nullable();
                $table->string('remark')->nullable();
                $table->string('status')->default(1);
                $table->timestamps();
            });

            Schema::create('transaction', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('transaction_id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('account_details_id')->unsigned()->nullable();
                $table->string('transaction_type');
                $table->decimal('amount', 18, 2);
                $table->string('cheque_no')->nullable();
                $table->bigInteger('bank_id')->unsigned()->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });



            Schema::create('customer_case_expense', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('case_ref_no');
                $table->string('expense_name');
                $table->string('expense_type');

                //additional field for add in
                $table->integer('acc_cat_id')->unsigned()->nullable();

                $table->decimal('amount', 18, 2);
                $table->string('remark');
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('internal_case_expense', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('ref_no');
                $table->string('expense_name');
                $table->string('expense_type');

                //additional field for add in
                $table->integer('acc_cat_id')->unsigned()->nullable();

                $table->decimal('amount', 18, 2);
                $table->string('remark');

                $table->integer('user_id')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });



            Schema::create('case_todo', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('type');
                $table->bigInteger('ref_id')->nullable();
                $table->bigInteger('case_id')->unsigned();

                //additional field for add in

                $table->timestamp('start_dttm')->nullable();
                $table->timestamp('expired_dttm')->nullable();
                $table->text('remark')->nullable();
                $table->string('request_user_id')->nullable();
                $table->string('approval_user_id')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('user_kpi_history', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('type');
                $table->bigInteger('case_id')->unsigned()->nullable();

                //additional field for add in
                $table->integer('point');
                $table->string('user_id');
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('case_checklist_template_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('display_name');
                $table->string('type');
                $table->integer('target_close_day')->nullable();
                $table->string('status');
                $table->timestamps();
            });


            // Revise checklist format base on user latest feedbacks
            Schema::create('checklist_template_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->integer('checklist_category_id');
                $table->integer('consent_type');
                $table->integer('encumbrances_type');
                $table->integer('target_close_day')->nullable();
                $table->text('remarks')->nullable();
                $table->string('status');
                $table->timestamps();
            });


            Schema::create('checklist_template_main_step_rel', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('template_main_id')->unsigned()->nullable();
                $table->bigInteger('checklist_step_id')->unsigned()->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('checklist_case_category', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('remarks')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('checklist_template_steps', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->bigInteger('category_id')->unsigned()->nullable();
                $table->string('From')->default('')->nullable();
                $table->text('remarks')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('checklist_template_item', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->bigInteger('step_id')->unsigned()->nullable();
                $table->bigInteger('order');
                $table->string('roles')->nullable();
                $table->decimal('kpi', 5, 2)->default(0);
                $table->decimal('days', 5, 2);
                $table->bigInteger('start')->default(0);
                $table->decimal('duration', 5, 2)->default(0);

                $table->integer('need_attachment')->default(0);
                $table->bigInteger('auto_dispatch')->default(0);
                $table->bigInteger('auto_receipt')->default(0);
                $table->integer('close_case')->default(0);
                $table->integer('open_case')->default(0);
                $table->bigInteger('email_template_id')->default(0);
                $table->integer('email_recurring')->default(0);
                $table->text('remark')->nullable();

                $table->string('status')->default(1);
                $table->timestamps();

                $table->foreign('step_id')->references('id')->on('checklist_template_steps');
            });

            Schema::create('checklist_template_categories', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name')->nullable();
                $table->string('code');
                $table->string('status');
                $table->text('remarks')->nullable();
                $table->timestamps();
            });

            Schema::create('banks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('short_code');
                $table->string('tel_no')->nullable();
                $table->string('fax')->nullable();
                $table->string('address')->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('portfolio', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('short_code');
                $table->integer('category')->nullable();
                $table->string('tel_no')->nullable();
                $table->string('fax')->nullable();
                $table->string('address')->nullable();
                $table->string('remark')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('bank_user_rel', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('bank_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('group_portfolio', function (Blueprint $table) {
                $table->bigIncrements('id');

                // case type and bank that handle by the group
                $table->bigInteger('portfolio_id')->unsigned();
                $table->bigInteger('group_id')->unsigned();
                $table->string('type');
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('case_checklist_template_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('role_id')->unsigned()->nullable();
                $table->bigInteger('template_main_id')->unsigned()->nullable();

            //? Can use id for sequence, but just incase in future need to sort or reorder checklist
                $table->integer('process_number')->default(0);
                $table->string('checklist_name');
                $table->decimal('kpi', 5, 2);
                $table->bigInteger('duration_base_item');
                $table->decimal('duration', 5, 2);

                $table->integer('need_attachment')->default(0);
                $table->string('remark')->nullable();
                $table->string('system_code')->nullable();
                $table->bigInteger('email_template_id')->default(0);
                $table->integer('email_recurring')->default(0);
                $table->bigInteger('auto_dispatch')->default(0);
                $table->string('bln_gen_doc')->default(0);

            //! Can be zero, the check point
                $table->integer('check_point')->default(0);
            
                $table->string('status');
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('roles');
                $table->foreign('template_main_id')->references('id')->on('case_checklist_template_main');
            });

            Schema::create('case_masterlist_field_category', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('code');
                $table->string('name');
                $table->integer('order')->default(0);
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('case_masterlist_field', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_field_id')->unsigned();
                $table->string('code');
                $table->string('name');
                $table->string('type');
                $table->string('status');
                $table->timestamps();

                $table->foreign('case_field_id')->references('id')->on('case_masterlist_field_category');
            });

            Schema::create('case_masterlist_value', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_field_id')->unsigned();
                $table->string('code');
                $table->string('name');
                $table->string('status');
                $table->timestamps();

                $table->foreign('case_field_id')->references('id')->on('case_masterlist_field_category');
            });

            Schema::create('email_template_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('desc')->nullable();
                $table->string('subject');
                $table->string('to')->nullable();
                $table->string('from')->nullable();
                $table->string('cc')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('email_template_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('email_template_id')->unsigned();
                $table->string('version_name');
                $table->text('content');
                $table->string('status');
                $table->timestamps();

                $table->foreign('email_template_id')->references('id')->on('email_template_main');

            });

            Schema::create('document_template_file', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('path');
                $table->string('type')->nullable();;
                $table->string('remarks')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('document_template_file_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('type')->nullable();;
                $table->string('remarks')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('document_template_file_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('document_template_file_main_id')->unsigned();
                $table->string('file_name');
                $table->string('ori_file_name');
                $table->string('path')->nullable();
                $table->string('type')->nullable();;
                $table->string('remarks')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('document_template_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('code')->nullable();;
                $table->string('desc')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('document_template_details', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('document_template_main_id')->unsigned();
                $table->string('version_name');
                $table->string('status');
                $table->timestamps();

                $table->foreign('document_template_main_id')->references('id')->on('document_template_main');
            });

            Schema::create('document_template_pages', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('document_template_details_id')->unsigned();
                $table->integer('page');
                $table->longText('content');
                $table->integer('is_locked')->default(0);
                $table->string('status');
                $table->timestamps();

                $table->foreign('document_template_details_id')->references('id')->on('document_template_details');
            });

            Schema::create('teams', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('desc')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('team_members', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('team_main_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
                $table->string('leader')->default(0);
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('member_portfolio', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('portfolio_id')->unsigned();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('team_main', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('desc')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('team_member', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('team_main_id')->unsigned();
                $table->bigInteger('user_id')->unsigned();
                $table->string('status');
                $table->timestamps();

                $table->foreign('team_main_id')->references('id')->on('team_main');

            });

            Schema::create('team_portfolio', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('team_main_id')->unsigned();
                $table->bigInteger('portfolio_id')->unsigned();
                $table->string('status');
                $table->timestamps();

                $table->foreign('team_main_id')->references('id')->on('team_main');

            });

            Schema::create('courier', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('short_code');
                $table->string('desc')->nullable(); 
                $table->string('tel_no')->nullable();
                $table->string('fax')->nullable();
                $table->string('address')->nullable();
                $table->string('status');
                $table->timestamps();
            });

            Schema::create('audit_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned();
                $table->string('model')->nullable();
                $table->string('desc')->nullable();
                $table->string('status');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users');

            });

            Schema::create('activity_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('user_id')->unsigned();
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('checklist_id')->unsigned();
                $table->string('action')->nullable(); 
                $table->string('desc')->nullable(); 
                $table->string('status');
                $table->timestamps();
    
                $table->foreign('user_id')->references('id')->on('users');
            });

            // user add columns
            Schema::table('users', function($table) {
                $table->string('nick_name',11)->nullable();
                $table->string('phone_no')->nullable();
                $table->string('office_no')->nullable();
                $table->string('portfolio')->nullable();
                $table->integer('min_files')->nullable();
                $table->integer('max_files')->nullable();
                $table->integer('race')->nullable();
                $table->integer('kpi')->nullable()->default(0);
                $table->decimal('commission', 7, 2)->default(0);
                $table->string('status')->nullable();
            });

            // user add columns
            Schema::table('roles', function($table) {
                $table->string('status')->default(0);
            });

            Schema::create('case_email_schedule', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('email_template_id')->unsigned();
                $table->date('effective_date');
                $table->timestamps();

                $table->foreign('case_id')->references('id')->on('loan_case');
                $table->foreign('email_template_id')->references('id')->on('email_template_main');

            });

            Schema::create('case_email_sent_log', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('case_id')->unsigned();
                $table->bigInteger('email_template_details_id')->unsigned();
                $table->longText('header_info');
                $table->longText('content');
                $table->timestamps();

                $table->foreign('case_id')->references('id')->on('loan_case');
                $table->foreign('email_template_details_id')->references('id')->on('email_template_details');
            });

        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            //drop if rollback
            Schema::dropIfExists('case_email_schedule');
            Schema::dropIfExists('case_email_sent_log');
            Schema::dropIfExists('handle_group');
            Schema::dropIfExists('parameter');
            Schema::dropIfExists('client');
            Schema::dropIfExists('referral');
            Schema::dropIfExists('voucher');
            Schema::dropIfExists('voucher_main');
            Schema::dropIfExists('voucher_details');
            Schema::dropIfExists('activity_log');
            Schema::dropIfExists('transaction');
            Schema::dropIfExists('case_account_transaction');
            Schema::dropIfExists('loan_case_files');
            Schema::dropIfExists('loan_case_trust');
            Schema::dropIfExists('loan_case_account');
            Schema::dropIfExists('loan_case_dispatch');
            Schema::dropIfExists('loan_case_document_version');
            Schema::dropIfExists('loan_case_document_page');
            Schema::dropIfExists('loan_case_masterlist');
            Schema::dropIfExists('loan_attachment');
            Schema::dropIfExists('loan_case_notes');
            Schema::dropIfExists('loan_case_checklist');
            Schema::dropIfExists('loan_case_checklist_details');
            Schema::dropIfExists('loan_case_checklist_main');
            Schema::dropIfExists('loan_case');
            Schema::dropIfExists('customer_case_expense');
            Schema::dropIfExists('internal_case_expense');
            Schema::dropIfExists('case_todo');
            Schema::dropIfExists('group_portfolio');
            Schema::dropIfExists('bank_user_rel');
            Schema::dropIfExists('courier');
            Schema::dropIfExists('portfolio');
            Schema::dropIfExists('banks');
            Schema::dropIfExists('user_kpi_history'); 
            Schema::dropIfExists('checklist_template_main_step_rel');
            Schema::dropIfExists('checklist_template_categories');
            Schema::dropIfExists('checklist_template_item');
            Schema::dropIfExists('checklist_template_steps');
            Schema::dropIfExists('checklist_template_main');
            Schema::dropIfExists('case_checklist_template_details');
            Schema::dropIfExists('case_checklist_template_main');
            Schema::dropIfExists('checklist_case_category');
            Schema::dropIfExists('account_category');
            Schema::dropIfExists('case_masterlist_value');
            Schema::dropIfExists('case_masterlist_field');
            Schema::dropIfExists('case_masterlist_field_category');
            Schema::dropIfExists('document_template_pages');
            Schema::dropIfExists('document_template_details');
            Schema::dropIfExists('document_template_main');
            Schema::dropIfExists('document_template_file_details');
            Schema::dropIfExists('document_template_file_main');
            Schema::dropIfExists('document_template_file');
            Schema::dropIfExists('email_template_details');
            Schema::dropIfExists('email_template_main');
            Schema::dropIfExists('team_portfolio');
            Schema::dropIfExists('team_member');
            Schema::dropIfExists('team_main');
            Schema::dropIfExists('team_members');
            Schema::dropIfExists('member_portfolio');
            Schema::dropIfExists('teams');
            Schema::dropIfExists('audit_log');
            Schema::dropIfExists('account');
            Schema::dropIfExists('account_template_details');
            Schema::dropIfExists('account_template_main');
            Schema::dropIfExists('loan_case_bill_main');
            Schema::dropIfExists('loan_case_bill_details');
            Schema::dropIfExists('quotation_template_details');
            Schema::dropIfExists('quotation_template_main');
            Schema::dropIfExists('account_item');
            Schema::dropIfExists('account_main_cat');



            // Schema::table('users', function (Blueprint $table) {
            //     $table->dropColumn('nick_name');
            //     $table->dropColumn('phone_no');
            //     $table->dropColumn('office_no');
            //     $table->dropColumn('status');
            //     });

                if (Schema::hasColumn('users', 'nick_name'))
                {
                    Schema::table('users', function (Blueprint $table)
                    {
                        $table->dropColumn(['nick_name', 'phone_no', 'office_no', 'status']);
                    });
                }

                if (Schema::hasColumn('users', 'portfolio'))
                {
                    Schema::table('users', function (Blueprint $table)
                    {
                        $table->dropColumn(['portfolio', 'min_files', 'max_files']);
                    });
                }

                if (Schema::hasColumn('users', 'commission'))
                {
                    Schema::table('users', function (Blueprint $table)
                    {
                        $table->dropColumn(['commission']);
                    });
                }

                if (Schema::hasColumn('users', 'kpi'))
                {
                    Schema::table('users', function (Blueprint $table)
                    {
                        $table->dropColumn(['kpi']);
                    });
                }

                if (Schema::hasColumn('users', 'race'))
                {
                    Schema::table('users', function (Blueprint $table)
                    {
                        $table->dropColumn(['race']);
                    });
                }

                if (Schema::hasColumn('roles', 'status'))
                {
                    Schema::table('roles', function (Blueprint $table)
                    {
                        $table->dropColumn('status');
                    });
                }

        }
    }
