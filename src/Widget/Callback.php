<?php

declare(strict_types=1);

namespace GateForm\Yii3\Widget;

/**
 * Форма на сайте GateForm.
 *
 * <?= (new \GateForm\Yii3\Widget\Callback('cf_...', 'https://gateform.ru', 'Заказать звонок'))->render() ?>
 */
final class Callback extends AbstractWidget
{
    private string $hash;
    private string $title;
    private string $target;

    public function __construct(
        string $hash,
        string $baseUrl = 'https://gateform.ru',
        string $title = '',
        string $target = ''
    ) {
        parent::__construct($baseUrl);

        $hash = strtolower(trim($hash));
        if (!preg_match('/^cf_[a-f0-9]{32}$/', $hash)) {
            throw new \InvalidArgumentException('GateForm: укажите публичный ключ callback-формы вида cf_...');
        }

        $this->hash = $hash;
        $this->title = trim($title);
        $this->target = $this->assertTarget($target);
    }

    public function render(): string
    {
        $attributes = ['data-callback-form' => $this->hash];
        if ($this->target !== '') {
            $attributes['data-target'] = $this->target;
        }

        $html = $this->script('/callback-embed.js', $attributes);
        if ($this->title !== '') {
            $html = '<h3 class="gateform-callback-title">' . $this->escape($this->title) . '</h3>' . $html;
        }

        return '<div class="gateform-callback">' . $html . '</div>';
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
