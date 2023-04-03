<?php

namespace App\Services;

use App\Models\Cart;

/**
 *  Gestionar la informacion del carrito
 */
class CartService
{
	private $total = 0;

	//funcion para obtener los items del carrito
	public function getItems()
	{
		return Cart::where('user_id', auth()->user()->id)->get();
	}

	//obtener el total de la orden
	public function getTotal()
	{
		$this->total = 0;

		$carts = Cart::join('products', 'products.id', 'carts.product_id')
		->join('users','users.id', 'carts.user_id') //obtener todos los carritos o items para el usuario
		->select('products.price','carts.quantity') // seleccionamos solo precio y cantidad
		->where('users.id', auth()->user()->id) //del usuario logueado
		->get();

		foreach ($carts as $cart) {
			$this->total += $cart->price * $cart->quantity;
		}

		return $this->total;

	}
		 
}

