<?php

namespace App\Entity;

use DateTime;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class AbstractEntity
 * @package AppBundle\Entity
 */
abstract class AbstractEntity
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer", options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var DateTime
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    protected $created;

//    /**
//     * @var DateTime
//     * @ORM\Column(name="updated", type="datetime")
//     * @Gedmo\Timestampable(on="update")
//     */
//    protected $updated;

    /**
     * Set created
     * @param DateTime $created
     * @return AbstractEntity
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
