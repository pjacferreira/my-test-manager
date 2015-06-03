<?php

/*
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2015 Paulo Ferreira <pf at sourcenotes.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace api\model;

/**
 * Handler for Complex PHQL Queries (Create Filters and Order By Conditions)
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
trait MixinComplexQueries {

  protected static function applyFilter($filter, $phql, $alias = null) {
    assert('!isset($filter) || is_array($filter)');
    assert('isset($phql) && is_string($phql)');

    // Does the Filter Seem Valid
    if (isset($filter) && is_array($filter) && count($filter)) { // YES
      $conditions = self::complexExpression('AND', $filter, $alias);
    }

    return isset($conditions) ? $phql . ' AND (' . $conditions . ')' : $phql;
  }

  protected static function applyOrder($sort, $phql, $alias = null) {
    assert('!isset($filter) || is_array($filter)');
    assert('isset($phql) && is_string($phql)');

    // Does the Filter Seem Valid?
    $condition = '';
    if (isset($sort) && is_array($sort)) { // YES
      $comma = false;
      foreach ($sort as $field => $ascending) {
        if (isset($field) && property_exists('\models\Container', $field)) {
          // Do we have an alias?
          if (isset($alias)) { // YES: Apply it
            $field = "{$alias}.{$field}";
          }

          $condition .= $comma ? ", {$field}" : $field;
          $comma = true;
          if (!$ascending) {
            $condition.=' DESC';
          }
        }
      }
    }

    return strlen($condition) ? $phql . ' ORDER BY ' . $condition : $phql;
  }

  protected static function complexExpression($unifier, $expressions, $alias) {
    assert('isset($unifier) && is_string($unifier)');
    assert('isset($expressions) && is_array($expressions) && count($expressions)');

    $conditions = [];
    foreach ($expressions as $field => $expression) {
      $ufield = strtoupper($field);
      if (($ufield === 'AND') || ($ufield === 'OR')) {
        $conditions[] = self::complexExpression($ufield, $expression, $alias);
      } else if (property_exists('\models\Container', $field)) {
        $condition = self::simpleExpression($field, $expression, $alias);
        if (isset($condition)) {
          $conditions[] = $condition;
        }
      }
    }

    if (count($conditions)) {
      return '(' . implode(") {$unifier} (", $conditions) . ')';
    }

    return null;
  }

  protected static function simpleExpression($field, $expression, $alias) {
    assert('isset($field) && is_string($field)');
    assert('isset($expression) && is_array($expression)');

    // Do we have an alias?
    if (isset($alias)) { // YES: Apply it
      $field = "{$alias}.{$field}";
    }

    $condition = null;
    if (count($expression)) {
      $operator = array_shift($expression);
      switch ($operator) {
        case 'NOT':
          return "NOT {$field}";
        case 'IN':
          return "{$field} IN (" . implode(',', $expression[0]) . ')';
        case '=':
        case '<>':
        case '<':
        case '>':
        case 'LIKE':
          return "{$field} $operator {$expression[0]}";
      }
    }

    return $condition;
  }

}
