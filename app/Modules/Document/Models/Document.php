<?php

namespace App\Modules\Document\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'description',
        'alert',
        'period',
        'inuse',
    ];

    public static function scopeFindInUses(Builder $query)
    {
        $query->where('inuse', true);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('type', $value)
            ->firstOrFail();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'inuse' => 'boolean',
        ];
    }
}
