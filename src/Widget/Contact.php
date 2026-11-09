<?php

declare(strict_types=1);

namespace GateForm\Yii3\Widget;

/**
 * Виджет связи GateForm.
 *
 * <?= (new \GateForm\Yii3\Widget\Contact('w_xxxxxxxx'))->render() ?>
 */
final class Contact extends AbstractWidget
{
    private string $id;

    public function __construct(string $id, string $baseUrl = 'https://gateform.ru')
    {
        parent::__construct($baseUrl);

        $id = trim($id);
        if (!preg_match('/^[A-Za-z0-9_-]{3,64}$/', $id)) {
            throw new \InvalidArgumentException('GateForm: укажите id виджета связи вида w_...');
        }

        $this->id = $id;
    }

    public function render(): string
    {
        return $this->script('/widget.js', ['data-widget-id' => $this->id]);
    }

    public function __toString(): string
    {
        return $this->render();
    }
}
