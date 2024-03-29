<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'category_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'label',
    ];

    /**
     * Get the establishment associated with the category
     */
    public function establishments(): BelongsToMany
    {
        return $this->belongsToMany(
            Establishment::class,
            'categories_establishments',
            'category_id',
            'establishment_id'
        );
    }

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'categories_events', 'category_id', 'event_id');
    }
}
