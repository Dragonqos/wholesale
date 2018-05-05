<?php

namespace App\Interfaces;

/**
 * Interface ResourceInterface
 * @package Helix\CoreBundle\Interfaces
 */
interface ResourceInterface {

    /**
     * @param int|null $id
     *
     * @return mixed
     */
    public function setId(?int $id);

    /**
     * @return int|null
     */
    public function getId(): ?int;
}