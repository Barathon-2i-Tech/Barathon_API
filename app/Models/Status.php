<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\AsArrayObject;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'status';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'status_id';

    /**

     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'comment' => AsArrayObject::class,
    ];

    /**

     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'comment'
    ];

    /**
     * Get the establishment associated with the status
     */
    public function establishments()
    {
        return $this->hasMany(Establishment::class, "establishment_id");
    }

    /**
     * Get the owner associated with the status
     */
    public function owners()
    {
        return $this->hasMany(Owner::class, "owner_id");
    }

    /**
     * Get the event associated with the status
     */
    public function events()
    {
        return $this->hasMany(Event::class, "event_id");
    }
}
