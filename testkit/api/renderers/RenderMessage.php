<?php

/* Test Center - Test Kit
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

namespace api\renderers;

require_once dirname(__FILE__).'/../utility.php';

/**
 * Render a Constant Message
 *
 * @author Paulo Ferreira
 */
class RenderMessage
  implements IRenderer {

  protected $m_sMessage;

  /**
   * 
   * @param type $sMessage
   */
  public function __construct($sMessage) {
    $this->m_sMessage = string_onEmpty($sMessage, '');
  }

  /**
   * 
   * @param type $response
   * @return type
   */
  public function render($response = null) {
    return $this->m_sMessage;
  }

}

?>
