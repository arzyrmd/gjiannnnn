<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarif extends Model
{
    use HasFactory;

    protected $fillable = [
        'kategori',
        'tarif_berhasil',
        'tarif_gagal',
    ];

    protected $casts = [
        'tarif_berhasil' => 'integer',
        'tarif_gagal' => 'integer',
    ];

    public function jobOrders(): HasMany
    {
        return $this->hasMany(JobOrder::class);
    }
}
