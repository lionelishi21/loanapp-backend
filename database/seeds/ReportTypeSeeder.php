<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/10/2019
 * Time: 23:40
 */

namespace database\seeds;

use App\Models\ReportType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportTypeSeeder extends Seeder
{

    public function run()
    {
        DB::table('report_types')->delete();

        ReportType::create([
            'name' => 'loans_due',
            'display_name' => 'Loans Due',
            'description' => "Loans Due"
        ]);

        ReportType::create([
            'name' => 'loans_overDue',
            'display_name' => 'Loans OverDue',
            'description' => "Loans OverDue"
        ]);

    }

}