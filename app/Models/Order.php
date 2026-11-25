<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'penyuruh_id',
        'disuruh_id',
        'title',
        'slug',
        'service_type',
        'pickup_location',
        'pickup_lat',
        'pickup_lng',
        'destination_location',
        'description',
        'price',
        'status',
        'taken_at',
        'completed_at',
    ];

    /**
     * Dapatkan slug order untuk teknik URL SEO-friendly.
     */
    public function getSlugAttribute()
    {
        if (!empty($this->attributes['slug'])) {
            return $this->attributes['slug'];
        }
        return 'order-' . \Illuminate\Support\Str::slug($this->title ?? 'tugas') . '-' . $this->id;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->slug) && \Illuminate\Support\Facades\Schema::hasColumn('orders', 'slug')) {
                $order->slug = 'order-' . \Illuminate\Support\Str::slug($order->title ?? 'tugas') . '-' . substr(uniqid(), -6);
            }
        });
    }

    /**
     * User yang menyuruh (Pemberi Tugas / Customer).
     */
    public function penyuruh()
    {
        return $this->belongsTo(User::class, 'penyuruh_id');
    }

    /**
     * User yang disuruh (Mitra Pesuruh yang mengambil order).
     */
    public function disuruh()
    {
        return $this->belongsTo(User::class, 'disuruh_id');
    }
}

