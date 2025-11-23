<?php

namespace Cms\Models;

use Spark\Database\Model;

/**
 * Class Option
 * 
 * This class represents the Option model for storing site-wide settings.
 * 
 * @package Cms\Models
 */
class Option extends Model
{
    public static string $table = 'options';

    protected array $guarded = [];

    /**
     * Get decoded value (handles JSON)
     *
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->attributes['option_value'] ?? null;
    }

    /**
     * Set value (encodes arrays/objects to JSON)
     *
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void
    {
        $this->attributes['option_value'] = $value;
    }

    /**
     * Get option by key (static helper)
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getOption(string $key, mixed $default = null): mixed
    {
        return get_option($key, $default);
    }

    /**
     * Set option by key (static helper)
     *
     * @param string $key
     * @param mixed $value
     * @param bool $autoload
     * @return bool
     */
    public static function setOption(string $key, mixed $value, bool $autoload = true): bool
    {
        return update_option($key, $value, $autoload);
    }

    /**
     * Delete option by key (static helper)
     *
     * @param string $key
     * @return bool
     */
    public static function deleteByKey(string $key): bool
    {
        return delete_option($key);
    }
}
