<?php

namespace Tests\Feature;

use App\Task;
use App\User;
use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiTasksControllerTest extends TestCase
{
use RefreshDatabase;

    /**
     * Set up tests.
     *
     */
    public function setUp()
    {
        parent::setUp();
     //   App::setLocale('en');
       //$this->withoutExceptionHandling();
    }

    public function can_list_tasks()
    {
        $tasks = factory(Task::class,4)->create();
        $user = factory(User::class)->create();
        $this->actingAs($user);

        $response = $this->json('GET','/api/tasks');

        $response->assertSuccessful();

        $response->assertJsonStructure([[
            'id',
            'name',
            'created_at',
            'updated_at'
        ]]);

    }

    /**
     * @test
     */
    public function cannot_add_task_if_no_name_provided()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        //EXECUTE

        $response = $this->json('POST','/api/tasks');

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

        $response = $this->json('POST','/api/tasks',[
            'name' => $name = $faker->word
        ]);

        //Assert
        $response->assertStatus(401);
    }
    

    /**
     * @test
     */
    public function can_add_a_task()
    {
        //PREPARE
        $faker = Factory::create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        //EXECUTE

        $response = $this->json('POST','/api/tasks',[
            'name' => $name = $faker->word
        ]);

        //ASSERT

        $response->assertSuccessful();

        $this->assertDatabaseHas('tasks', [
           'name' => $name
        ]);

        $response->assertJson([
           'name'=>$name
        ]);
    }

    /**
     * @test
     */
    /**
     * @test
     */
    public function can_delete_task()
    {
        $task = factory(Task::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->json('DELETE','/api/tasks/' . $task->id);

        $response->assertSuccessful();

        $this->assertDatabaseMissing('tasks',[
            'id' =>  $task->id,
            'name' => $task->name
        ]);
    }

    /**
     * @test
     */
    public function cannot_delete_unexisting_task()
    {
        $user = factory(User::class)->create();

        $this->actingAs($user);

        $response = $this->json('DELETE','/api/tasks/1');

        $response->assertStatus(404);
    }


    /**
     * @test
     */
    public function can_edit_task()
    {
        // PREPARE
        $task = Factory(Task::class)->create();

        $user = factory(User::class)->create();
        $this->actingAs($user,'api');

        // EXECUTE
        $response = $this->json('PUT', '/api/tasks/' . $task->id, [
            'name' => $newName = 'NOU NOM'
        ]);

        // ASSERT
        $response->assertSuccessful();

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => $newName
        ]);

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'name' => $task->name,
        ]);

        $this->assertDatabaseMissing('tasks',[
            'id' =>  $task->id,
            'name' => $newName
        ]);
    }

}