<?php

namespace Tests\Feature\Http\Controllers;


use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;

class AuthController extends TestCase
{

    use WithFaker, RefreshDatabase;

    /**
     * Test if the password recovery page doesn't exist.
     *
     * @return void
     */
    public function test_routes_password_confirm_doesnt_exist()
    {
        $this->get('password/confirm')->assertStatus(404);
        $this->post('password/confirm')->assertStatus(404);
    }


    /**
     * Test if the password recovery page doesn't exist.
     *
     * @return void
     */
    public function test_routes_password_restart_doesnt_exist()
    {
        $this->post('password/email')->assertStatus(404);
        $this->post('password/reset')->assertStatus(404);
        $this->get('password/reset')->assertStatus(404);
        $this->get('password/reset/token099292929')->assertStatus(404);
    }

    /**
     * Test if the user can view the register page.
     * @return void
     */
    public function test_auth_user_can_view_register_page()
    {
        $this->get('/register')->assertStatus(200)->assertViewIs('auth.register');
    }



    /**
     * Test user registration with inccorrect data.
     * @return void
     */
    public function test_user_registration_with_incorrect_data()
    {
        $user = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
        ];

        $this->post('/register', $user)
            ->assertStatus(302)
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }


    /**
     * Test user registration with correct data.
     * @return void
     */
    public function test_user_registration_with_correct_data()
    {
        $password = $this->faker->password(8, 20);

        $user = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'password' => $password,
            'password_confirmation' => $password,
        ];

        $this->post('/register', $user)
            ->assertStatus(302)
            ->assertRedirect(route('home'));

        $this->assertDatabaseHas('users', [
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    }


    /**
     * Test if the user can view the login page.
     * @return void
     */
    public function test_auth_user_can_view_login_page()
    {
        $this->get('/login')->assertStatus(200)->assertViewIs('auth.login');
    }


    /**
     * Test user login with incorrect data.
     * @return void
     */
    public function test_auth_user_login_with_incorrect_data()
    {
        $email = $this->faker->email;
        $passwordTextPlain = $this->faker->password(8, 20);
        $passwordHash = bcrypt($passwordTextPlain);

        $user = factory(User::class)->create(["email" => $email, "password" => $passwordHash]);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $email,
            'password' => $passwordHash,
        ]);

        $this->post('/login', ["email" => $this->faker->email(), "password" => bcrypt( $this->faker->password(8, 20) ) ])
            ->assertStatus(302)
            ->assertSessionHasErrors(['email']);
    }


    /**
     * Test user login with correct data.
     * @return void
     */
    public function test_auth_user_login_with_correct_data()
    {
        $email = $this->faker->email;
        $passwordTextPlain = $this->faker->password(8, 20);
        $passwordHash = bcrypt($passwordTextPlain);

        $user = factory(User::class)->create(["email" => $email, "password" => $passwordHash]);

        $this->assertDatabaseHas('users', [
            'name' => $user->name,
            'email' => $email,
            'password' => $passwordHash,
        ]);

        $this->post('/login', ["email" => $email, "password" => $passwordTextPlain])
            ->assertStatus(302)
            ->assertRedirect(route('home'));
    }
}
