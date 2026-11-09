<?php

declare(strict_types=1);

namespace GateForm\Yii3;

use GateForm\Client;
use GateForm\ValidationResult;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Проверка токена GateForm для Yii3 и PSR-7 запросов.
 *
 * Пример:
 *
 *   $validator = new TokenValidator(
 *       Client::create([
 *           'site_key' => getenv('GATEFORM_SITE_KEY'),
 *           'secret_key' => getenv('GATEFORM_SECRET_KEY'),
 *           'base_url' => getenv('GATEFORM_BASE_URL') ?: 'https://gateform.ru',
 *       ])
 *   );
 *
 *   $result = $validator->validateRequest($request);
 *   if (!$result->isValid()) {
 *       // отклонить форму
 *   }
 */
final class TokenValidator
{
    public function __construct(
        private Client $client,
        private string $tokenField = 'gf_token'
    ) {
        $tokenField = trim($tokenField);
        if (preg_match('/^[A-Za-z0-9_\-\[\]]{1,64}$/', $tokenField) !== 1) {
            throw new \InvalidArgumentException('GateForm: некорректное имя поля с токеном.');
        }
        $this->tokenField = $tokenField;
    }

    public function client(): Client
    {
        return $this->client;
    }

    public function tokenField(): string
    {
        return $this->tokenField;
    }

    public function validate(string $token): ValidationResult
    {
        return $this->client->validate($token);
    }

    /**
     * Читает токен из тела PSR-7 запроса или из заголовка.
     */
    public function validateRequest(
        ServerRequestInterface $request,
        ?string $header = null
    ): ValidationResult {
        $token = '';

        $body = $request->getParsedBody();
        if (is_array($body) && isset($body[$this->tokenField])) {
            $value = $body[$this->tokenField];
            if (is_string($value)) {
                $token = $value;
            }
        }

        if ($token === '' && $header !== null && $header !== '') {
            $values = $request->getHeader($header);
            if ($values !== [] && is_string($values[0])) {
                $token = $values[0];
            }
        }

        return $this->validate(trim($token));
    }
}
