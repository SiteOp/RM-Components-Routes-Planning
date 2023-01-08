<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Routes_planning
 * @author     Birgit Gebhard <info@routes-manager.de>
 * @copyright  2021 Birgit Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
defined('_JEXEC') or die;

JLoader::register('Routes_planningHelper', JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_routes_planning' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'routes_planning.php');

use \Joomla\CMS\Factory;
use \Joomla\CMS\MVC\Model\BaseDatabaseModel;
use \Joomla\Utilities\ArrayHelper;

/**
 * Class Routes_planningFrontendHelper
 *
 * @since  1.6
 */
class Routes_planningHelpersRoutes_planning
{
	/**
	 * Get an instance of the named model
	 *
	 * @param   string  $name  Model name
	 *
	 * @return null|object
	 */
	public static function getModel($name)
	{
		$model = null;

		// If the file exists, let's
		if (file_exists(JPATH_SITE . '/components/com_routes_planning/models/' . strtolower($name) . '.php'))
		{
			require_once JPATH_SITE . '/components/com_routes_planning/models/' . strtolower($name) . '.php';
			$model = BaseDatabaseModel::getInstance($name, 'Routes_planningModel');
		}

		return $model;
	}

	/**
	 * Gets the files attached to an item
	 *
	 * @param   int     $pk     The item's id
	 *
	 * @param   string  $table  The table's name
	 *
	 * @param   string  $field  The field's name
	 *
	 * @return  array  The files
	 */
	public static function getFiles($pk, $table, $field)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($field)
			->from($table)
			->where('id = ' . (int) $pk);

		$db->setQuery($query);

		return explode(',', $db->loadResult());
	}


	/**
	 * Hole den Namen des Griffherstellers
	 *
	 * @param   int     $id ID des Herstellers
	 *
	 * @return  string Name des Herstellers
	 */
	public static function getHoldManufacturer($id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('name')
			->from('#__act_holds_manufacturer')
			->where('id = ' . (int) $id);

		$db->setQuery($query);
		return $db->loadResult();
	}
	
	/**
	 * Erhalte den ganzen Grade aus einem Zwischengrad
	 * z.b 6- = 6
	 * Bezug aus der Table Grade Spalte filter_uiaa
	 *
	 * @param   int     $id 
	 *
	 * @return  string 
	 */
	public static function getFilterUiaa($id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('filter_uiaa')
			->from('#__act_grade')
			->where('id = ' . (int) $id);

		$db->setQuery($query);
		return $db->loadResult();
	}


	/**
	 * Wie lautet der Filter?
	 * Ausgabe als Grad
	 *
	 * @param   int     
	 *
	 * @return  string 
	 */
	public static function getGradeFilter($id_grade)
	{
		$params       = JComponentHelper::getParams('com_act');
		$grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

		$id_grade = (int) filter_var($id_grade, FILTER_SANITIZE_NUMBER_INT);  // Entferne grade aus der Variable damit nur die ID des Grades bleibt aus grade12 wird 12

		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('filter')
			->from('#__'.$grade_table)
			->where('id_grade = ' . (int) $id_grade);

		$db->setQuery($query);
		return $db->loadResult();
	}



    /**
     * Gets the edit permission for an user
     *
     * @param   mixed  $item  The item
     *
     * @return  bool
     */
    public static function canUserEdit($item)
    {
        $permission = false;
        $user       = Factory::getUser();

        if ($user->authorise('core.edit', 'com_routes_planning'))
        {
            $permission = true;
        }
        else
        {
            if (isset($item->created_by))
            {
                if ($user->authorise('core.edit.own', 'com_routes_planning') && $item->created_by == $user->id)
                {
                    $permission = true;
                }
            }
            else
            {
                $permission = true;
            }
        }

        return $permission;
    }
}
