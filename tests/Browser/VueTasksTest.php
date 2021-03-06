<?php

namespace Tests\Browser;

use App\Task;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\VueTasksPage;
use Tests\DuskTestCase;

/**
 * Class VueTasksTest.
 */
class VueTasksTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Set up tests.
     */
    public function setUp()
    {
        parent::setUp();
        initialize_task_permissions();
//        Artisan::call('passport:install');
//        $this->withoutExceptionHandling();
    }

    /**
     * Create user.
     *
     * @return mixed
     */
    protected function createUser()
    {
        return factory(User::class)->create();
    }

    /**
     * @param $browser
     *
     * @return mixed
     */
    protected function login($browser)
    {
        $user = factory(User::class)->create();
        $user->assignRole('task-manager');
        $user->assignRole('users-manager');
        $browser->loginAs($user);

        return $user;
    }

    /**
     * List tasks.
     *
     * @test
     *
     * @return void
     */
    public function list_tasks()
    {
        $this->browse(function (Browser $browser) {
            $tasks = factory(Task::class, 2)->create();
            $browser->maximize();

            $this->login($browser);

            $browser->visit(new VueTasksPage())
                    ->seeTitle('Tasks')
                    ->assertVisible('.table')
                    ->dontSeeAlert('Tasques new')
//                    ->seeBox('Tasques new');
                    ->assertVue('tasks', $tasks->toArray(), '@tasks')
                    ->seeTasks($tasks);
        });
    }

    /**
     * Reload.
     *
     * @test
     * @group test
     */
    public function reload()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $tasks = factory(Task::class, 5)->create();
            $browser->maximize();
            $browser->visit(new VueTasksPage())
                ->assertVue('tasks', $tasks->toArray(), '@tasks')
                ->seeTasks($tasks);

            $task = factory(Task::class)->create();

            //
            $browser->reload()
                ->assertVue('loading', true, '@tasks')
                ->pause(2000)
                ->assertVue('loading', false, '@tasks')
                ->seeTask($task);
        });
    }

    /**
     * See completed tasks.
     *
     * @test
     */
    public function see_completed_tasks()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $user = factory(User::class)->create();
            $tasks = [Task::create(['name' => 'pendeeeent', 'description' => 'Per al test', 'user_id' => $user->id, 'completed' => false]),
                Task::create(['name' => 'un altre nom', 'description' => 'Per al test pero segona tasca', 'user_id' => $user->id, 'completed' => false]), ];

            $completed_tasks = [Task::create(['name' => 'tercer nom', 'description' => 'Per al test complet', 'user_id' => $user->id, 'completed' => true]),
                Task::create(['name' => 'quartnom', 'description' => 'Per al test pero segona tasca completa', 'user_id' => $user->id, 'completed' => true]), ];

            $browser->maximize();
            $browser->visit(new VueTasksPage())
                ->applyCompletedFilter()
                ->seeTasks($completed_tasks);
        });
    }

    /**
     * See pending tasks.
     *
     * @test
     */
    public function see_pending_tasks()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $user = factory(User::class)->create();
            $tasks = [Task::create(['name' => 'ascaaa', 'description' => 'Per al test', 'user_id' => $user->id, 'completed' => false]),
                Task::create(['name' => 'prova', 'description' => 'Per al test pero segona tasca', 'user_id' => $user->id, 'completed' => false]), ];

            $completed_tasks = [Task::create(['name' => 'completa', 'description' => 'Per al test complet', 'user_id' => $user->id, 'completed' => true]),
                Task::create(['name' => 'tambeestafet', 'description' => 'Per al test pero segona tasca completa', 'user_id' => $user->id, 'completed' => true]), ];

            $browser->maximize();
            $browser->visit(new VueTasksPage())
                ->applyPendingFilter()
                ->seeTasks($tasks)
                ->dontSeeTasks($completed_tasks);
        });
    }

    /**
     * Add task
     *No el faig perque s'hauria de tocar codi JS per poder aplicar totes les funcionalitats.
     * //     */
//    public function add_task()
//    {
//        $this->browse(function (Browser $browser) {
//            $this->login($browser);
//            $browser->maximize();
//            $task = factory(Task::class)->make();
//            $browser->visit(new VueTasksPage())
//                ->store_task($task)
//                ->assertVue('adding', true, '@tasks') //  Test state
//                ->waitForSuccessfulCreateAlert($task) // TODO
//                ->assertVue('adding', false, '@tasks') //  Test state
//                ->seeTask($task);
//        });
//    }

    /**
     * Edit task.
     *
     * No el faig perque s'hauria de tocar codi JS per poder aplicar totes les funcionalitats.
     */
//    public function edit_task()
//    {
//        $this->browse(function (Browser $browser) {
//            $this->login($browser);
//            $browser->maximize();
//            $oldTask = factory(Task::class)->create();
//            $newtask = factory(Task::class)->make();
//            $newtask->id = $oldTask->id;
//            $browser->visit(new VueTasksPage())
//                ->update_task($newtask)
//                ->assertVue('submit_editing', true, '@tasks') //  Test state
//                ->waitForSuccessfulEditAlert($newtask) // TODO
//                ->assertVue('submit_editing', false, '@tasks') //  Test state
//                ->seeTask($newtask)
//                ->dontSeeTask($oldTask);
//        });
//    }

    /**
     * Cancel edit.
     *
     * No el faig perque s'hauria de tocar codi JS per poder aplicar totes les funcionalitats.
     * //     */
//    public function cancel_edit()
//    {
//        $this->browse(function (Browser $browser) {
//            $this->login($browser);
//            $browser->maximize();
//            $oldTask = factory(Task::class)->create();
//            $newtask = factory(Task::class)->make();
//            $newtask->id = $oldTask->id;
//            $browser->visit(new VueTasksPage())
//                ->edit_task($newtask)
//                ->assertVue('editing', true, '@tasks') //  Test state
//                ->cancel_update()
//                ->assertVue('editing', false, '@tasks') //  Test state
//                ->seeTask($oldTask)
//                ->dontSeeTask($newtask);
//        });
//    }

    /**
     * Delete task.
     *
     * @test
     */
    public function delete_task()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $browser->maximize();
            $user = factory(User::class)->create();
            $tasks = [Task::create(['name' => 'fhjoirfhh', 'description' => 'Per al test', 'user_id' => $user->id, 'completed' => false]),
                Task::create(['name' => 'ieeek', 'description' => 'Per al test pero segona tasca', 'user_id' => $user->id, 'completed' => false]), ];
            $task = $tasks[0];
            $browser->visit(new VueTasksPage())
                ->assertSee($tasks[0]->name)
                ->pause(1000)
                ->press('#delete-task-'.$tasks[0]->id)
                ->pause(1000)
                ->press('#delete-task')
                ->pause(1000)
                ->dontSeeTask($task);
        });
    }

    /**
     * @test
     * Cancel delete task
     */
    public function cancel_delete_task()
    {
        $this->browse(function (Browser $browser) {
            $this->login($browser);
            $browser->maximize();
            $user = factory(User::class)->create();
            $task = Task::create(['name' => 'quim', 'description' => 'Per al test', 'user_id' => $user->id, 'completed' => false]);
            $browser->visit(new VueTasksPage())
                ->press('#delete-task-'.$task->id)
                    ->pause(1000)
                ->cancel_delete()
                ->seeTask($task);
        });
    }

    /*
     * Toogle complete task.
     * No el faig perque s'hauria de tocar codi JS per poder aplicar totes les funcionalitats.

     */
//    public function toogle_complete_task()
//    {
//        $this->browse(function (Browser $browser) {
//            $this->login($browser);
//            $browser->maximize();
//            $task = factory(Task::class)->create();
//            $browser->visit(new VueTasksPage())
//                ->toogle_complete($task)
//                ->assertVue('toogle_completion', true, '@tasks') //  Test state
//                ->waitForCompletedTask() // TODO
//                ->assertVue('toogle_completion', false, '@tasks') //  Test state
//                ->seeCompletedTask($task) //TODO
//                ->toogle_complete($task)
//                ->assertVue('toogle_completion', true, '@tasks') //  Test state
//                ->waitForUnCompletedTask() // TODO
//                ->assertVue('toogle_completion', false, '@tasks') //  Test state
//                ->seeUnCompletedTask($task); //TODO
//        });
//    }
}
