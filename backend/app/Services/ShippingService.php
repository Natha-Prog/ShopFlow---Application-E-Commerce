<?php

namespace App\Services;

use InvalidArgumentException;

class ShippingService
{
    public function quote(string $method, array $payload): array
    {
        return match ($method) {
            'pickup' => $this->quotePickup(),
            'delivery' => $this->quoteDelivery(
                (float) ($payload['latitude'] ?? 0),
                (float) ($payload['longitude'] ?? 0),
            ),
            'shipping' => $this->quoteShipping((string) ($payload['region'] ?? '')),
            default => throw new InvalidArgumentException('Mode de livraison invalide'),
        };
    }

    public function quoteForOrder(string $method, array $shippingAddress): array
    {
        if ($method === 'pickup') {
            return $this->quotePickup();
        }

        if ($method === 'delivery') {
            if (! isset($shippingAddress['latitude'], $shippingAddress['longitude'])) {
                throw new InvalidArgumentException('Coordonnées GPS requises pour la livraison à domicile');
            }

            return $this->quoteDelivery(
                (float) $shippingAddress['latitude'],
                (float) $shippingAddress['longitude'],
            );
        }

        if ($method === 'shipping') {
            $region = $shippingAddress['region'] ?? '';
            if ($region === '') {
                throw new InvalidArgumentException('Région requise pour l\'expédition colis');
            }

            return $this->quoteShipping($region);
        }

        throw new InvalidArgumentException('Mode de livraison invalide');
    }

    public function getRegions(): array
    {
        $regions = config('shipping.regions', []);

        return collect($regions)->map(function (array $data, string $code) {
            return [
                'code' => $code,
                'name' => $data['name'],
                'fee' => $data['fee'],
            ];
        })->values()->all();
    }

    private function quotePickup(): array
    {
        return [
            'fee' => 0,
            'currency' => config('shipping.currency', 'MGA'),
            'distance_km' => null,
            'region' => null,
            'message' => 'Retrait gratuit en boutique',
        ];
    }

    private function quoteDelivery(float $latitude, float $longitude): array
    {
        if ($latitude === 0.0 && $longitude === 0.0) {
            throw new InvalidArgumentException('Coordonnées GPS invalides');
        }

        $shopLat = (float) config('shipping.shop.latitude');
        $shopLng = (float) config('shipping.shop.longitude');
        $maxRadius = (float) config('shipping.delivery.max_radius_km');
        $baseFee = (float) config('shipping.delivery.base_fee');
        $perKm = (float) config('shipping.delivery.per_km');

        $distanceKm = round($this->haversineDistance($shopLat, $shopLng, $latitude, $longitude), 1);

        if ($distanceKm > $maxRadius) {
            throw new InvalidArgumentException(
                "Livraison à domicile disponible uniquement dans un rayon de {$maxRadius} km autour d'Antananarivo"
            );
        }

        $fee = $baseFee + ($distanceKm * $perKm);

        return [
            'fee' => round($fee, 2),
            'currency' => config('shipping.currency', 'MGA'),
            'distance_km' => $distanceKm,
            'region' => 'Analamanga',
            'message' => "Livraison estimée à {$distanceKm} km depuis la boutique",
        ];
    }

    private function quoteShipping(string $region): array
    {
        $regionKey = $this->normalizeRegionKey($region);

        if ($this->isExcludedShippingRegion($regionKey)) {
            throw new InvalidArgumentException(
                'L\'expédition colis est réservée aux régions hors Antananarivo. Utilisez la livraison à domicile.'
            );
        }

        $regions = config('shipping.regions', []);

        if (! isset($regions[$regionKey])) {
            throw new InvalidArgumentException('Région non reconnue pour l\'expédition colis');
        }

        $regionData = $regions[$regionKey];

        return [
            'fee' => (float) $regionData['fee'],
            'currency' => config('shipping.currency', 'MGA'),
            'distance_km' => null,
            'region' => $regionData['name'],
            'message' => "Expédition colis vers {$regionData['name']}",
        ];
    }

    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    private function normalizeRegionKey(string $region): string
    {
        $key = $this->slugifyRegion($region);

        $regions = config('shipping.regions', []);

        if (isset($regions[$key])) {
            return $key;
        }

        foreach ($regions as $code => $data) {
            if ($this->slugifyRegion($data['name']) === $key || strtolower($data['name']) === strtolower(trim($region))) {
                return $code;
            }
        }

        return $key;
    }

    private function slugifyRegion(string $region): string
    {
        $key = strtolower(trim($region));
        $key = str_replace([' ', '-', "'"], '_', $key);

        return preg_replace('/_+/', '_', $key) ?? $key;
    }

    private function isExcludedShippingRegion(string $regionKey): bool
    {
        $excluded = config('shipping.excluded_shipping_regions', []);

        return in_array($regionKey, $excluded, true);
    }
}
