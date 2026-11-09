<?php

declare(strict_types=1);

namespace GateForm\Yii3\Middleware;

use GateForm\Yii3\TokenValidator;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * PSR-15 middleware: проверяет токен GateForm до обработчика.
 *
 * Регистрация в конвейере Yii3:
 *
 *   new VerifyTokenMiddleware(
 *       $validator,
 *       $responseFactory,
 *       $streamFactory,
 *       ['POST', 'PUT', 'PATCH', 'DELETE']
 *   );
 *
 * После успешной проверки результат доступен в атрибуте запроса `gateform`.
 */
final class VerifyTokenMiddleware implements MiddlewareInterface
{
    /**
     * @param list<string> $methods
     */
    public function __construct(
        private TokenValidator $validator,
        private ResponseFactoryInterface $responseFactory,
        private StreamFactoryInterface $streamFactory,
        private array $methods = ['POST', 'PUT', 'PATCH', 'DELETE'],
        private string $header = 'X-GateForm-Token',
        private string $message = 'Не удалось подтвердить, что вы человек. Обновите страницу и попробуйте снова.'
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        if (!$this->shouldVerify($request)) {
            return $handler->handle($request);
        }

        $result = $this->validator->validateRequest($request, $this->header);

        if (!$result->isValid()) {
            return $this->responseFactory
                ->createResponse(400)
                ->withHeader('Content-Type', 'text/plain; charset=UTF-8')
                ->withBody($this->streamFactory->createStream($this->message));
        }

        return $handler->handle($request->withAttribute('gateform', $result));
    }

    private function shouldVerify(ServerRequestInterface $request): bool
    {
        $method = strtoupper($request->getMethod());
        $allowed = array_map('strtoupper', $this->methods);

        return in_array($method, $allowed, true);
    }
}
