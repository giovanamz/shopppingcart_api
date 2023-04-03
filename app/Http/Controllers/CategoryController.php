<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
	/**
     * Muestra todas las categorias.
     * 
     * @return  {array} json con todas las categorias encontradas
     */
    public function index()
    { 
    	return response([
    		'categories' => new CategoryResource(Category::all())
    	]);
    }

    /**
     * Muestra los datos de una categoria dada.
     * 
     * @return  {array} con la info de la categoria encontrada
     */
    public function show(Category $category)
    { 
    	return response([
    		'category' => new CategoryResource($category)
    	]);
    }

    /**
     * Almacena los datos de una categoria.
     * 
     * @return  
     */
    public function store(CategoryRequest $request)
    { 
    	try {
    		$category = Category::create($request->all());
	    	return response([
	    		'category' => new CategoryResource($category),
	    		'message' => 'Categoria creada correctamente'
	    	]);
    		
    	} catch ( \Throwable $th) {
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    }

    /**
     * Actualiza los datos de una categoria.
     * 
     * @return 
     */
    public function update(CategoryRequest $request, Category $category)
    { 
    	try {
    		$category->update($request->all());
	    	return response([
	    		'category' => new CategoryResource($category),
	    		'message' => 'Categoria actualizada correctamente'
	    	]);
    		
    	} catch ( \Throwable $th) {
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    }

    /**
     * Elimina una categoria.
     * 
     * @return 
     */
    public function destroy(Category $category)
    {  
		$category->delete();
    	return response([
    		'message' => 'Categoria eliminada correctamente'
    	]); 
    }


}
