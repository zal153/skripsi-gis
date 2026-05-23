<?php

namespace App\Services;

class HaversineService
{
    private const EARTH_RADIUS_KM = 6371.0;

    private const EARTH_RADIUS_MILES = 3959.0;

    public function distanceInKilometers(
        float $latitudeOne,
        float $longitudeOne,
        float $latitudeTwo,
        float $longitudeTwo,
    ): float {
        return $this->calculate(
            $latitudeOne,
            $longitudeOne,
            $latitudeTwo,
            $longitudeTwo,
            self::EARTH_RADIUS_KM,
        );
    }

    public function distanceInMiles(
        float $latitudeOne,
        float $longitudeOne,
        float $latitudeTwo,
        float $longitudeTwo,
    ): float {
        return $this->calculate(
            $latitudeOne,
            $longitudeOne,
            $latitudeTwo,
            $longitudeTwo,
            self::EARTH_RADIUS_MILES,
        );
    }

    private function calculate(
        float $latitudeOne,
        float $longitudeOne,
        float $latitudeTwo,
        float $longitudeTwo,
        float $earthRadius,
    ): float {
        $latitudeOne = deg2rad($latitudeOne);
        $longitudeOne = deg2rad($longitudeOne);
        $latitudeTwo = deg2rad($latitudeTwo);
        $longitudeTwo = deg2rad($longitudeTwo);

        $distanceLongitude = $longitudeTwo - $longitudeOne;
        $distanceLatitude = $latitudeTwo - $latitudeOne;

        $a = sin($distanceLatitude / 2) ** 2
            + cos($latitudeOne) * cos($latitudeTwo) * sin($distanceLongitude / 2) ** 2;

        $a = max(0.0, min(1.0, $a));
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
