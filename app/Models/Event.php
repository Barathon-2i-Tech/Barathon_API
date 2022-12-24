<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'event_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'event_name',
        'description',
        'start_event',
        'end_event',
        'poster',
        'price',
        'capacity',
        'rejected',
        'establishment_id',
        'status_id',
        'user_id',
        'deleted_at',
    ];

    /**
     * Get the establishment associated with the event
     */
    public function establishments()
    {
        return $this->belongsTo(Establishment::class, "establishment_id");
    }

    /**
     * Get the status associated with the event
     */
    public function status()
    {
        return $this->belongsTo(Status::class, "status_id");
    }

    /**
     * Get the user associated with the event creation / modification
     */
    public function users()
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * Get the event update associated with the event
     */
    public function eventsUpdate()
    {
        return $this->hasMany(Event_update::class, "event_update_id");
    }

    /**
     * Get the tag  associated with the event
     */
    public function categories(){
        return $this->belongsToMany(Category::class, "category_event", "event_id", "category_id");
    }
}
