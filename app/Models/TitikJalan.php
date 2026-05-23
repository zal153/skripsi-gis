<?php

namespace App\Models;

use Database\Factories\TitikJalanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TitikJalan extends Model
{
    /** @use HasFactory<TitikJalanFactory> */
    use HasFactory;

    protected $table = 'titik_jalan';

    protected $fillable = [
        'osm_node_id',
        'nama_titik',
        'latitude',
        'longitude',
        'source',
    ];

    public function jalanAwal(): HasMany
    {
        return $this->hasMany(Jalan::class, 'titik_awal_id');
    }

    public function jalanAkhir(): HasMany
    {
        return $this->hasMany(Jalan::class, 'titik_akhir_id');
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_titik', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }
    }

    public function scopeSort($query, $sortBy, $sortDir)
    {
        $allowedSorts = ['id', 'nama_titik', 'latitude', 'longitude'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';
        $query->orderBy($sortBy, $sortDir);
    }
}
