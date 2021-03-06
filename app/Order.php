<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// Swagger Annotation for Product-Schema
/**
 *  @OA\Schema(
 *      @OA\Property(property="id", type="integer"),
 *      @OA\Property(property="product_id", type="integer"),
 *      @OA\Property(property="quantity", type="integer"),
 * )
 */
class Order extends Model
{
    public $table = 'orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id', 'quantity',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'pivot', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function user() {
        return $this->belongsToMany(User::class);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
