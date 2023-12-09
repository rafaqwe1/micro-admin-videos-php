<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\UseCase\CastMember\ListCastMembersUseCase;
use Core\UseCase\DTO\CastMember\ListCastMembers\ListCastMembersInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCastMembersTest extends TestCase
{
    public function test_list_empty()
    {
        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new ListCastMembersUseCase($repository);
        $response = $useCase->execute(new ListCastMembersInputDto());

        $this->assertEmpty($response->items);
    }

    public function test_list_all()
    {
        CastMember::factory(20)->create();

        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new ListCastMembersUseCase($repository);
        $response = $useCase->execute(new ListCastMembersInputDto());

        $this->assertCount(15, $response->items);
        $this->assertEquals(20, $response->total);
    }
}
