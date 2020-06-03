<?php

namespace Tests\Feature;

use App\Book;
use App\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookManagementTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_book_can_be_added_to_the_library()
    {        
        $response = $this->post('/books', $this->bookDetail());

        $book = Book::first();
        
        $this->assertCount(1, Book::all());
        $response->assertRedirect($book->path());

    }

     /** @test */
     public function a_title_is_required()
     {        
        $response = $this->post('/books', [
            'title' => '',
            'author' => 'Helen',
        ]);

        $response->assertSessionHasErrors('title');
     } 

     /** @test */
     public function an_author_is_required()
     {        
        $response = $this->post('/books', array_merge($this->bookDetail(), ['author_id' => '']));

        $response->assertSessionHasErrors('author_id');
     }

     /** @test */
     public function a_book_can_be_updated()
     {        
        $this->post('/books', $this->bookDetail());
            
        $book = Book::first();
            
        $response = $this->patch($book->path(), [
            'title' => 'New Title',
            'author_id' => 'New Author'
        ]);
            
        $this->assertEquals('New Title', Book::first()->title);
        $this->assertEquals(2, Book::first()->author_id);
        $response->assertRedirect($book->fresh()->path());
    }
    
    /** @test */
    public function a_book_can_be_deleted()
    {
        $this->post('/books', $this->bookDetail());
            
        $book = Book::first();
        $this->assertCount(1, Book::all());
            
        $response = $this->delete($book->path());

        $this->assertCount(0, Book::all());
        $response->assertRedirect('/books');
    }

    /** @test */
    public function a_new_author_automatically_added()
    {
        $this->withoutExceptionHandling();

        $this->post('/books', [
            'title' => 'Cool Title',
            'author_id' => 'Helen',
        ]);

        $book = Book::first();
        $author = Author::first();   

        $this->assertEquals($author->id, $book->author_id);
        $this->assertCount(1, Author::all());

    }
    
    private function bookDetail()
    {
        return [
            'title' => 'Cool Book Title',
            'author_id' => 'Helen',
        ];
    }
 }