<?php

namespace App\Domain\Subscription\DTOs;

use App\Domain\Subscription\Enums\PlanType;

class PlanData
{
    public $id;
    public $name;
    public $description;
    public $type;
    public $price;
    public $durationDays;
    public $features;

    public function __construct(
        string $id,
        string $name,
        string $description,
        PlanType $type,
        float $price,
        int $durationDays,
        array $features
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->type = $type;
        $this->price = $price;
        $this->durationDays = $durationDays;
        $this->features = $features;
    }

    public static function fromArray(array $data): self
    {
        $type = $data['type'];
        if (is_string($type)) {
            $type = PlanType::from($type);
        }

        return new self(
            $data['id'],
            $data['name'],
            $data['description'],
            $type,
            (float) $data['price'],
            (int) $data['duration_days'],
            $data['features'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type->value,
            'price' => $this->price,
            'duration_days' => $this->durationDays,
            'features' => $this->features,
        ];
    }
}
