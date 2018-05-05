<?php

namespace App\Entity;

use App\Interfaces\ResourceInterface;
use App\Interfaces\TimestampedInterface;
use App\Traits\ResourceTrait;
use App\Traits\TimestampedTrait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Assert\File( maxSize = "2028k", mimeTypes = {"text/csv", "text/plain"}, mimeTypesMessage = "Please upload a valid CSV File")
     */
    protected $warehousePrice;

    /**
     * @var string
     * @ORM\Column(name="hotline_price", type="string", length=255, unique=false)
     * @Assert\NotBlank()
     * @Assert\File( maxSize = "2028k", mimeTypes = {"text/csv", "text/plain"}, mimeTypesMessage = "Please upload a valid CSV File")
     */
    protected $hotlinePrice;

    /**
     * @var float
     * @ORM\Column(type="decimal", scale=4, nullable=true)
     * @Assert\NotBlank()
     */
    protected $rate;

    /**
     * Here we populate result file name
     * @var string
     * @ORM\Column(name="wholesale_price", type="string", length=255, unique=false, nullable=true)
     */
    protected $wholesalePrice;

    /**
     * @return string|UploadedFile
     */
    public function getWarehousePrice()
    {
        return $this->warehousePrice;
    }

    /**
     * @param string|UploadedFile $warehousePrice
     *
     * @return Job
     */
    public function setWarehousePrice($warehousePrice): self
    {
        $this->warehousePrice = $warehousePrice;
        return $this;
    }

    /**
     * @return string|UploadedFile
     */
    public function getHotlinePrice()
    {
        return $this->hotlinePrice;
    }

    /**
     * @param string|UploadedFile $hotlinePrice
     *
     * @return Job
     */
    public function setHotlinePrice($hotlinePrice): self
    {
        $this->hotlinePrice = $hotlinePrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getRate(): ?float
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

    /**
     * @return string|null
     */
    public function getWholesalePrice(): ?string
    {
        return $this->wholesalePrice;
    }

    /**
     * @param string $wholesalePrice
     */
    public function setWholesalePrice(string $wholesalePrice)
    {
        $this->wholesalePrice = $wholesalePrice;
    }

    /**
     * @return bool
     */
    public function hasWholesalePrice(): bool
    {
        return null !== $this->warehousePrice;
    }
}