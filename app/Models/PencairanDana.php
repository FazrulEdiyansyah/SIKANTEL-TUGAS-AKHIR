<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PencairanDana extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'judul',
        'pengelola_id',
        'tenant_id',
        'approver_name',
        'start_date',
        'end_date',
        'total_penjualan',
        'dana_tenant',
        'dana_telu',
        'keterangan',
        'status',
        'catatan_kaur',
        'catatan_kabag',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_penjualan' => 'decimal:2',
        'dana_tenant' => 'decimal:2',
        'dana_telu' => 'decimal:2',
    ];

    public function pengelola()
    {
        return $this->belongsTo(User::class, 'pengelola_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function details()
    {
        return $this->hasMany(PencairanDanaDetail::class);
    }
}
