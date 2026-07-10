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
        'contract_start_date',
        'contract_end_date',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'nik',
        'address',
        'ktp_document',
        'contract_document',
    ];

    protected $casts = [
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
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
