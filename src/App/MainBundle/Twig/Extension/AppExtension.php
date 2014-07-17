<?php

namespace App\MainBundle\Twig\Extension;

use App\MainBundle\DBAL\Types\Roles;
use App\MainBundle\Entity\Category;

class AppExtension extends \Twig_Extension
{
    /*public function getFilters()
    {
        return [
            'fullPath' => new \Twig_SimpleFilter('fullPath', [$this, 'fullPathCategory']),
            'money' => new \Twig_SimpleFilter('money', [$this, 'moneyFilter']),
            'number' => new \Twig_SimpleFilter('number', [$this, 'numberFilter']),
            'pluralize' => new \Twig_SimpleFilter('pluralize', ['Sirian\Helpers\TextUtils', 'pluralize']),
            'elapsed' => new \Twig_SimpleFilter('elapsed', [$this, 'elapsedFilter']),
            'day' => new \Twig_SimpleFilter('day', [$this, 'dayFilter']),
            'postfix' => new \Twig_SimpleFilter('postfix', [$this, 'postfixFilter'])
        ];
    }*/

    public function getFunctions()
    {
        return [
            'roles' => new \Twig_SimpleFunction('roles', [$this, 'rolesFunction']),
        ];
    }

    public function rolesFunction($roles)
    {
        $result = [];
        foreach ((array)$roles as $role) {
            if (Roles::isValueExist($role)) {
                $result[] = Roles::getReadableValue($role);
            }
        }

        return implode(', ', $result);
    }

    public function dayFilter($value)
    {
        $variants = ['%count% день', '%count% дня', '%count% дней'];

        return \Sirian\Helpers\TextUtils::pluralize($value, $variants);
    }

    public function moneyFilter($value)
    {
        return number_format($value, 2, '.', ' ');
    }

    public function numberFilter($value)
    {
        return number_format($value, 0, '.', ' ');
    }

    public function elapsedFilter($value)
    {
        $s = $value % 60;
        $m = ($value - $s) % 60;
        $h = floor($value / 60);
        $res = '';
        if ($h > 24) {

            $d = floor($h / 24);

            $res = $d . ' ' . TextUtils::pluralize($d, ['день', 'дня', 'дней']) . ', ';

            $h = $h % 24;
        }


        $res .= sprintf('%d:%02d:%02d', $h, $m, $s);
        return $res;
    }

    public function postfixFilter($value, $postfix)
    {
        return $value . $postfix;
    }

    public function fullPathCategory(Category $category, $separator = ' :: ')
    {
        $categoriesNames = [$category->getName()];

        while ($parent = $category->getParent()) {
            array_unshift($categoriesNames, $parent->getName());
            $category = $parent;
        }

        return implode($separator, $categoriesNames);
    }

    public function getName()
    {
        return 'cpatext';
    }
}
