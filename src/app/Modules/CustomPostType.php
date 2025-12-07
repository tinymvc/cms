<?php

namespace Cms\Modules;

use ArrayAccess;
use Cms\Contracts\CustomPostTypeContract;
use Spark\Contracts\Support\Arrayable;
use Spark\Support\Collection;

class CustomPostType implements CustomPostTypeContract, Arrayable, ArrayAccess
{
    protected string $id;
    protected string $label;
    protected array $labels;
    protected bool $public;
    protected bool $publicly_queryable;
    protected bool $show_ui;
    protected bool $show_in_menu;
    protected int $menu_position;
    protected string $menu_icon;
    protected array $supports;
    protected bool $has_archive;
    protected array $rewrite;
    protected Collection $taxonomies;
    protected Collection $metaBoxes;

    public function __construct(string $id, array $config = [])
    {
        $name = str($id)->title()->plural()->toString();
        $name_singular = str($id)->title()->singular()->toString();

        $config = [
            'label' => ucfirst($id),
            'labels' => [
                'name' => $name,
                'singular_name' => $name_singular,
                'add_new' => 'Add New',
                'add_new_item' => "Add New $name_singular",
                'edit_item' => "Edit $name_singular",
                'new_item' => "New $name_singular",
                'view_item' => "View $name_singular",
                'search_items' => "Search $name_singular",
                'not_found' => "No $name_singular found",
                'not_found_in_trash' => "No $name_singular found in Trash",
            ],
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-admin-post',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
            'has_archive' => true,
            'rewrite' => ['slug' => $id],
            ...$config
        ];

        $this->id = $id;
        $this->label = $config['label'];
        $this->labels = $config['labels'];
        $this->public = $config['public'];
        $this->publicly_queryable = $config['publicly_queryable'];
        $this->show_ui = $config['show_ui'];
        $this->show_in_menu = $config['show_in_menu'];
        $this->menu_position = $config['menu_position'];
        $this->menu_icon = $config['menu_icon'];
        $this->supports = $config['supports'];
        $this->has_archive = $config['has_archive'];
        $this->rewrite = $config['rewrite'];

        $this->taxonomies = new Collection();
        $this->metaBoxes = new Collection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getLabels(): array
    {
        return $this->labels;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function isPubliclyQueryable(): bool
    {
        return $this->publicly_queryable;
    }

    public function isShowUi(): bool
    {
        return $this->show_ui;
    }

    public function isShowInMenu(): bool
    {
        return $this->show_in_menu;
    }

    public function getMenuPosition(): int
    {
        return $this->menu_position;
    }

    public function getMenuIcon(): string
    {
        return $this->menu_icon;
    }

    public function getSupports(): array
    {
        return $this->supports;
    }

    public function hasArchive(): bool
    {
        return $this->has_archive;
    }

    public function getRewrite(): array
    {
        return $this->rewrite;
    }

    public function getTaxonomies(): Collection
    {
        return $this->taxonomies;
    }

    public function getMetaBox(): Collection
    {
        return $this->metaBoxes;
    }

    public function registerTaxonomy(string $taxonomyId, array $config = []): void
    {
        $config = [
            'label' => ucfirst($taxonomyId),
            'labels' => [
                'name' => ucfirst($taxonomyId),
                'singular_name' => ucfirst($taxonomyId),
                'search_items' => 'Search ' . ucfirst($taxonomyId),
                'all_items' => 'All ' . ucfirst($taxonomyId),
                'parent_item' => 'Parent ' . ucfirst($taxonomyId),
                'parent_item_colon' => 'Parent ' . ucfirst($taxonomyId) . ':',
                'edit_item' => 'Edit ' . ucfirst($taxonomyId),
                'update_item' => 'Update ' . ucfirst($taxonomyId),
                'add_new_item' => 'Add New ' . ucfirst($taxonomyId),
                'new_item_name' => 'New ' . ucfirst($taxonomyId) . ' Name',
                'menu_name' => ucfirst($taxonomyId),
            ],
            'public' => true,
            'publicly_queryable' => true,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => $taxonomyId],
            ...$config
        ];

        $this->taxonomies->put($taxonomyId, $config);
    }

    public function registerMetaBox(string $metaBoxId, array $config = []): void
    {
        $config = [
            'id' => $metaBoxId,
            'title' => "$metaBoxId Meta Box",
            'callback' => null,
            'context' => 'normal',
            'priority' => 'default',
            'callback_args' => [],
            ...$config
        ];

        $this->metaBoxes->put($metaBoxId, $config);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'labels' => $this->labels,
            'public' => $this->public,
            'publicly_queryable' => $this->publicly_queryable,
            'show_ui' => $this->show_ui,
            'show_in_menu' => $this->show_in_menu,
            'menu_position' => $this->menu_position,
            'menu_icon' => $this->menu_icon,
            'supports' => $this->supports,
            'has_archive' => $this->has_archive,
            'rewrite' => $this->rewrite,
        ];
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->{$offset});
    }

    public function offsetGet(mixed $offset): mixed
    {
        return isset($this->{$offset}) ? $this->{$offset} : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->{$offset} = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->{$offset});
    }
}