<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 28/08/2019
 * Time: 14:40
 */

namespace App\Http\Requests;

class JournalRequest extends BaseRequest
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
                        'branch_id'             => 'exists:branches,id',
                        'transaction_id'        => 'required',
                        'debit_account_id'      => 'required',
                        'credit_account_id'     => 'required',
                        'amount'                => 'required',
                        'narration'             => 'required',
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'exists:branches,id',
                        'transaction_id'        => 'required',
                        'debit_account_id'      => 'required',
                        'credit_account_id'     => 'required',
                        'amount'                => 'required',
                        'narration'             => 'required',
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}