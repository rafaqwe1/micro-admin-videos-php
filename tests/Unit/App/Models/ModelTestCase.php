<?php

namespace Tests\Unit\App\Models;

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;

abstract class ModelTestCase extends TestCase
{
    protected abstract function model(): Model;
    protected abstract function traits(): array;
    protected abstract function fillables(): array;
    protected abstract function casts(): array;

    public function testIfUseTraits()
    {
        $traitsUsed = array_keys(class_uses($this->model()));
        $this->assertEquals($this->traits(), $traitsUsed);
    }

    public function testFillable()
    {
        $fillable = $this->model()->getFillable();
        $this->assertEquals($this->fillables(), $fillable);
    }

    public function testIfIncrementingIsFalse()
    {
        $this->assertFalse($this->model()->incrementing);
    }

    public function testHasCasts()
    {
        $casts = $this->model()->getCasts();
        $this->assertEquals($this->casts(), $casts);
    }
}
