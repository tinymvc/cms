<?php

namespace Cms\Modules\Bread;

use Closure;
use Spark\Contracts\Support\Arrayable;
use Spark\Database\Model;
use Spark\Http\Request;
use Spark\Http\Validator;

/**
 * Form Class
 * 
 * Fluent form builder with validation, field management, and model integration.
 * Provides FilamentPHP-like interface for creating dynamic forms with validation rules.
 */
class Form
{
    /** @var Field[] */
    protected array $fields = [];

    protected Model $model;
    protected array $errors = [];
    protected array $validatedData = [];
    protected string|null $action = null;
    protected string $method = 'POST';
    protected array $tabs = [];
    protected array $fillable = [];
    protected array $groups = [];
    protected string $submitLabel = 'Save';
    protected Closure|null $beforeSave = null;
    protected Closure|null $afterSave = null;

    public function __construct(Model $model, array $fields = [])
    {
        $this->model = $model;

        // Initialize fields if provided as Field instances
        foreach ($fields as $field) {
            if ($field instanceof Field) {
                $this->fields[$field->getName()] = $field;
            }
        }

        // Auto-fill field values from model if it exists
        $model->exists() && $this->fillFromModel();
    }

    /**
     * Create a new form instance
     */
    public static function make(Model $model, array $fields = []): static
    {
        return new static($model, $fields);
    }

    /**
     * Add a text field
     */
    public function text(string $name): Field
    {
        $field = Field::make($name, 'text');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a textarea field
     */
    public function textarea(string $name): Field
    {
        $field = Field::make($name, 'textarea');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a rich editor field (WYSIWYG)
     */
    public function richEditor(string $name): Field
    {
        $field = Field::make($name, 'rich_editor');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a select dropdown field
     */
    public function select(string $name): Field
    {
        $field = Field::make($name, 'select');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a checkbox field
     */
    public function checkbox(string $name): Field
    {
        $field = Field::make($name, 'checkbox');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a radio field
     */
    public function radio(string $name): Field
    {
        $field = Field::make($name, 'radio');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a date field
     */
    public function date(string $name): Field
    {
        $field = Field::make($name, 'date');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a datetime field
     */
    public function datetime(string $name): Field
    {
        $field = Field::make($name, 'datetime');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a time field
     */
    public function time(string $name): Field
    {
        $field = Field::make($name, 'time');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a number field
     */
    public function number(string $name): Field
    {
        $field = Field::make($name, 'number');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add an email field
     */
    public function email(string $name): Field
    {
        $field = Field::make($name, 'email')
            ->rules('email');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a password field
     */
    public function password(string $name): Field
    {
        $field = Field::make($name, 'password');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a file upload field
     */
    public function file(string $name): Field
    {
        $field = Field::make($name, 'file');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add an image upload field
     */
    public function image(string $name): Field
    {
        $field = Field::make($name, 'image')
            ->rules('image');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a hidden field
     */
    public function hidden(string $name): Field
    {
        $field = Field::make($name, 'hidden');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a color picker field
     */
    public function color(string $name): Field
    {
        $field = Field::make($name, 'color');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a URL field
     */
    public function url(string $name): Field
    {
        $field = Field::make($name, 'url')
            ->rules('url');
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a repeater field (repeatable group of fields)
     */
    public function repeater(string $name): Field
    {
        $field = Field::make($name, 'repeater')
            ->repeatable(true);
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add a custom field
     */
    public function field(string $name, string $type = 'text'): Field
    {
        $field = Field::make($name, $type);
        $this->fields[$name] = $field;
        return $field;
    }

    /**
     * Add multiple fields at once
     */
    public function fields(array $fields): static
    {
        foreach ($fields as $field) {
            if ($field instanceof Field) {
                $this->fields[$field->getName()] = $field;
            } elseif (is_string($field)) {
                $this->text($field);
            }
        }
        return $this;
    }

    /**
     * Set form action URL
     */
    public function action(string $action): static
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Set form method
     */
    public function method(string $method): static
    {
        $this->method = strtoupper($method);
        return $this;
    }

    /**
     * Set submit button label
     */
    public function submitLabel(string $label): static
    {
        $this->submitLabel = $label;
        return $this;
    }

    /**
     * Add a tab
     */
    public function tab(string $name, string $label): static
    {
        $this->tabs[$name] = $label;
        return $this;
    }

    /**
     * Add a group/section
     */
    public function group(string $name, string $label): static
    {
        $this->groups[$name] = $label;
        return $this;
    }

    /**
     * Set callback to run before save
     */
    public function beforeSave(Closure $callback): static
    {
        $this->beforeSave = $callback;
        return $this;
    }

    /**
     * Set callback to run after save
     */
    public function afterSave(Closure $callback): static
    {
        $this->afterSave = $callback;
        return $this;
    }

    /**
     * Get fillable fields
     */
    public function getFillable(): array
    {
        return $this->fillable;
    }

    /**
     * Set fillable fields
     */
    public function fillable(array $fields): static
    {
        $this->fillable = $fields;
        return $this;
    }

    /**
     * Fill field values from model
     */
    protected function fillFromModel(): void
    {
        foreach ($this->fields as $field) {
            $fieldName = $field->getName();

            // Check if model has this attribute
            if ($this->model->isset($fieldName)) {
                $field->value($this->model->get($fieldName));
            }
        }
    }

    /**
     * Fill field values from request data
     */
    public function fillFromRequest(Request $request): static
    {
        foreach ($this->fields as $field) {
            $fieldName = $field->getName();
            $value = $request->input($fieldName);

            if ($value !== null) {
                $field->value($value);
            }
        }

        return $this;
    }

    /**
     * Validate form data
     * 
     * @param Request|array|null $data Request object or array of data to validate
     * @return bool True if validation passes
     */
    public function validate(Request|Arrayable|array|null $data = null): bool
    {
        // Prepare data for validation
        if ($data instanceof Request) {
            $inputData = $data->all();
        } elseif (is_array($data)) {
            $inputData = $data;
        } elseif ($data instanceof Arrayable) {
            $inputData = $data->toArray();
        } else {
            // No data provided
            return false;
        }

        // Build validation rules from fields
        $rules = [];
        foreach ($this->fields as $field) {
            $fieldRules = $field->getRules();
            if (!empty($fieldRules)) {
                $rules[$field->getName()] = $fieldRules;
            }
        }

        // If no rules, consider it valid
        if (empty($rules)) {
            $this->validatedData = $inputData;
            return true;
        }

        // Validate
        $validator = new Validator();
        $result = $validator->validate($rules, $inputData);

        if ($result === false) {
            $this->errors = $validator->getErrors();
            return false;
        }

        // Store validated data
        $this->validatedData = $result->all();
        return true;
    }

    /**
     * Get validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get error for specific field
     */
    public function getError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }

    /**
     * Check if field has error
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    /**
     * Get validated data
     */
    public function getValidatedData(): array
    {
        return $this->validatedData;
    }

    /**
     * Save form data to model
     * 
     * @param Request|array|null $data Data to validate and save
     * @param bool $validate Whether to validate before saving
     * @return bool|Model Returns model instance on success, false on validation failure
     */
    public function save(Request|Arrayable|array|null $data = null, bool $validate = true): bool|Model
    {
        // Validate if requested
        if ($validate) {
            if (!$this->validate($data)) {
                return false;
            }
            $dataToSave = $this->validatedData;
        } else {
            // Use provided data or request data
            if ($data instanceof Request) {
                $dataToSave = $data->all();
            } elseif (is_array($data)) {
                $dataToSave = $data;
            } elseif ($data instanceof Arrayable) {
                $dataToSave = $data->toArray();
            } else {
                return false;
            }
        }

        if (empty($this->fillable)) {
            // If no fillable specified, use all fields
            $fields = $this->fields;
        } else {
            // Filter fields based on fillable
            $fields = array_filter($this->fields, fn(Field $field) => in_array($field->getName(), $this->fillable));
        }

        // Filter only fillable fields
        $fillableData = [];
        foreach ($fields as $field) {
            $fieldName = $field->getName();
            if (array_key_exists($fieldName, $dataToSave)) {
                $fillableData[$fieldName] = $dataToSave[$fieldName];
            }
        }

        // Run beforeSave callback
        if ($this->beforeSave) {
            $fillableData = call_user_func($this->beforeSave, $fillableData, $this->model) ?? $fillableData;
        }

        // Fill and save model
        $this->model->fill($fillableData);
        $saved = $this->model->save();

        if (!$saved) {
            return false;
        }

        // Run afterSave callback
        if ($this->afterSave) {
            call_user_func($this->afterSave, $this->model, $fillableData);
        }

        return $this->model;
    }

    /**
     * Get all fields
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Get field by name
     */
    public function getField(string $name): ?Field
    {
        return $this->fields[$name] ?? null;
    }

    /**
     * Get fields organized by tabs
     */
    public function getFieldsByTabs(): array
    {
        $fieldsByTabs = [];

        foreach ($this->fields as $field) {
            $tab = $field->toArray()['tab'] ?? 'default';
            $fieldsByTabs[$tab][] = $field;
        }

        return $fieldsByTabs;
    }

    /**
     * Get fields organized by groups
     */
    public function getFieldsByGroups(): array
    {
        $fieldsByGroups = [];

        foreach ($this->fields as $field) {
            $group = $field->toArray()['group'] ?? 'default';
            $fieldsByGroups[$group][] = $field;
        }

        return $fieldsByGroups;
    }

    /**
     * Get model instance
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get form action URL
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * Get form method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get submit label
     */
    public function getSubmitLabel(): string
    {
        return $this->submitLabel;
    }

    /**
     * Get tabs
     */
    public function getTabs(): array
    {
        return $this->tabs;
    }

    /**
     * Get groups
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Render form view
     */
    public function render()
    {
        return fireline('cms::bread.form', [
            'form' => $this,
            'model' => $this->model,
            'fields' => $this->fields,
            'errors' => $this->errors,
            'tabs' => $this->tabs,
            'groups' => $this->groups,
        ]);
    }
}