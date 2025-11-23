<?php

namespace Cms\Modules;

use Cms\Contracts\HooksContract;
use Spark\Support\Collection;

class Hooks implements HooksContract
{
    public Collection $actions;
    public Collection $filters;

    public function __construct()
    {
        $this->actions = new Collection();
        $this->filters = new Collection();
    }

    /**
     * Add an action hook
     *
     * @param string $tag The name of the action
     * @param callable $callback The callback function to run
     * @param int $priority Priority of the action (lower runs first)
     * @param int $acceptedArgs Number of arguments the callback accepts
     * @return bool
     */
    public function addAction(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool
    {
        return $this->addHook('actions', $tag, $callback, $priority, $acceptedArgs);
    }

    /**
     * Add a filter hook
     *
     * @param string $tag The name of the filter
     * @param callable $callback The callback function to run
     * @param int $priority Priority of the filter (lower runs first)
     * @param int $acceptedArgs Number of arguments the callback accepts
     * @return bool
     */
    public function addFilter(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool
    {
        return $this->addHook('filters', $tag, $callback, $priority, $acceptedArgs);
    }

    /**
     * Execute an action hook
     *
     * @param string $tag The name of the action
     * @param mixed ...$args Arguments to pass to the callbacks
     * @return void
     */
    public function doAction(string $tag, ...$args): void
    {
        if (!$this->actions->has($tag)) {
            return;
        }

        $hooks = $this->actions->get($tag);

        // Sort by priority
        $hooks = collect($hooks)->sortBy('priority');

        foreach ($hooks as $hook) {
            $callback = $hook['callback'];
            $acceptedArgs = $hook['accepted_args'];

            // Call the callback with the specified number of arguments
            call_user_func_array($callback, array_slice($args, 0, $acceptedArgs));
        }
    }

    /**
     * Apply a filter hook
     *
     * @param string $tag The name of the filter
     * @param mixed $value The value to filter
     * @param mixed ...$args Additional arguments to pass to the callbacks
     * @return mixed The filtered value
     */
    public function applyFilters(string $tag, $value, ...$args): mixed
    {
        if (!$this->filters->has($tag)) {
            return $value;
        }

        $hooks = $this->filters->get($tag);

        // Sort by priority
        $hooks = collect($hooks)->sortBy('priority');

        foreach ($hooks as $hook) {
            $callback = $hook['callback'];
            $acceptedArgs = $hook['accepted_args'];

            // Prepend the value to the arguments
            $callbackArgs = array_merge([$value], array_slice($args, 0, $acceptedArgs - 1));

            // Apply the filter and update the value
            $value = call_user_func_array($callback, $callbackArgs);
        }

        return $value;
    }

    /**
     * Remove an action hook
     *
     * @param string $tag The name of the action
     * @param callable|null $callback The specific callback to remove (null removes all)
     * @param int|null $priority The priority of the callback to remove (null removes all priorities)
     * @return bool
     */
    public function removeAction(string $tag, ?callable $callback = null, ?int $priority = null): bool
    {
        return $this->removeHook('actions', $tag, $callback, $priority);
    }

    /**
     * Remove a filter hook
     *
     * @param string $tag The name of the filter
     * @param callable|null $callback The specific callback to remove (null removes all)
     * @param int|null $priority The priority of the callback to remove (null removes all priorities)
     * @return bool
     */
    public function removeFilter(string $tag, ?callable $callback = null, ?int $priority = null): bool
    {
        return $this->removeHook('filters', $tag, $callback, $priority);
    }

    /**
     * Check if an action has been registered
     *
     * @param string $tag The name of the action
     * @param callable|null $callback Optional specific callback to check
     * @return bool
     */
    public function hasAction(string $tag, ?callable $callback = null): bool
    {
        return $this->hasHook('actions', $tag, $callback);
    }

    /**
     * Check if a filter has been registered
     *
     * @param string $tag The name of the filter
     * @param callable|null $callback Optional specific callback to check
     * @return bool
     */
    public function hasFilter(string $tag, ?callable $callback = null): bool
    {
        return $this->hasHook('filters', $tag, $callback);
    }

    /**
     * Remove all actions for a tag
     *
     * @param string $tag The name of the action
     * @return bool
     */
    public function removeAllActions(string $tag): bool
    {
        if ($this->actions->has($tag)) {
            $this->actions->forget($tag);
            return true;
        }
        return false;
    }

    /**
     * Remove all filters for a tag
     *
     * @param string $tag The name of the filter
     * @return bool
     */
    public function removeAllFilters(string $tag): bool
    {
        if ($this->filters->has($tag)) {
            $this->filters->forget($tag);
            return true;
        }
        return false;
    }

    /**
     * Generic method to add a hook (action or filter)
     *
     * @param string $type 'actions' or 'filters'
     * @param string $tag Hook name
     * @param callable $callback Callback function
     * @param int $priority Priority
     * @param int $acceptedArgs Number of accepted arguments
     * @return bool
     */
    protected function addHook(string $type, string $tag, callable $callback, int $priority, int $acceptedArgs): bool
    {
        $hooks = $this->$type;

        if (!$hooks->has($tag)) {
            $hooks->put($tag, []);
        }

        $tagHooks = $hooks->get($tag);
        $tagHooks[] = [
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $acceptedArgs,
        ];

        $hooks->put($tag, $tagHooks);

        return true;
    }

    /**
     * Generic method to remove a hook
     *
     * @param string $type 'actions' or 'filters'
     * @param string $tag Hook name
     * @param callable|null $callback Specific callback to remove
     * @param int|null $priority Specific priority to remove
     * @return bool
     */
    protected function removeHook(string $type, string $tag, ?callable $callback, ?int $priority): bool
    {
        $hooks = $this->$type;

        if (!$hooks->has($tag)) {
            return false;
        }

        if ($callback === null) {
            // Remove all hooks for this tag
            $hooks->forget($tag);
            return true;
        }

        $tagHooks = $hooks->get($tag);
        $filtered = array_filter($tagHooks, function ($hook) use ($callback, $priority) {
            $callbackMatch = $hook['callback'] !== $callback;
            $priorityMatch = $priority === null || $hook['priority'] !== $priority;
            return $callbackMatch || !$priorityMatch;
        });

        if (empty($filtered)) {
            $hooks->forget($tag);
        } else {
            $hooks->put($tag, array_values($filtered));
        }

        return true;
    }

    /**
     * Generic method to check if a hook exists
     *
     * @param string $type 'actions' or 'filters'
     * @param string $tag Hook name
     * @param callable|null $callback Specific callback to check
     * @return bool
     */
    protected function hasHook(string $type, string $tag, ?callable $callback): bool
    {
        $hooks = $this->$type;

        if (!$hooks->has($tag)) {
            return false;
        }

        if ($callback === null) {
            return true;
        }

        $tagHooks = $hooks->get($tag);
        foreach ($tagHooks as $hook) {
            if ($hook['callback'] === $callback) {
                return true;
            }
        }

        return false;
    }
}