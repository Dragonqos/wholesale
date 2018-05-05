<?php

namespace App\EventListener\Doctrine;

use App\Entity\Job;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

/**
 * Class JobRemoveSubscriber
 * @package App\EventListener\Doctrine
 */
class JobRemoveSubscriber implements EventSubscriber
{
    /**
     * @var
     */
    private $downloadPath;
    /**
     * @var
     */
    private $uploadPath;

    /**
     * JobRemoveSubscriber constructor.
     *
     * @param $downloadPath
     * @param $uploadPath
     */
    public function __construct(string $downloadPath, string $uploadPath)
    {
        $this->downloadPath = $downloadPath;
        $this->uploadPath = $uploadPath;
    }

    /**
     * @return array
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove
        ];
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preRemove(LifecycleEventArgs $event): void
    {
        /** @var Job $job */
        $job = $event->getObject();

        $this->removeFile($this->uploadPath, $job->getWarehousePrice());
        $this->removeFile($this->uploadPath, $job->getHotlinePrice());
        $this->removeFile($this->downloadPath, $job->getWholesalePrice());
    }

    /**
     * @param string $path
     * @param string $filename
     */
    private function removeFile(string $path, ?string $filename): void
    {
        if(null !== $filename) {
            $path = sprintf('%s/%s', rtrim($path, '/'), ltrim($filename, '/'));
            if(file_exists($path)) {
                @unlink(rtrim($path));
            }
        }
    }
}