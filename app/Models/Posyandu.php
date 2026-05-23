<?php

namespace App\Models;

use Database\Factories\PosyanduFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Posyandu extends Model
{
    /** @use HasFactory<PosyanduFactory> */
    use HasFactory;

    protected $table = 'posyandu';

    protected $fillable = [
        'desa_id',
        'nama_posyandu',
        'alamat',
        'latitude',
        'longitude',
        'status',
        'keterangan',
    ];

    public function desa(): BelongsTo
    {
        return $this->belongsTo(Desa::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_posyandu', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('keterangan', 'like', "%{$search}%")
                    ->orWhereHas('desa', function ($q2) use ($search) {
                        $q2->where('nama_desa', 'like', "%{$search}%");
                    });
            });
        }
    }

    public function scopeSort($query, $sortBy, $sortDir)
    {
        if ($sortBy === 'nama_desa') {
            $query->join('desa', 'posyandu.desa_id', '=', 'desa.id')
                ->orderBy('desa.nama_desa', $sortDir)
                ->select('posyandu.*');
        } else {
            $allowedSorts = ['id', 'nama_posyandu', 'alamat', 'latitude', 'longitude', 'status', 'keterangan'];
            $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';
            $query->orderBy($sortBy, $sortDir);
        }
    }
}
