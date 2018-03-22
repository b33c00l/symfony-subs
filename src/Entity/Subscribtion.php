<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Subscribtion
{	
    /**
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @Assert\NotBlank()
     * @Assert\Email
     */
    private $email;

    /**
     * @Assert\NotBlank()
     */
    private $categories = [];

    private $date;

    private $id;

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function addCategory($category)
    {
        $this->categories[] = $category;
    }
    
}
