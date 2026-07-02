<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Generates URL slugs for properties: kebab(title)-{8 hex}.
 * The random suffix guarantees uniqueness without a counter table and keeps
 * the slug stable (see contract §10 — immutable after publish).
 */
class SlugGenerator
{
    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function generate(string $title, ?string $suffix = null): string
    {
        $base = strtolower((string) $this->slugger->slug($title));
        if ('' === $base) {
            $base = 'property';
        }

        // 8 hex chars; caller may pass a deterministic suffix (e.g. in fixtures).
        $suffix ??= substr(bin2hex(random_bytes(4)), 0, 8);

        return $base.'-'.$suffix;
    }
}
