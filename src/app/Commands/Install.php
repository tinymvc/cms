<?php

namespace Cms\Commands;

use Spark\Console\Prompt;
use Spark\Database\Migration;

class Install
{
    public function __invoke(Prompt $prompt)
    {
        $rootDir = dirname(__DIR__, 2);

        $migration = new Migration(
            migrationsFolder: "$rootDir/database/migrations",
            migrationFile: "$rootDir/database/migrations.json"
        );

        $migration->refresh(['all' => true]);

        $assetsDir = "$rootDir/resources/assets/cms";
        $publicDir = root_dir('public/assets/cms');

        fm()->link($assetsDir, $publicDir);

        $prompt->message('Assets published to public directory.');
    }
}