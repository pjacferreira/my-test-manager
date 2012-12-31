<?php

/* Test Center - Compliance Testing Application
 * Copyright (C) 2012 Paulo Ferreira <pf at sourcenotes.org>
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

namespace TestCenter\ServiceBundle\API;

use Library\ArrayUtilities;

/**
 * Description of CrudServiceController
 *
 * @author Paulo Ferreira
 */
class CrudServiceController
  extends EntityServiceController {

  /**
   * @param $entity
   */
  public function __construct($entity) {
    parent::__construct($entity);
  }

  /**
   * @param $action
   * @return bool
   */
  protected function startAction($context) {

    switch ($context->getAction()) {
      case 'Create':
      case 'Update':
      case 'Delete':
        // Set DOCTRINE to Manual Transaction Commit
        $this->getEntityManager()->getConnection()->beginTransaction();
        break;
    }

    return true;
  }

  /**
   * @param $action
   * @return bool
   */
  protected function successAction($context) {

    switch ($context->getAction()) {
      case 'Create':
      case 'Update':
      case 'Delete':
        // Everything Went OK : Commit Transaction
        $this->getEntityManager()->getConnection()->commit();
        break;
    }

    return true;
  }

  /**
   * @param $action
   * @return bool
   */
  protected function failedAction($context) {

    switch ($context->getAction()) {
      case 'Create':
      case 'Update':
      case 'Delete':
        // Something Went Wrong : Rollback Transaction
        $this->getEntityManager()->getConnection()->rollback();
        break;
    }

    return false;
  }

  /**
   * @param $parameters
   * @return object
   * @throws \Exception
   */
  protected function doCreateAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get Entity MetaData
    $meta = $this->getMetadata();

    /* TODO Re-factor this function
     * Idea: For relations, allow the use of parameters with a name in the format (relation variable.relation parameter)
     */

    // Create the Entity Object and Set it's Parameters
    $parameters = $context->getParameters();
    $entity = $this->setEntityValues($this->createEntity($parameters),
                                                         $parameters, $meta);

    // Persist the Entity
    $this->getEntityManager()->persist($entity);
    $this->getEntityManager()->flush();

    return $entity;
  }

  /**
   * @param $parameters
   * @return object
   * @throws \Exception
   */
  protected function doReadAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Identifier for the Entity
    $id = $context->getParameter('id');
    $name = $context->getParameter('name');
    assert('isset($id) || isset($name)');

    // Get Entity
    if (isset($id)) {
      $entity = $this->getRepository()->find($id);
    } else {
      $entity = $this->getRepository()->findOneByName($name);
    }

    if (!isset($entity)) {
      throw new \Exception('Entity not found', 1);
    }

    return $entity;
  }

  /**
   * @param $parameters
   * @return object
   * @throws \Exception
   */
  protected function doUpdateAction($parameters) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Entity to Update
    $entity = $context->getParameter('entity');
    assert('isset($entity)');

    // Get Entity MetaData
    $meta = $this->getMetadata();

    // Set Entity Values
    $this->setEntityValues($entity, $parameters, $meta);

    // Persist the Entity
    $this->getEntityManager()->flush();
    return $entity;
  }

  /**
   * @param $parameters
   * @return object
   * @throws \Exception
   */
  protected function doDeleteAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Get the Entity to Update
    $entity = $context->getParameter('entity');
    assert('isset($entity)');

    // Delete Entity
    $this->getEntityManager()->remove($entity);
    $this->getEntityManager()->flush();
    return true;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doListAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('e')
      ->from($this->m_oEntity->getEntity(), 'e');

    // Prepare Filter
    $__filter = $context->getParameter('__filter');
    if (isset($__filter)) {
      $filters = ParserQueryFilter::parse($__filter);

      if (count($filters)) {
        $query = $this->applyFilter($query, $filters);
      }
    }

    // Prepare Sort
    $sort = $this->extractSort($context);

    // Apply Sort
    $byID = false;
    foreach ($sort as $key => $direction) {
      if ($key == 'id') {
        $byID = true;
      }
      $query->addOrderBy("e.$key", $direction);
    }

    if (!$byID) {
      /* If not sorted by ID, always make sure that it is added, this garauntees
       * that the table lists, can also get sequential records, even if a filter
       * is applied
       */
      $query->addOrderBy('e.id');
    }

    // Apply Limit if it Exists
    $__limit = $context->getParameter('__limit');
    if (isset($__limit)) {
      $__limit = (integer) $__limit;
      if ($__limit > 0) {
        $query->setMaxResults($__limit);
      }
    }

    // Extract and Execute the Query
    $query = $query->getQuery();
    return $query->getResult();
  }

  /**
   * @param $parameters
   * @return int
   */
  protected function doCountAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Build Query
    $qb = $this->getEntityManager()->createQueryBuilder();

    // Create and Execute Query
    $query = $qb->select('count(e)')
      ->from($this->m_oEntity->getEntity(), 'e');

    // Prepare Filter
    $__filter = $context->getParameter('__filter');
    if (isset($__filter)) {
      $filters = ParserQueryFilter::parse($__filter);

      if (count($filters)) {
        $query = $this->applyFilter($query, $filters);
      }
    }

    $result = $query->getQuery()->getScalarResult();
    return (integer) $result[0][1];
  }

  /**
   * 
   * @param type $query
   * @param type $filters
   * @return type
   */
  protected function applyFilter($query, $filters) {
    $expressions = array();
    foreach ($filters as $field => $operations) {
      // TODO Verify that the Field Exists in the Entity
      // TODO How to Handle References to other Object (i.e. join by id)
      // TODO see if the entity name (that is part of the field, can be used for something, ex: relations)
      $period = strpos($field, '.');
      if ($period !== FALSE) { // Remove Entity Name from the field name
        $field = substr($field, $period + 1);
      }
      $key = "e.$field";

      array_push($expressions,
                 $this->queryExpression($query, $key, $operations[0],
                                        $operations[1]));
    }

    // Apply Filter
    if (count($expressions) > 1) { // Multiple Expressions (and them together)
      $method = new \ReflectionMethod(get_class($query), 'andWhere');
      $method->invokeArgs($query, $expressions);
    } else if (count($expressions) == 1) { // Single Expression
      $query->where($expressions[0]);
    }

    return $query;
  }

  /**
   * 
   * @param type $query
   * @param type $field
   * @param type $condition
   * @param type $value
   * @return type
   * @throws \Exception
   */
  protected function queryExpression($query, $field, $condition, $value) {
    switch ($condition) {
      case 'and':
        $expressions = array();
        foreach ($value as $entry) {
          array_push($expressions,
                     $this->queryExpression($query, $field, $entry[0], $entry[1]));
        }

        // Invoke the andx method on expr, converting the array of expressions to a list of arguments
        $expr = $query->expr();
        $method = new \ReflectionMethod(get_class($expr), 'andx');
        return $method->invokeArgs($expr, $expressions);
      case 'or':
        $expressions = array();
        foreach ($value as $entry) {
          array_push($expressions,
                     $this->queryExpression($query, $field, $entry[0], $entry[1]));
        }

        // Invoke the orx method on expr, converting the array of expressions to a list of arguments
        $expr = $query->expr();
        $method = new \ReflectionMethod(get_class($expr), 'orx');
        return $method->invokeArgs($expr, $expressions);
      case 'eq':
        return $query->expr()->eq($field, $value);
        break;
      case 'ne':
        return $query->expr()->neq($field, $value);
        break;
      case 'lt':
        return $query->expr()->lt($field, $value);
        break;
      case 'gt':
        return $query->expr()->gt($field, $value);
        break;
      case 'le':
        return $query->expr()->lte($field, $value);
        break;
      case 'ge':
        return $query->expr()->gte($field, $value);
        break;
      case 'like':
        // TODO Find a way to quote field whose value is a string
        return $query->expr()->like($field, "'$value'");
        break;
      default:
        throw new \Exception('Invalid Filter Operation.', 1);
    }
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function extractFilter($context) {
    $filters = array();
    $__filter = $context->getParameter('__filter');
    if (isset($__filter)) {
      return Parsers::queryFilter($__filter);
    }

    return $filters;
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function extractSort($context) {
    // Prepare Sort
    $sort = array();
    $__sort = $context->getParameter('__sort');
    if (isset($__sort)) {
      $__sort = explode(';', $__sort);

      foreach ($__sort as $field) {
        $ascending = true;

        $field = trim($field);
        if (strlen($field) == 0) {
          continue;
        }

        if ($field[0] == '!') {
          if (strlen($field) == 1) {
            continue;
          }

          $ascending = false;
          $field = substr($field, 1);
        }

        $period = strpos($field, '.');
        if ($period !== FALSE) { // Remove Entity Name from the field name
          $field = substr($field, $period + 1);
        }

        $sort[$field] = $ascending ? 'ASC' : 'DESC';
      }
    }

    return $sort;
  }

  /**
   * 
   * @param type $source
   * @param type $merge
   * @return type
   */
  protected function cleanURLParameters($source, $merge = null) {
    // Parameter Validation
    assert('isset($source) && is_array($source)');
    assert('!isset($merge) || is_array($merge)');

    $return = isset($merge) ? $merge : array();

    // Escape 
    foreach ($source as $key => $value) {
      $return[$key] = htmlentities($value, ENT_QUOTES, "UTF-8");
    }

    return $return;
  }

  /**
   * 
   * @param type $key
   * @return type
   */
  protected function requestParameter($key) {

    $value = null;
    if (isset($_GET[$key])) {
      $value = htmlentities($_GET[$key], ENT_QUOTES, "UTF-8");
    } else if (isset($_POST[$key])) {
      $value = htmlentities($_POST[$key], ENT_QUOTES, "UTF-8");
    }

    return $value;
  }

  /**
   * 
   * @return type
   */
  protected function requestParameters() {
    // Extract Clean (Security) URL Parameters
    $parameters = $this->cleanURLParameters($_GET);
    $parameters = $this->cleanURLParameters($_POST, $parameters);

    return $parameters;
  }

  /**
   * 
   * @param type $parameters
   * @return type
   */
  protected function serviceParameters($parameters = null) {

    // Get the Parameters
    $parameters = isset($parameters) ? $parameters : $this->requestParameters();

    // Get Entity MetaData
    $meta = $this->getMetadata();

    // Pass through only parameters that are valid for the Entity
    $array = array();
    foreach ($parameters as $key => $value) {

      // Check if the Key is Prefixed
      if (stripos($key, ':')) { // Yes
        list($type, $field) = explode(':', $key, 2);
        $key = $field;
      }

      // Skip Identifier Fields
      if ($meta->isIdentifier($key)) {
        continue;
      }

      // Allow Non-String Values to Pass-through untouched
      if (isset($value) && is_string($value)) {
        $value = StringUtilities::nullOnEmpty($value);
      }

      if (isset($value)) {
        if ($meta->hasField($key) || $meta->hasAssociation($key)) {
          $array[$key] = $value;
        }
      }
    }

    return $array;
  }

}
