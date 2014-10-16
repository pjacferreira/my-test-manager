<?php

/*
 * Test Center - Compliance Testing Application (Web Services)
 * Copyright (C) 2012-2014 Paulo Ferreira <pf at sourcenotes.org>
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

namespace api\utility;

use \shared\utility\StringUtilities;

/**
 * Parser for Request Filter Strings
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2012-2014 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class ParserQueryFilter {

  const TK_EOS = 0;
  // LITERALS
  const TK_COLON = 1; // ':'
  const TK_SEMI = 2; // ';'
  const TK_PIPE = 3; // '|'
  const TK_LSBRACKET = 4; // '['
  const TK_RSBRACKET = 5; // ']'
  // COMPLEX LITERALS
  const TK_DOTDOT = 10; // '..'
  const TK_VALUE = 11;
  // Operation Tokens
  const TK_EQ = 20; // '='
  const TK_NE = 21; // '<>'
  const TK_LT = 22; // '<'
  const TK_LE = 23; // '<='
  const TK_GT = 24; // '>'
  const TK_GE = 25; // '>='
  const TK_LIKE = 26;
  // AST_NODE_TYPES
  const AST_FIELD_FILTER = 1;
  const AST_AND = 2;
  const AST_OR = 3;
  const AST_FIELD_ID = 10;
  const AST_OPERATION = 11;

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  public static function parse($string) {
    // Clean up Filter String
    $string = StringUtilities::nullOnEmpty($string);

    // Parse the String
    list($ast_queryfilter, $remainder) = self::parseQueryFilter($string);
    return $ast_queryfilter;
  }

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  protected static function parseQueryFilter($string) {
    // Do we have anything to parse?
    if (!isset($string) || (strlen($string) == 0)) { // NO
      return null;
    }

    // EXTRACT: Field Filter
    list($ast_fieldFilter, $remainder) = self::parseFieldFilter($string);

    // EXTRACT: ';', '|' or END-OF-STRING
    list($token, $token_value, $remainder) = self::nextToken($remainder);

    // Did we find the End of the String?
    switch ($token) {
      case self::TK_EOS: // END-OF-STRING : We are done
        return array($ast_fieldFilter, null);
      case self::TK_SEMI: // AND Filters
        list($next_query_filter, $remainder) = self::parseQueryFilter($remainder);

        // Do we have another filter?
        if (isset($next_query_filter)) {
          return array(
              // AST NODE (type, values)
              array(self::AST_AND, array($ast_fieldFilter, $next_query_filter)),
              null
          );
        }
        // ELSE: No Dangling SEMICOLON
        return array($ast_fieldFilter, null);
      case self::TK_PIPE: // OR Filters
        list($next_query_filter, $remainder) = self::parseQueryFilter($remainder);

        // Do we have another filter?
        if (isset($next_query_filter)) { //YES
          return array(
              // AST NODE (type, values)
              array(self::AST_OR, array($ast_fieldFilter, $next_query_filter)),
              null
          );
        }
        // ELSE: No Dangling PIPE
        return array($ast_fieldFilter, null);
      default:
        throw new \Exception("Expecting (';','|',EOS). Found [$token:$token_value].", 1);
    }
  }

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  protected static function parseFieldFilter($string) {
    /* BNF: field_filter = field_id filter */

    // EXTRACT: field_id
    list($ast_field, $remainder) = self::parseFieldID($string);

    // EXTRACT: filter
    list($ast_filter, $remainder) = self::parseFilter($remainder);

    return array(
        // AST NODE (type, values)
        array(self::AST_FIELD_FILTER, array($ast_field, $ast_filter)),
        $remainder
    );
  }

  /**
   * 
   * @param type $string
   * @return type
   * @throws \Exception
   */
  protected static function parseFieldID($string) {
    /* BNF: field_id = entity_name ':' field_name */

    // EXTRACT: Entity Name
    list($token, $entity_name, $remainder) = self::nextToken($string);
    if ($token != self::TK_VALUE) {
      throw new Exception("Expecting Entity Name. Found [$token:$entity_name].", 1);
    }

    // EXTRACT: ':'
    list($token, $token_value, $remainder) = self::nextToken($remainder);
    if ($token != self::TK_COLON) {
      throw new \Exception("Expecting ':'. Found [$token:$token_value].", 1);
    }

    // EXTRACT: Field Name
    list($token, $field_name, $remainder) = self::nextToken($remainder);
    if ($token != self::TK_VALUE) {
      throw new Exception("Expecting Field Name. Found [$token:$field_name].", 1);
    }

    return array(
        // AST NODE (type, values)
        array(self::AST_FIELD_ID, array($entity_name, $field_name)),
        $remainder
    );
  }

  /* BNF:
   * filter = operation value
   * operation = '=' | '<=' | '<' | '>' | '>='
   * 
   * if value contains '*' '=' becomes 'like'
   * 
   * TODO: Implement Ranges
   * BNF: field_id '=[' value .. value ']'
   */

  /**
   * 
   * @param type $string
   * @return array
   * @throws \Exception
   */
  protected static function parseFilter($string) {
    // FILTER = FILTER_CONDITION ( ('and'| 'or') FILTER )*
    // EXTRACT: operation
    list($token_op, $operation, $remainder) = self::nextToken($string);
    switch ($token_op) {
      case self::TK_EQ:
      case self::TK_NE:
      case self::TK_LT:
      case self::TK_LE:
      case self::TK_GT:
      case self::TK_GE:
        break;
      default:
        throw new \Exception("Unexpected Token Found [{$token}:{$operation}].", 1);
    }

    // EXTRACT: Entity Name
    list($token, $value, $remainder) = self::nextToken($remainder);
    if ($token != self::TK_VALUE) {
      throw new \Exception("Expecting Entity Name. Found [{$token}:{$value}].", 1);
    }

    // Does the value have any asterisks?
    if ((stripos($value, '*') !== FALSE) && ($token_op == self::TK_EQ)) { // YES
      $token_op = self::TK_LIKE;
      $operation = 'like';
      $value = str_replace('*', '%', $value);
    }

    return array(
        // AST NODE (type, values)
        array(self::AST_OPERATION, array($token_op, $value)),
        $remainder
    );
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
        case '|':
          $token = self::TK_PIPE;
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
          /* CASES
           * 1. TERMINATING SPACE
           * 2. TERMINATING OPERATOR
           * 3. TERMINATING SEMI COLON / PIPE
           * 4. END OF STRING
           */
          $r_length = strlen($string);
          for ($i = 0; $i < $r_length; $i++) {
            if (stripos(' :;|=<>!', $string[$i]) !== FALSE) {
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

}

?>
