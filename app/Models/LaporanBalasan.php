<?php

namespace App\Models;

use Database\Factories\LaporanBalasanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanBalasan extends Model
{
    /** @use HasFactory<LaporanBalasanFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'laporan_id',
        'user_id',
        'pesan',
    ];

    /**
     * Get the report that this reply belongs to.
     *
     * @return BelongsTo<Laporan, $this>
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class);
    }

    /**
     * Get the admin user who created this reply.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
