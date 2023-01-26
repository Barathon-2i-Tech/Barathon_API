<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Establishment_Employee extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'establishments_employees';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'establishment_employee_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'establishment_id',
        'employee_id'
    ];

    protected $hidden = ['pivot'];

    public function establishments(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'establishment_id');
    }

    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
