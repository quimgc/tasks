<?php

namespace Tests\Feature;

use App\Task;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiUserControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up tests.
     */
    public function setUp()
    {
        parent::setUp();

        initialize_task_permissions();
        //create_user();
        //Artisan::call('passport:install');
        //   App::setLocale('en');
      //$this->withoutExceptionHandling();
    }

    public function loginAsAuthorizedUser()
    {
        $user = factory(User::class)->create();
        $user->assignRole('users-manager');
        $this->actingAs($user, 'api');

        return $user;
    }

    /**
     * @test
     */
    public function can_list_users()
    {
        factory(User::class, 3)->create();

        $this->loginAsAuthorizedUser();

        $response = $this->json('GET', '/api/v1/users');

        $response->assertSuccessful();

        $response->assertJsonStructure([[
            'id',
            'name',
            'created_at',
            'updated_at',
        ]]);
    }

    /**
     * @test
     */
//    public function can_show_a_user()
//    {
//
//        $this->loginAsAuthorizedUser();
//        $user = factory(User::class)->create();
//        //dd($user->only(['id','name']));
//        $response = $this->json('GET', 'api/v1/users/'.$user->id);
//
//        $response->assertSuccessful();
//        $response->assertJsonFragment([
//            'id' => $user->id,
//            'name' => $user->name,
//        ]);

//    }

    /**
     * @test
     */
    public function cannot_add_user_if_no_name_provided()
    {
        $this->loginAsAuthorizedUser();

        //$user = factory(User::class)->create();

        //$this->actingAs($user, 'api');

        //EXECUTE

        $response = $this->json('POST', '/api/v1/users');

        //Assert
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function cannot_add_user_if_not_logged()
    {
        $this->loginAsAuthorizedUser();
        $faker = Factory::create();

        //EXECUTE

        $response = $this->json('POST', '/api/v1/users', [
            'name'     => $name = $faker->word,
            'email'    => $email = $faker->email,
            'password' => $passwd = $faker->password,
        ]);
        // assert
        $response->assertSuccessful();
        $this->assertDatabaseHas('users', [
            'name'  => $name,
            'email' => $email,
        ]);
        $response->assertJson([
            'name'  => $name,
            'email' => $email,
        ]);
    }

    /**
     * @test
     */
    public function can_add_a_user()
    {
        // PREPARE
        $faker = Factory::create();
        $this->loginAsAuthorizedUser();

        // EXECUTE
        $response = $this->json('POST', '/api/v1/users', [
            'name'     => $name = $faker->word,
            'email'    => $email = $faker->email,
            'password' => $password = $faker->password,
        ]);

        // ASSERT
        $response->assertSuccessful();

        try {
            $user = User::findOrFail(json_decode($response->getContent())->id);
            $this->assertTrue(Hash::check($password, $user->password));
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }

        $this->assertDatabaseHas('users', [
            'name'  => $name,
            'email' => $email,
        ]);

        $response->assertJson([
            'name'  => $name,
            'email' => $email,
        ]);
    }

    /**
     * @test
     */
//    public function can_delete_user()
//    {
//
//        $this->loginAsAuthorizedUser();
//
//        $user = factory(Task::class)->create();
//
//        $response = $this->json('DELETE', '/api/v1/users/'.$user);
//
//        $response->assertSuccessful();
//
//        $this->assertDatabaseMissing('users', [
//            'id'   => $user->id,
//        ]);
//    }

    /**
     * @test
     */
    public function can_edit_user()
    {
        $user1 = factory(User::class)->create();
        $user = $this->loginAsAuthorizedUser();

        $response = $this->json('PUT', '/api/v1/users/'.$user1->id, [
            'name' => $newName = 'NOU NOM',
        ]);

        $response->assertSuccessful();

        $this->assertDatabaseHas('users', [
            'id'   => $user1->id,
            'name' => $newName,
        ]);

        $this->assertDatabaseMissing('users', [
            'id'   => $user1->id,
            'name' => $user->name,
        ]);

        $response->assertJson([
            'id'   => $user1->id,
            'name' => $newName,
        ]);
    }
}
