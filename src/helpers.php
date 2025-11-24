<?php

use Cms\Models\Option;
use Cms\Models\Post;
use Cms\Models\PostMeta;
use Cms\Models\Taxonomy;
use Cms\Services\Dashboard;
use Spark\Support\Collection;
use Spark\Database\DB;
use Spark\Support\Str;
use Spark\Utils\Paginator;

if (!function_exists('dashboard')) {
    /**
     * Get the CMS Dashboard instance
     *
     * @return Dashboard
     */
    function dashboard(): Dashboard
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
        return dashboard()->registerPostType($postType, $args);
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
        return dashboard()->getPostType($postType);
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
        return dashboard()->getPostTypes();
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
        return dashboard()->addMetaBox($id, $title, $callback, $postType, $context, $priority, $callbackArgs);
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
        return dashboard()->getMetaBoxes($postType, $context);
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
        return dashboard()->registerTaxonomy($taxonomy, $objectType, $args);
    }
}

if (!function_exists('get_taxonomy')) {
    /**
     * Get a registered taxonomy
     *
     * @param string $taxonomy Taxonomy key
     * @return array|null Taxonomy configuration or null if not found
     */
    function get_taxonomy(string $taxonomy): ?array
    {
        return dashboard()->getTaxonomy($taxonomy);
    }
}

if (!function_exists('get_taxonomies')) {
    /**
     * Get all registered taxonomies
     *
     * @param array $args Optional filters (output, object_type)
     * @return Collection
     */
    function get_taxonomies(array $args = []): Collection
    {
        $taxonomies = dashboard()->getTaxonomies();

        // Filter by object type (post type)
        if (isset($args['object_type'])) {
            $objectType = $args['object_type'];
            $taxonomies = $taxonomies->filter(function ($taxonomy) use ($objectType) {
                return in_array($objectType, $taxonomy['object_type'] ?? []);
            });
        }

        return $taxonomies;
    }
}

if (!function_exists('taxonomy_exists')) {
    /**
     * Check if a taxonomy is registered
     *
     * @param string $taxonomy Taxonomy key
     * @return bool
     */
    function taxonomy_exists(string $taxonomy): bool
    {
        return dashboard()->taxonomyExists($taxonomy);
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
        return dashboard()->hooks->addAction($tag, $callback, $priority, $acceptedArgs);
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
        dashboard()->hooks->doAction($tag, ...$args);
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
        return dashboard()->hooks->removeAction($tag, $callback, $priority);
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
        return dashboard()->hooks->hasAction($tag, $callback);
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
        return dashboard()->hooks->addFilter($tag, $callback, $priority, $acceptedArgs);
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
        return dashboard()->hooks->applyFilters($tag, $value, ...$args);
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
        return dashboard()->hooks->removeFilter($tag, $callback, $priority);
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
        return dashboard()->hooks->hasFilter($tag, $callback);
    }
}

if (!function_exists('add_menu')) {
    /**
     * Add a menu item to the CMS dashboard
     *
     * @param string $slug Menu slug
     * @param string $title Menu title
     * @param callable|string|null $callback Callback or URL
     * @param string|null $icon Menu icon
     * @param int $position Menu position
     * @param string|null $parent Parent menu slug for submenu
     * @return bool
     */
    function add_menu(
        string $slug,
        string $title,
        callable|string|null $callback = null,
        ?string $icon = null,
        int $position = 10,
        ?string $parent = null
    ): bool {
        return dashboard()->addMenu($slug, $title, $callback, $icon, $position, $parent);
    }
}

if (!function_exists('clear_option_cache')) {
    /**
     * Clear specific option from the global options cache
     *
     * @param string|null $key Specific option key to clear, or null to clear all
     * @return void
     */
    function clear_option_cache(?string $key = null): void
    {
        global $options;

        if ($key === null) {
            $options = null;
        }

        $options = $options->filter(fn($opt) => $opt['option_key'] !== $key);
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

        if (!isset($options)) {
            /** @var \Spark\Support\Collection $options */
            $options = Option::where('autoload', 1)->get();
        }

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
            'autoload' => intval($autoload),
        ], uniqueBy: ['option_key']);

        $updated = $option->isDirty() || !$option->hasOriginal() && $option->isset('id');

        $updated && clear_option_cache($key);

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
        $deleted && clear_option_cache($key);

        return $deleted;
    }
}

if (!function_exists('clear_post_meta_cache')) {
    /**
     * Clear the post meta cache
     *
     * @param int|null $postId Specific post ID to clear, or null to clear all
     * @return void
     */
    function clear_post_meta_cache(?int $postId = null): void
    {
        global $post_meta_cache;

        if (!isset($post_meta_cache)) {
            $post_meta_cache = [];
        }

        if ($postId !== null) {
            unset($post_meta_cache[$postId]);
        } else {
            $post_meta_cache = [];
        }
    }
}

if (!function_exists('get_post_meta')) {
    /**
     * Get post meta value with caching (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param string $key Meta key
     * @param bool $single Whether to return a single value
     * @return mixed Meta value(s)
     */
    function get_post_meta(int $postId, string $key = '', bool $single = false): mixed
    {
        global $post_meta_cache;

        if (!isset($post_meta_cache)) {
            $post_meta_cache = [];
        }

        // Load all meta for this post if not cached
        if (!isset($post_meta_cache[$postId])) {
            $metas = PostMeta::where('post_id', $postId)->all();
            $post_meta_cache[$postId] = [];

            foreach ($metas as $meta) {
                $value = $meta['meta_value'];
                if (!isset($post_meta_cache[$postId][$meta['meta_key']])) {
                    $post_meta_cache[$postId][$meta['meta_key']] = [];
                }
                $post_meta_cache[$postId][$meta['meta_key']][] = $value;
            }
        }

        // Return all meta
        if ($key === '') {
            return $post_meta_cache[$postId];
        }

        // Return specific key
        if (!isset($post_meta_cache[$postId][$key])) {
            return $single ? null : [];
        }

        $values = $post_meta_cache[$postId][$key];
        return $single ? ($values[0] ?? null) : $values;
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
            if (is_array($prevValue)) {
                $prevValue = json_encode($prevValue);
            }
            $query->where('meta_value', $prevValue);
        }

        $meta = $query->first();

        if ($meta) {
            $meta->meta_value = $value;
            $result = $meta->save();

            // Clear cache for this post
            $result && clear_post_meta_cache($postId);

            return $result;
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
                ->exists();

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

        // Clear cache for this post
        $meta->id && clear_post_meta_cache($postId);

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

        $result = (bool) $query->delete();

        // Clear cache for this post
        $result && clear_post_meta_cache($postId);

        return $result;
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
     * @return \Spark\Utils\Paginator|array<Post>
     */
    function get_posts(array $args = []): array|Paginator
    {
        $query = Post::query();

        // Post type filter
        if (isset($args['post_type'])) {
            if (is_array($args['post_type'])) {
                $query->whereIn('type', $args['post_type']);
            } else {
                $query->where('type', $args['post_type']);
            }
        }

        // Status filter
        if (isset($args['post_status'])) {
            if (is_array($args['post_status'])) {
                $query->whereIn('status', $args['post_status']);
            } else {
                $query->where('status', $args['post_status']);
            }
        }

        // Author filter
        if (isset($args['author'])) {
            $query->where('author_id', $args['author']);
        }

        // Taxonomy filter
        if (isset($args['taxonomy']) && isset($args['term'])) {
            $query->whereHas('taxonomies', function ($q) use ($args) {
                $q->where('type', $args['taxonomy']);
                if (is_array($args['term'])) {
                    $q->whereIn('slug', $args['term']);
                } else {
                    $q->where('slug', $args['term']);
                }
            });
        }

        // Search filter
        if (isset($args['s'])) {
            $query->grouped(function ($q) use ($args) {
                $q->where('title', 'LIKE', '%' . $args['s'] . '%')
                    ->orWhere('excerpt', 'LIKE', '%' . $args['s'] . '%');
            });
        }

        // Meta query filter
        if (isset($args['meta_key'])) {
            $query->whereHas('meta', function ($q) use ($args) {
                $q->where('meta_key', $args['meta_key']);
                if (isset($args['meta_value'])) {
                    $q->where('meta_value', $args['meta_value']);
                }
            });
        }

        // Add offset and limit
        if (isset($args['offset'], $args['posts_per_page'])) {
            $query->limit($args['offset'], $args['posts_per_page'] ?? null);
        }

        // Order
        if (isset($args['orderby'])) {
            $order = $args['order'] ?? 'DESC';
            $query->orderBy($args['orderby'], $order);
        } else {
            $query->orderDesc('created_at');
        }

        // Return all results if offset is set
        if (isset($args['offset'], $args['posts_per_page'])) {
            return $query->all();
        }

        return $query->paginate($args['posts_per_page'] ?? 15);
    }
}

if (!function_exists('insert_post')) {
    /**
     * Insert or update a post with meta support (WordPress-like API)
     *
     * @param array $data Post data (supports 'meta' key for post meta)
     * @return int|bool Post ID on success, false on failure
     */
    function insert_post(array $data): int|bool
    {
        try {
            // Extract meta data if provided
            $meta = $data['meta'] ?? [];
            unset($data['meta']);

            // Extract taxonomy data if provided
            $taxonomies = $data['taxonomies'] ?? [];
            unset($data['taxonomies']);

            // Update existing post
            if (isset($data['id']) && $data['id']) {
                $post = Post::find($data['id']);
                if ($post) {
                    $post->fill($data);
                    $post->save();
                    $postId = $post['id'];
                } else {
                    return false;
                }
            } else {
                // Create new post
                $post = Post::create($data);
                $postId = $post['id'] ?? false;

                if (!$postId) {
                    return false;
                }
            }

            // Save meta data
            if (!empty($meta)) {
                foreach ($meta as $key => $value) {
                    update_post_meta($postId, $key, $value);
                }
            }

            // Save taxonomy relationships
            if (!empty($taxonomies)) {
                foreach ($taxonomies as $taxonomy => $terms) {
                    set_post_terms($postId, $terms, $taxonomy);
                }
            }

            return $postId;
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

if (!function_exists('insert_term')) {
    /**
     * Insert a new term (WordPress-like API)
     *
     * @param string $name Term name
     * @param string $taxonomy Taxonomy type (e.g., 'category', 'tag')
     * @param array $args Optional arguments (slug, description, parent_id, image)
     * @return int|bool Term ID on success, false on failure
     */
    function insert_term(string $name, string $taxonomy, array $args = []): int|bool
    {
        try {
            // Generate slug if not provided
            $slug = $args['slug'] ?? Str::slug($name);

            // Check if term already exists
            $existing = Taxonomy::where('slug', $slug)
                ->where('type', $taxonomy)
                ->exists();

            if ($existing) {
                return false;
            }

            $data = [
                'name' => $name,
                'slug' => $slug,
                'type' => $taxonomy,
                'description' => $args['description'] ?? null,
                'parent_id' => $args['parent_id'] ?? null,
                'image' => $args['image'] ?? null,
            ];

            $term = Taxonomy::create($data);
            return $term['id'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('update_term')) {
    /**
     * Update a term (WordPress-like API)
     *
     * @param int $termId Term ID
     * @param array $data Term data to update
     * @return bool
     */
    function update_term(int $termId, array $data): bool
    {
        try {
            $term = Taxonomy::find($termId);

            if (!$term) {
                return false;
            }

            $term->fill($data);
            return (bool) $term->save();
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('delete_term')) {
    /**
     * Delete a term (WordPress-like API)
     *
     * @param int $termId Term ID
     * @return bool
     */
    function delete_term(int $termId): bool
    {
        try {
            $term = Taxonomy::find($termId);

            if (!$term) {
                return false;
            }

            return $term->remove();
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('get_term')) {
    /**
     * Get a term by ID (WordPress-like API)
     *
     * @param int $termId Term ID
     * @return Taxonomy|null
     */
    function get_term(int $termId): ?Taxonomy
    {
        return Taxonomy::find($termId) ?: null;
    }
}

if (!function_exists('get_term_by')) {
    /**
     * Get a term by field (WordPress-like API)
     *
     * @param string $field Field name (slug, name, id)
     * @param string|int $value Field value
     * @param string $taxonomy Taxonomy type
     * @return Taxonomy|null
     */
    function get_term_by(string $field, string|int $value, string $taxonomy): ?Taxonomy
    {
        $query = Taxonomy::where('type', $taxonomy);

        if ($field === 'id') {
            $query->where('id', $value);
        } elseif ($field === 'slug') {
            $query->where('slug', $value);
        } elseif ($field === 'name') {
            $query->where('name', $value);
        }

        return $query->first() ?: null;
    }
}

if (!function_exists('get_terms')) {
    /**
     * Get terms with optional filters (WordPress-like API)
     *
     * @param array $args Query arguments
     * @return array
     */
    function get_terms(array $args = []): array
    {
        $query = Taxonomy::query();

        // Taxonomy type filter
        if (isset($args['taxonomy'])) {
            if (is_array($args['taxonomy'])) {
                $query->whereIn('type', $args['taxonomy']);
            } else {
                $query->where('type', $args['taxonomy']);
            }
        }

        // Parent filter
        if (isset($args['parent'])) {
            $query->where('parent_id', $args['parent']);
        }

        // Hide empty terms
        if (isset($args['hide_empty']) && $args['hide_empty']) {
            $query->whereHas('posts');
        }

        // Search filter
        if (isset($args['search'])) {
            $query->grouped(function ($q) use ($args) {
                $q->where('name', 'LIKE', '%' . $args['search'] . '%')
                    ->orWhere('description', 'LIKE', '%' . $args['search'] . '%', 'OR');
            });
        }

        // Limit
        if (isset($args['number'])) {
            $query->limit($args['number']);
        }

        // Order
        if (isset($args['orderby'])) {
            $order = $args['order'] ?? 'ASC';
            $query->orderBy($args['orderby'], $order);
        } else {
            $query->orderBy('name', 'ASC');
        }

        return $query->all();
    }
}

if (!function_exists('set_post_terms')) {
    /**
     * Set post terms (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param array|int|string $terms Term IDs, slugs, or names
     * @param string $taxonomy Taxonomy type
     * @param bool $append Whether to append or replace existing terms
     * @return bool
     */
    function set_post_terms(int $postId, array|int|string $terms, string $taxonomy, bool $append = false): bool
    {
        try {
            $post = Post::find($postId);

            if (!$post) {
                return false;
            }

            // Normalize terms to array
            if (!is_array($terms)) {
                $terms = [$terms];
            }

            // Convert term slugs/names to IDs
            $termIds = [];
            foreach ($terms as $term) {
                if (is_numeric($term)) {
                    $termIds[] = $term;
                } else {
                    // Try to find by slug first, then by name
                    $termObj = get_term_by('slug', $term, $taxonomy);
                    if (!$termObj) {
                        $termObj = get_term_by('name', $term, $taxonomy);
                    }

                    // Create term if it doesn't exist
                    if (!$termObj) {
                        $termId = insert_term($term, $taxonomy);
                        if ($termId) {
                            $termIds[] = $termId;
                        }
                    } else {
                        $termIds[] = $termObj['id'];
                    }
                }
            }

            if (!$append) {
                // Remove all existing terms of this taxonomy
                $existingTermIds = Taxonomy::where('type', $taxonomy)
                    ->select('id')
                    ->all();

                $existingTermIdValues = array_column($existingTermIds, 'id');

                if (!empty($existingTermIdValues)) {
                    DB::table('posts_taxonomy')
                        ->where('post_id', $postId)
                        ->whereIn('taxonomy_id', $existingTermIdValues)
                        ->delete();
                }
            }

            // Insert new term relationships
            foreach ($termIds as $termId) {
                // Check if relationship already exists
                $exists = DB::table('posts_taxonomy')
                    ->where('post_id', $postId)
                    ->where('taxonomy_id', $termId)
                    ->exists();

                if (!$exists) {
                    DB::table('posts_taxonomy')->insert([
                        'post_id' => $postId,
                        'taxonomy_id' => $termId,
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('get_post_taxonomies')) {
    /**
     * Get taxonomies for a post (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param string|null $taxonomy Optional taxonomy type filter
     * @return array
     */
    function get_post_taxonomies(int $postId, ?string $taxonomy = null): array
    {
        $query = Taxonomy::query()
            ->join('posts_taxonomy', 'taxonomy.id', '=', 'posts_taxonomy.taxonomy_id')
            ->where('posts_taxonomy.post_id', $postId);

        if ($taxonomy) {
            $query->where('taxonomy.type', $taxonomy);
        }

        $query->select('taxonomy.*');

        return $query->all();
    }
}

if (!function_exists('remove_post_terms')) {
    /**
     * Remove terms from a post (WordPress-like API)
     *
     * @param int $postId Post ID
     * @param array|int|string $terms Term IDs to remove
     * @param string $taxonomy Taxonomy type
     * @return bool
     */
    function remove_post_terms(int $postId, array|int|string $terms, string $taxonomy): bool
    {
        try {
            // Normalize terms to array
            if (!is_array($terms)) {
                $terms = [$terms];
            }

            // Convert term slugs/names to IDs
            $termIds = [];
            foreach ($terms as $term) {
                if (is_numeric($term)) {
                    $termIds[] = $term;
                } else {
                    $termObj = get_term_by('slug', $term, $taxonomy);
                    if (!$termObj) {
                        $termObj = get_term_by('name', $term, $taxonomy);
                    }
                    if ($termObj) {
                        $termIds[] = $termObj['id'];
                    }
                }
            }

            if (empty($termIds)) {
                return false;
            }

            DB::table('posts_taxonomy')
                ->where('post_id', $postId)
                ->whereIn('taxonomy_id', $termIds)
                ->delete();

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('get_posts_by_taxonomy')) {
    /**
     * Get posts by taxonomy term (WordPress-like API)
     *
     * @param string $taxonomy Taxonomy type
     * @param string|int $term Term slug, name, or ID
     * @param array $args Additional query arguments
     * @return \Spark\Utils\Paginator|array<Post>
     */
    function get_posts_by_taxonomy(string $taxonomy, string|int $term, array $args = []): Paginator|array
    {
        // Find the term
        if (is_numeric($term)) {
            $termObj = get_term($term);
        } else {
            $termObj = get_term_by('slug', $term, $taxonomy);
            if (!$termObj) {
                $termObj = get_term_by('name', $term, $taxonomy);
            }
        }

        if (!$termObj) {
            return [];
        }

        // Merge with existing args
        $args['taxonomy'] = $taxonomy;
        $args['term'] = $termObj['slug'];

        return get_posts($args);
    }
}

if (!function_exists('admin_url')) {
    /**
     * Get the admin URL for the CMS dashboard
     * 
     * @param string $path Optional path to append
     * @param array $params Optional query parameters
     * @return string The absolute url
     */
    function admin_url(string $path = '', array $params = []): string
    {
        $url = route_url('cms.dashboard');

        if ($path) {
            $url .= '/' . trim($path, '/');
        }

        if (!empty($params)) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($params);
        }

        return rtrim($url, '/');
    }
}