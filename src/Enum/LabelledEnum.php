<?php

namespace App\Enum;

/**
 * Backed enums that expose a human-readable label, used to build the
 * `GET /api/enums` payload ({value, label}[]) consumed by the SPA.
 */
interface LabelledEnum
{
    public function label(): string;
}
