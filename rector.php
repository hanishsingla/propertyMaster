<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Doctrine\Set\DoctrineSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/config',
        __DIR__.'/public',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    // uncomment to reach your current PHP version
    // ->withPhpSets()
    // ->withTypeCoverageLevel(1)
    // here we can define, what prepared sets of rules will be applied
    ->withSets([
        SetList::PHP_82,
        DoctrineSetList::DOCTRINE_CODE_QUALITY,
        SymfonySetList::SYMFONY_71,
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true
    );
