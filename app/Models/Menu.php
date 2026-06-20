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
    ];
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
