<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 05/02/2020
 * Time: 16:31
 */

namespace App\Console\Commands;

use App\Events\Loan\LoanPendingAmountChecked;
use Illuminate\Console\Command;

class PayPendingAmountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pay:pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pay pending loan amount - penalty, interest or principal. On a given date. (Checked daily)';

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
        event(new LoanPendingAmountChecked());
    }
}
