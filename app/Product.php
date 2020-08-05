<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Swagger Annotation for Product-Schema
/**
 *  @OA\Schema(
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="available_stock", type="integer"),
 * )
 */
class Product extends Model
{
    public $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'available_stock',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at', 'updated_at', 'deleted_at'
    ];

    public function order() {
        return $this->hasMany(Order::class);
    }
}
