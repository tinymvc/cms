<?php

namespace Cms\Services;

use Cms\Contracts\DashboardContract;
use Cms\Modules\Hooks;
use Spark\Support\Collection;

class Dashboard implements DashboardContract
{
    public Collection $menu;
    public Hooks $hooks;
    public Collection $registeredPostTypes;
    public Collection $registeredMetaBoxes;
    public Collection $registeredTaxonomies;

    public function __construct()
    {
        $this->menu = new Collection();
        $this->hooks = new Hooks();
        $this->registeredPostTypes = new Collection();
        $this->registeredMetaBoxes = new Collection();
        $this->registeredTaxonomies = new Collection();
    }

    public function init(): void
    {
        // Register default post types
        $this->registerDefaultPostTypes();

        // Fire the init action
        $this->hooks->doAction('cms_init', $this);

        // dd($this);
    }

    /**
     * Register a new post type
     *
     * @param string $postType The post type slug
     * @param array $args Configuration arguments for the post type
     * @return bool
     */
    public function registerPostType(string $postType, array $args = []): bool
    {
        if ($this->registeredPostTypes->has($postType)) {
            return false;
        }

        $defaults = [
            'label' => ucfirst($postType),
            'labels' => [
                'name' => ucfirst($postType) . 's',
                'singular_name' => ucfirst($postType),
                'add_new' => 'Add New',
                'add_new_item' => 'Add New ' . ucfirst($postType),
                'edit_item' => 'Edit ' . ucfirst($postType),
                'new_item' => 'New ' . ucfirst($postType),
                'view_item' => 'View ' . ucfirst($postType),
                'search_items' => 'Search ' . ucfirst($postType),
                'not_found' => 'No ' . strtolower($postType) . ' found',
                'not_found_in_trash' => 'No ' . strtolower($postType) . ' found in Trash',
            ],
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post',
            'capability_type' => 'post',
            'hierarchical' => false,
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'has_archive' => true,
            'rewrite' => ['slug' => $postType],
        ];

        $config = array_merge($defaults, $args);

        $this->registeredPostTypes->put($postType, $config);

        // Fire action after post type registration
        $this->hooks->doAction('cms_registered_post_type', $postType, $config);

        return true;
    }

    /**
     * Get a registered post type
     *
     * @param string $postType
     * @return array|null
     */
    public function getPostType(string $postType): ?array
    {
        return $this->registeredPostTypes->get($postType);
    }

    /**
     * Get all registered post types
     *
     * @return Collection
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
     * @param callable $callback Callback function to render the meta box content
     * @param string|array $postType Post type(s) this meta box applies to
     * @param string $context Where the meta box should display (normal, side, advanced)
     * @param string $priority Priority of the meta box (high, core, default, low)
     * @param array $callbackArgs Additional arguments passed to callback
     * @return bool
     */
    public function addMetaBox(
        string $id,
        string $title,
        callable $callback,
        string|array $postType = 'post',
        string $context = 'normal',
        string $priority = 'default',
        array $callbackArgs = []
    ): bool {
        $postTypes = is_array($postType) ? $postType : [$postType];

        foreach ($postTypes as $type) {
            $key = "{$type}_{$id}";

            if ($this->registeredMetaBoxes->has($key)) {
                continue;
            }

            $this->registeredMetaBoxes->put($key, [
                'id' => $id,
                'title' => $title,
                'callback' => $callback,
                'post_type' => $type,
                'context' => $context,
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
     * @return Collection
     */
    public function getMetaBoxes(string $postType, ?string $context = null): Collection
    {
        return $this->registeredMetaBoxes
            ->filter(function ($metaBox) use ($postType, $context) {
                $typeMatch = $metaBox['post_type'] === $postType;
                $contextMatch = $context === null || $metaBox['context'] === $context;
                return $typeMatch && $contextMatch;
            })
            ->sortBy('priority');
    }

    /**
     * Register a taxonomy
     *
     * @param string $taxonomy Taxonomy key
     * @param string|array $objectType Post type(s) to associate with
     * @param array $args Configuration arguments
     * @return bool
     */
    public function registerTaxonomy(string $taxonomy, string|array $objectType, array $args = []): bool
    {
        if ($this->registeredTaxonomies->has($taxonomy)) {
            return false;
        }

        $defaults = [
            'label' => ucfirst($taxonomy),
            'labels' => [
                'name' => ucfirst($taxonomy),
                'singular_name' => ucfirst($taxonomy),
                'search_items' => 'Search ' . ucfirst($taxonomy),
                'all_items' => 'All ' . ucfirst($taxonomy),
                'edit_item' => 'Edit ' . ucfirst($taxonomy),
                'update_item' => 'Update ' . ucfirst($taxonomy),
                'add_new_item' => 'Add New ' . ucfirst($taxonomy),
                'new_item_name' => 'New ' . ucfirst($taxonomy) . ' Name',
                'menu_name' => ucfirst($taxonomy),
            ],
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'rewrite' => ['slug' => $taxonomy],
        ];

        $config = array_merge($defaults, $args);
        $config['object_type'] = is_array($objectType) ? $objectType : [$objectType];

        $this->registeredTaxonomies->put($taxonomy, $config);

        // Fire action after taxonomy registration
        $this->hooks->doAction('cms_registered_taxonomy', $taxonomy, $objectType, $config);

        return true;
    }

    /**
     * Add a menu item
     *
     * @param string $slug Menu slug
     * @param string $title Menu title
     * @param callable|string|null $callback Callback or URL
     * @param string|null $icon Menu icon
     * @param int $position Menu position
     * @param string|null $parent Parent menu slug for submenu
     * @return bool
     */
    public function addMenu(
        string $slug,
        string $title,
        callable|string|null $callback = null,
        ?string $icon = null,
        int $position = 10,
        ?string $parent = null
    ): bool {
        $menuItem = [
            'slug' => $slug,
            'title' => $title,
            'callback' => $callback,
            'icon' => $icon ?? 'dashicons-admin-generic',
            'position' => $position,
            'parent' => $parent,
            'children' => new Collection(),
        ];

        if ($parent) {
            // Add as submenu
            $parentMenu = $this->menu->firstWhere('slug', $parent);
            if ($parentMenu) {
                $parentMenu['children']->put($slug, $menuItem);
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
     * Register default post types (post, page)
     *
     * @return void
     */
    protected function registerDefaultPostTypes(): void
    {
        // Register 'post' post type
        $this->registerPostType('post', [
            'label' => 'Post',
            'labels' => [
                'name' => 'Posts',
                'singular_name' => 'Post',
            ],
            'menu_icon' => 'dashicons-admin-post',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'comments'],
        ]);

        // Register 'page' post type
        $this->registerPostType('page', [
            'label' => 'Page',
            'labels' => [
                'name' => 'Pages',
                'singular_name' => 'Page',
            ],
            'hierarchical' => true,
            'menu_icon' => 'dashicons-admin-page',
            'supports' => ['title', 'editor', 'thumbnail'],
        ]);
    }
}