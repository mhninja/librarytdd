<?php

namespace Tests\Unit;

use App\Book;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    Public function an_author_id_is_recorded()
    {        
        Book::create([
            'title' => 'Cool Title',
            'author_id' => 1
        ]);

        $this->assertCount(1, Book::all());
    }
}
