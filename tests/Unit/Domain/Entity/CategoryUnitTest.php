<?php

namespace Tests\Unit\Domain\Entity;

use Core\Domain\Entity\Category;
use Core\Domain\Exception\EntityValidationException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Throwable;

class CategoryUnitTest extends TestCase
{
    public function testAttributes()
    {
        $category = new Category(
            name: "New Cat",
            description: "New Desc",
            isActive: true
        );

        $this->assertNotEmpty($category->createdAt());
        $this->assertNotEmpty($category->id());
        $this->assertEquals("New Cat", $category->name);
        $this->assertEquals("New Desc", $category->description);
        $this->assertEquals(true, $category->isActive);
    }

    public function testActivated()
    {
        $category = new Category(
            id: "",
            name: "New Cat",
            isActive: false
        );

        $this->assertFalse($category->isActive);
        $category->activate();
        $this->assertTrue($category->isActive);
    }

    public function testDisabled()
    {
        $category = new Category(
            name: "New Cat",
        );

        $this->assertTrue($category->isActive);
        $category->disable();
        $this->assertFalse($category->isActive);
    }

    public function testUpdate()
    {
        $uuid = (string)Uuid::uuid4()->toString();
        $category = new Category(
            id: $uuid,
            name: "New Cat",
            description: "New desc",
            isActive: true,
            createdAt: '2023-01-01 12:12:12'
        );

        $category->update(
            name: "new_name",
            description: "new_desc"
        );

        $this->assertEquals('2023-01-01 12:12:12', $category->createdAt());
        $this->assertEquals($uuid, $category->id());
        $this->assertEquals("new_name", $category->name);
        $this->assertEquals("new_desc", $category->description);
    }

    public function testExceptionName()
    {
        try{
            new Category(
                name: "Ne",
                description: "New Desc"
            );
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testExceptionDescription()
    {
        try{
            new Category(
                name: "New",
                description: random_bytes(256)
            );
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }
}
