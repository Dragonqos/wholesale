<?php

namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class ResourceTrait
 * @package App\Traits
 */
trait ResourceTrait
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"basic"})
     */
    protected $id;

    /**
     * Sets id.
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId(?int $id)
    {
        if(null === $this->id) {
            $this->id = $id;
        }
        return $this;
    }

    /**
     * Gets id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}