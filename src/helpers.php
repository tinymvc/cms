<?php

use Cms\Models\Option;
use Cms\Models\Post;
use Cms\Models\PostMeta;
use Cms\Services\Dashboard;
use Spark\Support\Collection;

if (!function_exists('cms_dashboard')) {
    /**
     * Get the CMS Dashboard instance
     *
     * @return Dashboard
     */
    function cms_dashboard(): Dashboard
    {
        return app(Dashboard::class);
    }
}

if (!function_exists('register_post_type')) {
    /**
     * Register a custom post type (WordPress-like API)
     *
     * @param string $postType Post type key
     * @param array $args Array of arguments for registering a post type
     * @return bool True on success, false on failure
     */
    function register_post_type(string $postType, array $args = []): bool
    {
        return cms_dashboard()->registerPostType($postType, $args);
    }
}

if (!function_exists('get_post_type')) {
    /**
     * Get a registered post type
     *
     * @param string $postType Post type key
     * @return array|null Post type configuration or null if not found
     */
    function get_post_type(string $postType): ?array
    {
        return cms_dashboard()->getPostType($postType);
    }
}

if (!function_exists('get_post_types')) {
    /**
     * Get all registered post types
     *
     * @return Collection
     */
    function get_post_types(): Collection
    {
        return cms_dashboard()->getPostTypes();
    }
}

if (!function_exists('add_meta_box')) {
    /**
     * Add a meta box to one or more post types (WordPress-like API)
     *
     * @param string $id Unique identifier for the meta box
     * @param string $title Display title of the meta box
     * @param callable $callback Function that renders the meta box content
     * @param string|array $postType The post type(s) on which to show the meta box
     * @param string $context The context ('normal', 'side', 'advanced')
     * @param string $priority The priority ('high', 'core', 'default', 'low')
     * @param array $callbackArgs Additional arguments to pass to callback
     * @return bool
     */
    function add_meta_box(
        string $id,
        string $title,
        callable $callback,
        string|array $postType = 'post',
        string $context = 'normal',
        string $priority = 'default',
        array $callbackArgs = []
    ): bool {
        return cms_dashboard()->addMetaBox($id, $title, $callback, $postType, $context, $priority, $callbackArgs);
    }
}

if (!function_exists('get_meta_boxes')) {
    /**
     * Get meta boxes for a specific post type
     *
     * @param string $postType Post type key
     * @param string|null $context Optional context filter
     * @return Collection
     */
    function get_meta_boxes(string $postType, ?string $context = null): Collection
    {
        return cms_dashboard()->getMetaBoxes($postType, $context);
    }
}

if (!function_exists('register_taxonomy')) {
    /**
     * Register a custom taxonomy (WordPress-like API)
     *
     * @param string $taxonomy Taxonomy key
     * @param string|array $objectType Post type(s) to associate with
     * @param array $args Array of arguments for registering a taxonomy
     * @return bool
     */
    function register_taxonomy(string $taxonomy, string|array $objectType, array $args = []): bool
    {
        return cms_dashboard()->registerTaxonomy($taxonomy, $objectType, $args);
    }
}

if (!function_exists('add_action')) {
    /**
     * Add an action hook (WordPress-like API)
     *
     * @param string $tag The name of the action
     * @param callable $callback The callback function to run
     * @param int $priority Priority of execution (lower = earlier)
     * @param int $acceptedArgs Number of arguments the callback accepts
     * @return bool
     */
    function add_action(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool
    {
        return cms_dashboard()->hooks->addAction($tag, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('do_action')) {
    /**
     * Execute an action hook (WordPress-like API)
     *
     * @param string $tag The name of the action
     * @param mixed ...$args Arguments to pass to callbacks
     * @return void
     */
    function do_action(string $tag, ...$args): void
    {
        cms_dashboard()->hooks->doAction($tag, ...$args);
    }
}

if (!function_exists('remove_action')) {
    /**
     * Remove an action hook (WordPress-like API)
     *
     * @param string $tag The name of the action
     * @param callable|null $callback The callback to remove
     * @param int|null $priority The priority of the callback
     * @return bool
     */
    function remove_action(string $tag, ?callable $callback = null, ?int $priority = null): bool
    {
        return cms_dashboard()->hooks->removeAction($tag, $callback, $priority);
    }
}

if (!function_exists('has_action')) {
    /**
     * Check if an action has been registered (WordPress-like API)
     *
     * @param string $tag The name of the action
     * @param callable|null $callback Optional specific callback to check
     * @return bool
     */
    function has_action(string $tag, ?callable $callback = null): bool
    {
        return cms_dashboard()->hooks->hasAction($tag, $callback);
    }
}

if (!function_exists('add_filter')) {
    /**
     * Add a filter hook (WordPress-like API)
     *
     * @param string $tag The name of the filter
     * @param callable $callback The callback function to run
     * @param int $priority Priority of execution (lower = earlier)
     * @param int $acceptedArgs Number of arguments the callback accepts
     * @return bool
     */
    function add_filter(string $tag, callable $callback, int $priority = 10, int $acceptedArgs = 1): bool
    {
        return cms_dashboard()->hooks->addFilter($tag, $callback, $priority, $acceptedArgs);
    }
}

if (!function_exists('apply_filters')) {
    /**
     * Apply a filter hook (WordPress-like API)
     *
     * @param string $tag The name of the filter
     * @param mixed $value The value to filter
     * @param mixed ...$args Additional arguments to pass to callbacks
     * @return mixed The filtered value
     */
    function apply_filters(string $tag, $value, ...$args): mixed
    {
        return cms_dashboard()->hooks->applyFilters($tag, $value, ...$args);
    }
}

if (!function_exists('remove_filter')) {
    /**
     * Remove a filter hook (WordPress-like API)
     *
     * @param string $tag The name of the filter
     * @param callable|null $callback The callback to remove
     * @param int|null $priority The priority of the callback
     * @return bool
     */
    function remove_filter(string $tag, ?callable $callback = null, ?int $priority = null): bool
    {
        return cms_dashboard()->hooks->removeFilter($tag, $callback, $priority);
    }
}

if (!function_exists('has_filter')) {
    /**
     * Check if a filter has been registered (WordPress-like API)
     *
     * @param string $tag The name of the filter
     * @param callable|null $callback Optional specific callback to check
     * @return bool
     */
    function has_filter(string $tag, ?callable $callback = null): bool
    {
        return cms_dashboard()->hooks->hasFilter($tag, $callback);
    }
}

if (!function_exists('add_menu')) {
    /**
     * Add a menu item to the CMS dashboard
     *
     * @param string $slug Menu slug
     * @param string $title Menu title
     * @param string|null $capability Required capability
     * @param callable|string|null $callback Callback or URL
     * @param string|null $icon Menu icon
     * @param int $position Menu position
     * @param string|null $parent Parent menu slug for submenu
     * @return bool
     */
    function add_menu(
        string $slug,
        string $title,
        ?string $capability = null,
        callable|string|null $callback = null,
        ?string $icon = null,
        int $position = 10,
        ?string $parent = null
    ): bool {
        return cms_dashboard()->addMenu($slug, $title, $capability, $callback, $icon, $position, $parent);
    }
}

if (!function_exists('reset_options')) {
    /**
     * Reset the global options cache
     *
     * @param bool $hard Whether to force reload from database
     * @return void
     */
    function reset_options(bool $hard = false)
    {
        global $options;

        if (!isset($options) || $hard) {
            /** @var \Spark\Support\Collection $options */
            $options = Option::where('autoload', 1)->get();
        }
    }
}

if (!function_exists('get_option')) {
    /**
     * Get an option value (WordPress-like API)
     *
     * @param string $key Option name
     * @param mixed $default Default value if option doesn't exist
     * @return mixed Option value or default
     */
    function get_option(string $key, mixed $default = null): mixed
    {
        global $options;

        reset_options();

        $option = $options->firstWhere('option_key', $key);

        if ($option) {
            return $option->getValue();
        } elseif ($option === false) {
            return $default;
        }

        $option = Option::where('option_key', $key)->first();

        $options->put($key, $option);

        if (!$option) {
            return $default;
        }

        return $option->getValue();
    }
}

if (!function_exists('update_option')) {
    /**
     * Update an option value (WordPress-like API)
     *
     * @param string $key Option name
     * @param mixed $value Option value
     * @param bool $autoload Whether to autoload this option
     * @return bool
     */
    function update_option(string $key, mixed $value, bool $autoload = true): bool
    {
        $option = Option::createOrUpdate([
            'option_key' => $key,
            'option_value' => $value,
            'autoload' => $autoload,
        ], uniqueBy: ['option_key']);

        $updated = $option->isDirty() || isset($option['id']);

        if ($updated) {
            global $options;
            reset_options();

            $options = $options->map(function ($o) use ($option) {
                if ($o['option_key'] === $option['option_key']) {
                    return $option;
                }
                return $o;
            });
        }

        return $updated;
    }
}

if (!function_exists('delete_option')) {
    /**
     * Delete an option (WordPress-like API)
     *
     * @param string $key Option name
     * @return bool
     */
    function delete_option(string $key): bool
    {
        $deleted = (bool) Option::where('option_key', $key)->delete();

        // Reset options cache if an option was deleted
        if ($deleted) {
            global $options;
            reset_options();

            $options = $options->filter(
                fn($option) => $option['option_key'] !== $key
            );
        }

        return $deleted;
    }
}

if (!function_exists('get_post_meta')) {
    /**
     * Get post meta value (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param string $key Meta key
     * @param bool $single Whether to return a single value
     * @return mixed Meta value(s)
     */
    function get_post_meta(int $postId, string $key = '', bool $single = false): mixed
    {
        $query = PostMeta::query()->where('post_id', $postId);

        if ($key !== '') {
            $query->where('meta_key', $key);
        }

        if ($single && $key !== '') {
            $meta = $query->first();
            if (!$meta) {
                return null;
            }

            $value = $meta->meta_value;

            // Try to decode JSON
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }

            return $value;
        }

        $metas = $query->all();
        $result = [];

        foreach ($metas as $meta) {
            $value = $meta['meta_value'];

            // Try to decode JSON
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
            }

            if ($key === '') {
                $result[$meta['meta_key']][] = $value;
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }
}

if (!function_exists('update_post_meta')) {
    /**
     * Update post meta value (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param string $key Meta key
     * @param mixed $value Meta value
     * @param mixed $prevValue Previous value to match (optional)
     * @return bool|int
     */
    function update_post_meta(int $postId, string $key, mixed $value, mixed $prevValue = null): bool|int
    {
        // Encode arrays and objects to JSON
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $query = PostMeta::query()
            ->where('post_id', $postId)
            ->where('meta_key', $key);

        if ($prevValue !== null) {
            $query->where('meta_value', $prevValue);
        }

        $meta = $query->first();

        if ($meta) {
            $meta->meta_value = $value;
            return $meta->save();
        }

        return add_post_meta($postId, $key, $value);
    }
}

if (!function_exists('add_post_meta')) {
    /**
     * Add post meta value (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param string $key Meta key
     * @param mixed $value Meta value
     * @param bool $unique Whether the key should be unique
     * @return bool|int
     */
    function add_post_meta(int $postId, string $key, mixed $value, bool $unique = false): bool|int
    {
        if ($unique) {
            $exists = PostMeta::query()
                ->where('post_id', $postId)
                ->where('meta_key', $key)
                ->first();

            if ($exists) {
                return false;
            }
        }

        // Encode arrays and objects to JSON
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        $meta = PostMeta::create([
            'post_id' => $postId,
            'meta_key' => $key,
            'meta_value' => $value,
        ]);

        return $meta->id ?? false;
    }
}

if (!function_exists('delete_post_meta')) {
    /**
     * Delete post meta value (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param string $key Meta key
     * @param mixed $value Meta value to delete (optional)
     * @return bool
     */
    function delete_post_meta(int $postId, string $key, mixed $value = null): bool
    {
        $query = PostMeta::query()
            ->where('post_id', $postId)
            ->where('meta_key', $key);

        if ($value !== null) {
            // Encode arrays and objects to JSON
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }
            $query->where('meta_value', $value);
        }

        return (bool) $query->delete();
    }
}

if (!function_exists('get_post')) {
    /**
     * Get a post by ID (WordPress-like API)
     *
     * @param int $postId Post ID
     * @return Post|null
     */
    function get_post(int $postId): ?Post
    {
        return Post::find($postId);
    }
}

if (!function_exists('get_posts')) {
    /**
     * Get posts with optional filters (WordPress-like API)
     *
     * @param array $args Query arguments
     * @return array
     */
    function get_posts(array $args = []): array
    {
        $query = Post::query();

        // Post type filter
        if (isset($args['post_type'])) {
            $query->where('post_type', $args['post_type']);
        }

        // Status filter
        if (isset($args['post_status'])) {
            $query->where('status', $args['post_status']);
        }

        // Author filter
        if (isset($args['author'])) {
            $query->where('author_id', $args['author']);
        }

        // Limit
        if (isset($args['posts_per_page'])) {
            $query->limit($args['posts_per_page']);
        }

        // Order
        if (isset($args['orderby'])) {
            $order = $args['order'] ?? 'DESC';
            $query->orderBy($args['orderby'], $order);
        } else {
            $query->orderDesc('created_at');
        }

        return $query->all();
    }
}

if (!function_exists('insert_post')) {
    /**
     * Insert or update a post (WordPress-like API)
     *
     * @param array $data Post data
     * @return int|bool Post ID on success, false on failure
     */
    function insert_post(array $data): int|bool
    {
        try {
            // Update existing post
            if (isset($data['id']) && $data['id']) {
                $post = Post::find($data['id']);
                if ($post) {
                    $post->fill($data);
                    $post->save();
                    return $post['id'] ?? false;
                }
            }

            // Create new post
            $post = Post::create($data);
            return $post['id'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('delete_post')) {
    /**
     * Delete a post (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param bool $forceDelete Whether to bypass trash and force deletion
     * @return bool
     */
    function delete_post(int $postId, bool $forceDelete = false): bool
    {
        $post = Post::find($postId);

        if (!$post) {
            return false;
        }

        if ($forceDelete) {
            return $post->remove();
        }

        // Move to trash
        $post->set('status', 'trash');
        return (bool) $post->save();
    }
}

