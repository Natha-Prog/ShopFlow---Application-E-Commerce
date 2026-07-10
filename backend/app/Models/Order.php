<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'status',
        'subtotal',
        'shipping',
        'discount',
        'total',
        'shipping_address',
        'payment_method',
        'shipping_method',
        'payment_status',
        'payment_intent_id',
        'promo_code',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'shipping_address' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function toApiArray(): array
    {
        $this->loadMissing(['items.product', 'user']);

        $paymentMethod = config("payments.methods.{$this->payment_method}", []);

        return [
            'id' => $this->id,
            'reference' => $this->reference,
            'status' => $this->status,
            'subtotal' => (float) $this->subtotal,
            'shipping' => (float) $this->shipping,
            'discount' => (float) $this->discount,
            'total' => (float) $this->total,
            'shipping_address' => $this->shipping_address,
            'payment_method' => $this->payment_method,
            'payment_method_label' => $paymentMethod['name'] ?? $this->payment_method,
            'payment_instructions' => $paymentMethod['instructions'] ?? null,
            'payment_merchant_number' => $paymentMethod['merchant_number'] ?? null,
            'shipping_method' => $this->shipping_method,
            'payment_status' => $this->payment_status,
            'promo_code' => $this->promo_code,
            'created_at' => $this->created_at?->toISOString(),
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : null,
            'items' => $this->items->map(fn (OrderItem $item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'product' => $item->product ? [
                    'id' => $item->product->id,
                    'name' => $item->product->name,
                    'image_url' => $item->product->image,
                ] : null,
            ]),
        ];
    }

    public static function generateReference(): string
    {
        $length = random_int(5, 8);
        $reference = '';
        for ($i = 0; $i < $length; $i++) {
            $reference .= random_int(0, 9);
        }
        return $reference;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->reference)) {
                $order->reference = self::generateReference();
            }
        });
    }
}
