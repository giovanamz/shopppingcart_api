<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Artisan;

class AuthTest extends TestCase
{
    use RefreshDatabase;

  
    /**
     *  Test Login.
     *
     * @return void
     */
    public function test_login(){

        Artisan::call('passport:install');

        $this->withoutExceptionHandling();

        $user = User::create([
            'fullname'=>'giov',
            'email'=> time()."@gmail.com",
            'password'=> bcrypt("123456")
        ]);

        $user->createToken('Auth token')->accessToken;

        $response = $this->post(route('api.login'),[
            'email' => time().'@gmail.com',
            'password'=> '123456'
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token',$response->json());
    }


    /**
     * Test registro de usuario.
     *
     * @return void
     */
    public function test_register()
    {
        Artisan::call('passport:install');

        $this->withoutExceptionHandling();

        $response = $this->post(route('api.register'),[
            'fullname' => 'Luna',
            'role'=> User::ADMINISTRADOR,
            'email'=> time().'@gmail.com',
            'password'=> '123456'
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('access_token',$response->json());

         
    }
}
