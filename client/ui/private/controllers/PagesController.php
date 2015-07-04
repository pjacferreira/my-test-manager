<?php

/**
 * Test Center - Compliance Testing Application (Client UI)
 * Copyright (C) 2012 - 2015 Paulo Ferreira <pf at sourcenotes.org>
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
use Phalcon\Mvc\Controller;
use common\utility\Strings;
use common\utility\Arrays;
use common\utility\I18N;

/**
 * Controller used to Manage Displayed Pages
 *
 * @license http://opensource.org/licenses/AGPL-3.0 Affero GNU Public License v3.0
 * @copyright 2015 Paulo Ferreira
 * @author Paulo Ferreira <pf at sourcenotes.org>
 */
class PagesController extends Controller {
  /*
   * ---------------------------------------------------------------------------
   *  CONTROLLER: Action Entry Points
   * ---------------------------------------------------------------------------
   */

  /**
   * Produces the Rendering of the page
   * 
   * @param string $name Field ID
   * @return string HTTP Body Response
   */
  public function indexAction($name = null) {
    // Start / Resume a Session
    $this->sessionManager->start();

    // Initialize Set Locale
    $this->setLocale();

    // Validate Incoming Parameters
    $original = $name = Strings::nullOnEmpty($name);

    // Specific Page Requested?
    if (!isset($name)) { // NO: Do we have a last-page associated with the session?
      $name = $this->session->get('last-page');
    }

    // Do we have a page to try?
    if (!isset($name)) { // NO: Find the Correct Page Based on Session State
      $name = $this->giveMeAPage();
    }

    // Get a Valid Next Page (based on the requested page)
    $name = $this->validNextPage($name);

    // Get the Page Settings for the REquested Page
    $pageManager = $this->getDI()->getShared('pageManager');
    $page = $pageManager->page($name);

    // Does the Page Exist?
    if (!isset($page)) { // NO: fail-safe go back to landing page
      /* TODO: 
       * Better fail-safe page selection
       * if session mode group -> 'group:home'
       * if session mode user -> 'user:home'
       * real failsafe -> 'landing:home'
       */
      $name = 'landing:home';
      $page = $pageManager->page($name);
    }

    // Does the Page Exist?
    if (isset($page)) { // YES
      $this->session->set('last-page', $name);
      /* TODO: We can do this here, or, since the renderer is also Injectable
       * it could just as easily access the request DI service.
       */
      echo $this->renderer->render($name, $page, $this->request->get());
      return;
    }
    /* ELSE: System Error (We should have atleast gracefully fallen back to
     * landing page)
     */
    $this->response->setStatusCode(404, "System Error");
    $this->response->sendHeaders();
    echo "Page Not Found";
  }

  /**
   * 
   */
  protected function setLocale() {
    // Locale Defaults
    list($path, $default) = $this->getDI()->getShared('locale');

    // Initialize Localization System from Header
    I18N::initializeFromHeader($this->request->getHeader('HTTP_ACCEPT_LANGUAGE'), $path, null, $default);
  }

  protected function validNextPage($name) {
    // Make sure we have atleast a page to start with    
    $name = isset($name) ? $name : 'landing:home';

    // Does the Requested Page Exist?
    $pageManager = $this->getDI()->getShared('pageManager');
    $page = $pageManager->page($name);
    if (isset($page)) { // YES
      $missing = $this->missingPageRequirements($page);
      if (count($missing) > 0) {
        if ($this->anyInList($missing, ['session:start', 'session:logged_in'])) {
          $name = 'landing:home';
        } else if ($this->anyInList($missing, ['session:mode'])) {
          $this->session->set('session:mode', 'user');
          $name = $this->validNextUserPage();
        } else if ($this->anyInList($missing, ['user:not_suspended'])) {
          $name = 'user:suspended';
        }
      }
    }

    return $name;
  }

  protected function giveMeAPage() {
    // Do we have an active user?
    $user = $this->sessionUser();
    if (isset($user)) {
      $mode = $this->session->get('session:mode');
      switch ($mode) {
        case 'group':
          $group = $this->sessionGroup();
          if (isset($group)) {
            $page = $this->validNextGroupPage($page);
            break;
          }
        // ELSE: Fall through - invalid session mode - return to user:mode
        case 'user':
        default:
          $this->session->set('session:mode', 'user');
          $page = $this->validNextUserPage($page);
      }
    } else {
      // Default Page
      $page = 'landing:home';
    }

    return $page;
  }

  protected function validNextUserPage($page) {
    // Session User
    $user = $this->sessionUser();
    if ($user->getFlag('suspended')) {
      $page = 'user:reactivate';
    } else if (!$user->getFlag('validated')) {
      $page = 'user:validate';
    } else if (!$user->getFlag('profile_ready')) {
      $page = 'user:profile';
    }

    return isset($page) ? $page : 'user:home';
  }

  protected function validNextGroupPage($page) {
    // Session Group
    $group = $this->sessionGroup();
    if ($group->getFlag('suspended')) {
      $page = 'group:reactivate';
    } else if (!$group->getFlag('profile_ready')) {
      $page = 'group:profile';
    }

    return isset($page) ? $page : 'group:home';
  }

  protected function missingPageRequirements($settings) {
    $missing = [];
    $requirements = Arrays::get('requirements', $settings);
    if (isset($requirements)) {
      foreach ($requirements as $key => $value) {
        $function = 'Requirement' . implode('', array_map(function($word) {
              return ucfirst($word);
            }, explode(':', $key))
        );
        if (method_exists($this, $function)) {
          if (!$this->$function($value)) {
            $missing[] = $key;
          }
        }
      }
    }
    return $missing;
  }

  protected function anyInList($list, $required) {
    $list = Arrays::nullOnEmpty($list);
    $required = Arrays::nullOnEmpty($required);
    if (isset($list) && isset($required)) {
      for ($i = 0; $i < count($required); $i++) {
        if (in_array($required[$i], $list)) {
          return true;
        }
      }
    }

    return false;
  }

  /*
   * REQUIREMENT TESTS
   */

  protected function RequirementUserValidated($is_required) {
    if (!!$is_required) {
      $user = $this->session->get('user');
      if (isset($user)) {
        $user = new \api\wrappers\User($user);
        return $user->getFlag('validated');
      }
      return false;
    }
    return true;
  }

  /**
   * 
   * @param type $is_required
   * @return boolean
   */
  protected function RequirementUserProfile_ready($is_required) {
    if (!!$is_required) {
      $user = $this->session->get('user');
      if (isset($user)) {
        $user = new \api\wrappers\User($user);
        return $user->getFlag('profile_ready');
      }
      return false;
    }
    return true;
  }

  /**
   * 
   * @param type $is_required
   * @return boolean
   */
  protected function RequirementSessionStart($is_required) {
    if (!!$is_required) {
      return $this->sessionManager->isActive();
    }
    return true;
  }

  /**
   * 
   * @param boolean $is_required 
   * @return boolean
   */
  protected function RequirementSessionLogged_in($is_required) {
    if (!!$is_required) {
      return $this->sessionManager->isLoggedIn();
    }
    return true;
  }

  /**
   * 
   * @param type $is_required
   * @return boolean
   */
  protected function RequirementUserNot_suspended($is_required) {
    if (!!$is_required) {
      $user = $this->sessionManager->get('user');
      if (isset($user)) {
        $user = new \api\wrappers\User($user);
        return !$user->suspended;
      }
      return false;
    }
    return true;
  }

  protected function RequirementSessionMode($req_mode) {
    $req_mode = Strings::nullOnEmpty($req_mode);
    if (isset($req_mode)) {
      $mode = $this->sessionManager->getMode();
      return isset($mode) && ($mode === $req_mode);
    }
    return true;
  }

  protected function sessionUser() {
    if ($this->session->isStarted()) {
      $user = $this->session->get('user');
      return isset($user) ? new api\wrappers\User($user) : null;
    }
    return null;
  }

}
