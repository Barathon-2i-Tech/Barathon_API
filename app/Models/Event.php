<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'establishment_id',
        'status_id',
        'user_id',
        'deleted_at',
        'event_update_id',
    ];

    /**
     * Get the establishment associated with the event
     */
    public function establishments(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'establishment_id');
    }

    /**
     * Get the status associated with the event
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    /**
     * Get the user associated with the event creation / modification
     */
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the event associated with the event update
     */
    public function eventParent(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_update_id');
    }

    /**
     * Get the tag  associated with the event
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_event', 'event_id', 'category_id');
    }

    /*
     * Get the event associated with the event update
     */
    public function eventChild(): HasMany
    {
        return $this->hasMany(Event::class, 'event_id');
    }

    /**
     * Get the booking associated with the event
     */
    public function bookings(): BelongsToMany
    {
        return $this->belongsToMany(Booking::class, 'bookings', 'event_id', 'user_id');
    }
}
