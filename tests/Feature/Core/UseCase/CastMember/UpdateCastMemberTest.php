<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Exception\EntityNotFoundException;
use Core\UseCase\CastMember\UpdateCastMemberUseCase;
use Core\UseCase\DTO\CastMember\UpdateCastMember\UpdateCastMemberInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateCastMemberTest extends TestCase
{
    
    public function test_update()
    {
        $castMember = CastMember::factory()->create();
  
        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new UpdateCastMemberUseCase($repository);

        $name = "updated name";
        $output = $useCase->execute(new UpdateCastMemberInputDto($castMember->id, $name));

        $this->assertEquals($castMember->id, $output->id);
        $this->assertEquals($name, $output->name);

        $this->assertDatabaseHas("cast_members", [
            "id" => $castMember->id,
            "name" => $name
        ]);        
    }

    public function test_update_not_found()
    {
        $this->expectException(EntityNotFoundException::class);
        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new UpdateCastMemberUseCase($repository);
        $useCase->execute(new UpdateCastMemberInputDto("123", ""));   
    }
}
