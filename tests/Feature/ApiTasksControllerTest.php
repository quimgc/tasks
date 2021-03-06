<?php

namespace Tests\Feature;

use App\Task;
use App\User;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ApiTasksControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Set up tests.
     */
    public function setUp()
    {
        parent::setUp();

        initialize_task_permissions();
        create_user();
        Artisan::call('passport:install');

        //   App::setLocale('en');
       //$this->withoutExceptionHandling();
    }

    /**
     * @return mixed
     */
    protected function loginAndAuthorize()
    {
        $user = factory(User::class)->create();
        $user->assignRole('task-manager');
        $this->actingAs($user, 'api');

        return $user;
    }

    /**
     * @test
     */
    public function can_list_tasks()
    {
        factory(Task::class, 3)->create();

        $this->loginAndAuthorize();

        $response = $this->json('GET', '/api/v1/tasks');

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
    public function cannot_add_task_if_no_name_provided()
    {
        $this->loginAndAuthorize();

        //EXECUTE
        $response = $this->json('POST', '/api/v1/tasks');

        //Assert
        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function cannot_add_task_if_not_logged()
    {
        $faker = Factory::create();

        //EXECUTE

        $response = $this->json('POST', '/api/v1/tasks', [
            'name' => $name = $faker->word,
        ]);

        //Assert
        $response->assertStatus(401);
    }

    /**
     * @test
     */
//    public function can_add_a_task()
//    {
//        // PREPARE
//        $faker = Factory::create();
//        $user = $this->loginAndAuthorize();
//
//        // EXECUTE
//        $response = $this->json('POST', '/api/v1/tasks', [
//            'name'    => $name = $faker->word,
//            'user_id' => $user->id,
//        ]);
//
//        // ASSERT
//        $response->assertSuccessful();
//
//        $this->assertDatabaseHas('tasks', [
//            'name' => $name,
//        ]);
//
////        $response->dump();
//
//        $response->assertJson([
//            'name' => $name,
//        ]);
//    }

    /**
     * @test
     */

    /**
     * @test
     */
    public function can_delete_task()
    {
        $task = factory(Task::class)->create();
        $this->loginAndAuthorize();

        $response = $this->json('DELETE', '/api/v1/tasks/'.$task->id);

        $response->assertSuccessful();

        $this->assertDatabaseMissing('tasks', [
            'id'   => $task->id,
            'name' => $task->name,
        ]);
    }

    /**
     * @test
     */
    public function cannot_delete_unexisting_task()
    {
        $this->loginAndAuthorize();

        $response = $this->json('DELETE', '/api/v1/tasks/1');

        $response->assertStatus(404);
    }

    /**
     * @test
     */
    public function can_edit_task()
    {
        // PREPARE
        $task = factory(Task::class)->create();

        $this->loginAndAuthorize();

        // EXECUTE
        $response = $this->json('PUT', '/api/v1/tasks/'.$task->id, [
            'name' => $newName = 'NOU NOM',
        ]);

        // ASSERT
        $response->assertSuccessful();

        $this->assertDatabaseHas('tasks', [
            'id'   => $task->id,
            'name' => $newName,
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id'   => $task->id,
            'name' => $task->name,
        ]);

        $response->assertJson([
            'id'   => $task->id,
            'name' => $newName,
        ]);
    }
}
