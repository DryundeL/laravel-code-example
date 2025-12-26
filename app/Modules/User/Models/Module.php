<?php

namespace App\Modules\User\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'desc',
        'icon',
        'show',
        'inuse',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'icon' => 'boolean',
            'show' => 'array',
            'inuse' => 'boolean',
        ];
    }

    public static function scopeFindInUses(Builder $query)
    {
        $query->where('inuse', true);
    }

    public function accesses(): BelongsToMany
    {
        return $this->belongsToMany(Access::class)
            ->withPivot('order');
    }
}
