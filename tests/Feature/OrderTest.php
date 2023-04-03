<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OrderTest extends TestCase
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
     * Listar ordenes
     *
     * @return void
     */
    public function test_orders_can_be_retrived()
    {

        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get(route('api.orders.index'));

        $this->assertArrayHasKey('orders', $response->json()); // devuelve el carrito
        $response->assertStatus(200);
    }


    /**
     * Listar ordenes del usuario logueado
     *
     * @return void
     */
    public function test_orders_can_be_retrived_by_authenticated_user()
    {

        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token
        ])->get(route('api.orders.user'));

        $this->assertArrayHasKey('orders', $response->json()); // devuelve el carrito
        $response->assertStatus(200);
    }


    public function test_a_order_can_be_created()
    {
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
        $product2 = Product::create([
            'name' => 'Test product2',
            'sku'  => 'test-product2',
            'description' => 'Description test2',
            'price' => 10.2,
            'stock' => 6,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg')->hashName()        
        ]);

        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
        Cart::create([
            'user_id' => auth()->user()->id,
            'product_id' => $product2->id,
            'quantity' => 1
        ]);

         $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post(route('api.order.store'),[
            'shipping_address' => 'direccion',
            'order_address' => 'direccion 2',
            'order_email' => 'cliente@gmail.com',
            'order_status' => Order::PENDING,
        ]);

        $order = Order::with('order_details')->first();
        $prod1 = Product::find($product->id);
        $prod2 = Product::find($product2->id);

        $this->assertCount(1, Order::all());
        $this->assertEquals(20.4, $order->ammount); //checamos que sea la misma informacion que se había proporcionado
        $this->assertEquals('direccion', $order->shipping_address);
        $this->assertEquals('direccion 2' ,$order->order_address);
        $this->assertEquals('cliente@gmail.com',$order->order_email);
        $this->assertEquals(Order::PENDING, $order->order_status);
        $this->assertCount(2, $order->order_details);
        $this->assertEquals(9, $prod1->stock);
        $this->assertEquals(5, $prod2->stock);

        $response->assertStatus(200);
       
    }

}
