<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'category_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'label'
    ];

    public $timestamps = false;

    /**
     * Get the establishment associated with the category
     */
    public function establishments(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(
            Establishment::class,
            "categories_establishments",
            "category_id",
            "establishment_id"
        );
    }

}
