<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyTaskCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    


    public function testItDestroyTask(){

        $this->artisan('task:destroy', ['id'=>'12']);

        $resultAsText = Artisan::output();

        //missing el que fa es veure si existeix a la table, el camp amb el valor passat.
        $this->assertDatabaseMissing('tasks',['id'=>'12']);

        $this->assertContains('Task has been deleted to database succesfully',$resultAsText);
  }

  public function testItAsksForAtaskIdAndThenDeleteTask(){
      $command = Mockery::mock('App\Console\Commands\DestroyTaskCommand[ask]');

      $command->shouldReceive('ask')
          ->once()
          ->with('Event id?')
          -> andReturn('35');

      $this->app['Illuminate\Contracts\Console\Kernel']->registerCommand($command);

      $this->artisan('task:destroy');


      //missing el que fa es veure si existeix a la table, el camp amb el valor passat.
      $this->assertDatabaseMissing('tasks',['id'=>'12']);
      $resultAsText = Artisan::output();

      $this->assertContains('Task has been deleted to database succesfully',$resultAsText);


  }




}