<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'administrators';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'administrator_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'superAdmin'
    ];

    public $timestamps = false;

    /**
     * Get the user associated with the profile
     */
    public function users()
    {
        return $this->hasMany(User::class, "user_id");
    }

}
