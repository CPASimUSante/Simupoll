<?php

namespace CPASimUSante\SimupollBundle\Tag;

use CPASimUSante\SimupollBundle\Entity\Tag;
use Doctrine\Common\Collections\Collection;

class RecursiveTagIterator implements \RecursiveIterator
{
    /**
     * @var Collection | Tag[]
     */
    private $_data;

    public function __construct(Collection $data)
    {
        // initialize the iterator with the root tag, i.e. parent id null
        $this->_data = $data;
    }

    public function current()
    {
        return $this->_data->current();
    }

    public function next()
    {
        $this->_data->next();
    }

    public function key()
    {
        return $this->_data->key();
    }

    public function valid()
    {
        return $this->_data->current() instanceof Tag;
    }

    public function rewind()
    {
        $this->_data->first();
    }

    public function hasChildren()
    {
        return ( ! $this->_data->current()->getChildren()->isEmpty());
    }

    public function getChildren()
    {
        return new RecursiveTagIterator($this->_data->current()->getChildren());
    }
}