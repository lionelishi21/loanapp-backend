<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 27/10/2019
 * Time: 13:40
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SmsSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,

            'url'       => $this->url,
            'username'  => $this->username,
            'password'  => $this->password,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
