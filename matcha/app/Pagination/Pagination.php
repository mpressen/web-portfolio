<?php

namespace App\Pagination;

class Pagination
{
    private $total;
    private $perSet;
    private $setNumber;
    private $sets;

    public function __construct($setNumber, $perSet, $total) {
        $this->perSet = (int) $perSet;
        $this->total = (int) $total;
        $this->sets = (int) ceil($this->total / $perSet);
        $this->setNumber = $this->changeSet((int) $setNumber);
    }

    public function changeSet($setNumber) {
        if ($setNumber < 0) {
            $setNumber = 1;
        }
        if ($setNumber > $this->sets) {
            $setNumber = $this->sets;
        }
        return $setNumber;
    }

    public function getCount() {
        $rem = $this->total - ($this->perSet * ($this->setNumber - 1));
        if ($rem > $this->perSet) {
            return $this->perSet;
        }
        return $rem;
    }

    public function getOffset() {
        $offset = ($this->perSet * ($this->setNumber - 1));
        return ($offset < 1) ? 0 : $offset;
    }

    /**
     * Get the current set number
     * @return  int Current set number
     */
    public function getSetNumber() {
        return $this->setNumber;
    }
    /**
     * Get total number of sets available
     * return   int Sets available
     */
    public function getSets() {
        return $this->sets;
    }

    public function getTotal()
    {
        return $this->total;
    }
}