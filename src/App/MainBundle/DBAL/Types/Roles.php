<?php

namespace App\MainBundle\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Fresh\Bundle\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class Roles extends AbstractEnumType
{
    use SetTrait;

    protected $name = 'roles';

    const ROLE_USER = 'ROLE_USER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    protected static $choices = [
        self::ROLE_USER => 'Пользователь',
        self::ROLE_ADMIN => 'Администратор',
        self::ROLE_SUPER_ADMIN => 'Супер администратор',
    ];
}
