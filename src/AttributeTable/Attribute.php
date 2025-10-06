<?php

namespace Swis\Filament\Activitylog\AttributeTable;

use Illuminate\Contracts\Support\Htmlable;
use Stringable;

/** @phpstan-consistent-constructor */
class Attribute
{
    protected string $key;

    protected string | Htmlable | Stringable $value;

    protected null | string | Htmlable | Stringable $oldValue = null;

    protected string $label;

    public function __construct(string $key, string | Htmlable | Stringable $value, string $label)
    {
        $this->key = $key;
        $this->value = $value;
        $this->label = $label;
    }

    public static function make(string $key, string | Htmlable | Stringable $value, string $label): static
    {
        return new static($key, $value, $label);
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getValue(): string | Htmlable | Stringable
    {
        return $this->value;
    }

    public function getOldValue(): null | string | Htmlable | Stringable
    {
        return $this->oldValue;
    }

    /**
     * @return $this
     */
    public function withOldValue(null | string | Htmlable | Stringable $oldValue): static
    {
        $this->oldValue = $oldValue;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }
}
