<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
      parent::setUp();

      $this->thread = factory('App\Thread')->create();
    }

    /** @test */
    public function a_user_can_browse_threads()
    {
        $response = $this->get('/threads')
          ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $response = $this->get($this->thread->path())
          ->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_replies_that_are_associated_with_a_thread()
    {
        $reply = factory('App\Reply')
          ->create(['thread_id' => $this->thread->id]);

        $response = $this->get($this->thread->path())
          ->assertSee($reply->body);
    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_tag()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
        $threadNotInChannel = create('App\Thread');

        $response = $this->get('/threads/' . $channel->slug)
          ->assertSee($threadInChannel->title)
          ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    public function a_user_can_filter_threads_by_any_username()
    {
      $this->signIn(create('App\User', ['name' => 'JohnDoe']));

      $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
      $threadNotByJohn = create('App\Thread');

      $this->get('threads?by=JohnDoe')
        ->assertSee($threadByJohn->title)
        ->assertDontSee($threadNotByJohn->title);
    }




}
