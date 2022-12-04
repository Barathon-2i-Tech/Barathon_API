<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\AsArrayObject;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Establishment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'establishments';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'establishment_id';

    /**

     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'opening' => AsArrayObject::class,
    ];

    /**

     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'trade_name',
        'siret',
        'address',
        'postal_code',
        'city',
        'logo',
        'phone',
        'email',
        'website',
        'opening',
        'checked',
        'deleted_at',
        'owner_id',
        'status_id'
    ];

    /**
     * Get the owner associated with the establishment
     */
    public function owners()
    {
        return $this->hasMany(Owner::class, "owner_id");
    }

    /**
     * Get the employee associated with the establishment
     */
    public function employees(){
        return $this->belongsToMany(Employee::class, "establishment_employee", "establishment_id", "employee_id");
    }

    /**
     * Get the event associated with the establishment
     */
    public function events(){
        return $this->hasMany(Event::class, "event_id");
    }

    /**
     * Get the category associated with the establishment
     */
    public function categories(){
        return $this->belongsToMany(Category::class, "category_establishment", "establishment_id", "category_id");
    }

    /**
     * Get the status associated with the establishment
     */
    public function establishmentsStatus(){
        return $this->belongsTo(Status::class, "status_id");
    }
}
