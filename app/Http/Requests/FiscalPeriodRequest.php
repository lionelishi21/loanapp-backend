<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/08/2019
 * Time: 16:56
 */

namespace App\Http\Requests;

use Illuminate\Database\Schema\Builder;
use Illuminate\Validation\Rule;

class FiscalPeriodRequest extends BaseRequest
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
                        'start_on'      => '',
                        'end_on'        => '',
                        'closed_on'     => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'start_on'      => '',
                        'end_on'        => '',
                        'closed_on'     => ''
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}