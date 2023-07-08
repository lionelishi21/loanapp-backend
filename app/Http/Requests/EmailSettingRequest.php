<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 11:49
 */

namespace App\Http\Requests;

use Illuminate\Database\Schema\Builder;
use Illuminate\Validation\Rule;

class EmailSettingRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $rules = [];

        switch ($this->method()) {
            case 'GET':
            case 'DELETE':
                {
                    return [];
                    break;
                }
            case 'POST':
                {
                    $rules = [
                        'protocol'          => '',
                        'smpt_host'         => '',
                        'smpt_username'     => '',
                        'smpt_password'     => '',
                        'smpt_port'         => '',
                        'mail_gun_domain'   => '',
                        'mail_gun_secret'   => '',
                        'mandrill_secret'   => '',
                        'from_name'         => '',
                        'from_email'        => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'protocol'          => '',
                        'smpt_host'         => '',
                        'smpt_username'     => '',
                        'smpt_password'     => '',
                        'smpt_port'         => '',
                        'mail_gun_domain'   => '',
                        'mail_gun_secret'   => '',
                        'mandrill_secret'   => '',
                        'from_name'         => '',
                        'from_email'        => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}