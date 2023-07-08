<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 17:35
 */

namespace database\seeds;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{

    public function run()
    {

        $paymentMethods = [
            'CASH',
            'BANK'
        ];

        DB::table('payment_methods')->delete();

        foreach ($paymentMethods as $key => $value) {

            PaymentMethod::create([
                'name' => $value,
                'display_name' => $value,
                'description' => $value . ' Payment system.'
            ]);
        }

    }

}