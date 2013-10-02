<?php

namespace Deft\DataTables\DataSource\ORM;

use Doctrine\ORM\QueryBuilder;

class PropertyPathMappingFactory
{
    public function createPropertyPathMapping(QueryBuilder $qb, array $fieldMapping, array $existingMapping = [])
    {
        if (count($qb->getRootAliases()) > 1) throw new \UnexpectedValueException("Expected 1 root alias");
        $rootAlias = $qb->getRootAliases()[0];

        $aliasMapping = [$rootAlias => ''];
        foreach ($qb->getDQLPart('join') as $joinFrom => $joins) {
            foreach ($joins as $join) {
                $joinParts = explode(".", $join->getJoin());
                $aliasMapping[$join->getAlias()] = trim($aliasMapping[$joinParts[0]] . '.' . $joinParts[1], '.');
            }
        }

        $mapping = [];
        foreach ($fieldMapping as $key => $field) {
            if (array_key_exists($key, $existingMapping)) {
                $mapping[$key] = $existingMapping[$key];
                continue;
            }

            $fieldParts = explode('.', $field);
            if (isset($fieldParts[1])) {
                $alias = $fieldParts[0];
                $mapping[$key] = trim($aliasMapping[$fieldParts[0]] . '.' . $fieldParts[1], '.');
            } else {
                $mapping[$key] = trim($fieldParts[0]);
            }
        }

        return $mapping;
    }
}
