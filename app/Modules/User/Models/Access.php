<?php

namespace App\Modules\User\Models;

use App\Modules\Story\Models\Story;
use App\Modules\Widget\Models\Widget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Access extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
    ];

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class)
            ->withPivot('order')
            ->orderBy('pivot_order');
    }

    public function widgets(): BelongsToMany
    {
        return $this->belongsToMany(Widget::class)
            ->withPivot('order')
            ->orderBy('pivot_order');
    }

    public function stories(): BelongsToMany
    {
        return $this->belongsToMany(Story::class);
    }

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }
}
