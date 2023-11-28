<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class GenreApiTest extends TestCase
{
    protected $endpoint = "/api/genres";

    public function test_list_empty_genres()
    {
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "meta" => [
                "total",
                'current_page',
                "last_page",
                "first_page",
                "per_page",
                "to",
                "from"
            ]
        ]);
        $response->assertJsonCount(0, "data");
    }

    public function test_list_all_genres()
    {
        Genre::factory()->count(30)->create();
        $response = $this->getJson($this->endpoint);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "meta" => [
                "total",
                'current_page',
                "last_page",
                "first_page",
                "per_page",
                "to",
                "from"
            ]
        ]);
        $response->assertJsonCount(15, "data");
    }

    public function test_list_paginate_genres()
    {
        Genre::factory()->count(25)->create();
        $response = $this->getJson($this->endpoint . "?page=2");
        $response->assertStatus(200);
        $this->assertEquals(2, $response['meta']['current_page']);
        $this->assertEquals(25, $response['meta']['total']);
        $response->assertJsonCount(10, "data");
    }

    public function test_list_genre_not_found()
    {
        $response = $this->getJson($this->endpoint . "/123");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_list_genre_found()
    {
        $genre = Genre::factory()->create();
        $response = $this->getJson($this->endpoint . "/{$genre->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);

        $this->assertEquals($genre->id, $response['data']['id']);
    }

    public function test_validations_store()
    {
        $response = $this->postJson($this->endpoint, []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name'
            ]
        ]);

        $response = $this->postJson($this->endpoint, ['name' => 'a']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->postJson($this->endpoint, [
            'name' => "genre",
            "categories_id" => ["fake_id"]
        ]);
        
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_store()
    {
        $categories = Category::factory()->count(2)->create()->pluck("id")->toArray();
        $data = [
            'name' => "New Genre",
            'categories_id' => $categories
        ];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertDatabaseCount("category_genre", 2);

        $response = $this->postJson($this->endpoint, [
            'name' => 'new genre',
            'is_active' => false
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals('new genre', $response['data']['name']);
        $this->assertFalse($response['data']['is_active']);

        $this->assertDatabaseHas("genres", [
            "id" => $response['data']['id'], 
            "is_active" => $response['data']['is_active']
        ]);
    }

    public function test_not_found_update()
    {
        $data = [
            'name' => "updated name"
        ];
    
        $response = $this->putJson($this->endpoint . "/{fake_id}", $data);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJsonStructure(["message"]);
    }

    public function test_validations_update()
    {
        $genre = Genre::factory()->create();
    
        $response = $this->putJson($this->endpoint . "/{$genre->id}", []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            "message",
            "errors" => [
                "name"
            ]
        ]);
    }

    public function test_update()
    {
        $genre = Genre::factory()->create();
        $data = [
            'name' => "updated name"
        ];
        
        $response = $this->putJson($this->endpoint . "/{$genre->id}", $data);
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'is_active',
                'created_at'
            ]
        ]);

        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertDatabaseHas("genres", [
            "id" => $genre->id, 
            "name" => $data['name']
        ]);
    }

    public function test_not_found_delete()
    {
        $response = $this->delete($this->endpoint . "/fake_id");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_delete()
    {
        $genre = Genre::factory()->create();
        $response = $this->delete($this->endpoint . "/{$genre->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted("genres", [
            "id" => $genre->id
        ]);
    }
}
