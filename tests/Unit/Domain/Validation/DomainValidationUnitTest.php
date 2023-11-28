<?php

namespace Tests\Unit\Domain\Validation;

use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use PHPUnit\Framework\TestCase;
use Throwable;

class DomainValidationUnitTest extends TestCase
{
    public function testNotNull()
    {
        try{
            $value = 'teste';
            DomainValidation::notNull($value);
            $this->assertTrue(true);
            DomainValidation::notNull("");
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testNotNullCustomMessageException()
    {
        try{
            $value = '';
            DomainValidation::notNull($value, "custom message exception");
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
            $this->assertEquals("custom message exception", $th->getMessage());
        }
    }

    public function testMaxStrLength()
    {
        try{
            $value = 'teste';
            DomainValidation::strMaxLength($value, 5, 'Custom message');
            $this->assertTrue(true);
            DomainValidation::notNull("");
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testMinStrLength()
    {
        try{
            $value = 'teste';
            DomainValidation::strMinLength($value, 8, 'Custom message');
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }

    public function testStrCanNullAndMaxLength()
    {
        try{
            $value = 'teste';
            DomainValidation::strCanNullAndMaxLength($value, 3, 'Custom message');
            $this->assertTrue(false);
        }catch(Throwable $th){
            $this->assertInstanceOf(EntityValidationException::class, $th);
        }
    }
}
