<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 10:38
 */

namespace App\Http\Requests;

use Illuminate\Database\Schema\Builder;
use Illuminate\Validation\Rule;

class AccountRequest extends BaseRequest
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
                        'branch_id'         => 'required|exists:branches,id',
                        'account_number'    => 'required|unique:accounts,account_number,NULL,id,deleted_at,NULL',
                        'account_code'      => '',
                        'account_name'      => '',
                        'account_type_id'   => 'exists:account_types,id',
                        'account_status_id' => '',
                        'other_details'     => '',
                        'closed_on'         => '',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'         => 'required|exists:branches,id',
                        'account_number' => ['required', Rule::unique('accounts')->ignore($this->account, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                        'account_type_id'   => '',
                        'account_code'      => '',
                        'account_name'      => '',
                        'account_status_id' => '',
                        'other_details'     => '',
                        'closed_on'         => '',
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}