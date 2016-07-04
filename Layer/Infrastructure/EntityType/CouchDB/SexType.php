<?php

namespace Sfynx\DddBundle\Layer\Infrastructure\EntityType\CouchDB;

use Doctrine\ODM\CouchDB\Types\Type;

class SexType extends Type
{
    public function convertToCouchDBValue($value)
    {
        return (string) $value;
    }

    public function convertToDatabaseValue($value)
    {
        return (string) $value;
    }

    public function convertToPHPValue($value)
    {
        $className = $this->getNamespace().'\\'.$this->getName();

        return new $className($value);
    }

    public function getName()
    {
        return 'SexVO';
    }

    protected function getNamespace()
    {
        return 'Sfynx\DddBundle\Layer\Domain\ValueObject';
    }
}
