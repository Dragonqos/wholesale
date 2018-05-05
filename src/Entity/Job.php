<?php

namespace App\Entity;

use App\Interfaces\ResourceInterface;
use App\Interfaces\TimestampedInterface;
use App\Traits\ResourceTrait;
use App\Traits\TimestampedTrait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Job
 * @package App\Entity
 *
 * @ORM\Entity(repositoryClass="App\Repository\JobRepository")
 * @ORM\Table(name="jobs")
 * @ORM\HasLifecycleCallbacks()
 */
class Job implements ResourceInterface, TimestampedInterface
{
    use ResourceTrait, TimestampedTrait;

    /**
     * @var string
     * @ORM\Column(name="warehouse_price", type="string", length=255, unique=false)
     * @Assert\NotBlank()
     */
    protected $warehousePrice;

    /**
     * @var string
     * @ORM\Column(name="hotline_price", type="string", length=255, unique=false)
     */
    protected $hotlinePrice;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=4)
     */
    protected $rate;

    /**
     * @return string
     */
    public function getWarehousePrice(): string
    {
        return $this->warehousePrice;
    }

    /**
     * @param string $warehousePrice
     */
    public function setWarehousePrice(string $warehousePrice)
    {
        $this->warehousePrice = $warehousePrice;
    }

    /**
     * @return string
     */
    public function getHotlinePrice(): string
    {
        return $this->hotlinePrice;
    }

    /**
     * @param string $hotlinePrice
     */
    public function setHotlinePrice(string $hotlinePrice)
    {
        $this->hotlinePrice = $hotlinePrice;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->rate;
    }

    /**
     * @param float $rate
     */
    public function setRate(float $rate)
    {
        $this->rate = $rate;
    }
}