<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Http\Common\Result;
use App\Http\Common\ResultCode;
use Hyperf\Codec\Json;
use Hyperf\Context\Context;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Swow\Psr7\Message\ResponsePlusInterface;

class AppExceptionHandler extends AbstractHandler
{
    public function handleResponse(\Throwable $throwable): Result
    {
        $this->stopPropagation();
        return new Result(
            code: ResultCode::FAIL,
            message: $throwable->getMessage()
        );
    }

    public function handle(\Throwable $throwable, ResponsePlusInterface $response)
    {
        if ($this->isDebug()) {
            Context::set(static::class . '.throwable', [
                'message' => $throwable->getMessage(),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
                'trace' => $throwable->getTrace(),
            ]);
        }
        return parent::handle($throwable, $response); // TODO: Change the autogenerated stub
    }

    public function isValid(\Throwable $throwable): bool
    {
        return true;
    }

    protected function handlerResult(ResponsePlusInterface $responsePlus, Result $result): ResponsePlusInterface
    {
        if (! $this->isDebug()) {
            return parent::handlerResult($responsePlus, $result);
        }
        $result = $result->toArray();
        $result['throwable'] = Context::get(static::class . '.throwable');
        return $responsePlus
            ->setHeader('Content-Type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream(Json::encode($result)));
    }
}
