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

use Library\StringUtilities;

/**
 * Description of ParserQueryFilter
 *
 * @author Paulo Ferreira
 */
class ParserQueryFilter {

  const TK_EOS = 0;

  // Basic Tokens
  const TK_COLON = 1;
  const TK_SEMI = 2;
  const TK_VALUE = 3;
  const TK_AND = 4;
  const TK_OR = 5;

  // Operation Tokens
  const TK_EQ = 10;
  const TK_NE = 11;
  const TK_LT = 12;
  const TK_LE = 13;
  const TK_GT = 14;
  const TK_GE = 15;
  const TK_LIKE = 16;

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  public static function parse($string) {

    $query_filter = array();

    $remainder = trim($string);

    // QUERY_FILTER = FIELD_FILTER ( ';' QUERY_FILTER )*
    while (isset($remainder) && strlen($remainder)) {

      // QUERY_FILTER = FIELD_FILTER 
      list($field, $filter, $remainder) = self::fieldFilter($remainder);
      $query_filter[$field] = $filter;

      // Get the Next Token
      list($token, $token_value, $remainder) = self::nextToken($remainder);
      if ($token == self::TK_EOS) { // DONE
        break;
      } else if ($token != self::TK_SEMI) {
        throw new \Exception("Expecting a ';'. Found [$token:$token_value].", 1);
      }
    }

    return count($query_filter) ? $query_filter : null;
  }

  /**
   * 
   * @param type $string
   * @return type
   * @throws Exception
   * @throws \Exception
   */
  protected static function fieldFilter($string) {

    // FIELD_FILTER = FIELD_NAME ':' FILTER
    list($token, $field_name, $remainder) = self::nextToken($string);
    if ($token != self::TK_VALUE) {
      throw new Exception("Expecting Field Name. Found [$token:$field_name].", 1);
    }

    list($token, $token_value, $remainder) = self::nextToken($remainder);
    if ($token != self::TK_COLON) {
      throw new \Exception("Expecting ':'. Found [$token:$token_value].", 1);
    }

    list($filter, $remainder) = self::filter($remainder);
    return array($field_name, $filter, $remainder);
  }

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  protected static function filter($string) {
    // FILTER = FILTER_CONDITION ( ('and'| 'or') FILTER )*
    // Try to Parse 1st Condition
    list($condition, $remainder) = self::filterCondition($string);

    // LOOK AHEAD
    list($token, $token_value, $remainder) = self::nextToken($remainder);
    switch ($token) {
      case self::TK_EOS: // Finished
        break;
      case self::TK_SEMI: // END OF FIELD FILTER
        $remainder = self::pushBack(';', $remainder);
        break;
      // NEXT TOKEN is an OPERATION
      case self::TK_EQ:
      case self::TK_NE:
      case self::TK_LT:
      case self::TK_LE:
      case self::TK_GT:
      case self::TK_GE:
        $remainder = self::pushBack($token_value, $remainder);

        // INSERT an IMPLICIT AND
        $token = self::TK_AND;
        $token_value = 'and';
      case self::TK_AND:
      case self::TK_OR:
        // Continue Field Parser
        list($condition2, $remainder) = self::filter($remainder);

        if ($condition2[0] == $token_value) { // Collapse same nested operations
          $condition = array($condition);
          foreach ($condition2[1] as $entry) {
            array_push($condition, $entry);
          }

          $condition = array(self::mapOperation($token_value), $condition);
        } else {
          $condition = array(self::mapOperation($token_value), array($condition, $condition2));
        }
        break;
      default:
        throw new \Exception("Unexpected Token Found [$token:$token_value].", 1);
    }

    return array($condition, $remainder);
  }

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  protected static function filterCondition($string) {

    // FILTER_CONDITION = OPERATOR value
    list($token_op, $operation, $remainder) = self::nextToken($string);
    if (($token_op < self::TK_EQ) && ($token_op >= self::TK_LIKE)) {
      throw new \Exception("Expecting an Operator. Found [$token_op:$operation].", 1);
    }

    list($token, $value, $remainder) = self::nextToken($remainder);
    if (($token != self::TK_VALUE) && ($token != self::TK_AND) && ($token != self::TK_OR)) {
      throw new \Exception("Expecting a Field Value. Found [$token:$value].", 1);
    }

    // A = combined with '*' becomes a like %
    if ((stripos($value, '*') !== FALSE) && ($token_op == self::TK_EQ)) {
      $token_op = self::TK_LIKE;
      $operation = 'like';
      $value = str_replace('*', '%', $value);
    }

    return array(array(self::mapOperation($operation), $value), $remainder);
  }

  /**
   * 
   * @param type $string
   * @param type $into
   * @return type
   */
  protected static function pushBack($string, $into) {
    return isset($into) ? $string . $into : $string;
  }

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  protected static function nextToken($string) {
    assert('!isset($string) || is_string($string)');

    // Initialize Defaults;
    $token = self::TK_EOS;
    $token_value = null;
    $remainder = null;

    $string = StringUtilities::nullOnEmpty($string);

    if (isset($string) && strlen($string)) {
      $consume = 1;

      $token_value = $string[0];
      switch ($token_value) {
        case ':':
          $token = self::TK_COLON;
          break;
        case ';':
          $token = self::TK_SEMI;
          break;
        case '=': {
            switch ($string[1]) {
              case '<':
                $token = self::TK_LE;
                $token_value.= '<';
                $consume = 2;
                break;
              case '>':
                $token = self::TK_GE;
                $token_value.= '>';
                $consume = 2;
                break;
              default:
                $token = self::TK_EQ;
            }
          }
          break;
        case '<': {
            switch ($string[1]) {
              case '>':
                $token = self::TK_NE;
                $token_value.= '>';
                $consume = 2;
                break;
              case '=':
                $token = self::TK_LE;
                $token_value.= '=';
                $consume = 2;
                break;
              default:
                $token = self::TK_LT;
            }
            break;
          }
        case '>': {
            switch ($string[1]) {
              case '=':
                $token = self::TK_GE;
                $token_value.= '=';
                $consume = 2;
                break;
              default:
                $token = self::TK_GT;
                $consume = 1;
            }
            break;
          }
        case '!':
          if ($string[1] == '=') {
            $token = self::TK_NE;
            $token_value.= '=';
            $consume = 2;
          } else {
            throw new \Exception("Expecting [=] in the Filter Condition.", 1);
          }
          break;
        case '"':
        case "'":
          $pos = stripos($string, $string[0], 1);
          if ($pos === FALSE) {
            throw new \Exception("Missing closing quote [{$string[0]}].", 1);
          }
          $token = self::TK_VALUE;
          $token_value = substr($string, 1, $pos - 1);
          $consume = strlen($token_value) + 2;
          break;
        default:
          if (($token_value == 'A') || ($token_value == 'a')) {
            if (strtolower(substr($string, 0, 3)) == 'and') {
              if ((strlen($string) > 4) && (stripos(' :;=<>!', $string[3]) >= 0)) { // FOUND AND
                $token = self::TK_AND;
                $token_value = 'and';
                $consume = 3;
                break;
              }
            }
          } else if (($token_value == 'O') || ($token_value == 'o')) {
            if ($string[1] == 'r') {
              if ((strlen($string) > 3) && (stripos(' :;=<>!', $string[2]) >= 0)) { // FOUND OR
                $token = self::TK_OR;
                $token_value = 'or';
                $consume = 2;
                break;
              }
            }
          }
          /* CASES
           * 1. TERMINATING SPACE
           * 2. TERMINATING OPERATOR
           * 3. TERMINATING SEMI COLON
           * 4. END OF STRING
           */
          $r_length = strlen($string);
          for ($i = 0; $i < $r_length; $i++) {
            if (stripos(' :;=<>!', $string[$i]) !== FALSE) {
              break;
            }
          }

          $token = self::TK_VALUE;
          if ($i == ($r_length - 1)) {
            $token_value = $string;
          } else {
            $token_value = substr($string, 0, $i);
          }
          $consume = $i;
      }

      $remainder = trim(substr($string, $consume));
    }

    return array($token, $token_value, $remainder);
  }

  /**
   * 
   * @param type $operation
   * @return string
   */
  protected static function mapOperation($operation) {
    switch ($operation) {
      case '=':
        return 'eq';
      case '!=':
      case '<>':
        return 'ne';
      case '<':
        return 'lt';
      case '>':
        return 'gt';
      case '<=':
      case '=<':
        return 'le';
      case '>=':
      case '=>':
        return 'ge';
    }
    
    // Default Return the Operation as is
    return $operation;
  }

}

?>
