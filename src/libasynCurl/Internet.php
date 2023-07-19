<?php

declare(strict_types=1);

namespace libasynCurl;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\utils\InternetException;
use pocketmine\utils\InternetRequestResult;
use Throwable;
use vennv\vapm\Promise;
use vennv\vapm\System;
use vennv\vapm\VapmPMMP;

final class Internet extends PluginBase {

    protected function onEnable(): void {
        VapmPMMP::init($this);
    }
    
    protected static function fetch(
        string $page,
        string $method = "GET",
        string|array $body = [],
        string|array $headers = [],
        int $timeout = 0
    ): Promise {
        return new Promise(function($resolve, $reject) use ($page, $method, $body, $headers, $timeout) {
            System::setTimeout(function() use ($resolve, $reject, $page, $method, $body, $headers, $timeout) {
                try {
                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $page,
                        CURLOPT_TIMEOUT => $timeout,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_HEADER => true,
                        CURLOPT_CUSTOMREQUEST => $method,
                        CURLOPT_POSTFIELDS => $body,
                        CURLOPT_HTTPHEADER => $headers
                    ]);
                    $result = curl_exec($curl);
                    curl_close($curl);
                    if($result === false){
                        throw new InternetException(curl_error($curl));
                    }
                    if(!is_string($result)) throw new AssumptionFailedError("curl_exec() should return string|false when CURLOPT_RETURNTRANSFER is set");
                    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                    $rawHeaders = substr($result, 0, $headerSize);
                    $body = substr($result, $headerSize);
                    $headers = [];
                    foreach(explode("\r\n\r\n", $rawHeaders) as $rawHeaderGroup){
                        $headerGroup = [];
                        foreach(explode("\r\n", $rawHeaderGroup) as $line){
                            $nameValue = explode(":", $line, 2);
                            if(isset($nameValue[1])){
                                $headerGroup[trim(strtolower($nameValue[0]))] = trim($nameValue[1]);
                            }
                        }
                        $headers[] = $headerGroup;
                    }
                    $resolve(new InternetRequestResult($headers, $body, $httpCode));
                } catch (Throwable $e) {
                    $reject($e);
                }
            }, 0);
        }, false);
    }

    public static function getURL(
        string $page,
        string|array $body = [],
        string|array $headers = [],
        int $timeout = 0,
        ?InternetExecutor $executor = null
    ): void {
        self::fetch($page, "GET", $body, $headers, $timeout)
            ->then(function (InternetRequestResult $result) use ($executor) {
                $executor->getResolve()($result);
            })
            ->catch(function (Throwable $e)  use ($executor) {
                $executor->getReject()($e);
            });
    }

    public static function postURL(
        string $page,
        string|array $body = [],
        string|array $headers = [],
        int $timeout = 0,
        ?InternetExecutor $executor = null
    ): void {
        self::fetch($page, "POST", $body, $headers, $timeout)
            ->then(function (InternetRequestResult $result) use ($executor) {
                $executor->getResolve()($result);
            })
            ->catch(function (Throwable $e)  use ($executor) {
                $executor->getReject()($e);
            });
    }

    public static function putURL(
        string $page, 
        string|array $body = [],
        string|array $headers = [],
        int $timeout = 0,
        ?InternetExecutor $executor = null
    ): void {
        self::fetch($page, "PUT", $body, $headers, $timeout)
            ->then(function (InternetRequestResult $result) use ($executor) {
                $executor->getResolve()($result);
            })
            ->catch(function (Throwable $e)  use ($executor): void {
                $executor->getReject()($e);
            });
    }

    public static function deleteURL(
        string $page, 
        string|array $body = [],
        string|array $headers = [],
        int $timeout = 0,
        ?InternetExecutor $executor = null
    ): void {
        self::fetch($page, "DELETE", $body, $headers, $timeout)
            ->then(function (InternetRequestResult $result) use ($executor) {
                $executor->getResolve()($result);
            })
            ->catch(function (Throwable $e)  use ($executor): void {
                $executor->getReject()($e);
            });
    }

    public static function patchURL(
        string $page, 
        string|array $body = [],
        string|array $headers = [],
        int $timeout = 0,
        ?InternetExecutor $executor = null
    ): void {
        self::fetch($page, "PATCH", $body, $headers, $timeout)
            ->then(function (InternetRequestResult $result) use ($executor) {
                $executor->getResolve()($result);
            })
            ->catch(function (Throwable $e)  use ($executor): void {
                $executor->getReject()($e);
            });
    }
}