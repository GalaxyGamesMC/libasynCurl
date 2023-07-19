<?php

declare(strict_types=1);

namespace libasynCurl;

use Closure;

class InternetExecutor {

    public function __construct(
        private Closure $resolve,
        private Closure $reject
    ){}

    public function getResolve(): Closure {
        return $this->resolve;
    }

    public function getReject(): Closure {
        return $this->reject;
    }
}