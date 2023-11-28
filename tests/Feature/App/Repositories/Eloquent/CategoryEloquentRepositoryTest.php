<?php

namespace Tests\Feature\App\Repositories\Eloquent;

use App\Models\Category as Model;
use App\Models\Category;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\Domain\Entity\Category as EntityCategory;
use Core\Domain\Exception\EntityNotFoundException;
use Core\Domain\Repository\CategoryRepositoryInterface;
use Core\Domain\Repository\PaginationInterface;
use Tests\TestCase;
use Throwable;

class CategoryEloquentRepositoryTest extends TestCase
{

    /**
     * @var CategoryEloquentRepository
     */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CategoryEloquentRepository(new Model());
    }

    public function testInsert()
    {
        $entity = new EntityCategory(
            name: "teste"
        );

        $response = $this->repository->insert($entity);
        $this->assertInstanceOf(CategoryRepositoryInterface::class, $this->repository);
        $this->assertInstanceOf(EntityCategory::class, $response);

        $this->assertDatabaseHas('categories', [
            'id' => $entity->id(),
            'name' => $entity->name
        ]);
    }

    public function testFindById()
    {
        $category = Model::factory()->create();

        $response = $this->repository->findById($category->id);

        $this->assertInstanceOf(EntityCategory::class, $response);
        $this->assertEquals($category->id, $response->id());
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
            $category = new EntityCategory(name: "test");
            $this->repository->update($category);
            $this->assertTrue(false);
        } catch (Throwable $th) {
            $this->assertInstanceOf(EntityNotFoundException::class, $th);
        }
    }

    public function testUpdate()
    {
        $categoryDb = Category::factory()->create();

        $category = new EntityCategory(id: $categoryDb->id, name: 'updated name');

        $response = $this->repository->update($category);

        $this->assertInstanceOf(EntityCategory::class, $response);
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
        $categoryDb = Category::factory()->create();
        $response = $this->repository->delete($categoryDb->id);
        $this->assertTrue($response);
    }
}
