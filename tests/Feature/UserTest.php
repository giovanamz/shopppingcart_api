<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Artisan;

class UserTest extends TestCase
{
    /**
     *  Para obtener los datos del usuario primero hay que validar su acceso
     *  creamos esta funcion para validar dicho acceso
     * @return   
    */
    public function authenticate()
    {
        Artisan::call('passport:install');

        $user = User::create([
            'fullname' =>'test',
            'role'  => User::CLIENT,
            'email' => rand(12345,678910)."@gmail.com",
            'password'=> bcrypt("123456")
        ]);

        if(!auth()->attempt(['email'=>$user->email, 'password'=>'123456'])){
            return response([
                'message' => 'Las credenciales son invalidas' //Lofin credentials are invalid
            ]);
        }

        return $user->createToken('Auth token')->accessToken;

    }

    /**
     * Test para validar que se puedan obtener los datos del usuario
     *
     * @return {array} json regresa el token y datos del usuario
     */
    public function test_a_user_can_be_retrived()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticate();
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get(route('api.auth.me'));

        $response->assertStatus(200);
        $this->assertArrayHasKey('user',$response->json());
    }

    /**
     *  Validar que el usuario pueda cerrar sesion
     * @return  
     */
    public function test_logout()
    {
        $this->withoutExceptionHandling();
        $token = $this->authenticate();

         $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get(route('api.auth.logout'));

        $response->assertStatus(200);
        $this->assertArrayHasKey('message',$response->json());
        $this->assertEquals('Ha cerrado sesión satisfactoriamente', $response->json()['message']);
    }
}
