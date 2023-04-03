<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


class CartTest extends TestCase
{
    use RefreshDatabase; // cada que se ejecuta el test limpia la BD

    /**
     *  Creamos un usuario y obtenemos su token
     * @return   
    */
    public function authenticate()
    {
        Artisan::call('passport:install');

        $user = User::create([
            'fullname' =>'test',
            'role'  => User::ADMINISTRADOR,
            'email' => rand(12345,678910)."@gmail.com",
            'password'=> bcrypt("123456")
        ]);

        if(!auth()->attempt(['email'=>$user->email, 'password'=>'123456'])){
            return response(['message' => 'Las credenciales son invalidas']); //Lofin credentials are invalid
        }

        return $user->createToken('Auth token')->accessToken;
    }

    /**
     * Un carrito puede ser creado.
     *
     * @return void
     */
    public function test_a_cart_can_be_created()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate(); 

        $category = Category::create([
            'name' => 'Lacteos',
            'description' => 'Productos lacteos',
        ]);

        //creamos una categoria porque cada que se ejecuta el test limpia la BD
        $product = Product::create([
            'name' => 'Test product',
            'sku'  => 'test-product',
            'description' => 'Description test',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg')->hashName()        
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            //'Accept' => 'application/json'
        ])->post(route('api.cart.store'),[
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $cart = Cart::first(); //obtenemos el primer registro porque estaba vacio y el primero que se creo es el que creamos en el test

        $this->assertCount(1, Cart::all());
        $this->assertEquals($cart->product_id, $product->id );
        $this->assertEquals($cart->quantity, 5);

        $response->assertStatus(200);

    }

    public function test_a_cart_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate(); 

        $category = Category::create([
            'name' => 'Lacteos',
            'description' => 'Productos lacteos',
        ]);
        //creamos una categoria porque cada que se ejecuta el test limpia la BD
        $product = Product::create([
            'name' => 'Test product',
            'sku'  => 'test-product',
            'description' => 'Description test',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg')->hashName()        
        ]); 

        $cart = Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product->id,
            'quantity' => 15
        ]); 

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            //'Accept' => 'application/json'
        ])->put(route('api.cart.updated', $cart->id),[
            'product_id' => $product->id,
            'quantity' => 5
        ]);

        $cartUpdated = Cart::find($cart->id); 
        
        $this->assertEquals( 5, $cartUpdated->quantity); //verificar que el valor es 5
        $this->assertArrayHasKey('cart', $response->json()); // devuelve el carrito

        $response->assertStatus(200);
    }

    /**
     * Test para probar que se puede borrar una carrito.
     *
     * @return void
     */
    public function test_a_cart_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate(); 

        $category = Category::create([
            'name' => 'Lacteos',
            'description' => 'Productos lacteos',
        ]);

        //creamos una categoria porque cada que se ejecuta el test limpia la BD
        $product = Product::create([
            'name' => 'Test product',
            'sku'  => 'test-product',
            'description' => 'Description test',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg')->hashName()        
        ]);

        $cart = Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]); 

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->delete(route('api.cart.delete', $cart->id));
         
        $cartDeleted = Cart::find($cart->id);

        $this->assertCount(0, Cart::all()); //comprobamos que si se haya borrado
        $response->assertStatus(200);
    }
    
     /**
     * Test para probar que se pueden obtener todos los carritos.
     *
     * @return void
     */
    public function test_a_cart_can_be_retrived()
    {
        $token = $this->authenticate(); 

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get(route('api.cart.index'));

        $this->assertArrayHasKey('carts', $response->json());
        $response->assertStatus(200);
    }

}
