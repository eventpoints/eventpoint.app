<?php

declare(strict_types=1);

namespace App\Repository\Util;

use Doctrine\ORM\QueryBuilder;

final class QueryHelper
{
    public function hasJoinDefined(QueryBuilder $qb, string $joinAlias): bool
    {
        $existingJoins = $qb->getDQLPart('join');
        return array_key_exists($joinAlias, $existingJoins);
    }
}
