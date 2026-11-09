<?php

declare(strict_types=1);

use GateForm\Yii3\Widget\Callback;

?>
<div class="callback-block">
    <?= (new Callback(
        'cf_0123456789abcdef0123456789abcdef',
        'https://gateform.ru',
        'Заказать звонок'
    ))->render() ?>
</div>
