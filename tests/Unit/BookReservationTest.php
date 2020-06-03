<?php

namespace Tests\Unit;

use App\Book;
use App\User;
use Tests\TestCase;
use App\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_book_can_be_checked_out()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();

        $book->checkout($user);

        $this->assertCount(1, Reservation::all());
        $this->assertEquals($user->id, Reservation::first()->user_id);
        $this->assertEquals($book->id, Reservation::first()->book_id);
        $this->assertEquals(now(), Reservation::first()->check_out_at);
    }
    
    /** @test */
    public function a_book_can_be_returned()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        $book->checkout($user);

        $book->checkin($user);

        $this->assertCount(1, Reservation::all());
        $this->assertEquals($user->id, Reservation::first()->user_id);
        $this->assertEquals($book->id, Reservation::first()->book_id);
        $this->assertNotNull(Reservation::first()->check_in_at);
        $this->assertEquals(now(), Reservation::first()->check_in_at);
    }

    // If not checkout, then exception
    /** @test */
    public function if_not_checked_out_exception_is_thrown()
    {
        $this->expectException(\Exception::class);

        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();

        $book->checkIn($user);

    }

    // A user can check out a book twice
    /** @test */
    public function a_user_can_check_out_a_book_twice()
    {
        $book = factory(Book::class)->create();
        $user = factory(User::class)->create();
        $book->checkout($user);        
        $book->checkin($user);

        $book->checkout($user);

        $this->assertCount(2, Reservation::all());
        $this->assertEquals($user->id, Reservation::find(2)->user_id);
        $this->assertEquals($book->id, Reservation::find(2)->book_id);
        $this->assertNull(Reservation::find(2)->check_in_at);
        $this->assertEquals(now(), Reservation::find(2)->check_out_at);

        $book->checkin($user);

        $this->assertCount(2, Reservation::all());
        $this->assertEquals($user->id, Reservation::find(2)->user_id);
        $this->assertEquals($book->id, Reservation::find(2)->book_id);
        $this->assertNotNull(Reservation::find(2)->check_in_at);
        $this->assertEquals(now(), Reservation::find(2)->check_in_at);
    }

}
