<?php
/**
 * @package     Molajo
 * @subpackage  Page
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

interface DoctrineController {
    public function setEntityManager(Doctrine\ORM\EntityManager $entityManager);
}
