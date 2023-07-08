<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

#Installation and setup
Route::group(array('prefix'=>'/v1'),function(){
    Route::get('/install', 'Api\Install\InstallationController@checkRequirements');
    Route::get('/install/permissions', 'Api\Install\InstallationController@checkPermissions');
    Route::post('/install/database', 'Api\Install\InstallationController@databaseSetup');
    Route::post('/install/user', 'Api\Install\InstallationController@userSetup');
});

/**
 * Routes to obtain access_token and manage token refresh
 */
Route::group(array('prefix' => '/v1'), function () {
    Route::post('/login', 'Api\Oauth\LoginController@login');
    Route::post('/login/refresh', 'Api\Oauth\LoginController@refresh');

    Route::post('forgot_password', 'Api\Oauth\ForgotPasswordController@forgotPassword');
    Route::post('reset_password', 'Api\Oauth\ForgotPasswordController@resetPassword');
});

#Mpesa Payment Routes
Route::group(array('prefix'=>'/v1'),function(){
    Route::get('/mobilepay', 'Api\Mpesa\MpesaPaymentController@index');

    // C2B
    Route::post('/mobilepay/c2b/validation_url', 'Api\Mpesa\MpesaPaymentController@validationC2B');
    Route::post('/mobilepay/c2b/confirmation', 'Api\Mpesa\MpesaPaymentController@confirmationC2B');

    // B2C Payment Request
    Route::post('/mobilepay/b2c_request/time_out', 'Api\Mpesa\MpesaPaymentController@b2CPaymentRequestTimeOut');
    Route::post('/mobilepay/b2c_request/result', 'Api\Mpesa\MpesaPaymentController@b2CPaymentRequestResult');

    //Transaction Status
    Route::post('/mobilepay/tran_status/time_out', 'Api\Mpesa\MpesaPaymentController@transactionStatusQueueTimeOut');
    Route::post('/mobilepay/tran_status/result', 'Api\Mpesa\MpesaPaymentController@transactionStatusResult');

    //Reverse Transaction
    Route::post('/mobilepay/tran_reverse/time_out', 'Api\Mpesa\MpesaPaymentController@reverseTransactionQueueTimeOut');
    Route::post('/mobilepay/tran_reverse/result', 'Api\Mpesa\MpesaPaymentController@reverseTransactionResult');

    // Account Balance
    Route::post('/mobilepay/bal/time_out', 'Api\Mpesa\MpesaPaymentController@accountBalanceQueueTimeOut');
    Route::post('/mobilepay/bal/result', 'Api\Mpesa\MpesaPaymentController@accountBalanceResult');
});


/**
 * System routes
 */
Route::namespace('Api')->prefix('v1')->middleware('auth:api', 'throttle:60,1')->group(function () {

    Route::resource('users', 'UserController', ['except' => ['create', 'edit']])
        ->middleware('scopes:settings-users');
    Route::resource('roles', 'RoleController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-users']);
    Route::resource('permissions', 'PermissionController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-users']);

    Route::resource('employees', 'EmployeeController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-users']);

    Route::resource('borrowers', 'BorrowerController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-borrowers']);
    Route::resource('borrower_statuses', 'BorrowerStatusController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-borrowers']);

    Route::resource('branches', 'BranchController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-branches']);

    Route::resource('members', 'MemberController', ['except' => ['create', 'edit']])
        ->middleware(['scope:member-add']);
    Route::post('members/profile_pic', 'MemberController@profilePic')->where(['file_name' => '.*'])
        ->middleware(['scope:member-add']);
    Route::post('members/membership_form', 'MemberController@membershipForm')->where(['file_name' => '.*'])
        ->middleware(['scope:member-add']);
    Route::post('members/membership_form_update', 'MemberController@updateMembershipForm')
        ->middleware(['scope:member-add']);
    Route::post('members/fetch_photo', 'MemberController@fetchPhoto')->where(['file_name' => '.*'])
        ->middleware(['scope:member-add']);
    Route::post('members/update_photo', 'MemberController@updatePhoto')
        ->middleware(['scope:member-add']);

    Route::resource('loans', 'LoanController', ['except' => ['create', 'edit']])
        ->middleware(['scope:loans-view']);
    Route::post('loans/amortization', 'LoanController@amortizationReport');
    Route::post('loans/calculator', 'LoanController@calculatorReport');

    Route::resource('loan_types', 'LoanTypeController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-loans']);
    Route::resource('loan_applications', 'LoanApplicationController', ['except' => ['create', 'edit']])
        ->middleware(['scope:loan-application-add']);
    Route::post('loan_applications/application_form', 'LoanApplicationController@applicationForm')->where(['file_name' => '.*'])
        ->middleware(['scope:loan-application-add']);
    Route::post('loan_applications/application_form_update', 'LoanApplicationController@updateApplicationForm')
        ->middleware(['scope:loan-application-add']);

    Route::resource('loan_statuses', 'LoanStatusController', ['except' => ['create', 'edit']]);
    Route::resource('loan_application_statuses', 'LoanApplicationStatusController', ['except' => ['create', 'edit']]);

    Route::resource('loan_penalties', 'LoanPenaltyController', ['only' => ['index', 'show']]);
    Route::post('loan_penalties/waive', 'LoanPenaltyController@waive')
        ->middleware(['scope:loans-view']);

    Route::resource('loan_interests', 'LoanInterestRepaymentController', ['only' => ['index', 'show']]);
    Route::resource('loan_principals', 'LoanPrincipalRepaymentController', ['only' => ['index', 'show']]);

    Route::resource('payments', 'PaymentController', ['only' => ['index', 'store', 'show']])
        ->middleware(['scope:payments-add']);
    Route::resource('payment_methods', 'PaymentMethodController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-payments']);

    Route::resource('general_settings', 'GeneralSettingController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-general']);
    Route::post('general_settings/upload_logo', 'GeneralSettingController@uploadLogo')
        ->middleware(['scope:settings-general']);
    Route::post('general_settings/fetch_logo', 'GeneralSettingController@fetchLogo')->where(['file_name' => '.*'])
        ->middleware(['scope:settings-general']);

    Route::resource('assets', 'AssetController', ['except' => ['create', 'edit']])
        ->middleware(['scope:member-add']);
    Route::resource('asset_photos', 'AssetPhotoController', ['except' => ['create', 'edit']])
        ->middleware(['scope:member-add']);

    Route::resource('witness_types', 'WitnessTypeController', ['except' => ['create', 'edit']]);
    Route::resource('accounts', 'AccountController', ['only' => ['index', 'show']]);

    Route::post('accounts/member', 'AccountController@depositAccountStatement');
    Route::post('accounts/loan', 'AccountController@loanAccountStatement');
    Route::post('accounts/general', 'AccountController@generalAccountStatement');

    Route::resource('account_statuses', 'AccountStatusController', ['except' => ['create', 'edit']]);
    Route::resource('account_types', 'AccountTypeController', ['only' => ['index', 'show']]);
    Route::resource('interest_types', 'InterestTypeController', ['except' => ['create', 'edit']]);
    Route::resource('fiscal_periods', 'FiscalPeriodController', ['except' => ['create', 'edit']]);
    Route::resource('journals', 'JournalController', ['except' => ['create', 'edit']]);
    Route::resource('transactions', 'TransactionController', ['except' => ['create', 'edit']]);
    Route::resource('transaction_types', 'TransactionTypeController', ['except' => ['create', 'edit']]);
    Route::resource('account_classes', 'AccountClassController', ['except' => ['create', 'edit']]);
    Route::resource('payment_frequencies', 'PaymentFrequencyController', ['except' => ['create', 'edit']]);

    Route::resource('summaries', 'SummaryController', ['only' => ['index']]);
    Route::post('summaries/due_today', 'SummaryController@downloadLoansDueTodayReport');
    Route::post('summaries/over_due', 'SummaryController@downloadLoansOverdueReport');

    Route::resource('guarantors', 'GuarantorController', ['except' => ['create', 'edit']]);

    Route::resource('withdrawals', 'WithdrawalController', ['except' => ['create', 'edit']]);
    Route::resource('mpesa_bulk_payments', 'Mpesa\MpesaBulkPaymentController', ['except' => ['create', 'edit']]);
    Route::get('mpesa_summary', 'Mpesa\MpesaSummaryController@index');
    Route::get('mpesa_summary/balance', 'Mpesa\MpesaSummaryController@mpesaBalance');
    Route::resource('mpesa_scheduled_disbursements', 'Mpesa\MpesaScheduledDisbursementController');


    Route::resource('penalty_frequencies', 'PenaltyFrequencyController', ['except' => ['create', 'edit']]);
    Route::resource('penalty_types', 'PenaltyTypeController', ['except' => ['create', 'edit']]);

    Route::post('user_profile/forgot_password', 'UserProfileController@forgotPassword');

    Route::resource('email_settings', 'EmailSettingController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-communication']);
    Route::resource('sms_settings', 'SmsSettingController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-communication']);
    Route::resource('email_templates', 'EmailTemplateController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-communication']);
    Route::resource('sms_templates', 'SmsTemplateController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-communication']);

    Route::resource('communication_settings', 'CommunicationSettingController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-communication']);

    Route::resource('expenses', 'ExpenseController', ['except' => ['create', 'edit']])
        ->middleware(['scope:expense-add']);
    Route::resource('expense_categories', 'ExpenseCategoryController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-expenses']);

    Route::resource('reports', 'ReportController', ['only' => ['index', 'show']])
        ->middleware(['scope:view-reports']);
    Route::resource('finance_statements', 'FinanceStatementController', ['only' => ['index', 'store']])
        ->middleware(['scope:view-reports']);
    Route::post('finance_statements/report', 'FinanceStatementController@downloadReport')
        ->middleware(['scope:view-reports']);

    Route::resource('capitals', 'CapitalController', ['except' => ['create', 'edit']])
        ->middleware(['scope:settings-accounting']);

    Route::resource('user_profile', 'UserProfileController', ['only' => ['index', 'update']])
        ->middleware(['scope:my-profile']);
    Route::post('user_profile/upload_photo', 'UserProfileController@uploadPhoto')
        ->middleware(['scope:my-profile']);
    Route::post('user_profile/fetch_photo', 'UserProfileController@fetchPhoto')->where(['file_name' => '.*'])
        ->middleware(['scope:my-profile']);

    Route::resource('loan_histories', 'LoanHistoryController', ['only' => ['index']]);
    Route::get('/logout', 'Oauth\LoginController@logout');
});
