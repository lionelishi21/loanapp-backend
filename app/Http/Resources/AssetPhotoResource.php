<?php
/**
 * Created by PhpStorm.
 * User: kevin
 * Email: robisignals@gmail.com
 * Date: 11/07/2019
 * Time: 13:26
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetPhotoResource extends JsonResource
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
            'id'            => $this->id,

            'asset_id'      => $this->asset_id,
            'title'         => $this->title,
            'description'   => $this->description,
            'date_taken'    => $this->date_taken,
            'url'           => $this->url,
            'notes'         => $this->notes,

            'created_by'        => $this->created_by,
            'updated_by'        => $this->updated_by,

            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
