<?php

namespace App\Models;

use Database\Factories\DesaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method bool update(array $attributes = [], array $options = [])
 */
class Desa extends Model
{
    /** @use HasFactory<DesaFactory> */
    use HasFactory;

    protected $table = 'desa';

    protected $fillable = [
        'nama_desa',
    ];

    public function posyandu(): HasMany
    {
        return $this->hasMany(Posyandu::class);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            $query->where('nama_desa', 'like', "%{$search}%");
        }
    }

    public function scopeSort($query, $sortBy, $sortDir)
    {
        $allowedSorts = ['id', 'nama_desa'];
        $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'id';

        $query->orderBy($sortBy, $sortDir);
    }
}
