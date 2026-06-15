<?php

namespace App\Models;

use Database\Factories\LaporanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Laporan extends Model
{
    /** @use HasFactory<LaporanFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_posyandu',
        'alamat',
        'keterangan',
    ];

    /**
     * Get the replies/comments for the report.
     *
     * @return HasMany<LaporanBalasan, $this>
     */
    public function balasans(): HasMany
    {
        return $this->hasMany(LaporanBalasan::class);
    }
}
