<?php

namespace Tests\Feature\Core\UseCase\Category;

use App\Models\Category as Model;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use Core\UseCase\Category\CreateCategoryUseCase;
use Core\UseCase\DTO\Category\CreateCategory\CategoryCreateInputDto;
use Tests\TestCase;

class CreateCategoryUseCaseTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create()
    {
        $respository = new CategoryEloquentRepository(new Model());
        $useCase = new CreateCategoryUseCase($respository);

        $response = $useCase->execute(
            new CategoryCreateInputDto(
                "Teste"
            )
        );

        $this->assertEquals("Teste", $response->name);
        $this->assertNotEmpty($response->id);
    }
}
