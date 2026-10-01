<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tarif_id',
        'kategori',
        'status',
        'tarif',
        'tanggal',
        'catatan',
    ];

    protected $casts = [
        'tarif' => 'integer',
        'tanggal' => 'date:Y-m-d',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tarifRef(): BelongsTo
    {
        return $this->belongsTo(Tarif::class, 'tarif_id');
    }
}
