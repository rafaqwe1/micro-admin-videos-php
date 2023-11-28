<?php

use App\Models\Category;
use Tests\TestCase;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Models\Genre as Model;
use App\Models\Genre;
use Core\Domain\Entity\Genre as Entity;
use Core\Domain\Entity\Genre as EntityGenre;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\GenreRepositoryInterface;
use Core\Domain\ValueObject\Uuid;

class GenreEloquentRepositoryTest extends TestCase
{
    /**
     * @var GenreEloquentRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new GenreEloquentRepository(new Model());
    }

    public function testImplementsInterface()
    {
        $this->assertInstanceOf(GenreRepositoryInterface::class, $this->repository);
    }

    public function testInsert()
    {
        $entity = new Entity(
            name: 'new genre'
        );

        $response = $this->repository->insert($entity);
        $this->assertEquals($entity->id(), $response->id());
        $this->assertEquals($entity->name, $response->name);
        $this->assertDatabaseHas("genres", [
            "id" => $entity->id(),
            "name" => $entity->name
        ]);
    }

    public function testInsertDeactivate()
    {
        $entity = new Entity(
            name: 'new genre'
        );
        $entity->deactivate();

        $this->repository->insert($entity);

        $this->assertDatabaseHas("genres", [
            "id" => $entity->id(),
            "is_active" => $entity->isActive
        ]);
    }

    public function testInsertWithRelationships()
    {
        $categories = Category::factory()->count(4)->create();

        $categories->pluck("id")->toArray();

        $entity = new Entity(
            name: "test",
            categoriesId: $categories->pluck("id")->toArray()
        );

        $this->repository->insert($entity);
        $this->assertDatabaseCount("category_genre", 4);
    }

    public function testByIdNotFound()
    {
        $genre = 'fake_value';
        $this->expectException(EntityNotFoundException::class);

        $this->repository->findById($genre);
    }

    public function testFindById()
    {
        $genre = Genre::factory()->create();

        $response = $this->repository->findById($genre->id);
        $this->assertEquals($genre->id, $response->id());
        $this->assertEquals($genre->name, $response->name);
    }

    public function testFindAll()
    {
        Genre::factory()->count(20)->create();

        $response = $this->repository->findAll();
        $this->assertCount(20, $response);
    }

    public function testFindAllEmpty()
    {
        $response = $this->repository->findAll();
        $this->assertEmpty($response);
    }

    public function testFindAllWithFilter()
    {
        Genre::factory()->count(20)->create();
        Genre::factory()->create(['name' => "testFindAllFilter"]);

        $response = $this->repository->findAll("testFindAllFilter");
        $this->assertCount(1, $response);

        $response = $this->repository->findAll();
        $this->assertCount(21, $response);
    }

    public function testPaginate()
    {
        Genre::factory()->count(60)->create();

        $response = $this->repository->paginate();

        $this->assertCount(15, $response->items());
        $this->assertEquals(60, $response->total());
        $this->assertEquals(4, $response->lastPage());

        $response = $this->repository->paginate(totalPage: 34);
        $this->assertCount(34, $response->items());

        $response = $this->repository->paginate(totalPage: 34, page: 2);
        $this->assertCount(26, $response->items());
    }

    public function testPaginateEmpty()
    {
        $response = $this->repository->paginate();
        $this->assertEmpty($response->items());
    }

    public function testUpdate()
    {
        $genre = Genre::factory()->create();

        $entity = new EntityGenre(
            id: new Uuid($genre->id),
            name: $genre->name,
            isActive: $genre->is_active,
            createdAt: new DateTime($genre->created_at)
        );
        $entity->update(name: "new name");

        $this->repository->update($entity);
        
        $this->assertDatabaseHas("genres", [
            "id" => $genre->id,
            "name" => "new name"
        ]);
    }

    public function testUpdateNotFound()
    {
        $this->expectException(EntityNotFoundException::class);
        $entity = new EntityGenre(
            name: "name"
        );
        $this->repository->update($entity);
    }

    public function testDelete()
    {
        $genreDb = Genre::factory()->create();

        $result = $this->repository->delete($genreDb->id);
        $this->assertTrue($result);
        $this->assertSoftDeleted("genres", ["id" => $genreDb->id]);
    }

    public function testDeleteNotFound()
    {
        $this->expectException(EntityNotFoundException::class);
        $this->repository->delete("fake_id");
    }
}
