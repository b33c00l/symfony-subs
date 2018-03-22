<?php

namespace App\Repository;

use App\Entity\Subscribtion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Subscriber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscriber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscriber[]    findAll()
 * @method Subscriber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscribtionRepository
{   
    private $filename;

    private $categoryRepository;

    private $dateFormat = 'Y-m-d H:i:s';

    public function __construct($filename, CategoryRepository $categoryRepository)
    {
        $this->filename = $filename;

        $this->categoryRepository = $categoryRepository;

    }

    public function findAll() 
    {
        $filename = $this->filename;
        if (!is_file($filename))
        {
            return [];
        }
        $file = fopen($filename, 'r');
        $subscribers = [];
        $i = -1;
        while(! feof($file))
        {   
            $i++;
            $subscriber = fgetcsv($file);
            if($subscriber != false)
            {
                $object = $this->fromArray($subscriber, $i);
                $subscribers[] = $object;
            }
        }
        fclose($file);

        return $subscribers;
    }

    public function delete($subscriber)
    {   
        $subscribers = $this->findAll();

        unset($subscribers[$subscriber->getId()]);

        $filename = $this->filename;

        $file = fopen($filename, 'w');
        foreach ($subscribers as $subscriber) {
            fputcsv($file, $this->toArray($subscriber));
        }
        fclose($file);

        return $subscribers;   
    }

    public function find($id)
    {
        $subscribers = $this->findAll();
        
        if (array_key_exists($id, $subscribers))
        {
           return $subscribers[$id];
       } 
    }

    public function save($subscribtion)
    {
        if ($subscribtion->getId() == null) 
        {
            $this->appendToFile($subscribtion);

        } else{
            $this->updateFile($subscribtion);
        }
    }

    protected function toArray($subscribtion)
    {   
        $categories = "";
        foreach ($subscribtion->getCategories() as $category) {
            $categories .= $category->getId() ." ";
        }

        return 
        [
            $subscribtion->getUser(), 
            $subscribtion->getEmail(),
            $categories,
            $subscribtion->getDate()->format($this->dateFormat)
        ];
    }

    protected function fromArray($row, $id)
    {
        $object = new Subscribtion;
        $object->setId($id);
        $object->setUser($row[0]);
        $object->setEmail($row[1]);
        $categories = explode(" ", $row[2]);
        foreach ($categories as $categoryId) {
            $category = $this->categoryRepository->find($categoryId);
            if($category != null)
            {
                $object->addCategory($category);     
            }
        }

        $object->setDate(\DateTime::createFromFormat($this->dateFormat, $row[3]));

        return $object;
    }

    protected function appendToFile($subscribe)
    {   
        $filename = $this->filename;

        $file = fopen($filename, "a+");
        fputcsv($file, $this->toArray($subscribe));
        
        fclose($file);

    }

    protected function updateFile($subscribtion)
    {
        $subscribers = $this->findAll();
        foreach ($subscribers as $key => $subscriber) {
            if ($subscriber->getId() == $subscribtion->getId()) {
                $subscribers[$key] = $subscribtion;
                break;
              }  
        }

        $filename = $this->filename;
        $file = fopen($filename, 'w');
        foreach ($subscribers as $subscriber) {
            fputcsv($file, $this->toArray($subscriber));
        }
        fclose($file);
    }

}
