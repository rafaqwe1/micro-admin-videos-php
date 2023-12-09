<?php

namespace Tests\Feature\Core\UseCase\CastMember;

use App\Models\CastMember;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Exception\EntityNotFoundException;
use Core\UseCase\CastMember\DeleteCastMemberUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteCastMemberTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_delete()
    {
        $castMember = CastMember::factory()->create();

        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new DeleteCastMemberUseCase($repository);

        $output = $useCase->execute($castMember->id);
        $this->assertTrue($output->success);
        $this->assertSoftDeleted("cast_members", ["id" => $castMember->id]);
    }

    public function test_not_found()
    {
        $this->expectException(EntityNotFoundException::class);
        $repository = new CastMemberEloquentRepository(new CastMember());
        $useCase = new DeleteCastMemberUseCase($repository);

        $useCase->execute("fake_id");
    }
}
