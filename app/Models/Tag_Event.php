<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag_Event extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags_events';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'tag_event_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'tag_id',
        'event_id'
    ];

    protected $hidden = ['pivot'];

    public function tags()
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    public function events()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

}
