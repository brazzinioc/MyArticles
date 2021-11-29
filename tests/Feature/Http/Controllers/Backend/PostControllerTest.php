<?php

namespace Tests\Feature\Http\Controllers\Backend;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use App\{User, Post};

class PostControllerTest extends TestCase
{
    use WithFaker, RefreshDatabase;


    /**************************************
    *   AUTHENTICATION
    ***************************************/

    /**
     * Test access denied when user is not authenticated.
     *
     * @return void
     */
    public function test_access_denied_when_user_is_not_authenticated()
    {
        // Index
        $response = $this->get( route('posts.index') );
        $response->assertStatus(302)
            ->assertRedirect( route('login') );

        // Create
        $response = $this->get( route('posts.create') );
        $response->assertStatus(302)
            ->assertRedirect( route('login') );

        // Store
        $response = $this->post( route('posts.store'), [] );
        $response->assertStatus(302)
            ->assertRedirect( route('login') );

        // Edit
        $response = $this->get( route('posts.edit', 1) );
        $response->assertStatus(302)
            ->assertRedirect( route('login') );

        // Update
        $response = $this->put( route('posts.update', 1), [] );
        $response->assertStatus(302)
            ->assertRedirect( route('login') );

        // Destroy
        $response = $this->delete( route('posts.destroy', 1) );
        $response->assertStatus(302)
            ->assertRedirect( route('login') );
    }



    /**************************************
    *   POSTS INDEX
    ***************************************/

    /**
     * Test index route is working. When database has posts.
     *
     * @return void
     */
    public function test_posts_index_with_data()
    {
        $user = factory(User::class)->create();
        $posts = factory(Post::class, 20)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                        ->get( route('posts.index') );

        $response->assertStatus(200)
                ->assertViewIs('posts.index')
                ->assertViewHas('posts');

        $this->assertInstanceOf(Collection::class, $response->viewData('posts'));
    }

    /**
     * test index route is working. When database has no posts.
     *
     * @return void
     */
    public function test_posts_index_without_data()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                        ->get( route('posts.index') );

        $response->assertStatus(200)
                ->assertViewIs('posts.index')
                ->assertViewHas('posts')
                ->assertSee('Aún no se publicó ningún artículo. Sé el primero en pubicar.');
    }



    /**************************************
    *   POSTS CREATE
    ***************************************/

    /**
     * Test create route is working.
     *
     * @return void
     */
    public function test_posts_create()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                        ->get( route('posts.create') );

        $response->assertStatus(200)
                ->assertViewIs('posts.create')
                ->assertViewHas('post')
                ->assertSee('Título')
                ->assertSee('Imagen')
                ->assertSee('Contenido')
                ->assertSee('Contenido Embebido');
    }



    /**************************************
    *   POSTS STORE
    ***************************************/

    /**
     * Test store route validations.
     *
     * @return void
     */
    public function test_posts_store_validations()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                        ->from( route('posts.create') )
                        ->post( route('posts.store'), [] );

        $response->assertStatus(302)
                ->assertRedirect( route('posts.create') )
                ->assertSessionHasErrors(['titulo', 'contenido']);
    }


    /**
     * Test store route is working. With valid data.
     *
     * @return void
     */
    public function test_posts_store_with_all_valid_data()
    {
        Storage::fake('posts');

        $user = factory(User::class)->create();
        $postImg = UploadedFile::fake()->image('post.jpg');

        $post = [
            "titulo" => $this->faker->sentence(35),
            "contenido" => $this->faker->text(500),
            "iframe" => $this->faker->text(200),
            "imagen" => $postImg,
        ];

        $response = $this->actingAs($user)
                        ->post( route('posts.store'), $post );

        $response->assertStatus(302)
                ->assertRedirect( route('posts.create') )
                ->assertSessionHas('status', 'Creado con éxito');

        $this->assertDatabaseHas('posts', [
                                            'title' => $post['titulo'],
                                            'body' => $post['contenido'],
                                            'iframe' => $post['iframe'],
                                            'image' =>  "posts/" . $postImg->hashName(),
                                            'user_id' => $user->id
                                        ]);

        Storage::disk('public')->assertExists("posts/" . $postImg->hashName());
    }


    /**
     * Test store route is working. Without image.
     *
     * @return void
     */
    public function test_posts_store_without_image()
    {

        $user = factory(User::class)->create();

        $post = [
            "titulo" => $this->faker->sentence(35),
            "contenido" => $this->faker->text(500),
            "iframe" => $this->faker->text(200),
            "imagen" => null,
        ];

        $response = $this->actingAs($user)
                        ->post( route('posts.store'), $post );

        $response->assertStatus(302)
                ->assertRedirect( route('posts.create') )
                ->assertSessionHas('status', 'Creado con éxito');

        $this->assertDatabaseHas('posts', [
                                            'title' => $post['titulo'],
                                            'body' => $post['contenido'],
                                            'iframe' => $post['iframe'],
                                            'image' =>  null,
                                            'user_id' => $user->id
                                        ]);
    }





    /**************************************
    *   POSTS EDIT
    ***************************************/

    /**
    * Test edit route show 404 page when post not found.
    * @return void
    */
    public function test_post_edit_with_id_inexistent()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                        ->from('posts.index')
                        ->get( route('posts.edit', 9999) );

        $response->assertStatus(404);
}



    /**
     * Test edit route show post data when post found and post.
     * @return void
     */
    public function test_post_edit_with_id_existent()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                        ->from('posts.index')
                        ->get( route('posts.edit', $post->id) )
                        ->assertStatus(200);

        $response->assertViewIs('posts.edit')
                ->assertViewHas('post')
                ->assertSee($post->title)
                ->assertSee($post->body)
                ->assertSee($post->iframe)
                ->assertSee($post->image);
    }



    /**************************************
    *   POSTS EDIT
    ***************************************/

    /**
     * Test update route validations.
     *
     * @return void
     */
    public function test_post_update_validations()
    {
        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
                        ->from( route('posts.edit', $post->id) )
                        ->put( route('posts.update', $post->id), [] );

        $response->assertStatus(302)
                ->assertRedirect( route('posts.edit', $post->id) )
                ->assertSessionHasErrors(['titulo', 'contenido']);
    }


    /**
     * Test update route is working. With valid data.
     *
     * @return void
     */
    public function test_post_update_with_valid_data()
    {
        Storage::fake('posts');

        $postImg = UploadedFile::fake()->image('post.jpg');

        $user = factory(User::class)->create();
        $post = factory(Post::class)->create( [ 'user_id' => $user->id, 'image' => "posts/" . $postImg->hashName() ] );

        $newImg = UploadedFile::fake()->image('update-post.jpg');

        $newPost = [
            "titulo" => $this->faker->sentence(35),
            "contenido" => $this->faker->text(500),
            "iframe" => $this->faker->text(200),
            "imagen" => $newImg,
        ];

        $response = $this->actingAs($user)
                        ->put( route('posts.update', $post->id), $newPost );

        $response->assertStatus(302)
                ->assertRedirect( route('posts.edit', $post->id) )
                ->assertSessionHas('status', 'Actualizado con éxito');


        $this->assertDatabaseHas('posts', [
                                            'id' => $post->id,
                                            'title' => $newPost['titulo'],
                                            'body' => $newPost['contenido'],
                                            'iframe' => $newPost['iframe'],
                                            'image' =>  "posts/" . $newImg->hashName(),
                                            'user_id' => $user->id
                                        ]);

        Storage::disk('public')->assertMissing("posts/" . $postImg->hashName());
        Storage::disk('public')->assertExists("posts/" . $newImg->hashName());
    }



    /**************************************
    *   POSTS DELETE
    ***************************************/

    /**
     * Test delete route with id post inexistent.
     *  @return void
     */
    public function test_post_delete_with_id_inexistent()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)
                        ->from('posts.index')
                        ->delete( route('posts.destroy', 9999) );

        $response->assertStatus(404);
    }


    /**
     * Test delete route with id post inexistent.
     *  @return void
     */
    public function test_post_delete_with_id_existent()
    {
        Storage::fake('posts');

        $postImg = UploadedFile::fake()->image('post.jpg');

        $user = factory(User::class)->create();
        $post = factory(Post::class)->create(['user_id' => $user->id, 'image' => "posts/" . $postImg->hashName()]);

        $response = $this->actingAs($user)
                        ->from('posts.index')
                        ->delete( route('posts.destroy', $post->id) );

        $response->assertStatus(302)
                ->assertRedirect( route('posts.index') )
                ->assertSessionHas('status', 'Eliminado con éxito');

        Storage::assertMissing("posts/" . $postImg->hashName());
        $this->assertDatabaseMissing('posts', ['id' => $post->id ] );
    }
}

