<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewVerificationCode extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'code', 'product_id'];

    public function orders()
    {
        return $this->belongsTo(Order::class);
    }

    public function products()
    {
        return $this->belongsTo(Product::class);
    }
}
