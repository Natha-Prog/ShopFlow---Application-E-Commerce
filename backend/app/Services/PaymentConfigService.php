<?php

namespace App\Services;

class PaymentConfigService
{
    public function getEnabledMethods(): array
    {
        $methods = config('payments.methods', []);

        return collect($methods)
            ->filter(fn (array $method) => $method['enabled'] ?? false)
            ->map(function (array $method, string $id) {
                return $this->formatForApi($id, $method);
            })
            ->values()
            ->all();
    }

    public function getAllowedIds(): array
    {
        return collect(config('payments.methods', []))
            ->filter(fn (array $method) => $method['enabled'] ?? false)
            ->keys()
            ->all();
    }

    public function isValid(string $id): bool
    {
        return in_array($id, $this->getAllowedIds(), true);
    }

    public function getMethod(string $id): ?array
    {
        $methods = config('payments.methods', []);

        if (! isset($methods[$id]) || ! ($methods[$id]['enabled'] ?? false)) {
            return null;
        }

        return $this->formatForApi($id, $methods[$id]);
    }

    public function requiresPhone(string $id): bool
    {
        $methods = config('payments.methods', []);

        return ($methods[$id]['requires_phone'] ?? false) && ($methods[$id]['enabled'] ?? false);
    }

    public function getLabel(string $id): string
    {
        $methods = config('payments.methods', []);

        return $methods[$id]['name'] ?? $id;
    }

    private function formatForApi(string $id, array $method): array
    {
        return [
            'id' => $id,
            'name' => $method['name'],
            'description' => $method['description'],
            'requires_phone' => (bool) ($method['requires_phone'] ?? false),
            'merchant_number' => $method['merchant_number'] ?? null,
            'merchant_name' => $method['merchant_name'] ?? null,
            'bank_name' => $method['bank_name'] ?? null,
            'account_number' => $method['account_number'] ?? null,
            'account_holder' => $method['account_holder'] ?? null,
            'instructions' => $method['instructions'] ?? null,
            'prefixes' => $method['prefixes'] ?? [],
        ];
    }
}
