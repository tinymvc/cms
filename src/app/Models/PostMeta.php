<?php

namespace Cms\Models;

use Spark\Database\Model;
use Spark\Database\Relation\BelongsTo;

/**
 * Class PostMeta
 * 
 * This class represents the PostMeta model for storing post metadata.
 * 
 * @package Cms\Models
 */
class PostMeta extends Model
{
    public static string $table = 'posts_meta';

    protected array $guarded = [];

    /**
     * Get the post relationship
     *
     * @return \Spark\Database\Relation\BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    /**
     * Get decoded value (handles JSON)
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->attributes['meta_value'] ?? null;
    }

    /**
     * Set value (encodes arrays/objects to JSON)
     *
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->attributes['meta_value'] = $value;
    }
}
