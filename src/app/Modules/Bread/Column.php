<?php

namespace Cms\Modules\Bread;

use Closure;

/**
 * Column Class
 * 
 * Represents a table column with formatting, sorting, and rendering options.
 */
class Column
{
    protected string $name;
    protected ?string $label = null;
    protected bool $sortable = false;
    protected bool $searchable = false;
    protected ?Closure $formatter = null;
    protected ?string $align = null; // left, center, right
    protected bool $hidden = false;
    protected ?Closure $condition = null;
    protected string $type = 'text'; // text, badge, image, date, boolean, custom
    protected array $badgeColors = []; // For badge type: ['value' => 'color-class']
    protected ?string $width = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Create a new column instance
     */
    public static function make(string $name): static
    {
        return new static($name);
    }

    /**
     * Set column label
     */
    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Make column sortable
     */
    public function sortable(bool $sortable = true): static
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * Make column searchable
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * Set value formatter
     */
    public function formatter(Closure $formatter): static
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Set column alignment
     */
    public function align(string $align): static
    {
        $this->align = $align;
        return $this;
    }

    /**
     * Hide column
     */
    public function hidden(bool $hidden = true): static
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * Set conditional visibility
     */
    public function when(Closure $condition): static
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Set column type as badge with color mapping
     */
    public function badge(array $colors = []): static
    {
        $this->type = 'badge';
        $this->badgeColors = $colors;
        return $this;
    }

    /**
     * Set column type as image
     */
    public function image(): static
    {
        $this->type = 'image';
        return $this;
    }

    /**
     * Set column type as date
     */
    public function date(string $format = 'Y-m-d'): static
    {
        $this->type = 'date';
        $this->formatter(fn($value) => $value ? date($format, strtotime($value)) : '-');
        return $this;
    }

    /**
     * Set column type as datetime
     */
    public function datetime(string $format = 'Y-m-d H:i:s'): static
    {
        $this->type = 'date';
        $this->formatter(fn($value) => $value ? date($format, strtotime($value)) : '-');
        return $this;
    }

    /**
     * Set column type as boolean
     */
    public function boolean(): static
    {
        $this->type = 'boolean';
        return $this;
    }

    /**
     * Set column width
     */
    public function width(string $width): static
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Get column name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get column label
     */
    public function getLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        // Auto-generate label from column name
        return str($this->name)
            ->replace('_', ' ')
            ->replace('-', ' ')
            ->title()
            ->toString();
    }

    /**
     * Check if column is sortable
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Check if column is searchable
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Check if column is hidden
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Check if column is visible based on condition
     */
    public function isVisible(array $data = []): bool
    {
        if ($this->condition) {
            return call_user_func($this->condition, $data);
        }
        return !$this->hidden;
    }

    /**
     * Format value
     */
    public function formatValue(mixed $value, $record = null): mixed
    {
        if ($this->formatter) {
            return call_user_func($this->formatter, $value, $record);
        }
        return $value;
    }

    /**
     * Get column type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get badge color for value
     */
    public function getBadgeColor(mixed $value): string
    {
        return $this->badgeColors[$value] ?? 'bg-gray-500';
    }

    /**
     * Get column alignment
     */
    public function getAlign(): ?string
    {
        return $this->align;
    }

    /**
     * Get column width
     */
    public function getWidth(): ?string
    {
        return $this->width;
    }

    /**
     * Get all column properties as array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->getLabel(),
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'hidden' => $this->hidden,
            'type' => $this->type,
            'align' => $this->align,
            'width' => $this->width,
            'badgeColors' => $this->badgeColors,
        ];
    }
}
