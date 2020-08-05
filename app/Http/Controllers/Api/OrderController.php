<?php

namespace App\Http\Controllers\Api;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderProductRequest;
use App\Http\Requests\ProductShowRequest;

// Swagger Annotation for Order-Service
/**
 * @OA\Tag(
 *     name="order-service",
 *     description="Order Controller",
 * )
 * @OA\Post(
 *      path="/order",
 *      operationId="orderProduct",
 *      tags={"order-service"},
 *      summary="Order a product",
 *      description="Users must be authorized to access this.",
 *      @OA\RequestBody(
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                  @OA\Property(
 *                     property="product_id",
 *                     type="integer"
 *                 ),
 *                 @OA\Property(
 *                     property="quantity",
 *                     type="integer"
 *                 ),
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Success",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Unprocessable Entity",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server",
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 * @OA\Get(
 *      path="/product/{product_id}",
 *      operationId="getProductById",
 *      tags={"order-service"},
 *      summary="Get product by id",
 *      description="Users must be authorized to access this.",
 *      @OA\Parameter(
 *          name="product_id",
 *          in="path",
 *          description="Existing product id",
 *          required=true,
 *          @OA\Schema(
 *              type="integer"
 *          )
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Success",
 *          @OA\MediaType(
 *              mediaType="application/json",
 *          )
 *      ),
 *      @OA\Response(
 *          response=400,
 *          description="Bad Request",
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Unauthorized",
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Unprocessable Entity",
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal Server",
 *      ),
 *      security={
 *          {"bearerAuth": {}}
 *      }
 * )
 */
class OrderController extends Controller
{
    const SUCCESS_STATUS = 201;
    const BAD_REQUEST_STATUS = 400;
    const UNAUTHORIZED_STATUS = 401;
    const INTERNAL_SERVER_STATUS = 500;

    // orders a product
    public function orderProduct(OrderProductRequest $request) {
        $validated = $request->validated(); //validation
        
        $input = $request->input();

        $input['product_id'] = $request->request->get('product_id');
        $input['quantity'] = $request->request->get('quantity');

        DB::beginTransaction();

        try {
            $order = Order::create([
                'product_id' => $input['product_id'],
                'quantity' => $input['quantity'],
            ]);

            $product = Product::find($input['product_id']);

            if ($input['quantity'] > $product->available_stock || $product->available_stock == 0) {
                return response()->json([
                    'success'=> false,
                    'message'=> 'Failed to order this product due to unavailability of the stock'
                ], self::BAD_REQUEST_STATUS); 
            }

            $productUpdate['available_stock'] = $product->available_stock - $input['quantity'];

            $product->fill($productUpdate)->save();

            Auth::user()->order()->attach($order->id);

            DB::commit();

            return response()->json([
                'success'=> true,
                'message'=> 'You have successfully ordered this product'
            ], self::SUCCESS_STATUS); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success'=> false,
                'message'=> 'Failed to order product'
            ], self::INTERNAL_SERVER_STATUS); 
        }
    }

    // get product
    public function getProduct(ProductShowRequest $request, $product_id) {
        $validated = $request->validated(); //validation

        $product = Product::find($product_id);

        return response()->json([
            'success' => true,
            'data' => $product
        ], self::SUCCESS_STATUS);
    }
}
