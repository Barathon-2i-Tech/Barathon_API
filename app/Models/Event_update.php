<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event_update extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events_updates';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'event_update_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'event_name',
        'event_id',
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
     * Get the event associated with the event update
     */
    public function events()
    {
        return $this->belongsTo(Event_update::class, "event_update_id");
    }

}
