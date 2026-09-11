<?php

declare(strict_types=1);

namespace App\Core;

interface MiddlewareInterface
{
    /**
     * Return true to allow the request to continue; false if the
     * middleware has already sent a response (e.g. a redirect) and the
     * router should stop dispatching.
     */
    public function handle(): bool;
}
