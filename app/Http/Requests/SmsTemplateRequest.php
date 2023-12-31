<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/11/2019
 * Time: 23:03
 */

namespace App\Http\Requests;

use Illuminate\Database\Schema\Builder;
use Illuminate\Validation\Rule;

class SmsTemplateRequest extends BaseRequest
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
                        'name'   => 'unique:sms_templates,name,NULL,id,deleted_at,NULL',
                        'body'   => 'required',
                        'tags'   => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name'      => [Rule::unique('sms_templates')->ignore($this->sms_template, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'body'   => 'required',
                        'tags'   => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}