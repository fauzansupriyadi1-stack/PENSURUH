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
        'service_type',
        'pickup_location',
        'destination_location',
        'description',
        'price',
        'status',
        'taken_at',
        'completed_at',
    ];

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

