<?php

/**
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

namespace api\controller;

use api\utility\ParserQueryFilter;
use \common\utility\Strings;

/**
 * Class that provides Basic Structure for a CRUD(LC) Controller.
 * C(reate)
 * R(ead)
 * U(pdate)
 * D(elete)
 * L(ist)
 * C(ount)
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
abstract class CrudServiceController extends EntityServiceController {
  /*
   * ---------------------------------------------------------------------------
   * Abstract Methods
   * ---------------------------------------------------------------------------
   */

  /**
   * Creates an instance of the Entity Managed by the Controller
   * 
   * @return \api\model\AbstractEntity An instance of the Entity Managed by the
   *   Controller
   */
  abstract protected function createEntity();

  /*
   * ---------------------------------------------------------------------------
   * HANDLER: BaseServiceController - do_initAction()
   * ---------------------------------------------------------------------------
   */

  /**
   * Initializes Transaction Management for Actions that Require it
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return boolean 'true' to show that the transaction management started
   * @throws \Exception On failure to initiate the transaction (for any reason)
   */
  protected function startAction($context) {

    // Merge HTTP Request Parameters with Route Parameters
    $parameters = array_merge($this->requestParameters(), $context->getParameters());
    $context->setParameters($parameters);

    // Perform Action Specific Initialization
    switch ($context->getAction()) {
      case 'Create':
      case 'Update':
      case 'Delete':
        /* See http://docs.phalconphp.com/en/latest/reference/models.html#manual-transactions
         * TODO: This Mode Applies to only a single Database Connection, if we use
         * more than one connection we have to use transaction manager.
         */
        $this->db->begin();
        break;
    }

    return true;
  }

  /*
   * ---------------------------------------------------------------------------
   * HANDLER: BaseController - do_sucess() / do_failed()
   * ---------------------------------------------------------------------------
   */

  /**
   * Handles Success when Performing an Action that Requires Transaction Management
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return boolean 'true' to show that the action passed
   * @throws \Exception On failure to commit the transaction (for any reason)
   */
  protected function successAction($context) {

    switch ($context->getAction()) {
      case 'Create':
      case 'Update':
      case 'Delete':
        // Everything Went OK : Commit Transaction
        $this->db->commit();
        break;
    }

    return true;
  }

  /**
   * Handles Failure to Perform an Action that Requires Transaction Management
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return boolean 'false' to show that the action failed
   * @throws \Exception On failure to rollback the transaction (for any reason)
   */
  protected function failedAction($context) {

    switch ($context->getAction()) {
      case 'Create':
      case 'Update':
      case 'Delete':
        // Something Went Wrong : Rollback Transaction
        $this->db->rollback();
        break;
    }

    return false;
  }

  /*
   * ---------------------------------------------------------------------------
   * CONTROLLER: Internal Action Handlers
   * ---------------------------------------------------------------------------
   */

  /**
   * Create a new Entity Based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \api\model\AbstractEntity Newly Created Entity
   * @throws \Exception On failure to create the entity (for any reason)
   */
  protected function doCreateAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Stage 1: Create the Entity
    $entity = $this->stageCreateEntity($context);

    // Stage 2: Initialize the Entities Properties
    $entity = $this->stageInitializeEntity($context, $entity);

    // Stage 3: Save the Entity
    return $this->stagePersistEntity($context, $entity);
  }

  /**
   * Retrieve the Entity that Matches the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \api\model\AbstractEntity Retrieved Entity
   * @throws \Exception On failure to retrieve the entity (for any reason)
   */
  protected function doReadAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Stage 1: Get the Entity
    return $this->stageGetEntity($context);
  }

  /**
   * Perform an Update for the Context Entity
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \api\model\AbstractEntity Modified Entity
   * @throws \Exception On failure to perform the action
   */
  protected function doUpdateAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Stage 1: Get the Entity
    $entity = $this->stageGetEntity($context);

    // Stage 2: Update the Entities Properties
    $entity = $this->stageUpdateEntity($context, $entity);

    // Stage 3: Save the Entity
    return $this->stagePersistEntity($context, $entity);
  }

  /**
   * Delete the Context Entity from the Database
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return boolean 'true' if Entity Deleted, 'false' otherwise
   * @throws \Exception On failure to perform the action
   */
  protected function doDeleteAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Stage 1: Get the Entity
    $entity = $this->stageGetEntity($context);

    // Stage 2: Delete the Entity
    $entity = $this->stageDeleteEntity($context, $entity);

    return true;
  }

  /**
   * List Entities in the Database based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return  Phalcon\Mvc\Model\Resultset\Simple Result Set containing List of Entities
   * @throws \Exception On failure to perform the action
   */
  protected function doListAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Create an Instance of the Entity for Metadata Purposes
    $entity = $this->createEntity();

    // Extract Possible Query Parameters
    $__filter = $context->getParameter('__filter');
    $__sort = $context->getParameter('__sort');
    $__limit = $context->getParameter('__limit');

    // Create the Query
    $select = $this->buildSelectQuery($entity, $__filter, $__sort, $__limit);

    // Instantiate the Query
    $query = new \Phalcon\Mvc\Model\Query($select, $this->getDI());

    // Execute the query returning a result if any
    $entities = $query->execute();

    // Return Result Set
    return $entities;
  }

  /**
   * Count Entities in the Database based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return integer Number of Entities Matching the Action Context
   * @throws \Exception On failure to perform the action
   */
  protected function doCountAction($context) {
    // Parameter Validation
    assert('isset($context) && is_object($context)');

    // Create an Instance of the Entity for Metadata Purposes
    $entity = $this->createEntity();

    // Extract Possible Query Parameters
    $__filter = $context->getParameter('__filter');

    // Create the Query
    $count = $this->buildCountQuery($entity, $__filter);

    // Instantiate the Query
    $query = new \Phalcon\Mvc\Model\Query($count, $this->getDI());

    // Execute the query returning a result if any
    $result = $query->execute()->getFirst();

    return (integer) $result['count'];
  }

  /*
   * ---------------------------------------------------------------------------
   * ACTION STAGES Functions
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieve the Entity from the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \api\model\AbstractEntity Action Context Entity
   * @throws \Exception On failure to retrieve the entity (for any reason)
   */
  protected function stageGetEntity($context) {
    // Get the Entity to Update
    $entity = $context->getParameter('entity');
    if (!isset($entity)) {
      throw new \Exception('Entity not found', 1);
    }

    return $entity;
  }

  /**
   * Create a new Entity Based on the Action Context
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @return \api\model\AbstractEntity Newly Created Entity
   * @throws \Exception On failure to create the entity (for any reason)
   */
  protected function stageCreateEntity($context) {
    // Create the Entity
    $entity = $this->createEntity();

    return $entity;
  }

  /**
   * Initializes the Entity
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @param \api\model\AbstractEntity $entity Entity to be Updated
   * @return \api\model\AbstractEntity Updated Entity
   * @throws \Exception On failure to create the entity (for any reason)
   */
  protected function stageInitializeEntity($context, $entity) {
    // Set Entity Values
    $this->setEntityValues($entity, $context->getParameters(), false);

    // If the Entity Allows it Set the Creation User and Date
    $this->setCreator($entity, $context->getParameter('cm_user'));

    return $entity;
  }

  /**
   * Update the Entity
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @param \api\model\AbstractEntity $entity Entity to be Updated
   * @return \api\model\AbstractEntity Updated Entity
   * @throws \Exception On failure to update the entity (for any reason)
   */
  protected function stageUpdateEntity($context, $entity) {
    // Set Entity Values
    $this->setEntityValues($entity, $context->getParameters(), false);

    // If the Entity Allows it Set the Modifying User and Date
    $this->setModifier($entity, $context->getParameter('cm_user'));

    return $entity;
  }

  /**
   * Deletes the Entity from the Back-end Data Store
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @param \api\model\AbstractEntity $entity Entity to be Deleted
   * @return \api\model\AbstractEntity Deleted Entity
   * @throws \Exception On failure to delete the entity (for any reason)
   */
  protected function stageDeleteEntity($context, $entity) {
    // Delete Entity
    $this->_delete($entity);

    return $entity;
  }

  /**
   * Saves Changes back to the Backend Data Store
   * 
   * @param \api\controller\ActionContext $context Context for Action
   * @param \api\model\AbstractEntity $entity Entity to be Saved
   * @return \api\model\AbstractEntity Saved Entity
   * @throws \Exception On failure to create the entity (for any reason)
   */
  protected function stagePersistEntity($context, $entity) {
    // Persist the Entity
    $this->_persist($entity);

    return $entity;
  }

  /*
   * ---------------------------------------------------------------------------
   * HELPER FUNCTIONS: Query Builder
   * ---------------------------------------------------------------------------
   */

  /**
   * If the Entity Manages it, this sets the User and Timestamp of when the
   * Entity was created
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param \User $user User to set as the Objects Modifier
   * @return \api\model\AbstractEntity Modified Entity
   */
  protected function setCreator($entity, $user) {
    // Is the User Set?
    if (isset($user)) { // YES
      // Does this Entity Object Have a Creator Property?
      if (property_exists($entity, 'creator')) { // YES
        // Set Modifier
        $entity->creator = \models\User::extractID($user);

        // Set the Modification Date and Time
        $now = new \DateTime();
        $entity->date_created = $now->format('Y-m-d H:i:s');
      }
    }

    return $entity;
  }

  /**
   * If the Entity Manages it, this sets the User and Timestamp of when the
   * Entity was modified
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param \User $user User to set as the Objects Modifier
   * @return \api\model\AbstractEntity Modified Entity
   */
  protected function setModifier($entity, $user) {
    // Is the User Set?
    if (isset($user)) { // YES
      // Does this Entity Object Have a Modifier Property?
      if (property_exists($entity, 'modifier')) { // YES
        // Set Modifier
        $entity->modifier = \models\User::extractID($user);

        // Set the Modification Date and Time
        $now = new \DateTime();
        $entity->date_modified = $now->format('Y-m-d H:i:s');
      }
    }

    return $entity;
  }

  /**
   * Build a PHQL 'SELECT *' Query based on the given conditions
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $filter OPTIONAL Filter Condition for the query
   * @param string $sort OPTIONAL Sort Definition for the query
   * @param string $limit OPTIONAL Returned number of entities limit
   * @return string PHQL Select Query
   */
  protected function buildSelectQuery($entity, $filter = null, $sort = null, $limit = null) {
    // Prepare WHERE Conditions
    $conditions = null;

    // Do we have a Filter Conditions Defined?
    if (isset($filter)) {  // YES
      // HTML Special Characters Decode the String
      $filter = html_entity_decode($filter);
      $filters = ParserQueryFilter::parse($filter);

      // Did the Filters Parser Return Anything?
      if (isset($filters)) { // YES: Convert this to PHQL Conditions
        $conditions = $this->filtersToConditions($entity, 'e', $filters);
      }
    }

    // Do we have any conditions to build the a Query Filter?
    $whereClause = null;
    if (isset($conditions)) { // YES
      $whereClause = " WHERE {$conditions}";
    }

    // Prepare ORDER BY
    $conditions = null;

    // Do we have a sort definition?
    if (isset($sort)) { // YES: Try to build 
      $conditions = $this->sortToOrderBy($entity, $sort, 'e');
    }

    if (!isset($conditions)) {
      $conditions = $this->sortFromPrimaryKey($entity, 'e');
    }

    // Build Order By Clause
    $orderByClause = ' ORDER BY ' . implode(',', $conditions);

    // Prepare LIMIT Condition
    $limitClause = null;

    // Is a Limit Set?
    if (isset($limit)) { // YES
      $limit = (integer) $limit;

      // Is the Limit Value Positive?
      if ($limit > 0) { // YES
        $limitClause = ' LIMIT ' . $limit;
      }
    }

    // Create Query
    $query = 'SELECT * FROM ' . get_class($entity) . ' AS e ';

    // Do we have a filter condition defined?
    if (isset($whereClause)) { // YES
      $query.=$whereClause;
    }

    // Do we have a sort order defined?
    if (isset($orderByClause)) { // YES
      $query.=$orderByClause;
    }

    // Do we have limit on returned entites defined?
    if (isset($limitClause)) { // YES
      $query.=$limitClause;
    }

    return $query;
  }

  /**
   * Build a PHQL 'SELECT count(*)' Query based on the given conditions
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $filters OPTIONAL Filter Condition for the query
   * @return string PHQL Select Query
   */
  protected function buildCountQuery($entity, $filter = null) {
    // Prepare WHERE Conditions
    $whereClause = null;
    $conditions = null;

    // Do we have a Filter Conditions Defined?
    if (isset($filter)) {  // YES
      // HTML Special Characters Decode the String
      $filter = html_entity_decode($filter);
      $filters = ParserQueryFilter::parse($filter);

      // Did the Filters Parser Return Anything?
      if (isset($filters)) { // YES: Convert this to PHQL Conditions
        $conditions = $this->filtersToConditions($entity, 'e', $filters);
      }
    }

    // Do we have any conditions to build the a Query Filter?
    if (isset($conditions)) { // YES
      $whereClause = " WHERE {$conditions}";
    }

    // Create Query
    $query = 'SELECT COUNT(*) AS count FROM ' . get_class($entity);
    // Do we have a filter condition defined?
    if (isset($whereClause)) { // YES
      $query.=$whereClause;
    }

    return $query;
  }

  /**
   * Convert the Filter AST to a valid PHQL Filter Condition
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $alias Entity Alias to be used in field references
   * @param array $ast Filter AST Node / Tree
   * @return string PHQL version of filter condition in AST
   * @throws \Exception on Failure to Build Filter Condition
   */
  protected function filtersToConditions($entity, $alias, $ast) {
    switch ($ast[0]) {
      case ParserQueryFilter::AST_FIELD_FILTER:
        return $this->filterFieldToCondition($entity, $alias, $ast[1][0], $ast[1][1]);
      case ParserQueryFilter::AST_AND:
        return $this->filterAndToCondition($entity, $alias, $ast[1][0], $ast[1][1]);
      case ParserQueryFilter::AST_OR:
        return $this->filterOrToCondition($entity, $alias, $ast[1][0], $ast[1][1]);
      default:
        throw new \Exception("Unexpected AST Node Type [{$ast[0]}]", 1);
    }
  }

  /**
   * Convert the Field Filter AST to a valid PHQL Filter Condition
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $alias Entity Alias to be used in field references
   * @param array $ast_field AST Node for Field ID
   * @param array $ast_filter AST Node for Filter Condition
   * @return string PHQL version of filter condition in AST
   * @throws \Exception on Failure to Build Filter Condition
   */
  protected function filterFieldToCondition($entity, $alias, $ast_field, $ast_filter) {
    // Is the AST Node of the Correct Type?
    if ($ast_field[0] !== ParserQueryFilter::AST_FIELD_ID) { // NO
      throw new \Exception("AST for Field is not of the correct type.", 1);
    }
    // Is the AST Node of the Correct Type?
    if ($ast_filter[0] !== ParserQueryFilter::AST_OPERATION) { // NO
      throw new \Exception("AST for Filter is not of the correct type.", 2);
    }
    // Is the field referening to the Correct Entity?
    if ($ast_field[1][0] !== $entity->entityName()) { // NO
      throw new \Exception("Field Reference to an invalid entity [{$entity->entityName()}].", 3);
    }
    // Is the field part of the Entity?
    $field = $ast_field[1][1];
    if (!$this->isFieldInEntity($entity, $field)) { // NO
      throw new \Exception("Field is not part of the entity [{$field}].", 4);
    }

    return "({$alias}.{$field} " . $this->operationToString($entity, $alias, $ast_filter) . ")";
  }

  /**
   * Convert the AND Filter AST to a valid PHQL Filter Condition
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $alias Entity Alias to be used in field references
   * @param array $ast_lhs Left-Hand-Side of AND Filter AST Node / Tree
   * @param array $ast_rhs Right-Hand-Side of AND Filter AST Node / Tree
   * @return string PHQL version of filter condition in AST
   * @throws \Exception on Failure to Build Filter Condition
   */
  protected function filterAndToCondition($entity, $alias, $ast_lhs, $ast_rhs) {
    $lhs = $this->filtersToConditions($entity, $alias, $ast_lhs);
    $rhs = $this->filtersToConditions($entity, $alias, $ast_rhs);
    return "({$lhs} and {$rhs})";
  }

  /**
   * Convert the OR Filter AST to a valid PHQL Filter Condition
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $alias Entity Alias to be used in field references
   * @param array $ast_lhs Left-Hand-Side of AND Filter AST Node / Tree
   * @param array $ast_rhs Right-Hand-Side of AND Filter AST Node / Tree
   * @return string PHQL version of filter condition in AST
   * @throws \Exception on Failure to Build Filter Condition
   */
  protected function filterOrToCondition($entity, $alias, $ast_lhs, $ast_rhs) {
    $lhs = $this->filtersToConditions($entity, $alias, $ast_lhs);
    $rhs = $this->filtersToConditions($entity, $alias, $ast_rhs);
    return "({$lhs} or {$rhs})";
  }

  /**
   * Convert the AST Operation Node to a valid PHQL Condition
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $alias Entity Alias to be used in field references
   * @param array $ast AST Representation of the Operation
   * @return string Partial PHQL for filter operation (missing LHS)
   * @throws \Exception on Failure to Build Filter Condition
   */
  protected function operationToString($entity, $alias, $ast) {
    // Is the AST Node of the Correct Type?
    if ($ast[0] !== ParserQueryFilter::AST_OPERATION) { // NO
      throw new \Exception("Expecting AST Operation Node, found [{$ast[0]}].", 1);
    }

    $value = $ast[1][1];
    switch ($ast[1][0]) {
      case ParserQueryFilter::TK_EQ:
        return "= {$this->valueToString($value)}";
      case ParserQueryFilter::TK_NE:
        return "<> {$this->valueToString($value)}";
      case ParserQueryFilter::TK_LT:
        return "< {$this->valueToString($value)}";
      case ParserQueryFilter::TK_GT:
        return "> {$this->valueToString($value)}";
      case ParserQueryFilter::TK_LE:
        return "<= {$this->valueToString($value)}";
      case ParserQueryFilter::TK_GE:
        return ">= {$this->valueToString($value)}";
      case ParserQueryFilter::TK_LIKE:
        return "LIKE {$this->valueToString($value)}";
      default:
        throw new \Exception('Invalid Filter Operation.', 1);
    }
  }

  /**
   * Return a String Representation of the Value to be used in a PHQL Query
   * 
   * @param mixed $value
   * @return string String Representation of Value
   * @throws \Exception If unable to convert the value to a string representation
   */
  protected function valueToString($value) {
    // Is the Value a Number
    if (is_numeric($value)) { // YES
      // Return a string representation unquoted
      return (string) $value;
    } else if (is_string($value)) { // NO: It's a String
      // Quote the Value and Return
      return "'{$value}'";
    } else if (is_object($value)) { // NO: It's an Object of Some Type
      // Convert to a String Representation and Return the Value Quoted
      return "'" . $value->toString() . "'";
    } else {
      throw new \Exception('Invalid Filter Value Type.', 1);
    }
  }

  /**
   * Build sort conditions from the sort string provided.
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $sort Sort Conditions String
   * @param string $alias Entity Alias in PHQL Query
   * @return string[] Sort Conditions or NULL if no valid conditions found
   */
  protected function sortToOrderBy($entity, $sort, $alias) {
    // Get Entity Name
    $ename = $entity->entityName();

    // Explode Sort String into Entries
    $entries = explode(';', $sort);

    // Process each Sort Entry
    $conditions = array();
    foreach ($entries as $entry) {
      $ascending = true;

      // Do we have anything to work with?
      $entry = Strings::nullOnEmpty($entry);
      if (!isset($entry)) { // NO: Skip it
        continue;
      }

      // Do we want Descending Sort?
      if ($entry[0] == '!') { // YES
        // Do we have a entity field id?
        if (strlen($entry) == 1) { // NO: Skip it
          continue;
        }

        $ascending = false;
        $entry = substr($entry, 1);
      }

      // Split $key into Entity and Field Portions
      list($type, $field) = explode('.', $entry, 2);

      // Are we dealing with a field, in the correct entity?
      if (!isset($type) || ($type !== $ename)) { // NO: Skip it
        continue;
      }

      // Does the field exist in the Entity?
      if (!$this->isFieldInEntity($entity, $field)) { // NO: Skip it
        continue;
      }

      $period = strpos($field, '.');
      if ($period !== FALSE) { // Remove Entity Name from the field name
        $field = substr($field, $period + 1);
      }

      // Create Sort Condition
      $condition = "{$alias}.{$field}";
      if (!$ascending) {
        $condition.= ' DESC';
      }

      // Add to List of Conditions
      $conditions[] = $condition;
    }

    return count($conditions) > 0 ? $conditions : null;
  }

  /**
   * Build sort conditions from the entities primary key
   * 
   * @param \api\model\AbstractEntity $entity Entity to Base Query on
   * @param string $alias Entity Alias in PHQL Query
   * @return string[] Sort Conditions 
   * @throws \Exception If unable to convert the value to a string representation
   */
  protected function sortFromPrimaryKey($entity, $alias) {
    // Get List of Primary Key Fields
    $keyFields = $this->primaryKey($entity);

    // Do we have a Primary Key?
    if (count($keyFields) == 0) { // NO: Throw Exception (All Entities are required to have a primary key)
      throw new \Exception('Entity [' . $entity->entityName() . '] has no Primary Key.', 1);
    }

    // Process each Sort Entry
    $conditions = array();
    foreach ($keyFields as $field) {
      // Add to List of Conditions
      $conditions[] = "{$alias}.{$field}";
    }

    return $conditions;
  }

}
