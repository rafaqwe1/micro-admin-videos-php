<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\DeleteCategoryUseCase;
use Tests\TestCase;

class DeleteCategoryUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_delete()
    {
        $categoryDb = Model::factory()->create();
        $respository = new CategoryEloquentRepository(new Model());
        $useCase = new DeleteCategoryUseCase($respository);

        $response = $useCase->execute($categoryDb->id);

        $this->assertTrue($response->success);
        $this->assertSoftDeleted($categoryDb->getTable(), ["id" => $categoryDb->id]);
    }
}
