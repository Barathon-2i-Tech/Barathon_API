<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The primary key associated with the model
     */
    protected $primaryKey = 'user_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar',
        'deleted_at',
        'owner_id',
        'barathonien_id',
        'administrator_id',
        'employee_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the owner profile associated with the user
     */
    public function owners()
    {
        return $this->belongsTo(Owner::class, "owner_id");
    }

    /**
     * Get the employee profile associated with the user
     */
    public function employees()
    {
        return $this->belongsTo(Employee::class, "employee_id");
    }

    /**
     * Get the administrator profile associated with the user
     */
    public function administrators()
    {
        return $this->belongsTo(Administrator::class, "administrator_id");
    }

    /**
     * Get the barathonien profile associated with the user
     */
    public function barathoniens()
    {
        return $this->belongsTo(Barathonien::class, "barathonien_id");
    }

    /**
     * Get the event associated with the user
     */
    public function events()
    {
        return $this->belongsTo(Event::class, "event_id");
    }

    /**
     * Get the booking associated with the user
     */
    public function bookings(){
        return $this->belongsToMany(Booking::class, "bookings", "user_id", "booking_id" );
    }

}
