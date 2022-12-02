<?php

namespace Stichoza\NbgCurrency\Data;

use Carbon\Carbon;

class Currency
{
    /**
     * @var int Change direction (-1: Rate decreased, 0: No change, 1: Rate increased).
     */
    public readonly int $change;

    public function __construct(
        public readonly string $code,
        public readonly float $rate,
        public readonly string $name,
        public readonly float $diff,
        public readonly Carbon $date,
        public readonly Carbon $validFromDate,
    ) {
        $this->change = $this->diff <=> 0;
    }
}