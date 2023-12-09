<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Enum\CastMemberType;
use Core\UseCase\CastMember\CreateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\CreateCastMember\CreateCastMemberInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateCastMemberTest extends TestCase
{
    public function test_insert()
    {
        $castMemberRepository = new CastMemberEloquentRepository(new CastMember());

        $useCase = new CreateCastMemberUseCase($castMemberRepository);

        $output = $useCase->execute(new CreateCastMemberInputDto(name: 'cast member', type: CastMemberType::ACTOR->value));
        $this->assertEquals('cast member', $output->name);
        $this->assertDatabaseHas("cast_members", [
            "id" => $output->id
        ]);
    }
}
