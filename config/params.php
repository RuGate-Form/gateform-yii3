<?php

declare(strict_types=1);

/**
 * Параметры GateForm для yiisoft/config.
 *
 * Скопируйте значения в свой config/params.php или подключите этот файл
 * через config-plugin. Секретный ключ храните только в переменных окружения.
 */

return [
    'gateform' => [
        'site_key' => getenv('GATEFORM_SITE_KEY') ?: '',
        'secret_key' => getenv('GATEFORM_SECRET_KEY') ?: '',
        'base_url' => getenv('GATEFORM_BASE_URL') ?: 'https://gateform.ru',
        'fail_open' => filter_var(getenv('GATEFORM_FAIL_OPEN') ?: 'false', FILTER_VALIDATE_BOOL),
        'token_field' => 'gf_token',
        'middleware' => [
            'methods' => ['POST', 'PUT', 'PATCH', 'DELETE'],
            'header' => 'X-GateForm-Token',
        ],
    ],
];
