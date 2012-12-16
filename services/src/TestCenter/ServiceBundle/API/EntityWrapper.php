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
 * Description of EntityWrapper
 *
 * @author Paulo Ferreira
 */
class EntityWrapper {
  // Entity Managed
  protected $m_sEntity;

  /*
   * Cache Variables
   */
  protected $m_oController;
  protected $m_oDoctrine;
  protected $m_oEntityManager;
  protected $m_oMetadata;
  protected $m_oRepository;

  /**
   * @param $entity
   */
  public function __construct($controller, $entity) {
    // Save Controller (Required to Retrieve Doctrine)
    assert('isset($controller) && is_object($controller)');
    $this->m_oController = $controller;

    // Save Entity
    $this->m_sEntity = StringUtilities::nullOnEmpty($entity);
    assert('isset($entity) && is_string($entity)');
  }

  /**
   * @return null|string
   */
  public function getEntity() {
    return $this->m_sEntity;
  }

  /**
   * @return mixed
   */
  public function getEntityManager() {
    if (!isset($this->m_oDoctrine)) {
      $this->m_oDoctrine = $this->m_oController->getDoctrine();
    }
    assert('isset($this->m_oDoctrine)');

    if (!isset($this->m_oEntityManager)) {
      $this->m_oEntityManager = $this->m_oDoctrine->getEntityManager();
    }

    assert('isset($this->m_oEntityManager)');
    return $this->m_oEntityManager;
  }

  /**
   * @return mixed
   */
  public function getRepository($entity = null) {
    if (!isset($this->m_oDoctrine)) {
      $this->m_oDoctrine = $this->m_oController->getDoctrine();
    }
    assert('isset($this->m_oDoctrine)');

    $entity = StringUtilities::nullOnEmpty($entity);
    if (isset($entity)) {
      return $this->m_oDoctrine->getRepository($entity);
    } else {
      if (!isset($this->m_oRepository)) {
        $this->m_oRepository = $this->m_oDoctrine->getRepository($this->m_sEntity);
      }

      assert('isset($this->m_oRepository)');
      return $this->m_oRepository;
    }
  }


  /**
   * @return mixed
   */
  public function getMetadata() {
    if (isset($this->m_oEntityManager)) {
      $this->m_oMetadata = $this->m_oEntityManager->getClassMetadata($this->m_sEntity);
    } else {
      $this->m_oMetadata = $this->getEntityManager()->getClassMetadata($this->m_sEntity);
    }

    assert('isset($this->m_oMetadata)');
    return $this->m_oMetadata;
  }
}
