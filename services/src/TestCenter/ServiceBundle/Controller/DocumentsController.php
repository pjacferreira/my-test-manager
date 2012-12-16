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
namespace TestCenter\ServiceBundle\Controller;

use TestCenter\ServiceBundle\API\BaseServiceController;

/**
 * Description of DocumentsController
 *
 * @author Paulo Ferreira
 */
class DocumentsController
  extends BaseServiceController {

  /**
   * @param $id
   * @return null
   */
  public function orgDocAddAction($id) {
    return $this->doAction('org_document_add', array('id' => (integer)$id));
  }

  /**
   * @param $id
   * @return null
   */
  public function orgDocRemoveAction($id, $doc_id) {
    return $this->doAction('org_document_remove', array('id' => (integer)$id, 'doc_id' => (integer)$doc_id));
  }

  /**
   * @param $id
   * @param $user_id
   * @return null
   */
  public function orgDocListAction($id) {
    return $this->doAction('org_documents_list', array('id' => (integer)$id));
  }

  /**
   * @param $id
   * @param $user_id
   * @return null
   */
  public function orgDocCountAction($id) {
    return $this->doAction('org_documents_count', array('id' => (integer)$id));
  }

  /**
   * @param $action
   * @param $parameters
   */
  protected function sessionChecks($action, $parameters) {
    // Parameter Validation
    assert('isset($action) && is_string($action)');
    assert('isset($parameters) && is_array($parameters)');

    // Need a Session for all the Session Commands
    $this->checkInSession();
    $this->checkLoggedIn();

    // Extract Organization ID
    $parameters = $this->processParameter($action, $parameters, array('OrgDocumentAdd', 'OrgDocumentRemove', 'OrgDocumentList', 'OrgDocumentCount'),
      function($controller, $parameters) {
        // Get the Identifier for the Organization
        $id = ArrayUtilities::extract($parameters, 'id');
        if (!isset($id)) {
          throw new \Exception('Missing Required Action Parameter [id].', 1);
        }

        // Get Organization for Action
        $org = $controller->getRepository()->find($id);
        if (!isset($org)) {
          throw new \Exception('Organization not found', 1);
        }

        // Save the Organization for the Action
        $parameters['entity'] = $org;
        $parameters['org'] = $org;
        return $parameters;
      });

    // Extract Document ID
    $parameters = $this->processParameter($action, $parameters, array('DocumentRemove'), function($controller, $parameters) {
      // Get the Identified for the Document
      $doc_id = ArrayUtilities::extract($parameters, 'doc_id');
      if (!isset($doc_id)) {
        throw new \Exception('Missing Required Action Parameter [doc_id].', 1);
      }

      // Get Document for Action
      $document = $controller->getRepository('TestCenter\ModelBundle\Entity\Document')->find($doc_id);
      if (!isset($user)) {
        throw new \Exception('Document not found', 1);
      }

      // Save the Document
      $parameters['document'] = $document;
      return $parameters;
    });

    return $parameters;
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doOrgDocumentAddAction($parameters) {
    assert('isset($parameters) && is_array($parameters)');

    $document = ArrayUtilities::extract($parameters, 'document');
    $org = ArrayUtilities::extract($parameters, 'org');
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Document');
    return $repository->addDocument($this->getTypeRepository()->typeFromEntity($this->m_sEntity), $org->getId());
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doOrgDocumentRemoveAction($parameters) {
    assert('isset($parameters) && is_array($parameters)');

    $document = ArrayUtilities::extract($parameters, 'document');
    $org = ArrayUtilities::extract($parameters, 'org');
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Document');
    return $repository->removeDocument($this->getTypeRepository()->typeFromEntity($this->m_sEntity), $org->getId(), $document->getId());
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doOrgDocumentsListAction($parameters) {
    assert('isset($parameters) && is_array($parameters)');

    $org = ArrayUtilities::extract($parameters, 'org');
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Document');
    return $repository->listDocuments($this->getTypeRepository()->typeFromEntity($this->m_sEntity), $org->getId());
  }

  /**
   * @param $parameters
   * @return mixed
   */
  protected function doOrgDocumentsCountAction($parameters) {
    assert('isset($parameters) && is_array($parameters)');

    $org = ArrayUtilities::extract($parameters, 'org');
    $repository = $this->getRepository('TestCenter\ModelBundle\Entity\Document');
    return $repository->countDocuments($this->getTypeRepository()->typeFromEntity($this->m_sEntity), $org->getId());
  }
}
