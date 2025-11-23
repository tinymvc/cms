<?php

namespace Cms\Models;

use Spark\Database\Model;

/**
 * Class Meta
 * 
 * This class represents the generic Meta model for storing metadata
 * for various object types (users, terms, etc.).
 * 
 * @package Cms\Models
 */
class Taxonomy extends Model
{
    public static string $table = 'taxonomy';

    protected array $guarded = [];
}
