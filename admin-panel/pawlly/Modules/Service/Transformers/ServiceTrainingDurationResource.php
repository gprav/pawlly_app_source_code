<?php

namespace Modules\Service\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceTrainingDurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'duration' => $this->duration,
            'amount' => $this->amount,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'deleted_by' => $this->deleted_by,
        ];
    }
}
