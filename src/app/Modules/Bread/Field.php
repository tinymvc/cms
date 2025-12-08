<?php

namespace Cms\Modules\Bread;

use Closure;

/**
 * Field Class
 * 
 * Represents a form field with validation rules, rendering options, and value handling.
 * Provides a fluent interface for building form fields with various types and configurations.
 */
class Field
{
    protected string $name;
    protected string $type;
    protected mixed $value = null;
    protected string|null $label = null;
    protected string|null $placeholder = null;
    protected string|null $helperText = null;
    protected array $rules = [];
    protected array $options = []; // For select, radio, checkbox
    protected bool $required = false;
    protected bool $disabled = false;
    protected bool $readonly = false;
    protected array $attributes = [];
    protected Closure|null $formatter = null;
    protected Closure|null $condition = null; // Conditional rendering
    protected string|null $group = null; // Field group/section
    protected string|null $tab = null; // Field tab
    protected int $columnSpan = 12; // Grid column span (1-12)
    protected mixed $default = null;
    protected string|null $dependsOn = null; // Field dependency
    protected array $dependsOnValues = []; // Values that trigger visibility
    protected bool $repeatable = false;
    protected int $maxRepeat = 10;

    public function __construct(string $name, string $type = 'text')
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Create a new field instance
     */
    public static function make(string $name, string $type = 'text'): static
    {
        return new static($name, $type);
    }

    /**
     * Set field label
     */
    public function label(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set field placeholder
     */
    public function placeholder(string $placeholder): static
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * Set helper text
     */
    public function helperText(string $text): static
    {
        $this->helperText = $text;
        return $this;
    }

    /**
     * Set validation rules
     * 
     * @param string|array $rules Validation rules (string with | separator or array)
     */
    public function rules(string|array $rules): static
    {
        if (is_string($rules)) {
            $this->rules = array_map('trim', explode('|', $rules));
        } else {
            $this->rules = $rules;
        }

        // Auto-detect required field
        if (in_array('required', $this->rules, true)) {
            $this->required = true;
        }

        return $this;
    }

    /**
     * Set field options (for select, radio, checkbox)
     * 
     * @param array $options Key-value pairs ['value' => 'label']
     */
    public function options(array $options): static
    {
        $this->options = $options;
        return $this;
    }

    /**
     * Mark field as required
     */
    public function required(bool $required = true): static
    {
        $this->required = $required;
        if ($required && !in_array('required', $this->rules, true)) {
            $this->rules[] = 'required';
        }
        return $this;
    }

    /**
     * Mark field as disabled
     */
    public function disabled(bool $disabled = true): static
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * Mark field as readonly
     */
    public function readonly(bool $readonly = true): static
    {
        $this->readonly = $readonly;
        return $this;
    }

    /**
     * Set custom HTML attributes
     */
    public function attributes(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Set value formatter (for display purposes)
     */
    public function formatter(Closure $formatter): static
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Set conditional rendering
     * 
     * @param Closure $condition Callback that returns true to show field
     */
    public function when(Closure $condition): static
    {
        $this->condition = $condition;
        return $this;
    }

    /**
     * Set field group
     */
    public function group(string $group): static
    {
        $this->group = $group;
        return $this;
    }

    /**
     * Set field tab
     */
    public function tab(string $tab): static
    {
        $this->tab = $tab;
        return $this;
    }

    /**
     * Set column span for grid layout
     */
    public function columnSpan(int $span): static
    {
        $this->columnSpan = min(12, max(1, $span));
        return $this;
    }

    /**
     * Set default value
     */
    public function default(mixed $default): static
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Set field dependency (show/hide based on other field)
     */
    public function dependsOn(string $fieldName, array $values = []): static
    {
        $this->dependsOn = $fieldName;
        $this->dependsOnValues = $values;
        return $this;
    }

    /**
     * Make field repeatable
     */
    public function repeatable(bool $repeatable = true, int $max = 10): static
    {
        $this->repeatable = $repeatable;
        $this->maxRepeat = $max;
        return $this;
    }

    /**
     * Set field value
     */
    public function value(mixed $value): static
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get field name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get field type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get field value (with default fallback)
     */
    public function getValue(): mixed
    {
        return $this->value ?? $this->default;
    }

    /**
     * Get field label (auto-generate from name if not set)
     */
    public function getLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        // Auto-generate label from field name
        return str($this->name)->headline()->toString();
    }

    /**
     * Get validation rules
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Get field options
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Check if field is required
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Check if field is disabled
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * Check if field is readonly
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * Check if field should be visible (based on condition)
     */
    public function isVisible(array $formData = []): bool
    {
        if ($this->condition) {
            return call_user_func($this->condition, $formData);
        }
        return true;
    }

    /**
     * Get formatted value
     */
    public function getFormattedValue(): mixed
    {
        if ($this->formatter) {
            return call_user_func($this->formatter, $this->getValue());
        }
        return $this->getValue();
    }

    /**
     * Get all field properties as array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'value' => $this->getValue(),
            'label' => $this->getLabel(),
            'placeholder' => $this->placeholder,
            'helperText' => $this->helperText,
            'rules' => $this->rules,
            'options' => $this->options,
            'required' => $this->required,
            'disabled' => $this->disabled,
            'readonly' => $this->readonly,
            'attributes' => $this->attributes,
            'group' => $this->group,
            'tab' => $this->tab,
            'columnSpan' => $this->columnSpan,
            'default' => $this->default,
            'dependsOn' => $this->dependsOn,
            'dependsOnValues' => $this->dependsOnValues,
            'repeatable' => $this->repeatable,
            'maxRepeat' => $this->maxRepeat,
        ];
    }

    /**
     * Get HTML attributes string
     */
    public function getAttributesString(): string
    {
        $attrs = [];

        if ($this->required) {
            $attrs[] = 'required';
        }

        if ($this->disabled) {
            $attrs[] = 'disabled';
        }

        if ($this->readonly) {
            $attrs[] = 'readonly';
        }

        if ($this->placeholder) {
            $attrs[] = sprintf('placeholder="%s"', htmlspecialchars($this->placeholder));
        }

        foreach ($this->attributes as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $attrs[] = $key;
                }
            } else {
                $attrs[] = sprintf('%s="%s"', $key, htmlspecialchars($value));
            }
        }

        return implode(' ', $attrs);
    }
}
