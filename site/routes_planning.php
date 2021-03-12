<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Routes_planning
 * @author     Birgit Gebhard <info@routes-manager.de>
 * @copyright  2021 Birgit Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Routes_planning', JPATH_COMPONENT);
JLoader::register('Routes_planningController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = BaseController::getInstance('Routes_planning');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
