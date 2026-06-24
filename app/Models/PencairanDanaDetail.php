<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PencairanDanaDetail extends Model
{
    protected $guarded = [];

    public function pencairanDana()
    {
        return $this->belongsTo(PencairanDana::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
