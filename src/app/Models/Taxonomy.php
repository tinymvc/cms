<?php

namespace Cms\Models;

use Spark\Database\Model;
use Spark\Database\Relation\BelongsTo;
use Spark\Database\Relation\BelongsToMany;
use Spark\Database\Relation\HasMany;

/**
 * Class Taxonomy
 * 
 * This class represents the Taxonomy model for storing terms
 * (categories, tags, custom taxonomies).
 * 
 * @package Cms\Models
 */
class Taxonomy extends Model
{
    public static string $table = 'taxonomy';

    protected array $guarded = [];

    /**
     * Get the posts relationship
     *
     * @return \Spark\Database\Relation\BelongsToMany
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            'posts_taxonomy',
            'taxonomy_id',
            'post_id'
        );
    }

    /**
     * Get the parent taxonomy relationship
     *
     * @return \Spark\Database\Relation\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'parent_id', 'id');
    }

    /**
     * Get the children taxonomies relationship
     *
     * @return \Spark\Database\Relation\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Taxonomy::class, 'parent_id', 'id');
    }

    /**
     * Check if this taxonomy has a parent
     *
     * @return bool
     */
    public function hasParent(): bool
    {
        return !empty($this->attributes['parent_id']);
    }

    /**
     * Check if this taxonomy has children
     *
     * @return bool
     */
    public function hasChildren(): bool
    {
        return Taxonomy::where('parent_id', $this->attributes['id'])->exists();
    }
}
