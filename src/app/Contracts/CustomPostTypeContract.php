<?php

namespace Cms\Contracts;

use Spark\Support\Collection;

interface CustomPostTypeContract
{
    public function getId(): string;

    public function getLabel(): string;

    public function getLabels(): array;

    public function isPublic(): bool;

    public function isPubliclyQueryable(): bool;

    public function isShowUi(): bool;

    public function isShowInMenu(): bool;

    public function getMenuPosition(): int;

    public function getMenuIcon(): string;

    public function getSupports(): array;

    public function hasArchive(): bool;

    public function getRewrite(): array;

    public function getTaxonomies(): Collection;

    public function getMetaBox(): Collection;

    public function registerTaxonomy(string $taxonomyId, array $config = []): void;

    public function registerMetaBox(string $metaBoxId, array $config = []): void;
}