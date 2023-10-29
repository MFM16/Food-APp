<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'profile_id',
        'item',
        'address',
        'total_amount',
        'message',
        'status',
    ];

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }

    public function rating()
    {
        return $this->hasOne(Rating::class);
    }
}
