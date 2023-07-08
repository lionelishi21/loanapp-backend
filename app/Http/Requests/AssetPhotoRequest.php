<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 13:26
 */

namespace App\Http\Requests;

use Illuminate\Database\Schema\Builder;
use Illuminate\Validation\Rule;

class AssetPhotoRequest extends BaseRequest
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
                        'branch_id'             => 'required|exists:branches,id',
                        'asset_id'      => 'required|exists:assets,id',
                        'title'         => 'required|min:2',
                        'description'   => '',
                        'date_taken'    => 'required|min:2',
                        'url'           => 'required|min:2',
                        'notes'         => ''
                    ];

                    break;
                }
            case 'PUT':
            case 'PATCH':
                {
                    $rules = [
                        'branch_id'             => 'required|exists:branches,id',
                        'asset_id'      => 'required|exists:assets,id',
                        'title'         => 'required|min:2',
                        'description'   => '',
                        'date_taken'    => 'required',
                        'url'           => 'required|min:2',
                        'notes'         => 'required'
                    ];
                    break;
                }
            default:
                break;
        }

        return $rules;

    }
}