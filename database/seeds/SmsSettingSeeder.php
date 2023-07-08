<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/10/2019
 * Time: 13:44
 */

namespace database\seeds;

use App\Models\EmailSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsSettingSeeder extends Seeder
{
    public function run()
    {
        DB::table('sms_settings')->delete();

        EmailSetting::create([
            'url' => 'smtp',
            'username' => 'sendmail.gmail.com',
            'password' => 'gmasdfa@gmail.com'
        ]);
    }

}