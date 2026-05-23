<?php

namespace App\Models;

use Database\Factories\JalanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Jalan extends Model
{
    /** @use HasFactory<JalanFactory> */
    use HasFactory;

    protected $table = 'jalan';

    protected $fillable = [
        'osm_way_id',
        'osm_segment_index',
        'titik_awal_id',
        'titik_akhir_id',
        'jarak',
        'source',
    ];

    public function titikAwal(): BelongsTo
    {
        return $this->belongsTo(TitikJalan::class, 'titik_awal_id');
    }

    public function titikAkhir(): BelongsTo
    {
        return $this->belongsTo(TitikJalan::class, 'titik_akhir_id');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('titikAwal', function ($q2) use ($search) {
                    $q2->where('nama_titik', 'like', "%{$search}%");
                })->orWhereHas('titikAkhir', function ($q2) use ($search) {
                    $q2->where('nama_titik', 'like', "%{$search}%");
                });
            });
        }
    }

    public function scopeSort($query, $sortBy, $sortDir)
    {
        if ($sortBy === 'titik_awal') {
            $query->join('titik_jalan as t_awal', 'jalan.titik_awal_id', '=', 't_awal.id')
                ->orderBy('t_awal.nama_titik', $sortDir)
                ->select('jalan.*');
        } elseif ($sortBy === 'titik_akhir') {
            $query->join('titik_jalan as t_akhir', 'jalan.titik_akhir_id', '=', 't_akhir.id')
                ->orderBy('t_akhir.nama_titik', $sortDir)
                ->select('jalan.*');
        } else {
            $allowedSorts = ['id', 'jarak'];
            $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';
            $query->orderBy('jalan.'.$sortBy, $sortDir);
        }
    }
}
