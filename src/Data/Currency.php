<?php

namespace Stichoza\NbgCurrency\Data;

use Carbon\Carbon;

class Currency
{
    public function __construct(
        public readonly string $code,
        public readonly float $rate,
        public readonly string $name,
        public readonly float $diff,
        public readonly Carbon $date,
        public readonly Carbon $validFromDate,
    ) {
        //
    }
}