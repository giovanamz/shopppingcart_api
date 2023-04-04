<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use App\services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; //use DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
	/**
     * Muestra todas las ordenes.
     * 
     * @return  {array} json con todos las ordenes encontradas
     */
    public function index()
    { 
    	$orders = Order::all();
    	return response(['orders' => $orders ]);
    }


    public function getByUser()
    {
    	$orders = Order::where('user_id', auth()->user()->id)->get();
    	return response(['orders' => $orders ]);
    }
    

    public function store( Request $request)
    {
    	try {
    		//DB::beignTransaction();
    		$cart = new CartService();

    		$order = Order::create([
    			'user_id' => auth()->user()->id,
				'ammount' => $cart->getTotal(),
				'shipping_address' => $request->shipping_address,
				'order_address' => $request->order_address,
				'order_email' => $request->order_email,
				'order_status' => $request->order_status
    		]);

    		foreach ($cart->getItems() as $item) {
    			$product = Product::find($item['product_id']);
    			OrderDetail::create([
    				'order_id' => $order->id,
    				'product_id' => $product->id,
    				'price' => $product->price,
    				'quantity' => $item['quantity'],
    			]); 

    			$product->stock = $product->stock - $item['quantity'];
    			$product->save();
    		}

    		//ya que se proceso la orden eliminamos el carrito
    		Cart::where('user_id', auth()->user()->id)->delete();
    		//DB::commit();
    		return response([
	    		'order' => $order,
	    		'message' => 'Orden creada satisfactoriamente'
	    	]);
    		
    	} catch ( \Throwable $th) {
    		//DB::rollBack();
    		//Log::info($th->getMessage());
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    }
}
