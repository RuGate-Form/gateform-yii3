<?php

declare(strict_types=1);

/** @var array{sku:string,name:string,url:string} $product */

use GateForm\Yii3\Widget\Reviews;

?>
<section class="product-reviews">
    <h2>Отзывы покупателей</h2>
    <?= (new Reviews('rv_0123456789abcdef0123456789abcdef'))
        ->withProduct($product['sku'], $product['name'], $product['url'])
        ->withSource('yii3')
        ->render() ?>
</section>
