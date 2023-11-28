<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\ListCategoriesUseCase;
use Core\UseCase\DTO\Category\ListCategories\ListCategoriesInputDto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ListCategoriesUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_list_empty()
    {
        $repository = new CategoryEloquentRepository(new Model());
        $useCase = new ListCategoriesUseCase($repository);
        $response = $useCase->execute(new ListCategoriesInputDto());

        $this->assertEmpty($response->items);
    }

    public function test_list_all()
    {
        Model::factory(20)->create();

        $repository = new CategoryEloquentRepository(new Model());
        $useCase = new ListCategoriesUseCase($repository);
        $response = $useCase->execute(new ListCategoriesInputDto());

        $this->assertCount(15, $response->items);
        $this->assertEquals(20, $response->total);
    }
}
