<?php

declare(strict_types=1);

/**
 * Пример проверки токена GateForm в Yii3.
 *
 * Переменные окружения:
 *   GATEFORM_SITE_KEY, GATEFORM_SECRET_KEY, GATEFORM_BASE_URL
 */

require __DIR__ . '/../vendor/autoload.php';

use GateForm\Client;
use GateForm\Yii3\TokenValidator;

$validator = new TokenValidator(
    Client::create([
        'site_key' => (string) (getenv('GATEFORM_SITE_KEY') ?: ''),
        'secret_key' => (string) (getenv('GATEFORM_SECRET_KEY') ?: ''),
        'base_url' => (string) (getenv('GATEFORM_BASE_URL') ?: 'https://gateform.ru'),
        'fail_open' => false,
    ])
);

$token = (string) ($_POST['gf_token'] ?? '');
$result = $validator->validate($token);

if (!$result->isValid()) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Не удалось проверить форму. Обновите страницу и попробуйте снова.';
    exit;
}

echo 'Токен принят.';
