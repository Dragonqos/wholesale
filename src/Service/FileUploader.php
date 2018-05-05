<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader
 * @package App\Service
 */
class FileUploader
{
    /**
     * @var string
     */
    private $uploadDir;

    /**
     * FileUploader constructor.
     *
     * @param $uploadDir
     */
    public function __construct($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    public function upload(UploadedFile $file): string
    {
        $fileName = md5(uniqid('upl_', true)) . '.' . $file->guessExtension();
        $file->move($this->getUploadDir(), $fileName);

        return $fileName;
    }

    /**
     * @return string
     */
    private function getUploadDir(): string
    {
        return $this->uploadDir;
    }
}