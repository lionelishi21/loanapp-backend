<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 08/09/2019
 * Time: 22:31
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PaymentFrequencyRequest extends BaseRequest
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
                        'name' => 'required|unique:payment_frequencies,name,NULL,id,deleted_at,NULL',
                        'display_name' => 'required|unique:payment_frequencies,display_name,NULL,id,deleted_at,NULL',
                        'description' => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name' => ['required', 'exists:payment_frequencies', Rule::unique('payment_frequencies')->ignore($this->payment_frequency, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'display_name' => ['required', Rule::unique('payment_frequencies')->ignore($this->payment_frequency, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}