<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Establishment extends Model
{
    use HasFactory, softDeletes;

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
        'address_id',
        'logo',
        'phone',
        'email',
        'website',
        'opening',
        'owner_id',
        'status_id',
        'validation_code',
    ];

    /**
     * Get the owner associated with the establishment
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(Owner::class, "owner_id");
    }

    /**
     * Get the employee associated with the establishment
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, "establishment_employee", "establishment_id", "employee_id");
    }

    /**
     * Get the event associated with the establishment
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, "event_id",);
    }

    /**
     * Get the category associated with the establishment
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, "categories_establishments", "establishment_id", "category_id");
    }

    /**
     * Get the status associated with the establishment
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, "status_id");
    }

    /**
     * Get the Address associated with the Establishment
     *
     * @return HasOne
     */
    public function address(): HasOne
    {
        return $this->hasOne(Address::class, 'address_id');
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }

        return null;
    }
}

