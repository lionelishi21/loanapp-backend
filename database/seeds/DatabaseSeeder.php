<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    protected $tables = [

    ];

    protected $seeders = [
        'database\seeds\AccountClassSeeder',
        'database\seeds\AccountTypeSeeder',
        'database\seeds\BranchTableSeeder',
        'database\seeds\EmployeeTableSeeder',
        'database\seeds\PermissionSeeder',
        'database\seeds\RoleSeeder',
        'database\seeds\GeneralSettingTableSeeder',
        'database\seeds\EmailSettingTableSeeder',
        'database\seeds\SmsSettingSeeder',
        'database\seeds\InterestTypeSeeder',
        'database\seeds\PaymentMethodSeeder',
        'database\seeds\PaymentFrequencySeeder',
        'database\seeds\ReportTypeSeeder',
        'database\seeds\FinanceStatementSeeder',
        'database\seeds\PenaltyTypeSeeder',
        'database\seeds\PenaltyFrequencySeeder',
        'database\seeds\EmailTemplateSeeder',
        'database\seeds\SmsTemplateSeeder',
        'database\seeds\WitnessTypeSeeder',
        'database\seeds\CommunicationSettingSeeder',
        'database\seeds\UsersTableSeeder'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->cleanDatabase();

        foreach ($this->seeders as $seedClass) {
            $this->call($seedClass);
        }
    }

    /**
     * Clean out the database for a new seed generation
     */
    private function cleanDatabase()
    {
        foreach ($this->tables as $table) {
            DB::statement('TRUNCATE TABLE ' . $table . ' CASCADE;');
        }
    }

}

