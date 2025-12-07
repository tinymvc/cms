<?php

namespace Cms\Models;

use App\Models\User;
use Spark\Database\Model;
use Spark\Database\Relation\BelongsTo;
use Spark\Database\Relation\BelongsToMany;
use Spark\Database\Relation\HasMany;

/**
 * Class Post
 * 
 * This class represents the Post model for the CMS.
 * Handles posts, custom post types, and their relationships.
 * 
 * @package Cms\Models
 */
class Post extends Model
{
    public static string $table = 'posts';

    protected array $guarded = [];

    /**
     * Get the post meta relationship
     *
     * @return \Spark\Database\Relation\HasMany
     */
    public function meta(): HasMany
    {
        return $this->hasMany(PostMeta::class, 'post_id', 'id');
    }

    /**
     * Get the taxonomies relationship
     *
     * @return \Spark\Database\Relation\BelongsToMany
     */
    public function taxonomies(): BelongsToMany
    {
        return $this->belongsToMany(
            Taxonomy::class,
            'posts_taxonomy',
            'post_id',
            'taxonomy_id'
        );
    }

    /**
     * Get taxonomies of a specific type
     *
     * @param string $type Taxonomy type (e.g., 'category', 'tag')
     * @return array
     */
    public function getTaxonomiesByType(string $type): array
    {
        return get_post_taxonomies($this->attributes['id'], $type);
    }

    /**
     * Get a specific meta value
     *
     * @param string $key Meta key
     * @param bool $single Return single value or array
     * @return mixed
     */
    public function getMeta(string $key, bool $single = true): mixed
    {
        return get_post_meta($this->attributes['id'], $key, $single);
    }

    /**
     * Update a meta value
     *
     * @param string $key Meta key
     * @param mixed $value Meta value
     * @return bool|int
     */
    public function updateMeta(string $key, mixed $value): bool|int
    {
        return update_post_meta($this->attributes['id'], $key, $value);
    }

    /**
     * Delete a meta value
     *
     * @param string $key Meta key
     * @return bool
     */
    public function deleteMeta(string $key): bool
    {
        return delete_post_meta($this->attributes['id'], $key);
    }

    /**
     * Check if post is published
     *
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->attributes['status'] === 'published';
    }

    /**
     * Check if post is scheduled
     *
     * @return bool
     */
    public function isScheduled(): bool
    {
        return ($this->attributes['status'] === 'scheduled') && !empty($this->attributes['scheduled_at']) && (strtotime($this->attributes['scheduled_at']) > time());
    }

    /**
     * Check if post is pending
     *
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->attributes['status'] === 'pending';
    }

    /**
     * Check if post is draft
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->attributes['status'] === 'draft';
    }

    /**
     * Publish the post
     *
     * @return bool|int
     */
    public function publish(): bool|int
    {
        $this->attributes['status'] = 'published';
        $this->attributes['published_at'] = now();
        return $this->save();
    }

    /**
     * Move post to trash
     *
     * @return bool|int
     */
    public function trash(): bool|int
    {
        $this->attributes['status'] = 'trash';
        return $this->save();
    }

    /**
     * Restore post from trash
     *
     * @return bool|int
     */
    public function restore(): bool|int
    {
        $this->attributes['status'] = 'draft';
        return $this->save();
    }

    /**
     * Get the author relationship
     *
     * @return \Spark\Database\Relation\BelongsTo|null
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
}
