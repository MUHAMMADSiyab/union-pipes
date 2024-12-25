<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PartnerWithdrawalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'partner' => [
                'id' => $this->partner->id,
                'name' => $this->partner->name,
                'photo' => $this->partner->photo,
            ],
            'payment' => [
                'amount' => $this->payment->amount,
                'payment_method' => $this->payment->payment_method,
                'payment_date' => $this->payment->payment_date,
                'cheque_type' => $this->payment->cheque_type,
                'cheque_no' => $this->payment->cheque_no,
                'cheque_due_date' => $this->payment->cheque_due_date,
                'cheque_images' => $this->payment->cheque_images,
            ],
        ];
    }
}
