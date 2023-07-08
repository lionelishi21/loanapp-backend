<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 02/08/2019
 * Time: 11:49
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailSettingResource extends JsonResource
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

            'protocol'          => $this->protocol,
            'smpt_host'         => $this->smpt_host,
            'smpt_username'     => $this->smpt_username,
            'smpt_password'     => $this->smpt_password,
            'smpt_port'         => $this->smpt_port,
            'mail_gun_domain'   => $this->mail_gun_domain,
            'mail_gun_secret'   => $this->mail_gun_secret,
            'mandrill_secret'   => $this->mandrill_secret,
            'from_name'         => $this->from_name,
            'from_email'        => $this->from_email,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
