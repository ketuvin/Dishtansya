<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

// Swagger Annotation for User-Schema
/**
 *  @OA\Schema(
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="password", type="string"),
 * )
 */
class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    public function order() {
        return $this->belongsToMany(Order::class, 'order_user');
    }
}
