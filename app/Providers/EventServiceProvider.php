<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\Loan\LoanNextPeriodChecked' => [
            'App\Listeners\Loan\CalculateLoanAmountDue',
            'App\Listeners\Loan\CalculateLoanPenaltyDue'
        ],
        'App\Events\Loan\LoanPendingAmountChecked' => [
            'App\Listeners\Loan\PayLoanPendingAmount'
        ],
        'App\Events\Payment\PaymentReceived' => [
            'App\Listeners\Loan\PayLoanPendingAmount'
        ],
        'App\Events\Payment\PaidLoan' => [
            'App\Listeners\Payment\CheckCloseLoanListener'
        ],
        'App\Events\Oauth\LoginSuccess' => [
            'App\Listeners\LoginSuccessfulListener'
        ],
        'App\Events\Oauth\LoginFailed' => [
            'App\Listeners\LoginFailureListener'
        ],
        'App\Events\Oauth\Logout' => [
            'App\Listeners\LogoutSuccessfulListener'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
