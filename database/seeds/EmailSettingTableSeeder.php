<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 07/08/2019
 * Time: 09:17
 */

namespace database\seeds;

use App\Models\EmailSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailSettingTableSeeder extends Seeder
{

    public function run()
    {
        DB::table('email_settings')->delete();

        EmailSetting::create([
            'protocol'          => 'smtp',
            'smpt_host'         => 'sendmail.gmail.com',
            'smpt_username'     => 'gmasdfa@gmail.com',
            'smpt_password'     => 'dsfasdf',
            'smpt_port'         => '222',
            'mail_gun_domain'   => '',
            'mail_gun_secret'   => '',
            'mandrill_secret'   => '',
            'from_name'         => '',
            'from_email'        => ''
        ]);

    }

}