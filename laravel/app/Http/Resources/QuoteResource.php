<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'quote_number' => $this->quote_number,
            'client_id' => $this->client_id,
            'client' => $this->whenLoaded('client', function () {
                return [
                    'id' => $this->client->id,
                    'name' => $this->client->name,
                    'email' => $this->client->email,
                    'phone' => $this->client->phone,
                ];
            }),
            'aircraft_id' => $this->aircraft_id,
            'aircraft' => $this->whenLoaded('aircraft', function () {
                return [
                    'id' => $this->aircraft->id,
                    'registration' => $this->aircraft->registration,
                    'model' => $this->aircraft->model,
                    'capacity' => $this->aircraft->capacity,
                ];
            }),
            'origin' => $this->origin,
            'destination' => $this->destination,
            'departure_date' => $this->departure_date->format('Y-m-d'),
            'departure_time' => $this->departure_time,
            'passengers' => $this->passengers,
            'flight_distance' => $this->flight_distance,
            'flight_hours' => $this->flight_hours,
            'flight_route' => $this->flight_route,
            'fuel_cost' => (float) $this->fuel_cost,
            'landing_fees' => (float) $this->landing_fees,
            'handling_fees' => (float) $this->handling_fees,
            'accommodation' => (float) $this->accommodation,
            'catering' => (float) $this->catering,
            'other_expenses' => (float) $this->other_expenses,
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'notes' => $this->notes,
            'sent_at' => $this->sent_at?->toDateTimeString(),
            'accepted_at' => $this->accepted_at?->toDateTimeString(),
            'rejected_at' => $this->rejected_at?->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_by_user' => $this->whenLoaded('createdBy', function () {
                return $this->createdBy ? [
                    'id' => $this->createdBy->id,
                    'name' => $this->createdBy->name,
                    'email' => $this->createdBy->email,
                ] : null;
            }),
            'updated_by_user' => $this->whenLoaded('updatedBy', function () {
                return $this->updatedBy ? [
                    'id' => $this->updatedBy->id,
                    'name' => $this->updatedBy->name,
                    'email' => $this->updatedBy->email,
                ] : null;
            }),
            'ai_response' => $this->ai_response,
            'links' => [
                'self' => route('api.quotes.show', $this->id),
                'pdf' => route('api.quotes.pdf', $this->id),
                'send' => route('api.quotes.send', $this->id),
                'accept' => route('api.quotes.accept', $this->id),
                'reject' => route('api.quotes.reject', $this->id),
            ],
        ];
    }
    
    /**
     * Get the status label for the quote
     *
     * @return string
     */
    protected function getStatusLabel()
    {
        $statuses = [
            'draft' => 'Rascunho',
            'sent' => 'Enviada',
            'accepted' => 'Aceita',
            'rejected' => 'Rejeitada',
        ];
        
        return $statuses[$this->status] ?? $this->status;
    }
}
