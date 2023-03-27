<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Barathonien extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'barathoniens';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'barathonien_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'birthday',
        'address_id',
    ];

    /**
     * Get the user associated with the profile
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'user_id');
    }

    /**
     * Get the Address associated with the Barathonien
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'address_id');
    }
}
