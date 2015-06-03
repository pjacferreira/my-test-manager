<?php

/* Test Center - Compliance Testing Application (Web Services)
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

namespace models;

use common\utility\Strings;

/**
 * Project Settings Entity (Encompasses Project Settings and Defaults).
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class ProjectSettings extends \api\model\AbstractEntity {

  /**
   *
   * @var integer
   */
  public $project;

  /**
   *
   * @var integer
   */
  public $run_pass;

  /**
   *
   * @var integer
   */
  public $run_incomplete;

  /**
   *
   * @var integer
   */
  public $run_fail;

  /**
   *
   * @var integer
   */
  public $step_pass;

  /**
   *
   * @var integer
   */
  public $step_fail;

  /**
   *
   * @var integer
   */
  public $modifier;

  /**
   *
   * @var string
   */
  public $date_modified;

  /**
   * Independent Column Mapping.
   */
  /*
   * ---------------------------------------------------------------------------
   * PHALCON Model Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * PHALCON per request Contructor
   */
  public function initialize() {
    // Define Relations
    // A Single Project is Linked to a Single Settings Entry
    $this->belongsTo("project", "models\Project", "id");
    // A Single User can Be the Modifier for Many Other Users
    $this->hasMany("modifier", "models\User", "id");
  }

  /**
   * Define alternate table name for Project Settings
   * 
   * @return string Project Settings Table Name
   */
  public function getSource() {
    return "t_projects_settings";
  }

  /**
   * Independent Column Mapping.
   * 
   * @return array Mapping of Table Column Name to Entity Field Name 
   */
  public function columnMap() {
    return array(
      'id_project' => 'project',
      'id_run_pass' => 'run_pass',
      'id_run_incomplete' => 'run_incomplete',
      'id_run_fail' => 'run_fail',
      'id_step_pass' => 'step_pass',
      'id_step_fail' => 'step_fail',
      'id_modifier' => 'modifier',
      'dt_modified' => 'date_modified',
    );
  }

  /**
   * Called by PHALCON after a Record is Retrieved from the Database
   */
  public function afterFetch() {
    $this->project = (integer) $this->project;
    $this->run_pass = (integer) $this->run_pass;
    $this->run_incomplete = (integer) $this->run_incomplete;
    $this->run_fail = (integer) $this->run_fail;
    $this->step_pass = (integer) $this->step_pass;
    $this->step_fail = (integer) $this->step_fail;
    $this->modifier = isset($this->modifier) ? (integer) $this->modifier : null;
  }

  /*
   * ---------------------------------------------------------------------------
   * AbstractEntity: Overrides
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieve the name used to reference the entity in Metadata
   * 
   * @return string Name
   */
  public function entityName() {
    return "ps";
  }

  /*
   * ---------------------------------------------------------------------------
   * PHP Standard Conversions
   * ---------------------------------------------------------------------------
   */

  /**
   * Retrieves a Map representation of the Entities Field Values
   * 
   * @param boolean $header (DEFAULT = true) Add Entity Header Information?
   * @return array Map of field <--> value tuplets
   */
  public function toArray($header = true) {
    $array = parent::toArray($header);

    $array = $this->addKeyProperty($array, 'project', $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'project', null, $header);
    $array = $this->addProperty($array, 'test_state_create', null, $header);
    $array = $this->addProperty($array, 'test_state_ud', null, $header);
    $array = $this->addProperty($array, 'test_state_ready', null, $header);
    $array = $this->addProperty($array, 'set_state_create', null, $header);
    $array = $this->addProperty($array, 'set_state_ud', null, $header);
    $array = $this->addProperty($array, 'set_state_ready', null, $header);
    $array = $this->addProperty($array, 'run_state_create', null, $header);
    $array = $this->addProperty($array, 'run_state_ud', null, $header);
    $array = $this->addProperty($array, 'run_state_ready', null, $header);
    $array = $this->addProperty($array, 'run_code_create', null, $header);
    $array = $this->addProperty($array, 'run_code_ip', null, $header);
    $array = $this->addProperty($array, 'run_code_complete', null, $header);
    $array = $this->addReferencePropertyIfNotNull($array, 'owner', null, $header);
    return $array;
  }

  /**
   * String Representation of Entity
   * 
   * @return string Entity Identifier String
   */
  public function __toString() {
    return (string) $this->project;
  }

  /*
   * ---------------------------------------------------------------------------
   * Public Helper Functions
   * ---------------------------------------------------------------------------
   */

  /**
   * Try to Extract a Project Settings ID from the incoming parameter
   * 
   * @param mixed $settings Project Settings Entity (object) or Project Settings ID (integer)
   * @return mixed Returns the Test ID or 'null' on failure;
   */
  public static function extractID($settings) {
    assert('isset($settings)');

    // Is the parameter an Test Object?
    if (is_object($settings) && is_a($settings, __CLASS__)) { // YES
      return $settings->project;
    } else if (is_integer($settings) && ($settings >= 0)) { // NO: It's a Positive Integer
      return $settings;
    }
    // ELSE: None of the above
    return null;
  }

  /*
   * ---------------------------------------------------------------------------
   * PHALCON Model Extensions
   * ---------------------------------------------------------------------------
   */
}
