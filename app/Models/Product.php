<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'title',
        'price',
        'shipping_cost',
        'quantity',
    ];

    public function photos()
    {
        return $this->hasMany(\App\Models\Photo::class);
    }

    public function orders()
    {
        return $this->belongsToMany(\App\Models\Order::class, 'order_items');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function exceedsQuantityThreshold($threshold)
    {
        return $this->quantity > $threshold;
    }

    public function isQuantityAboveThreshold($threshold)
    {
        $cacheKey = "product_quantity_threshold_{$this->id}";

        return Cache::remember($cacheKey, now()->addMinutes(60), function () use ($threshold) {
            return $this->quantity > $threshold;
        });
    }
    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->queued()
            ->width(100)
            ->height(100);

        $this->addMediaConversion('small')
            ->queued()
            ->width(300)
            ->height(200);

        $this->addMediaConversion('medium')
            ->queued()
            ->width(600)
            ->height(400);

        $this->addMediaConversion('large')
            ->queued()
            ->width(1200)
            ->height(800);
    }
}
