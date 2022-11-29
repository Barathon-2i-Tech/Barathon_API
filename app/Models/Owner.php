<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'owners';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'owner_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'siren',
        'avatar',
        'kbis',
        'active',
        'deleted_at',
        'status_id'
    ];

    /**
     * Get the status associated with the user
     */
    public function owners_status(){
        return $this->belongsTo(Status::class,  "status_id");
    }

    /**
     * Get the user associated with the profile
     */
    public function users()
    {
        return $this->hasMany(User::class, "user_id");
    }

    /**
     * Get the establishment associated with the owner
     */
    public function establishments()
    {
        return $this->hasMany(Establishment::class, "establishment_id");
    }
}
