<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category_Establishment extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'categories_establishments';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'category_establishment_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'category_id',
        'establishment_id'
    ];

    protected $hidden = ['pivot'];

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function establishments(): BelongsTo
    {
        return $this->belongsTo(Establishment::class, 'establishment_id');
    }

}
