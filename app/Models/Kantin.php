<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kantin extends Model
{
    protected $fillable = [
        'nama_kantin',
        'lokasi',
        'foto',
        'status',
    ];

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }
}
