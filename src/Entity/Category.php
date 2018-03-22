<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Category
{
    private $value;

    private $id;



    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }
}
