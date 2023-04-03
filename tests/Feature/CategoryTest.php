<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Artisan;

class CategoryTest extends TestCase
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

        if(!auth()->attempt(['email'=>$user->email, 'password'=>'123456'])){
            return response([
                'message' => 'Las credenciales son invalidas' //Lofin credentials are invalid
            ]);
        }

        return $user->createToken('Auth token')->accessToken;
    }

    /**
     * Test para probar que se puede obtener todas las categorias.
     *
     * @return void
     */
    public function test_categories_can_be_retrived()
    {
        
        $this->withoutExceptionHandling();

        $response = $this->get(route('api.categories.index'));
        $response->assertStatus(200);
        $this->assertArrayHasKey('categories',$response->json());
    }

    /**
     * Test para probar que se puede obtener una categorias.
     *
     * @return void
     */
    public function test_a_category_can_be_retrived()
    {
        
        $this->withoutExceptionHandling();

        //creamos una categoria porque cada que se ejecuta el test limpia la BD
        $category = Category::create([
            'name' => 'category test',
            'description' => 'description category test'
        ]);

        $response = $this->get(route('api.category.show',$category->id));

        //verificamos que los datos que se han pasado sean los que se guarden en la BD
        $this->assertEquals($category->name,'category test');
        $this->assertEquals($category->description,'description category test');
        $this->assertArrayHasKey('category',$response->json()); // devuelve la categoria

        $response->assertStatus(200);
    }

    /**
     * Test para probar que se puede crear una categorias.
     *
     * @return void
     */
    public function test_a_category_can_be_created()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->post(route('api.category.store'),[
            'name' => 'category test',
            'description' => 'description'
        ]);

        $response->assertStatus(200);
        $this->assertCount(1, Category::all());
        $response->assertJsonMissing(['error']);

    }


    /**
     * Test para probar que se puede modificar una categorias.
     *
     * @return void
     */
    public function test_a_category_can_be_updated()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate();

        $category = Category::create([
            'name' => 'category test',
            'description' => 'description test 1'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->put(route('api.category.update', $category->id),[
            'name' => 'test 1',
            'description' => 'description test 1'
        ]);

        $updatedCategory = Category::find($category->id);

        $this->assertEquals($updatedCategory->name,'test 1');
        $this->assertEquals($updatedCategory->description,'description test 1');
        $this->assertArrayHasKey('category', $response->json());
        $response->assertJsonMissing(['error']);
        $response->assertStatus(200);
        
    }

    /**
     * Test para probar que se puede borrar una categorias.
     *
     * @return void
     */
    public function test_a_category_can_be_deleted()
    {
        $this->withoutExceptionHandling();

        $token = $this->authenticate(); 

        $category = Category::create([
            'name' => 'category test',
            'description' => 'description test 1'
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'
        ])->delete(route('api.category.delete', $category->id));
         

        $this->assertCount(0, Category::all());
        $response->assertStatus(200);
    }



}
