<?php

namespace App\DTOs;

class SwapInputDTO
{
    public function __construct(
        public string $pair,
        public float  $lotSize,
        public string $positionType, // Long|Short
        public float  $swapLong,
        public float  $swapShort,
        public int    $days,
        public bool   $crossWednesday = false,
        public ?int   $profileId = null,
    ) {}

    public function chosenRate(): float
    {
        return $this->positionType === 'Long' ? $this->swapLong : $this->swapShort;
    }
}
