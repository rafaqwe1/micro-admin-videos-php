<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Exception\EntityNotFoundException;
use Core\UseCase\CastMember\ListCastMemberUseCase;
use Core\UseCase\DTO\CastMember\ListCastMember\ListCastMemberInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCastMemberTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testFindById()
    {
        $castMember = CastMember::factory()->create();

        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new ListCastMemberUseCase($repository);
        $output = $useCase->execute(new ListCastMemberInputDto($castMember->id));

        $this->assertEquals($castMember->id, $output->id);
        $this->assertEquals($castMember->name, $output->name);
        $this->assertEquals($castMember->type, $output->type);
    }

    public function testNotFound()
    {
        $this->expectException(EntityNotFoundException::class);
        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new ListCastMemberUseCase($repository);
        $useCase->execute(new ListCastMemberInputDto("fake_id"));
    }
}
