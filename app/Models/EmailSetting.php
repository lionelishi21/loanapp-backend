<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 11:47
 */

namespace App\Models;

class EmailSetting extends BaseModel
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'email_settings';

    /**
     * Main table primary key
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'protocol',
        'smpt_host',
        'smpt_username',
        'smpt_password',
        'smpt_port',
        'mail_gun_domain',
        'mail_gun_secret',
        'mandrill_secret',
        'from_name',
        'from_email'
    ];
}