<?php

namespace Cms\Services;

use Cms\Contracts\DashboardContract;
use Cms\Http\Controllers\PostController;
use Cms\Http\Controllers\TaxonomyController;
use Cms\Modules\Hooks;
use Cms\Modules\CustomPostType;
use Spark\Facades\Route;
use Spark\Support\Collection;
use function is_array;
use function sprintf;

class Dashboard implements DashboardContract
{
    /** @var Collection The menu items */
    public Collection $menu;

    /** @var Hooks The hooks manager */
    public Hooks $hooks;

    /** @var Collection<string, CustomPostType> The registered post types */
    public Collection $registeredPostTypes;

    /** @var Collection The site settings */
    public Collection $settings;

    public function __construct()
    {
        $this->menu = new Collection();
        $this->hooks = new Hooks();
        $this->registeredPostTypes = new Collection();
        $this->settings = new Collection();
    }

    public function init(): void
    {
        // Fire the init action
        $this->hooks->doAction('cms_init', $this);

        // Register default menu items
        $this->registerMenusItems();

        // Register menu items from post types and taxonomies
        $this->registerMenuItemsForCustomPostTypes();
    }

    /**
     * Register a new post type
     *
     * @param string $id The post type slug
     * @param array $args Configuration arguments for the post type
     * @return bool
     */
    public function registerPostType(string $id, array $args = []): bool
    {
        if ($this->registeredPostTypes->has($id)) {
            return false;
        }

        $postType = new CustomPostType($id, $args);

        $this->registeredPostTypes->put($id, $postType);

        // Fire action after post type registration
        $this->hooks->doAction('cms_registered_post_type', $id, $postType);

        return true;
    }

    /**
     * Get a registered post type
     *
     * @param string $id
     * @return CustomPostType|null
     */
    public function getPostType(string $id): ?CustomPostType
    {
        return $this->registeredPostTypes->get($id);
    }

    /**
     * Get all registered post types
     *
     * @return Collection<string, CustomPostType>
     */
    public function getPostTypes(): Collection
    {
        return $this->registeredPostTypes;
    }

    /**
     * Register a meta box
     *
     * @param string $id Unique identifier for the meta box
     * @param string $title Display title of the meta box
     * @param string|array|callable $callback Callback function to render the meta box content
     * @param string|array $postType Post type(s) this meta box applies to
     * @param int $priority Priority of the meta box (high, core, default, low)
     * @param array $callbackArgs Additional arguments passed to callback
     * @return bool
     */
    public function addMetaBox(
        string $id,
        string $title,
        string|array|callable $callback,
        string|array $postType = 'post',
        int $priority = 0,
        array $callbackArgs = []
    ): bool {
        $postTypes = is_array($postType) ? $postType : [$postType];
        foreach ($postTypes as $type) {
            $postType = $this->getPostType($type);
            if (!$postType) {
                continue; // Skip if post type not found
            }

            $postType->registerMetaBox($id, [
                'id' => $id,
                'title' => $title,
                'callback' => $callback,
                'priority' => $priority,
                'callback_args' => $callbackArgs,
            ]);
        }

        return true;
    }

    /**
     * Get meta boxes for a specific post type
     *
     * @param string $postType
     * @param string|null $context
     * @return Collection<string, array>
     */
    public function getMetaBoxes(string $postType, ?string $context = null): Collection
    {
        $postType = $this->getPostType($postType);
        if (!$postType) {
            return new Collection(); // Return empty collection if post type not found
        }

        return $postType->getMetaBox()
            ->filter(function ($metaBox) use ($context) {
                $contextMatch = $context === null || $metaBox['context'] === $context;
                return $contextMatch;
            })
            ->sortBy('priority');
    }

    /**
     * Register a taxonomy
     *
     * @param string $taxonomy Taxonomy key
     * @param string|array $postTypes Post type(s) to associate with
     * @param array $args Configuration arguments
     * @return bool
     */
    public function registerTaxonomy(string $taxonomy, string|array $postTypes, array $args = []): bool
    {
        $postTypes = is_array($postTypes) ? $postTypes : [$postTypes];

        foreach ($postTypes as $type) {
            $postType = $this->getPostType($type);
            if (!$postType) {
                continue; // Skip if post type not found
            }

            $postType->registerTaxonomy($taxonomy, $args);
        }

        return true;
    }

    /**
     * Find a menu item by its slug
     *
     * @param string $slug
     * @return array|null
     */
    public function findMenuItemBySlug(string $slug): ?array
    {
        $slug = str($slug)->trim('/')->lower()->toString();

        // Search top-level menu items
        foreach ($this->menu as $menuItem) {
            if ($menuItem['slug'] === $slug) {
                return $menuItem;
            }

            // Search in children
            foreach ($menuItem['children'] as $childItem) {
                if ($childItem['slug'] === $slug) {
                    return $childItem;
                }
            }
        }

        return null; // Not found
    }

    /**
     * Get a registered taxonomy
     *
     * @param string $taxonomy
     * @return array|null
     */
    public function getTaxonomy(string $taxonomy): ?array
    {
        $taxonomy = null;
        foreach ($this->registeredPostTypes as $postType) {
            $taxonomies = $postType->getTaxonomies();
            if ($taxonomies->has($taxonomy)) {
                $taxonomy = $taxonomies->get($taxonomy);
                break;
            }
        }

        return $taxonomy;
    }

    /**
     * Get all registered taxonomies
     *
     * @return Collection
     */
    public function getTaxonomies(): Collection
    {
        $taxonomies = new Collection();
        foreach ($this->registeredPostTypes as $postType) {
            $taxonomies = $taxonomies->merge($postType->getTaxonomies());
        }
        return $taxonomies;
    }

    /**
     * Get taxonomies for a specific post type
     *
     * @param string $postType
     * @return Collection
     */
    public function getTaxonomiesForPostType(string $postType): Collection
    {
        $postType = $this->getPostType($postType);
        if (!$postType) {
            return new Collection(); // Return empty collection if post type not found
        }

        return $postType->getTaxonomies();
    }

    /**
     * Check if a taxonomy is registered
     *
     * @param string $taxonomy
     * @return bool
     */
    public function taxonomyExists(string $taxonomy): bool
    {
        return $this->getTaxonomy($taxonomy) !== null;
    }

    /**
     * Add a menu item
     *
     * @param string $slug Menu slug
     * @param string $title Menu title
     * @param string|callable|array|null $callback Callback or URL
     * @param string|null $icon Menu icon
     * @param int $position Menu position
     * @param string|null $parent Parent menu slug for submenu
     * @return bool
     */
    public function addMenu(
        string $slug,
        string $title,
        string|callable|array|null $callback = null,
        string|null $icon = null,
        int $position = 10,
        string|null $parent = null
    ): bool {
        $slug = str($slug)->trim('/')->lower()->toString();

        $menuItem = [
            'slug' => $slug,
            'title' => $title,
            'callback' => $callback,
            'icon' => $icon ?? 'dashicons-admin-generic',
            'position' => $position,
            'children' => new Collection(),
        ];

        if ($parent) {
            // Add as submenu
            $parent = str($parent)->trim('/')->lower()->toString();
            $parentMenu = $this->menu->firstWhere('slug', $parent);
            if ($parentMenu) {
                unset($menuItem['children']); // No need for children in submenu items
                $menuItem['parent'] = $parent; // Set parent slug
                $parentMenu['children']->put($slug, $menuItem); // Add to parent's children
            }
        } else {
            // Add as top-level menu
            $this->menu->put($slug, $menuItem);
        }

        return true;
    }

    /**
     * Get all menu items
     *
     * @return Collection
     */
    public function getMenu(): Collection
    {
        return $this->menu->sortBy('position');
    }

    /**
     * Register a setting
     *
     * @param string $id Setting identifier
     * @param array $args Configuration arguments
     * @return bool
     */
    public function registerSetting(string $id, array $args = []): bool
    {
        if ($this->settings->has($id)) {
            return false;
        }

        $setting = [
            'label' => str($id)->headline()->toString(),
            'icon' => 'dashicons-admin-tools',
            'view' => null,
            'callback' => null,
            ...$args
        ];

        $this->settings->put($id, $setting);

        return true;
    }

    /**
     * Get a registered setting
     *
     * @param string $id
     * @return array|null
     */
    public function getSetting(string $id): ?array
    {
        return $this->settings->get($id);
    }

    /**
     * Get all registered settings
     *
     * @return Collection
     */
    public function getSettings(): Collection
    {
        return $this->settings;
    }

    protected function registerMenuItemsForCustomPostTypes()
    {
        Route::group(function () {
            // add menu items for registered post types
            foreach ($this->getPostTypes() as $id => $postType) {
                if ($postType->isShowUi()) {
                    // Add menu items for post type if set to show in menu
                    if ($postType->isShowInMenu()) {
                        $this->addMenu(
                            $id,
                            $postType['labels']['name'] ?? str($id)->ucfirst()->plural(),
                            null,
                            $postType['menu_icon'] ?? 'dashicons-admin-post',
                            $postType['menu_position'] ?? 10
                        );
                        $this->addMenu(
                            $id,
                            sprintf('All %s', $postType['labels']['name'] ?? str($id)->ucfirst()->plural()),
                            null,
                            'dashicons-editor-table',
                            parent: $id
                        );
                        $this->addMenu(
                            "$id/create",
                            sprintf('Create %s', $postType['labels']['singular_name'] ?? str($id)->ucfirst()->singular()),
                            null,
                            'dashicons-plus',
                            parent: $id
                        );
                    }

                    foreach ($postType->getTaxonomies() as $taxonomyId => $taxonomy) {
                        // Add menu item for taxonomy if set to show in menu
                        if ($postType->isShowInMenu()) {
                            $this->addMenu(
                                "{$id}/{$taxonomyId}",
                                $taxonomy['labels']['name'] ?? ucfirst($id),
                                null,
                                'dashicons-tag',
                                20,
                                $id
                            );
                        }

                        // Register routes for taxonomy terms
                        $slug = $taxonomy['rewrite']['slug'] ?? $taxonomyId;
                        Route::resource("{$id}/{$slug}", TaxonomyController::class);
                    }

                    // Register routes for the post type
                    Route::resource($id, PostController::class);
                }
            }
        })
            ->path(dashboard_prefix())
            ->middleware('cms.auth')
            ->name('cms');
    }

    protected function registerMenusItems(): void
    {
        $this->addMenu('/', 'Dashboard', null, 'dashicons-dashboard', 1);

        if ($this->settings->isNotEmpty()) {
            $this->addMenu('/settings', 'Settings', null, 'dashicons-admin-generic', 50);

            foreach ($this->settings as $key => $setting) {
                $this->addMenu(
                    "/settings/{$key}",
                    $setting['label'] ?? str($key)->headline()->toString(),
                    null,
                    $setting['icon'] ?? 'dashicons-admin-tools',
                    parent: '/settings'
                );
            }
        }
    }
}