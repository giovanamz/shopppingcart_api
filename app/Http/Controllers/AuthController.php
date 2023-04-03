<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class AuthController extends Controller
{
	 /**
     * Realiza la autenticacion del usuario al validar sus credenciales.
     * 
     * @return  {array} json con token de acceso
     */
    public function login(LoginRequest $request)
    {
    	$validatedData = $request->validated();

    	if(!auth()->attempt($validatedData)){
    		return response([
    			'message' => 'La autenticacion ha fallado'
    		]);
    	}

    	$access_token = auth()->user()->createToken('Auth token')->accessToken;
    	return response([
    		'user' => auth()->user(),
    		'access_token' => $access_token
    	]);

    }

    /**
     * Realiza la registro del usuario y proporciona credenciales.
     *
     * @return  {array} json con token de acceso
     */
    public function register(RegisterRequest $request)
    {
    	$user = User::create($request->all());
    	$access_token = $user->createToken('Auth token')->accessToken;

    	return response([
    		'user' => $user,
    		'access_token' => $access_token
    	]);

    }

    /**
     * Regresa los datos del usuario logueado
     *
     * @return  
     */
    public function me()
    {
    	return response([
    		'user' => auth()->user()
    	]);
    }

    /**
     * Cierra sesión del usuario logueado
     *
     * @return  
     */
    public function logout()
    {
    	try {
	    	auth()->user()->token()->revoke();
	    	return response([
	    		'message' => 'Ha cerrado sesión satisfactoriamente'
	    	]);
    	} catch ( \Throwable $th) {
    		return response([
	    		'error' => $th->getMessage()
	    	], 500);
    	}
    }



}
