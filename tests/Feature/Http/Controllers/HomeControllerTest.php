<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class HomeControllerTest extends TestCase
{

    use WithFaker, RefreshDatabase;

    /**
     * Test home page without login.
     *
     * @return void
     */
    public function test_home_page_whithout_login()
    {
        $response = $this->get( route('home') );

        $response->assertStatus( 302 )
                ->assertRedirect( route('login') );
    }

    /**
     * Test home page is accessible.
     *
     * @return void
     */
    public function test_home_page_with_user_logged()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user)->get( route('home') );

        $response->assertStatus(200);
    }
}
