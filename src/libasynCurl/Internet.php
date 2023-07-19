<?php

declare(strict_types=1);

namespace libasynCurl;

use pocketmine\utils\Internet as PMInternet;
use Throwable;
use vennv\vapm\Promise;
use vennv\vapm\System;

final class Internet {
    
    protected static function fetch(string $page, int $timeout = 10, array $extraHeaders = [], array $extraOpts = []): Promise {
        return new Promise(function($resolve, $reject) use ($page, $timeout, $extraHeaders, $extraOpts) {
            System::setTimeout(function() use ($resolve, $reject, $page, $timeout, $extraHeaders, $extraOpts) {
                try {
                    $resolve(PMInternet::simpleCurl(
                        $page, 
                        $timeout, 
                        $extraHeaders,
                        $extraOpts
                    ));
                } catch (Throwable $e) {
                    $reject($e);
                }
            }, 0);
        });
    }

    public static function getURL(
        string $page, 
        int $timeout = 10, 
        array $extraHeaders = [],
        string|array $body = [],
        ?InternetExecutor $executor = null
    ): void {
        Internet::fetch($page, $timeout, $extraHeaders, [
            CURLOPT_POSTFIELDS => $body
        ])->then(function ($result) use ($executor): void {
            $executor->getResolve()($result);
        })->reject(function (Throwable $e)  use ($executor): void {
            $executor->getReject()($e);
        });
    }

    public static function postURL(
        string $page, 
        int $timeout = 10, 
        array $extraHeaders = [],
        string|array $body = [],
        ?InternetExecutor $executor = null
    ): void {
        Internet::fetch($page, $timeout, $extraHeaders, [
            CURLOPT_POST => 1,
			CURLOPT_POSTFIELDS => $body
        ])->then(function ($result) use ($executor): void {
            $executor->getResolve()($result);
        })->reject(function (Throwable $e)  use ($executor): void {
            $executor->getReject()($e);
        });
    }

    public static function putURL(
        string $page, 
        int $timeout = 10, 
        array $extraHeaders = [],
        string|array $body = [],
        ?InternetExecutor $executor = null
    ): void {
        Internet::fetch($page, $timeout, $extraHeaders, [
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $body
        ])->then(function ($result) use ($executor): void {
            $executor->getResolve()($result);
        })->reject(function (Throwable $e)  use ($executor): void {
            $executor->getReject()($e);
        });
    }

    public static function deleteURL(
        string $page, 
        int $timeout = 10, 
        array $extraHeaders = [],
        string|array $body = [],
        ?InternetExecutor $executor = null
    ): void {
        Internet::fetch($page, $timeout, $extraHeaders, [
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_POSTFIELDS => $body
        ])->then(function ($result) use ($executor): void {
            $executor->getResolve()($result);
        })->reject(function (Throwable $e)  use ($executor): void {
            $executor->getReject()($e);
        });
    }

    public static function patchURL(
        string $page, 
        int $timeout = 10, 
        array $extraHeaders = [],
        string|array $body = [],
        ?InternetExecutor $executor = null
    ): void {
        Internet::fetch($page, $timeout, $extraHeaders, [
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS => $body
        ])->then(function ($result) use ($executor): void {
            $executor->getResolve()($result);
        })->reject(function (Throwable $e)  use ($executor): void {
            $executor->getReject()($e);
        });
    }
}