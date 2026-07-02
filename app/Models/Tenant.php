<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kantin_id',
        'nama_tenant',
        'jenis_makanan',
        'no_telepon',
        'foto',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kantin()
    {
        return $this->belongsTo(Kantin::class);
    }

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
