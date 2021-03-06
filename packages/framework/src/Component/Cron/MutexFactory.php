<?php

namespace Shopsys\FrameworkBundle\Component\Cron;

use NinjaMutex\Lock\LockInterface;
use NinjaMutex\Mutex;

class MutexFactory
{
    const MUTEX_CRON_NAME = 'cron';

    /**
     * @var \NinjaMutex\Lock\LockInterface
     */
    protected $lock;

    /**
     * @var \NinjaMutex\Mutex[]
     */
    protected $mutexesByName;

    /**
     * @param \NinjaMutex\Lock\LockInterface $lock
     */
    public function __construct(LockInterface $lock)
    {
        $this->lock = $lock;
        $this->mutexesByName = [];
    }

    /**
     * @return \NinjaMutex\Mutex
     */
    public function getCronMutex()
    {
        if (!array_key_exists(self::MUTEX_CRON_NAME, $this->mutexesByName)) {
            $this->mutexesByName[self::MUTEX_CRON_NAME] = new Mutex(self::MUTEX_CRON_NAME, $this->lock);
        }

        return $this->mutexesByName[self::MUTEX_CRON_NAME];
    }
}
