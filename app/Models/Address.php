<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'address';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'address_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'address',
        'postal_code',
        'city',
    ];

    /**
     * Get the barathonien that owns the Address
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barathonien(): BelongsTo
    {
        return $this->belongsTo(barathonien::class, 'address_id');
    }

    /**
     * Get the establishment that owns the Address
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(establishment::class, 'address_id');
    }
}
