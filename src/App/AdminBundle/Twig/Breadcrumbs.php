<?php

namespace App\AdminBundle\Twig;

class Breadcrumbs implements \Iterator, \Countable
{
    protected $breadcrumbs = [];

    public function append($title, $url = '')
    {
        $this->breadcrumbs[] = [
            'title' => $title,
            'url' => $url
        ];
    }

    public function prepend($title, $url = '')
    {
        array_unshift($this->breadcrumbs, [
            'title' => $title,
            'url' => $url
        ]);
    }

    public function current()
    {
        return current($this->breadcrumbs);
    }

    public function next()
    {
        next($this->breadcrumbs);
    }

    public function key()
    {
        key($this->breadcrumbs);
    }

    public function valid()
    {
        return false !== current($this->breadcrumbs);
    }

    public function rewind()
    {
        reset($this->breadcrumbs);
    }

    public function count()
    {
        return count($this->breadcrumbs);
    }
}
