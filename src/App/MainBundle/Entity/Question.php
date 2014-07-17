<?php
namespace App\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="question")
 */
class Question
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $name;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $category;



    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = (string) $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCategory()
    {
        return $this->category;
    }

    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }
}