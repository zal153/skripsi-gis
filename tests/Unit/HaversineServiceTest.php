<?php

use App\Services\HaversineService;

test('it returns zero for identical coordinates', function () {
    $service = new HaversineService;

    $distance = $service->distanceInKilometers(-6.2, 106.816666, -6.2, 106.816666);

    expect($distance)->toBe(0.0);
});

test('it calculates haversine distance in kilometers and miles', function () {
    $service = new HaversineService;

    $distanceInMiles = $service->distanceInMiles(33.4151843, -111.8314724, 33.0581063, -112.0476423);
    $distanceInKilometers = $service->distanceInKilometers(33.4151843, -111.8314724, 33.0581063, -112.0476423);

    expect($distanceInMiles)->toBeFloat()->toBeGreaterThan(27.65)->toBeLessThan(27.66)
        ->and($distanceInKilometers)->toBeFloat()->toBeGreaterThan(44.50)->toBeLessThan(44.51);
});
