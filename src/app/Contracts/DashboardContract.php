<?php

namespace Cms\Contracts;

use Spark\Support\Collection;

interface DashboardContract
{
    /**
     * Initialize the dashboard
     */
    public function init(): void;

    /**
     * Register a new post type
     */
    public function registerPostType(string $postType, array $args = []): bool;

    /**
     * Get a registered post type
     */
    public function getPostType(string $postType): ?array;

    /**
     * Get all registered post types
     */
    public function getPostTypes(): Collection;

    /**
     * Add a meta box
     */
    public function addMetaBox(
        string $id,
        string $title,
        callable $callback,
        string|array $postType = 'post',
        string $context = 'normal',
        string $priority = 'default',
        array $callbackArgs = []
    ): bool;

    /**
     * Get meta boxes for a post type
     */
    public function getMetaBoxes(string $postType, ?string $context = null): Collection;

    /**
     * Register a taxonomy
     */
    public function registerTaxonomy(string $taxonomy, string|array $objectType, array $args = []): bool;

    /**
     * Get a registered taxonomy
     */
    public function getTaxonomy(string $taxonomy): ?array;

    /**
     * Get all registered taxonomies
     */
    public function getTaxonomies(): Collection;

    /**
     * Get taxonomies for a specific post type
     */
    public function getTaxonomiesForPostType(string $postType): Collection;

    /**
     * Check if a taxonomy is registered
     */
    public function taxonomyExists(string $taxonomy): bool;

    /**
     * Add a menu item
     */
    public function addMenu(
        string $slug,
        string $title,
        callable|string|null $callback = null,
        ?string $icon = null,
        int $position = 10,
        ?string $parent = null
    ): bool;

    /**
     * Get all menu items
     */
    public function getMenu(): Collection;
}