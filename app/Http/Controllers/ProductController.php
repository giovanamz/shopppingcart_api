<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
    	return response([
    		'products' => new ProductResource(Product::with('category')->get())
    	]);
    }

    /**
     * Muestra los datos de un producto dada.
     * 
     * @return  {array} con la info del producto encontrado
     */
    public function show(Product $product)
    { 
    	return response([
    		'product' => new ProductResource($product)
    	]);
    }

    /**
     * Almacena los datos de un producto.
     * 
     * @return  
     */
    public function store(ProductRequest $request)
    { 
    	try {
    		if($request->hasFile('image')){
    			$image = $request->file('image');
    			$path = $image->store('products');
    		}

    		$request->image = $path;

    		$product = Product::create([
    			'name' => $request->name,
	            'description' => $request->description,
	            'sku' => Str::slug($request->name),
	            'price' => $request->price,
	            'image' => $path,
	            'stock' => $request->stock,
	            'category_id' => $request->category_id,
    		]);
	    	return response([
	    		'product' => new ProductResource($product),
	    		'message' => 'Producto creado correctamente'
	    	]);
    		
    	} catch ( \Throwable $th) {
    		//Log::debug($th->getMessage());
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    }

    /**
     * Actualiza los datos de un producto.
     * 
     * @return 
     */
    public function update(ProductRequest $request, Product $product)
    { 
    	try {
    		if($request->hasFile('image')){
    			$image = $request->file('image');
    			$path = $image->store('products');
    		}

    		$product->name = $request->name;
    		$product->description = $request->description;
    		$product->sku = Str::slug($request->name);
    		$product->price = $request->price;
    		$product->stock = $request->stock;
    		$product->category_id = $request->category_id;

    		if (isset($path)) $product->image = $path;

    		$product->save();

	    	return response([
	    		'product' => new ProductResource($product),
	    		'message' => 'Producto actualizado correctamente'
	    	]);
    		
    	} catch ( \Throwable $th) {
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    }

    /**
     * Elimina un producto.
     * 
     * @return 
     */
    public function destroy(Product $product)
    {  
    	try {
    		if ($product) {
    			$product->delete();
		    	return response([
		    		'message' => 'Producto eliminado correctamente'
		    	]); 
    		}
    		return respose([
    			'error' => 'El producto no existe'
    		]);

    	} catch ( \Throwable $th) {
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
		
    }

}
