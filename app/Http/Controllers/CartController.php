<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

	/**
     * Muestra todos los carritos.
     * 
     * @return  {array} json con todos los carritos encontradas
     */
    public function index()
    { 
    	$carts = Cart::where('user_id', auth()->user()->id)->get();
    	return response(['carts' => $carts ]);
    }

   /**
     * Almacena los datos del carrito de compras.
     * 
     * @return  
     */
    public function store(Request $request)
    { 
    	try {

    		$cart = Cart::create([
    			'user_id' => auth()->user()->id,
	            'product_id' => $request->product_id,
	            'quantity' => $request->quantity
    		]);
	    	return response([
	    		'cart' => $cart,
	    		'message' => 'Carrito agregado correctamente'
	    	]);
    		
    	} catch ( \Throwable $th) {
    		//Log::debug($th->getMessage());
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    }

    public function update(Request $request, Cart $cart)
    {
    	try {
    		$cart->update($request->all());
	    	return response([
	    		'cart' => $cart,
	    		'message' => 'Carrito actualizado correctamente'
	    	]);
    		
    	} catch ( \Throwable $th) {
    		//Log::debug($th->getMessage());
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    	
    }

    /**
     * Elimina un carrito.
     * 
     * @return 
     */
    public function destroy(Cart $cart)
    {  
    	try {
    		if ($cart) {
    			$cart->delete();
		    	return response([
		    		'message' => 'Carrito eliminado correctamente'
		    	]); 
    		}
    		return respose([
    			'error' => 'El carrito no existe'
    		]);

    	} catch ( \Throwable $th) {
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
		
    }


}
