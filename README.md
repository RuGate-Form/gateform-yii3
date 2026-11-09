# GateForm для Yii3

[GateForm](https://gateform.ru) — капча и защита форм от спама и ботов.
Пакет добавляет в Yii3:

- публичные виджеты формы, отзывов и связи;
- `TokenValidator` для серверной проверки токена;
- PSR-15 middleware `VerifyTokenMiddleware`;
- параметры конфигурации для `yiisoft/config`.

- Сайт: <https://gateform.ru>
- Документация: <https://gateform.ru/docs>
- Поддержка: <info@gateform.ru>

Виджеты не наследуют внутренние классы Yii и не требуют секретного ключа.
Серверная проверка выполняется через `gateform/sdk`.

## Установка

```bash
composer require gateform/yii3:^1.0
```

Пакет `gateform/sdk` установится автоматически.

## Настройка

Переменные окружения:

```env
GATEFORM_SITE_KEY=gf_site_...
GATEFORM_SECRET_KEY=gf_sec_...
GATEFORM_BASE_URL=https://gateform.ru
GATEFORM_FAIL_OPEN=false
```

Подключите параметры из `config/params.php` в конфигурацию приложения или
скопируйте секцию `gateform` в свой `config/params.php`.

Пример регистрации в контейнере:

```php
use GateForm\Client;
use GateForm\Yii3\TokenValidator;

return [
    TokenValidator::class => static function (Psr\Container\ContainerInterface $container): TokenValidator {
        $params = $container->get('config-params')['gateform'];

        return new TokenValidator(
            Client::create([
                'site_key' => $params['site_key'],
                'secret_key' => $params['secret_key'],
                'base_url' => $params['base_url'],
                'fail_open' => (bool) $params['fail_open'],
                'token_field' => $params['token_field'],
            ]),
            $params['token_field']
        );
    },
];
```

Точные имена сервисов контейнера зависят от вашей конфигурации Yii3. Пример
показывает состав зависимостей; подставьте свои id.

## Виджеты

### Форма на сайте

```php
<?= (new \GateForm\Yii3\Widget\Callback(
    'cf_0123456789abcdef0123456789abcdef',
    'https://gateform.ru',
    'Заказать звонок'
))->render() ?>
```

### Отзывы товара

```php
<?= (new \GateForm\Yii3\Widget\Reviews(
    'rv_0123456789abcdef0123456789abcdef'
))
    ->withProduct($product->getSku(), $product->getName(), $product->getUrl())
    ->withSource('yii3')
    ->render() ?>
```

Если данные товара не переданы, `review-embed.js` попробует определить их по
JSON-LD и разметке страницы. Для магазина лучше передавать артикул, название
и URL явно.

### Виджет связи

```php
<?= (new \GateForm\Yii3\Widget\Contact('w_xxxxxxxx'))->render() ?>
```

## Проверка токена

### В обработчике запроса

```php
use GateForm\Yii3\TokenValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ContactAction
{
    public function __construct(private TokenValidator $validator)
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->validator->validateRequest($request, 'X-GateForm-Token');

        if (!$result->isValid()) {
            // вернуть 400 или добавить ошибку в форму
        }

        // ...
    }
}
```

Токен читается из тела запроса (`gf_token`) или из заголовка
`X-GateForm-Token`. Токен одноразовый.

### PSR-15 middleware

```php
use GateForm\Yii3\Middleware\VerifyTokenMiddleware;

$middleware = new VerifyTokenMiddleware(
    $validator,
    $responseFactory,
    $streamFactory,
    ['POST', 'PUT', 'PATCH', 'DELETE'],
    'X-GateForm-Token'
);
```

При отказе middleware возвращает `400` с текстом ошибки. После успешной
проверки результат доступен в атрибуте запроса `gateform`:

```php
$result = $request->getAttribute('gateform');
```

## Безопасность

- Все hash-идентификаторы и target проверяются по формату.
- HTML-атрибуты экранируются через `htmlspecialchars`.
- Секретный ключ GateForm не передаётся в браузер.
- `baseUrl` ограничен схемами `http` и `https`.

## Лицензия

MIT. См. [LICENSE](LICENSE).

Разработчик: **GateForm** — <https://gateform.ru>
