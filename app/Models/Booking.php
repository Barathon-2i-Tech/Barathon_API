<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bookings';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'booking_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'event_id',
        'isFav'
    ];

    protected $hidden = ['pivot'];

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function events()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
