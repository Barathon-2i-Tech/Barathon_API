<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'employees';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'employee_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'hiring_date',
        'dismissal_date'
    ];

    public $timestamps = false;


    /**
     * Get the user associated with the profile
     */
    public function user(): HasMany
    {
        return $this->hasMany(User::class, "user_id");
    }

    /**
     * Get the establishment associated with the employee
     */
    public function establishments(): BelongsToMany
    {
        return $this->belongsToMany(Establishment::class, "establishments_employees", "employee_id", "establishment_id");
    }

}
