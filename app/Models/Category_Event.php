<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category_Event extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories_events';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'category_event_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'event_id',
    ];

    protected $hidden = ['pivot'];

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function events(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
