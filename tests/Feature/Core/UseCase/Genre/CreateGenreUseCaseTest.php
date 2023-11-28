<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category;
use App\Models\Genre;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DBTransaction;
use Core\Domain\Exception\EntityNotFoundException;
use Core\UseCase\DTO\Genre\CreateGenre\CreateGenreInputDto;
use Core\UseCase\Genre\CreateGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Throwable;

class CreateGenreUseCaseTest extends TestCase
{
    
    public function test_insert()
    {
        $genreRepository = new GenreEloquentRepository(new Genre());
        $categoryRepository = new CategoryEloquentRepository(new Category());

        $useCase = new CreateGenreUseCase($genreRepository, new DBTransaction(), $categoryRepository);

        $output = $useCase->execute(new CreateGenreInputDto(name: 'genre'));

        $this->assertEquals('genre', $output->name);
        $this->assertDatabaseHas("genres", [
            "id" => $output->id
        ]);
    }

    public function test_category_found()
    {
    	$categoriesIds = Category::factory()->count(2)->create()->pluck('id')->toArray();

        $genreRepository = new GenreEloquentRepository(new Genre());
        $categoryRepository = new CategoryEloquentRepository(new Category());

        $useCase = new CreateGenreUseCase($genreRepository, new DBTransaction(), $categoryRepository);
        $useCase->execute(new CreateGenreInputDto(name: 'genre', categories_id: $categoriesIds));

        $this->assertDatabaseCount("category_genre", 2);
    }

    public function test_category_not_found()
    {
        $this->expectException(EntityNotFoundException::class);
    	$categoriesIds = Category::factory()->count(2)->create()->pluck('id')->toArray();
        $categoriesIds[] = 'fake_id';

        $genreRepository = new GenreEloquentRepository(new Genre());
        $categoryRepository = new CategoryEloquentRepository(new Category());

        $useCase = new CreateGenreUseCase($genreRepository, new DBTransaction(), $categoryRepository);
        $useCase->execute(new CreateGenreInputDto(name: 'genre', categories_id: $categoriesIds));
    }

    public function testTransactions()
    {
        $genreRepository = new GenreEloquentRepository(new Genre());
        $categoryRepository = new CategoryEloquentRepository(new Category());

        $useCase = new CreateGenreUseCase($genreRepository, new DBTransaction(), $categoryRepository);

        try{
            $output = $useCase->execute(new CreateGenreInputDto(name: 'genre'));
            $this->assertDatabaseHas("genres", [
                "id" => $output->id
            ]);
        }catch(Throwable $th){
            $this->assertDatabaseCount("category_genre", 0);
            $this->assertDatabaseCount("category_genre", 0);
        }
    }
}
