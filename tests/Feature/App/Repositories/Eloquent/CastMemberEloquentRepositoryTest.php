<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\CastMember as Model;
use App\Models\CastMember;
use App\Repositories\Eloquent\CastMemberEloquentRepository;
use Core\Domain\Entity\CastMember as EntityCastMember;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CastMemberRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Core\Domain\ValueObject\Uuid;
use Tests\TestCase;
use Throwable;

class CastMemberEloquentRepositoryTest extends TestCase
{

    /**
     * @var CastMemberEloquentRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CastMemberEloquentRepository(new Model());
    }

    public function testInsert()
    {
        $entity = new EntityCastMember(
            name: "teste",
            type: CastMemberType::ACTOR
        );

        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(CastMemberRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCastMember::class, $response);

        $this->assertDatabaseHas('cast_members', [
            'id' => $entity->id(),
            'name' => $entity->name,
            'type' => $entity->type->value
        ]);
    }

    public function testFindById()
    {
        $castMember = Model::factory()->create();

        $response = $this->repository->findById($castMember->id);

        $this->assertInstanceOf(EntityCastMember::class, $response);
        $this->assertEquals($castMember->id, $response->id());
    }

    public function testFindByIdNotFound()
    {
        try {
            $this->repository->findById("fakevalue");
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testFindAll()
    {
        Model::factory()->count(10)->create();

        $response = $this->repository->findAll();
        $this->assertCount(10, $response);
    }

    public function testPaginate()
    {
        Model::factory()->count(20)->create();
        $response = $this->repository->paginate();

        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertCount(15, $response->items());
    }

    public function testPaginateEmpty()
    {
        $response = $this->repository->paginate();
        $this->assertInstanceOf(PaginationInterface::class, $response);
        $this->assertEmpty($response->items());
    }

    public function testUpdateIdNotFound()
    {
        try {
            $castMember = new EntityCastMember(name: "test", type: CastMemberType::ACTOR);
            $this->repository->update($castMember);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testUpdate()
    {
        $castMemberDb = CastMember::factory()->create();

        $castMember = new EntityCastMember(id: new Uuid($castMemberDb->id), name: 'updated name', type: CastMemberType::DIRECTOR);

        $response = $this->repository->update($castMember);

        $this->assertInstanceOf(EntityCastMember::class, $response);
        $this->assertEquals("updated name", $response->name);
    }

    public function testDeleteNotFound()
    {
        try{
            $this->repository->delete("fake_id");
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testDelete()
    {
        $castMemberDb = CastMember::factory()->create();
        $response = $this->repository->delete($castMemberDb->id);
        $this->assertTrue($response);
    }
}
