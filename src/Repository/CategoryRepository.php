<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository
{
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function findAll()
    {
        $filename = $this->filename;
        $file = fopen($filename, 'r');
        $categories = [];
        while(! feof($file))
        {   
            $category = fgetcsv($file);
            if($category != false)
            {
                $categories[] = $this->fromArray($category);
            }

        }

        return $categories;
    }

    public function find($id)
    {
        $categories = $this->findAll();
        
        foreach ($categories as $category) {
            if ($category->getId() == $id )
            {
             return $category;
            } 
        }
    }

    public function save($category)
    {   
        $this->appendToFile($category);
    }

    protected function fromArray($row)
    {
        $object = new Category;
        $object->setId($row[0]);
        $object->setValue($row[1]);

        return $object;
    }

    protected function toArray($category)
    {   
        return 
        [
            $category->getId(), 
            $category->getValue()
        ];
    }

    protected function appendToFile($category)
    {   
        $filename = $this->filename;

        $file = fopen($filename, "a+");
        fputcsv($file, $this->toArray($category));
        
        fclose($file);

    }

}
