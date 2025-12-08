<?php

namespace Cms\Modules\Bread;

use Closure;
use Spark\Database\Model;
use Spark\Foundation\Application;
use Spark\Http\Request;

/**
 * Table Class
 * 
 * Fluent table builder for BREAD operations with columns, filters, actions, and pagination.
 * Provides FilamentPHP-like interface for creating dynamic data tables.
 */
class Table
{
    /** @var Column[] */
    protected array $columns = [];

    /** @var array */
    protected array $filters = [];

    /** @var array */
    protected array $actions = [];

    /** @var array */
    protected array $bulkActions = [];

    protected Model $model;
    protected int $perPage = 15;
    protected bool $searchable = true;
    protected string $searchPlaceholder = 'Search...';
    protected Closure|null $query = null;
    protected string|null $emptyStateMessage = null;
    protected string|null $emptyStateIcon = null;
    protected array $defaultSort = ['id', 'desc'];
    protected bool $striped = true;
    protected bool $hoverable = true;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->emptyStateMessage = 'No records found.';
    }

    /**
     * Create a new table instance
     */
    public static function make(Model $model): static
    {
        return new static($model);
    }

    /**
     * Add a text column
     */
    public function column(string $name): Column
    {
        $column = Column::make($name);
        $this->columns[$name] = $column;
        return $column;
    }

    /**
     * Add multiple columns at once
     */
    public function columns(array $columns): static
    {
        foreach ($columns as $column) {
            if ($column instanceof Column) {
                $this->columns[$column->getName()] = $column;
            } elseif (is_string($column)) {
                $this->column($column);
            }
        }
        return $this;
    }

    /**
     * Add a filter
     * 
     * @param string $name Filter name
     * @param string $label Filter label
     * @param array $options Filter options ['value' => 'label']
     * @param Closure|null $query Query modification callback
     */
    public function filter(string $name, string $label, array $options, ?Closure $query = null): static
    {
        $this->filters[$name] = [
            'label' => $label,
            'options' => $options,
            'query' => $query,
        ];
        return $this;
    }

    /**
     * Add status filter
     */
    public function statusFilter(array $statuses = []): static
    {
        if (empty($statuses)) {
            $statuses = [
                'published' => 'Published',
                'draft' => 'Draft',
                'pending' => 'Pending',
                'trash' => 'Trash',
            ];
        }

        return $this->filter('status', 'Status', $statuses, function ($query, $value) {
            $query->where('status', $value);
        });
    }

    /**
     * Add date range filter
     */
    public function dateRangeFilter(string $column = 'created_at', string $label = 'Date Range'): static
    {
        $this->filters['date_range'] = [
            'type' => 'date_range',
            'label' => $label,
            'column' => $column,
        ];
        return $this;
    }

    /**
     * Add a row action
     * 
     * @param string $name Action name
     * @param string $label Action label
     * @param string|Closure $url URL or callback to generate URL
     * @param array $options Additional options (icon, color, modal, etc.)
     */
    public function action(string $name, string $label, string|Closure $url, array $options = []): static
    {
        $this->actions[$name] = array_merge([
            'label' => $label,
            'url' => $url,
        ], $options);
        return $this;
    }

    /**
     * Add edit action
     */
    public function editAction(string|Closure $url): static
    {
        return $this->action('edit', 'Edit', $url, [
            'icon' => 'pencil',
            'color' => 'primary',
        ]);
    }

    /**
     * Add view action
     */
    public function viewAction(string|Closure $url): static
    {
        return $this->action('view', 'View', $url, [
            'icon' => 'eye',
            'color' => 'secondary',
        ]);
    }

    /**
     * Add delete action
     */
    public function deleteAction(string|Closure $url): static
    {
        return $this->action('delete', 'Delete', $url, [
            'icon' => 'trash',
            'color' => 'danger',
            'confirm' => 'Are you sure you want to delete this record?',
        ]);
    }

    /**
     * Add a bulk action
     * 
     * @param string $name Action name
     * @param string $label Action label
     * @param Closure $handler Callback to handle bulk action
     */
    public function bulkAction(string $name, string $label, Closure $handler): static
    {
        $this->bulkActions[$name] = [
            'label' => $label,
            'handler' => $handler,
        ];
        return $this;
    }

    /**
     * Add bulk delete action
     */
    public function bulkDeleteAction(): static
    {
        return $this->bulkAction('delete', 'Delete Selected', function (array $ids) {
            $this->model::query()->whereIn($this->model::$primaryKey, $ids)->delete();
        });
    }

    /**
     * Set custom query modifier
     */
    public function modifyQuery(Closure $callback): static
    {
        $this->query = $callback;
        return $this;
    }

    /**
     * Set records per page
     */
    public function perPage(int $perPage): static
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Enable/disable search
     */
    public function searchable(bool $searchable = true): static
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * Set search placeholder
     */
    public function searchPlaceholder(string $placeholder): static
    {
        $this->searchPlaceholder = $placeholder;
        return $this;
    }

    /**
     * Set default sort
     */
    public function defaultSort(string $column, string $direction = 'desc'): static
    {
        $this->defaultSort = [$column, strtolower($direction)];
        return $this;
    }

    /**
     * Set empty state message
     */
    public function emptyStateMessage(string $message, ?string $icon = null): static
    {
        $this->emptyStateMessage = $message;
        $this->emptyStateIcon = $icon;
        return $this;
    }

    /**
     * Set table striped
     */
    public function striped(bool $striped = true): static
    {
        $this->striped = $striped;
        return $this;
    }

    /**
     * Set table hoverable
     */
    public function hoverable(bool $hoverable = true): static
    {
        $this->hoverable = $hoverable;
        return $this;
    }

    /**
     * Get table data with applied filters, search, and pagination
     */
    public function getData()
    {
        // Get current request
        $request = Application::$app->make(Request::class);

        // Start query
        $query = $this->model::query();

        // Apply custom query modifier
        if ($this->query) {
            call_user_func($this->query, $query);
        }

        // Apply search
        if ($this->searchable && $request->has('search')) {
            $searchTerm = $request->input('search');
            $searchableColumns = array_filter($this->columns, fn($col) => $col->isSearchable());

            if (!empty($searchableColumns) && $searchTerm) {
                $query->grouped(function ($q) use ($searchableColumns, $searchTerm) {
                    foreach ($searchableColumns as $column) {
                        $q->orWhere($column->getName(), 'like', "%{$searchTerm}%");
                    }
                });
            }
        }

        // Apply filters
        foreach ($this->filters as $filterName => $filter) {
            if ($request->has($filterName)) {
                $filterValue = $request->input($filterName);

                if ($filterValue !== null && $filterValue !== '') {
                    if (isset($filter['query'])) {
                        call_user_func($filter['query'], $query, $filterValue);
                    }
                }
            }
        }

        // Apply sorting
        $sortColumn = $request->input('sort', $this->defaultSort[0]);
        $sortDirection = $request->input('direction', $this->defaultSort[1]);

        // Validate sort column exists
        if (isset($this->columns[$sortColumn]) && $this->columns[$sortColumn]->isSortable()) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy($this->defaultSort[0], $this->defaultSort[1]);
        }

        // Paginate
        return $query->paginate($this->perPage);
    }

    /**
     * Get columns
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Get visible columns
     */
    public function getVisibleColumns(): array
    {
        return array_filter($this->columns, fn($col) => $col->isVisible());
    }

    /**
     * Get filters
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Get actions
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Get bulk actions
     */
    public function getBulkActions(): array
    {
        return $this->bulkActions;
    }

    /**
     * Get action URL for a record
     */
    public function getActionUrl(string $actionName, $record): ?string
    {
        if (!isset($this->actions[$actionName])) {
            return null;
        }

        $action = $this->actions[$actionName];
        $url = $action['url'];

        if ($url instanceof Closure) {
            return call_user_func($url, $record);
        }

        // Replace {id} placeholder
        return str_replace('{id}', $record->id ?? $record['id'] ?? '', $url);
    }

    /**
     * Get model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * Get per page
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Check if searchable
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Get search placeholder
     */
    public function getSearchPlaceholder(): string
    {
        return $this->searchPlaceholder;
    }

    /**
     * Get empty state message
     */
    public function getEmptyStateMessage(): string
    {
        return $this->emptyStateMessage;
    }

    /**
     * Check if table is striped
     */
    public function isStriped(): bool
    {
        return $this->striped;
    }

    /**
     * Check if table is hoverable
     */
    public function isHoverable(): bool
    {
        return $this->hoverable;
    }

    /**
     * Render table view
     */
    public function render()
    {
        $data = $this->getData();

        return view('cms::bread.table', [
            'table' => $this,
            'data' => $data,
            'columns' => $this->getVisibleColumns(),
            'filters' => $this->filters,
            'actions' => $this->actions,
            'bulkActions' => $this->bulkActions,
        ]);
    }
}
