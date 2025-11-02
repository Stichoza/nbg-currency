<?php

declare(strict_types=1);

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
        public readonly float  $rate,
        public readonly string $name,
        public readonly float  $diff,
        public readonly Carbon $date,
        public readonly Carbon $validFrom,
    ) {
        $this->change = $this->diff <=> 0;
    }

    /**
     * @return bool If the rate has increased.
     */
    public function increased(): bool
    {
        return $this->change > 0;
    }

    /**
     * @return bool If the rate has decreased.
     */
    public function decreased(): bool
    {
        return $this->change < 0;
    }

    /**
     * @return bool If the rate hasn't changed.
     */
    public function unchanged(): bool
    {
        return $this->change === 0;
    }

    /**
     * Returns string according to rate change direction. Useful for colors, icons, classes, etc.
     *
     * Example: `<span class="<?= $currency->changeString('text-red', 'text-gray', 'text-green'); ?>">...</span>`
     *
     * @param string $decreased String to be returned if rate was decreased
     * @param string $unchanged String to be returned if rate wasn't changed
     * @param string $increased String to be returned if rate was increased
     *
     * @return string Returns string according to rate change direction
     */
    public function changeString(string $decreased, string $unchanged, string $increased): string
    {
        return [$decreased, $unchanged, $increased][$this->change + 1];
    }
}
