<?php

namespace App\Console\Commands;

use App\Events\Loan\LoanNextPeriodChecked;
use Illuminate\Console\Command;

class CalculateNextCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calculate:next';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate interest, principal or penalty for the next loan repayment period. (To be checked daily)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        event(new LoanNextPeriodChecked());
    }
}
