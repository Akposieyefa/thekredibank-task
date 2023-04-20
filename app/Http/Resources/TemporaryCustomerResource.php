<?php

namespace App\Http\Resources;

use App\Helpers\SystemHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $first_name
 * @property mixed $last_name
 * @property mixed $email
 * @property mixed $slug
 * @property mixed $user
 * @property mixed $created_at
 * @property mixed $updated_at
 * @property mixed $deleted_at
 * @property mixed $status
 * @property mixed $approval
 */
class TemporaryCustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $helper = new SystemHelper();
        return [
            'id' => $this->id,
            'firstname' => $helper->cleanStringHelper($this->first_name),
            'lastname' => $helper->cleanStringHelper($this->last_name),
            'email'  => $helper->cleanStringHelper($this->email),
            'slug' => $helper->cleanStringHelper($this->slug),
            'status' => $helper->cleanStringHelper($this->status),
            'approval' => $helper->cleanStringHelper($this->approval),
            'relationships' => [
                'creator' => new UserResource($this->user)
            ],
            'created_at' => $this->created_at,
            'updated_at'  => $this->updated_at,
            'deleted_at' => $this->deleted_at
        ];
    }
}
