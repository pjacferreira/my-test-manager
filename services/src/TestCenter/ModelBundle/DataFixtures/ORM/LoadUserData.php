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

namespace TestCenter\ModelBundl\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use TestCenter\ModelBundle\Entity\User;

/**
 * Description of LoadUserData
 *
 * @author Paulo Ferreira
 */
class LoadUserData
  extends AbstractFixture
  implements OrderedFixtureInterface {

  public function load(ObjectManager $manager) {
    $userAdmin = new User();
    $userAdmin->setName('admin');
    $userAdmin->setPassword(md5('admin'));

    $manager->persist($userAdmin);
    $manager->flush();

    $this->addReference('admin-user', $userAdmin);
  }

  public function getOrder() {
    return 1; // the order in which fixtures will be loaded
  }

}
