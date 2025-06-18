<?php

namespace Swis\Filament\ActivityLog\AttributeTable;

use Stringable;

/** @phpstan-consistent-constructor */
class Attribute
{
    protected string $key;

    /**
     * @var string|Stringable
     */
    protected $value;

    /**
     * @var string|Stringable|null
     */
    protected $oldValue = null;

    protected string $label;

    public function __construct(string $key, string | Stringable $value, string $label)
    {
        $this->key = $key;
        $this->value = $value;
        $this->label = $label;
    }

    public static function make(string $key, string | Stringable $value, string $label): static
    {
        return new static($key, $value, $label);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string|Stringable
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @return string|Stringable|null
     */
    public function getOldValue()
    {
        return $this->oldValue;
    }

    /**
     * @param  string|Stringable  $oldValue
     * @return $this
     */
    public function withOldValue($oldValue): static
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
