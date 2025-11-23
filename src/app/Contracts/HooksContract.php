<?php

namespace Cms\Contracts;

interface HooksContract
{
    /**
     * Add an action hook
     */
    public function addAction(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool;

    /**
     * Add a filter hook
     */
    public function addFilter(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool;

    /**
     * Execute an action hook
     */
    public function doAction(string $tag, ...$args): void;

    /**
     * Apply a filter hook
     */
    public function applyFilters(string $tag, $value, ...$args): mixed;

    /**
     * Remove an action hook
     */
    public function removeAction(string $tag, ?callable $callback = null, ?int $priority = null): bool;

    /**
     * Remove a filter hook
     */
    public function removeFilter(string $tag, ?callable $callback = null, ?int $priority = null): bool;

    /**
     * Check if an action exists
     */
    public function hasAction(string $tag, ?callable $callback = null): bool;

    /**
     * Check if a filter exists
     */
    public function hasFilter(string $tag, ?callable $callback = null): bool;

    /**
     * Remove all actions for a tag
     */
    public function removeAllActions(string $tag): bool;

    /**
     * Remove all filters for a tag
     */
    public function removeAllFilters(string $tag): bool;
}