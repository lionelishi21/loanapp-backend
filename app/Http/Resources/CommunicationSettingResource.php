<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 24/11/2019
 * Time: 17:43
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommunicationSettingResource extends JsonResource
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
            'id'                => $this->id,
            'name'              => $this->name,
            'display_name'      => $this->display_name,
            'email_template'    => $this->email_template ? true: false,
            'sms_template'      => $this->sms_template ? true: false,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
