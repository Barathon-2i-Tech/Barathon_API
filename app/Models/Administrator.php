<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Administrator extends Model
{
    use HasFactory;

    public $timestamps = false;

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
        'superAdmin',
    ];

    /**
     * Get the user associated with the profile
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_id');
    }
}
