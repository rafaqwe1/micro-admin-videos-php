<?php

namespace Core\Domain\Entity;

use Core\Domain\Entity\Traits\MethodsMagicsTrait;
use Core\Domain\Enum\CastMemberType;
use Core\Domain\Exception\EntityValidationException;
use Core\Domain\Validation\DomainValidation;
use Core\Domain\ValueObject\Uuid;
use DateTime;
use InvalidArgumentException;

class CastMember
{
    use MethodsMagicsTrait;

    public function __construct(
        protected string $name,
        protected CastMemberType|int $type,
        protected ?Uuid $id = null,
        protected ?DateTime $createdAt = null
    )
    {
        $this->id = $this->id ?? Uuid::random();
        $this->$createdAt = $this->createdAt ?? new DateTime();
        $this->validate();
        try{
            $this->type = is_object($type) ? $type : CastMemberType::createIfValid($type);
        }catch(InvalidArgumentException $e){
            throw new EntityValidationException($e->getMessage());
        }
    }

    public function update(string $name)
    {
        $this->name = $name;
        $this->validate();
    }

    private function validate()
    {
        DomainValidation::strMaxLength($this->name);
        DomainValidation::strMinLength($this->name, 3);
    }
}
