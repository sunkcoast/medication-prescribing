<?php

namespace App\Services;

use Carbon\Carbon;
use RuntimeException;

class MedicinePricingService
{
    public function resolveByDate(array $prices, Carbon $date): array
    {
        foreach ($prices as $price) {
            $start = Carbon::parse($price['start_date']['value']);
            
            $endDateValue = $price['end_date']['value'] ?? null;
            $end = $endDateValue ? Carbon::parse($endDateValue) : null;

            if ($date->greaterThanOrEqualTo($start) && ($end === null || $date->lessThanOrEqualTo($end))) {
                return [
                    'unit_price'       => $price['unit_price'],
                    'price_start_date' => $start->toDateString(),
                    'price_end_date'   => $end ? $end->toDateString() : null,
                ];
            }
        }

        throw new RuntimeException('No valid price found for the given examination date.');
    }
}