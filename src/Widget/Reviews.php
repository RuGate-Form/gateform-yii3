<?php

declare(strict_types=1);

namespace GateForm\Yii3\Widget;

/**
 * Отзывы товара GateForm.
 *
 * <?= (new \GateForm\Yii3\Widget\Reviews('rv_...'))
 *     ->withProduct($sku, $name, $url)
 *     ->render() ?>
 */
final class Reviews extends AbstractWidget
{
    private string $hash;
    private string $productKey = '';
    private string $productName = '';
    private string $productUrl = '';
    private string $source = 'yii3';
    private string $target = '';

    public function __construct(string $hash, string $baseUrl = 'https://gateform.ru')
    {
        parent::__construct($baseUrl);

        $hash = strtolower(trim($hash));
        if (!preg_match('/^rv_[a-f0-9]{32}$/', $hash)) {
            throw new \InvalidArgumentException('GateForm: укажите публичный ключ отзывов вида rv_...');
        }

        $this->hash = $hash;
    }

    public function withProduct(string $key, string $name = '', string $url = ''): self
    {
        $this->productKey = trim($key);
        $this->productName = trim($name);
        $this->productUrl = trim($url);

        return $this;
    }

    public function withSource(string $source): self
    {
        $source = preg_replace('/[^a-z0-9_-]/i', '', trim($source)) ?? '';
        $this->source = $source !== '' ? substr($source, 0, 32) : 'yii3';

        return $this;
    }

    public function withTarget(string $target): self
    {
        $this->target = $this->assertTarget($target);

        return $this;
    }

    public function render(): string
    {
        $attributes = [
            'data-gateform-reviews' => $this->hash,
            'data-source' => $this->source,
            'data-product-key' => $this->productKey,
            'data-product-name' => $this->productName,
            'data-product-url' => $this->productUrl,
            'data-target' => $this->target,
        ];

        return '<div class="gateform-reviews">'
            . $this->script('/review-embed.js', $attributes)
            . '</div>';
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
