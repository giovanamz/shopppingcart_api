<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Artisan;

class ProductTest extends TestCase
{
    use RefreshDatabase; // cada que se ejecuta el test limpia la BD

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
            'role'  => User::ADMINISTRADOR,
            'email' => rand(12345,678910)."@gmail.com",
            'password'=> bcrypt("123456")
        ]);

        return $user->createToken('Auth token')->accessToken;
    }

    /**
     * Testpara probar que se pueden obtener todos los productos
     *
     * @return void
     */
    public function test_products_can_be_retrived()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('api.products.index'));
        $response->assertStatus(200);
        $this->arrayHasKey('products', $response->json());
    }


    /**
     * Test para probar que se puede crear una categorias.
     *
     * @return void
     */
    public function test_a_product_can_be_created()
    {
        $this->withoutExceptionHandling(); //para evitar excepciones de ..

        storage::fake('products');

        $token = $this->authenticate();

        $category = Category::create([
            'name' => 'Lacteos',
            'description' => 'Productos lacteos',
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->post(route('api.product.store'),[
            'name' => 'product test',
            'description' => 'description test',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => $file = UploadedFile::fake()->image('product.jpg')
        ]);

        $product = Product::first();//verificamos el primer producto porque la  bd inicia vacia
        $this->assertEquals($product->name, 'product test'); //checamos que sea la misma informacion que se había proporcionado
        $this->assertEquals($product->description, 'description test');
        $this->assertEquals($product->sku, 'product-test');
        $this->assertEquals($product->price, 10.2);
        $this->assertEquals($product->stock, 10);
        $this->assertEquals($product->category_id, $category->id);
        $this->assertEquals($product->image, 'products/'.$file->hashName());

        Storage::deleteDirectory('products');
        $response->assertStatus(200);
        $response->assertJsonMissing(['error']);

    }

     /**
     * Test para probar que se pueden obtener todos los productos.
     *
     * @return void
     */
    public function test_a_product_can_be_retrived()
    {
        
        $this->withoutExceptionHandling();

        $category = Category::create([
            'name' => 'Lacteos',
            'description' => 'Productos lacteos',
        ]);
        //creamos una categoria porque cada que se ejecuta el test limpia la BD
        $product = Product::create([
            'name' => 'product test',
            'sku'  => 'product-test',
            'description' => 'description test',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg')->hashName()
        ]);

        $response = $this->get(route('api.product.show',$product->id));
        $response->assertStatus(200);
        $this->assertArrayHasKey('product',$response->json()); // devuelve el producto
 
    }


    /**
     * Test para probar que se puede modificar una categorias.
     *
     * @return void
     */
    public function test_a_product_can_be_updated()
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
            'Accept' => 'application/json'
        ])->put(route('api.product.update', $product->id),[
            'name' => 'Test product',
            'description' => 'Description test',
            'price' => 10.2,
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        $updatedProduct = Product::find($product->id);
        $this->assertEquals($updatedProduct->name, 'Test product'); //checamos que sea la misma informacion que se había proporcionado
        $this->assertEquals($updatedProduct->description, 'Description test');
        $this->assertEquals($updatedProduct->sku, 'test-product');
        $this->assertEquals($updatedProduct->price, 10.2);
        $this->assertEquals($updatedProduct->stock, 10);
        $this->assertEquals($updatedProduct->category_id, $category->id);
       
        $response->assertStatus(200);
        $response->assertJsonMissing(['error']);
        
    }

    /**
     * Test para probar que se puede borrar una categorias.
     *
     * @return void
     */
    public function test_a_product_can_be_deleted()
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
        ])->delete(route('api.product.delete', $product->id));
         
        $productDeleted = Product::find($product->id);

        $this->assertEmpty($productDeleted); //comprobamos que si se haya borrado
        $response->assertStatus(200);
    }
    
}
