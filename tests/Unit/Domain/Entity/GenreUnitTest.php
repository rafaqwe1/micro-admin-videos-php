<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Genre;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid as RamseyUuid;

class GenreUnitTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testAttributes()
    {
        $uuid = RamseyUuid::uuid4();
        $date = date("Y-m-d H:i:s");
        $genre = new Genre(
            id: new Uuid($uuid),
            name: 'New Genre',
            isActive: true,
            createdAt: new DateTime($date)
        );

        $this->assertEquals($uuid, $genre->id());
        $this->assertEquals("New Genre", $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertEquals($date, $genre->createdAt());
    }

    public function testAttributesCreate()
    {
        $genre = new Genre(
            name: 'New Genre'
        );

        $this->assertNotEmpty($genre->id());
        $this->assertEquals("New Genre", $genre->name);
        $this->assertTrue($genre->isActive);
        $this->assertNotEmpty($genre->createdAt());
    }

    public function testActivate()
    {
        $genre = new Genre(
            name: 'New Genre',
            isActive: false
        );

        $this->assertFalse($genre->isActive);
        $genre->activate();
        $this->assertTrue($genre->isActive);
    }

    public function testDeactivate()
    {
        $genre = new Genre(
            name: 'New Genre'
        );

        $this->assertTrue($genre->isActive);
        $genre->deactivate();
        $this->assertFalse($genre->isActive);
    }

    public function testUpdate()
    {
        $genre = new Genre(
            name: 'test'
        );

        $genre->update(name: "new name");
        $this->assertEquals("new name", $genre->name);
    }

    public function testEntityException()
    {
        $this->expectException(EntityValidationException::class);
        new Genre(name: 's');
    }

    public function testEntityUpdateException()
    {
        $genre = new Genre(name: 'genre');
        $this->expectException(EntityValidationException::class);
        $genre->update(name: "");
    }

    public function testAddCategoryToGenre()
    {
        $categoryId = RamseyUuid::uuid4();

        $genre = new Genre(name: "genre");

        $this->assertIsArray($genre->categoriesId);
        $this->assertEmpty($genre->categoriesId);

        $genre->addCategory(categoryId : $categoryId);
        $genre->addCategory(categoryId : $categoryId);
        $this->assertCount(2, $genre->categoriesId);
    }

    public function testRemoveCategoryToGenre()
    {
        $genre = new Genre(name: "genre", categoriesId: ["1", "2"]);
        $this->assertCount(2, $genre->categoriesId);

        $genre->removeCategory("1");
        $this->assertCount(1, $genre->categoriesId);
        $this->assertEquals("2", current($genre->categoriesId));
    }
}
