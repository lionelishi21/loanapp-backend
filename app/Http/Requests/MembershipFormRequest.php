<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/12/2019
 * Time: 14:44
 */

namespace App\Http\Requests;

class MembershipFormRequest extends BaseRequest
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
                        'membership_form' => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'membership_form' => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}