<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'tag_id';


    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'label'
    ];

    /**
     * Get the events associated with the category
     */
    public function events(){
        return $this->belongsToMany(Event::class, "categories_establishments", "category_id", "establishment_id" );
    }

}
