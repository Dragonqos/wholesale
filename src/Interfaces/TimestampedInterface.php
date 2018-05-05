<?php

namespace App\Interfaces;

/**
 * Interface TimestampedInterface
 * @package Helix\CoreBundle\Interfaces
 */
interface TimestampedInterface
{
    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @param \DateTimeInterface $createdAt
     *
     * @return mixed
     */
    public function setCreatedAt(\DateTimeInterface $createdAt);

    /**
     * @return mixed
     */
    public function getUpdatedAt();

    /**
     * @param \DateTimeInterface $updatedAt
     *
     * @return mixed
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt);
}