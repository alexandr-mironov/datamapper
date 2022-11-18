<?php

declare(strict_types=1);

namespace DataMapper\Helpers;

use Exception;

trait ColumnTrait
{
    /**
     * @param string $definition
     *
     * @return string
     * @throws Exception
     */
    public function enrichColumnDefinition(string $definition): string
    {
        if (
            !property_exists(static::class, 'length')
            || !property_exists(static::class, 'default')
            || !property_exists(static::class, 'options')
        ) {
            throw new Exception('Usage on this instance is unavailable, instance SHOULD contain properties: lentgh, default and options');
        }

        if ($this->length) {
            $definition .= '(' . $this->length . ')';
        }

        if (!is_null($this->default)) {
            $definition .= ' DEFAULT ' . $this->default;
        }

        if ($this->options) {
            $definition .= ' ' . implode(' ', $this->options);
        }

        return $definition;
    }
}
