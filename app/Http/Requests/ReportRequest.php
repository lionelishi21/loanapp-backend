<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 22/10/2019
 * Time: 11:24
 */

namespace App\Http\Requests;

class ReportRequest extends BaseRequest
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
                        'branch_id'     => 'exists:branches,id',
                        'start_date'    => 'required',
                        'end_date'      => 'required',
                        'report_type'     => '',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'     => 'exists:branches,id',
                        'start_date'    => 'required',
                        'end_date'      => 'required',
                        'report_type'     => '',
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}
