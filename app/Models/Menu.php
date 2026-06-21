<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'tenant_id',
        'nama_menu',
        'deskripsi',
        'harga',
        'foto',
        'status',
        'is_customizable',
        'customizations',
    ];

    protected $casts = [
        'customizations' => 'array',
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
