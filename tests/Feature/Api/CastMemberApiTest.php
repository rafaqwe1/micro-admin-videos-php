<?php

namespace Tests\Feature\Api;

use App\Models\CastMember;
use Core\Domain\Enum\CastMemberType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class CastMemberApiTest extends TestCase
{

    protected $endpoint = "/api/cast-members";

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_empty_cast_members()
    {
        $response = $this->getJson($this->endpoint);
        $response->assertStatus(200);
        $response->assertJsonCount(0, "data");
    }

    public function test_list_all_cast_members()
    {
        CastMember::factory()->count(30)->create();
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

    public function test_list_paginate_cast_members()
    {
        CastMember::factory()->count(25)->create();
        $response = $this->getJson($this->endpoint . "?page=2");
        $response->assertStatus(200);
        $this->assertEquals(2, $response['meta']['current_page']);
        $this->assertEquals(25, $response['meta']['total']);
        $response->assertJsonCount(10, "data");
    }

    public function test_list_cast_member_not_found()
    {
        $response = $this->getJson($this->endpoint . "/123");
        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_list_cast_member_found()
    {
        $castMember = CastMember::factory()->create();
        $response = $this->getJson($this->endpoint . "/{$castMember->id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);

        $this->assertEquals($castMember->id, $response['data']['id']);
    }

    public function test_validations_store()
    {
        $response = $this->postJson($this->endpoint, []);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'name',
                'type'
            ]
        ]);

        $response = $this->postJson($this->endpoint, ['name' => 'a']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

        $response = $this->postJson($this->endpoint, ['name' => 'test', 'type' => 9]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_store()
    {
        $data = [
            'name' => "New CastMember",
            'type' => CastMemberType::ACTOR->value
        ];

        $response = $this->postJson($this->endpoint, $data);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);
        $this->assertEquals($data['name'], $response['data']['name']);

        $response = $this->postJson($this->endpoint, [
            'name' => 'new cast',
            'type' => CastMemberType::DIRECTOR->value,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals('new cast', $response['data']['name']);
        $this->assertEquals(CastMemberType::DIRECTOR->value, $response['data']['type']);

        $this->assertDatabaseHas("cast_members", [
            "id" => $response['data']['id'], 
            "type" => $response['data']['type']
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
        $castMember = CastMember::factory()->create();
    
    
        $response = $this->putJson($this->endpoint . "/{$castMember->id}", []);
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
        $castMember = CastMember::factory()->create();
        $data = [
            'name' => "updated name"
        ];
        
        $response = $this->putJson($this->endpoint . "/{$castMember->id}", $data);
        $response->assertStatus(Response::HTTP_OK);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);

        $this->assertEquals($data['name'], $response['data']['name']);
        $this->assertDatabaseHas("cast_members", [
            "id" => $castMember->id, 
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
        $castMember = CastMember::factory()->create();
        $response = $this->delete($this->endpoint . "/{$castMember->id}");
        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertSoftDeleted("cast_members", [
            "id" => $castMember->id
        ]);
    }
}
