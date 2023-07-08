<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 10/11/2019
 * Time: 15:59
 */

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class PenaltyFrequencyRequest extends BaseRequest
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
                        'name' => 'required|unique:penalty_frequencies,name,NULL,id,deleted_at,NULL',
                        'display_name' => 'required|unique:penalty_frequencies,display_name,NULL,id,deleted_at,NULL',
                        'description' => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'name' => ['required', 'exists:penalty_frequencies', Rule::unique('penalty_frequency')->ignore($this->penalty_frequency, 'id')
                            ->where(function ($query) {
                                $query->where('deleted_at', NULL);
                            })],

                        'display_name' => ['required', Rule::unique('penalty_frequencies')->ignore($this->penalty_frequency, 'id')
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