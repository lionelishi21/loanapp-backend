<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 09/12/2019
 * Time: 14:59
 */

namespace App\Http\Requests;

class ApplicationFormRequest extends BaseRequest
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
                        'attach_application_form' => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'attach_application_form' => 'nullable|file|mimes:doc,pdf,docx,zip|max:10000'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}