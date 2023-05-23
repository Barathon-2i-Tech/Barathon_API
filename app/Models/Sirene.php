<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sirene extends Model
{
    use HasFactory;

    /**
     * connection to 2nd database
     */
    protected $connection = 'pgsql_db_sirene';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sirene';
}
