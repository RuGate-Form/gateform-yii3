<?php

declare(strict_types=1);

namespace GateForm\Yii3\Widget;

/**
 * Общая сборка HTML для публичных виджетов GateForm.
 *
 * Класс не зависит от конкретной версии Yii и подходит для представлений Yii3.
 */
abstract class AbstractWidget
{
    protected string $baseUrl;

    public function __construct(string $baseUrl = 'https://gateform.ru')
    {
        $baseUrl = rtrim(trim($baseUrl), '/');
        if (!preg_match('#^https?://#i', $baseUrl)) {
            throw new \InvalidArgumentException('GateForm: baseUrl должен начинаться с http:// или https://.');
        }
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param array<string, string> $attributes
     */
    protected function script(string $path, array $attributes): string
    {
        $parts = ['src="' . $this->escape($this->baseUrl . $path) . '"'];
        foreach ($attributes as $name => $value) {
            if ($value !== '') {
                $parts[] = $this->escape($name) . '="' . $this->escape($value) . '"';
            }
        }
        $parts[] = 'defer';

        return '<script ' . implode(' ', $parts) . '></script>';
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    protected function assertTarget(string $target): string
    {
        $target = trim($target);
        if ($target !== '' && !preg_match('/^#[A-Za-z][A-Za-z0-9_-]{0,63}$/', $target)) {
            throw new \InvalidArgumentException('GateForm: target должен быть CSS-идентификатором вида #gateform-reviews.');
        }

        return $target;
    }
}
