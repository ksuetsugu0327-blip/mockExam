<?php

namespace App\Traits;

use App\Observers\OptimisticLockObserver;
use Carbon\Carbon;

trait OptimisticLockObserverTrait
{
    protected static function bootOptimisticLockObserverTrait()
    {
        self::observe(OptimisticLockObserver::class);
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->{OptimisticLockObserver::OPTIMISTIC_LOCK_CHECK_COLUMN} = $updatedAt ? Carbon::parse($updatedAt) : null;
    }
}
