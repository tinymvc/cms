<?php

namespace Cms\Commands;

use Spark\Database\Migration;

class Install
{
    public function __invoke()
    {
        $rootDir = dirname(__DIR__, 2);

        $migration = new Migration(
            migrationsFolder: "$rootDir/database/migrations",
            migrationFile: "$rootDir/database/migrations.json"
        );

        $migration->refresh(['all' => true]);
    }
}