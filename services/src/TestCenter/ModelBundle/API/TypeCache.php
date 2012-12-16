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

namespace TestCenter\ModelBundle\API;

/**
 * Description of TypeCache
 *
 * @author Paulo Ferreira
 */
class TypeCache {

  protected static $m_oInstance;
  protected $m_mapClass;
  protected $m_mapIds;
  protected $m_mapName;

  protected function __construct() {
    $this->m_mapTypes = array();
    $this->m_mapIds = array();
    $this->m_mapName = array();
  }

  public static function getInstance() {
    if (!isset(static::$m_oInstance)) {
      static::$m_oInstance = new TypeCache();
      static::$m_oInstance->loadCache();
    }

    return static::$m_oInstance;
  }

  private function cacheEntry($id, $namespace, $class) {
    return array('id' => $id, 'namespace' => $namespace, 'class' => $class);
  }

  protected function loadCache() {
    $types = array(
      $this->cacheEntry(1, 'TestCenter\ModelBundle\Entity', 'User'),
      $this->cacheEntry(2, 'TestCenter\ModelBundle\Entity', 'Organization'),
      $this->cacheEntry(3, 'TestCenter\ModelBundle\Entity', 'Project'),
      $this->cacheEntry(4, 'TestCenter\ModelBundle\Entity', 'Container'),
      $this->cacheEntry(5, 'TestCenter\ModelBundle\Entity', 'Test'),
      $this->cacheEntry(6, 'TestCenter\ModelBundle\Entity', 'TestSet'),
      $this->cacheEntry(7, 'TestCenter\ModelBundle\Entity', 'Run')
    );

    foreach ($types as $type) {
      $class = $type['namespace'] . '\\' . $type['class'];
      $this->m_mapIds[$type['id']] = $class;
      $this->m_mapClass[$class] = $type['id'];
      $this->m_mapName[strtolower($type['class'])] = $class;
    }
  }

  public function typeID($type) {
    assert('isset($type)');

    if (is_string($type)) {
      if (isset($this->m_mapName[$type])) { // 1st Search by Name
        return $this->m_mapClass[$this->m_mapName[$type]];
      } else if (isset($this->m_mapClass[$type])) { // 2nd Search by Class
        return $this->m_mapClass[$type];
      }
    } else if (is_object($type)) {
      $class = get_class($type);
      if (isset($this->m_mapClass[$class])) {
        return $this->m_mapClass[$class];
      }
    }

    return null;
  }

  public function typeClass($type) {
    if (is_integer($type)) {
      if (isset($this->m_mapIds[$type])) {
        return $this->m_mapIds[$type];
      }
    } else if (is_string($type)) {
      if (isset($this->m_mapName[$type])) {
        return $this->m_mapClass[$this->m_mapName[$type]];
      }
    }

    return null;
  }

}

?>
