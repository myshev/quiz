<?php

namespace App\MainBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

trait SetTrait
{
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        $allowedValues = $this->getValues();
        foreach ($value as $val) {
            if (!in_array($val, $allowedValues)) {
                throw new \InvalidArgumentException(sprintf('Invalid value "%s" for SET %s', $val, $this->getName()));
            }
        }

        return implode(',', $value);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (!$value) {
            return [];
        }
        return explode(',', $value);
    }


    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(
            function ($value) {
                return "'{$value}'";
            },
            $this->getValues()
        );

        return 'SET(' . implode(', ', $values) . ')';
    }
}
