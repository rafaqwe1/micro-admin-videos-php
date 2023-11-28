<?php

namespace Tests\Feature\Core\UseCase\Genre;

use App\Models\Category;
use App\Models\Genre;
use App\Repositories\Eloquent\CategoryEloquentRepository;
use App\Repositories\Eloquent\GenreEloquentRepository;
use App\Repositories\Transaction\DBTransaction;
use Core\Domain\Exception\EntityNotFoundException;
use Core\UseCase\DTO\Genre\UpdateGenre\UpdateGenreInputDto;
use Core\UseCase\Genre\UpdateGenreUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Throwable;

class UpdateGenreUseCaseTest extends TestCase
{
  
    public function test_category_found()
    {
    	$categoriesIds = Category::factory()->count(2)->create()->pluck('id')->toArray();

        $genre = Genre::factory()->create();

        $genreRepository = new GenreEloquentRepository(new Genre());
        $categoryRepository = new CategoryEloquentRepository(new Category());

        $useCase = new UpdateGenreUseCase($genreRepository, new DBTransaction(), $categoryRepository);
        $useCase->execute(new UpdateGenreInputDto(id: $genre->id, name: 'genre', categories_id: $categoriesIds));

        $this->assertDatabaseCount("category_genre", 2);
    }

    public function test_category_not_found()
    {
        $this->expectException(EntityNotFoundException::class);
    	$categoriesIds = Category::factory()->count(2)->create()->pluck('id')->toArray();
        $categoriesIds[] = 'fake_id';

        $genre = Genre::factory()->create();

        $genreRepository = new GenreEloquentRepository(new Genre());
        $categoryRepository = new CategoryEloquentRepository(new Category());

        $useCase = new UpdateGenreUseCase($genreRepository, new DBTransaction(), $categoryRepository);
        $useCase->execute(new UpdateGenreInputDto(id: $genre, name: 'genre', categories_id: $categoriesIds));
    }

    public function testTransactions()
    {
        $genreRepository = new GenreEloquentRepository(new Genre());
        $categoryRepository = new CategoryEloquentRepository(new Category());

        $useCase = new UpdateGenreUseCase($genreRepository, new DBTransaction(), $categoryRepository);
        $genre = Genre::factory()->create();
        try{
            $useCase->execute(new UpdateGenreInputDto(id: $genre->id, name: 'genre'));
            $this->assertDatabaseHas("genres", [
                "name" => "genre"
            ]);
        }catch(Throwable $th){
            $this->assertDatabaseCount("category_genre", 0);
            $this->assertDatabaseCount("category_genre", 0);
        }
    }
}
