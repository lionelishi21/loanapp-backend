<?php
/**
 * Created by PhpStorm.
 * Employee: kevin
 * Date: 27/10/2018
 * Time: 11:06
 */

namespace database\seeds;

use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->delete();

        Employee::create([
            'salutation'    => 'Mr',
            'first_name'    => 'Devtest',
            'last_name'     => 'Devtest Last',
            'email'         => 'devtest@devtest.com',
            'staff_no'      => '123456',
        ]);
    }
}