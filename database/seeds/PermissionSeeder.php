<?php

namespace database\seeds;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{

    public function run()
    {
        DB::table('permissions')->delete();

        $permissions = [
            [
                'name'           => 'expense-add',
                'display_name'   => 'Expense Add',
                'description'    => 'Expense Add'
            ],
            [
                'name'           => 'member-add',
                'display_name'   => 'Members Add',
                'description'    => 'Members Add'
            ],
            [
                'name'           => 'loans-view',
                'display_name'   => 'Loans View Details',
                'description'    => 'Loans View Details'
            ],
            [
                'name'           => 'loan-application-add',
                'display_name'   => 'Add Loan Application',
                'description'    => 'Add Loan Application'
            ],
            [
                'name'           => 'loan-application-review',
                'display_name'   => 'Loan Application Review',
                'description'    => 'Ability to Confirm or Reject loans'
            ],
            [
                'name'           => 'payments-add',
                'display_name'   => 'Payments Add-View',
                'description'    => 'Payments Add-View'
            ],
            [
                'name'           => 'settings-general',
                'display_name'   => 'General Settings',
                'description'    => 'General Settings'
            ],
            [
                'name'           => 'settings-accounting',
                'display_name'   => 'Accounting Settings',
                'description'    => 'Accounting Settings'
            ],
            [
                'name'           => 'settings-borrowers',
                'display_name'   => 'Borrowers Settings',
                'description'    => 'Borrowers Settings'
            ],
            [
                'name'           => 'settings-branches',
                'display_name'   => 'Branches Settings',
                'description'    => 'Branches Settings'
            ],
            [
                'name'           => 'settings-communication',
                'display_name'   => 'Communication Settings',
                'description'    => 'Communication Settings'
            ],
            [
                'name'           => 'settings-expenses',
                'display_name'   => 'Expense Settings',
                'description'    => 'Expense Settings'
            ],
            [
                'name'           => 'settings-loans',
                'display_name'   => 'Loan Settings',
                'description'    => 'Loan Settings'
            ],
            [
                'name'           => 'settings-payments',
                'display_name'   => 'Payment Settings',
                'description'    => 'Payment Settings'
            ],
            [
                'name'           => 'settings-users',
                'display_name'   => 'Users - Add-Edit-Delete',
                'description'    => 'Users - Add-Edit-Delete'
            ],
            [
                'name'           => 'view-reports',
                'display_name'   => 'View Reports',
                'description'    => 'View Reports'
            ],
            [
                'name'           => 'my-profile',
                'display_name'   => 'Edit Own Profile',
                'description'    => 'Edit Own Profile'
            ]
        ];

        foreach ($permissions as $key => $value){
            Permission::create($value);
        }
    }

}