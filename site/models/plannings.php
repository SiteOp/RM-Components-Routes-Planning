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
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Helper\TagsHelper;
use \Joomla\CMS\Layout\FileLayout;
use \Joomla\Utilities\ArrayHelper;

/**
 * Methods supporting a list of Routes_planning records.
 *
 * @since  1.6
 */
class Routes_planningModelPlannings extends \Joomla\CMS\MVC\Model\ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				
			);
		}

		parent::__construct($config);
	}

        
        
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app  = Factory::getApplication();
            
           
            
        // List state information.

        parent::populateState($ordering, $direction);

        $context = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
        $this->setState('filter.search', $context);

        

        // Load the parameters.
		$params = $app->getParams();
		$this->setState('params', $params);

        // Split context into component and optional section
        $parts = FieldsHelper::extract($context);

        if ($parts)
        {
            $this->setState('filter.component', $parts[0]);
            $this->setState('filter.section', $parts[1]);
        }
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		$db	= $this->getDbo();
		$query	= $db->getQuery(true);
		$query->select(array('COUNT(CASE WHEN t.calc_grade = 10 then 1 ELSE NULL END) as  ist_grade_10',
						     'COUNT(CASE WHEN t.calc_grade = 11 then 1 ELSE NULL END) as  ist_grade_11',
							 'COUNT(CASE WHEN t.calc_grade = 12 then 1 ELSE NULL END) as  ist_grade_12',
							 'COUNT(CASE WHEN t.calc_grade = 13 then 1 ELSE NULL END) as  ist_grade_13',
							 'COUNT(CASE WHEN t.calc_grade = 14 then 1 ELSE NULL END) as  ist_grade_14',
							 'COUNT(CASE WHEN t.calc_grade = 15 then 1 ELSE NULL END) as  ist_grade_15',
							 'COUNT(CASE WHEN t.calc_grade = 16 then 1 ELSE NULL END) as  ist_grade_16',
							 'COUNT(CASE WHEN t.calc_grade = 17 then 1 ELSE NULL END) as  ist_grade_17',
							 'COUNT(CASE WHEN t.calc_grade = 18 then 1 ELSE NULL END) as  ist_grade_18',
							 'COUNT(CASE WHEN t.calc_grade = 19 then 1 ELSE NULL END) as  ist_grade_19',
							 'COUNT(CASE WHEN t.calc_grade = 20 then 1 ELSE NULL END) as  ist_grade_20',
							 'COUNT(CASE WHEN t.calc_grade = 21 then 1 ELSE NULL END) as  ist_grade_21',
							 'COUNT(CASE WHEN t.calc_grade = 22 then 1 ELSE NULL END) as  ist_grade_22',
							 'COUNT(CASE WHEN t.calc_grade = 23 then 1 ELSE NULL END) as  ist_grade_23',
							 'COUNT(CASE WHEN t.calc_grade = 24 then 1 ELSE NULL END) as  ist_grade_24',
							 'COUNT(CASE WHEN t.calc_grade = 25 then 1 ELSE NULL END) as  ist_grade_25',
							 'COUNT(CASE WHEN t.calc_grade = 26 then 1 ELSE NULL END) as  ist_grade_26',
							 'COUNT(CASE WHEN t.calc_grade = 27 then 1 ELSE NULL END) as  ist_grade_27',
							 'COUNT(CASE WHEN t.calc_grade = 28 then 1 ELSE NULL END) as  ist_grade_28',
							 'COUNT(CASE WHEN t.calc_grade = 29 then 1 ELSE NULL END) as  ist_grade_29',
							 'COUNT(CASE WHEN t.calc_grade = 30 then 1 ELSE NULL END) as  ist_grade_30',
							 'COUNT(CASE WHEN t.calc_grade = 31 then 1 ELSE NULL END) as  ist_grade_31',
							 'COUNT(CASE WHEN t.calc_grade = 32 then 1 ELSE NULL END) as  ist_grade_32',
							 'COUNT(CASE WHEN t.calc_grade = 33 then 1 ELSE NULL END) as  ist_grade_33',
							 'COUNT(CASE WHEN t.calc_grade = 34 then 1 ELSE NULL END) as  ist_grade_34',
							 'COUNT(CASE WHEN t.calc_grade = 35 then 1 ELSE NULL END) as  ist_grade_35',
							 'COUNT(CASE WHEN t.calc_grade = 36 then 1 ELSE NULL END) as  ist_grade_36',
						
							 'SUM(DISTINCT(s.soll10)) as  soll_grade_10',
							 'SUM(DISTINCT(s.soll11)) as  soll_grade_11',
							 'SUM(DISTINCT(s.soll12)) as  soll_grade_12',
							 'SUM(DISTINCT(s.soll13)) as  soll_grade_13',
							 'SUM(DISTINCT(s.soll14)) as  soll_grade_14',
							 'SUM(DISTINCT(s.soll15)) as  soll_grade_15',
							 'SUM(DISTINCT(s.soll16)) as  soll_grade_16',
							 'SUM(DISTINCT(s.soll17)) as  soll_grade_17',
							 'SUM(DISTINCT(s.soll18)) as  soll_grade_18',
							 'SUM(DISTINCT(s.soll19)) as  soll_grade_19',
							 'SUM(DISTINCT(s.soll20)) as  soll_grade_20',
							 'SUM(DISTINCT(s.soll21)) as  soll_grade_21',
							 'SUM(DISTINCT(s.soll22)) as  soll_grade_22',
							 'SUM(DISTINCT(s.soll23)) as  soll_grade_23',
							 'SUM(DISTINCT(s.soll24)) as  soll_grade_24',
							 'SUM(DISTINCT(s.soll25)) as  soll_grade_25',
							 'SUM(DISTINCT(s.soll26)) as  soll_grade_26',
							 'SUM(DISTINCT(s.soll27)) as  soll_grade_27',
							 'SUM(DISTINCT(s.soll28)) as  soll_grade_28',
							 'SUM(DISTINCT(s.soll29)) as  soll_grade_29',
							 'SUM(DISTINCT(s.soll30)) as  soll_grade_30',
							 'SUM(DISTINCT(s.soll31)) as  soll_grade_31',
							 'SUM(DISTINCT(s.soll32)) as  soll_grade_32',
							 'SUM(DISTINCT(s.soll33)) as  soll_grade_33',
							 'SUM(DISTINCT(s.soll34)) as  soll_grade_34',
							 'SUM(DISTINCT(s.soll35)) as  soll_grade_35',
							 'SUM(DISTINCT(s.soll36)) as  soll_grade_36',

							 'COUNT(a.state) as  totalroutes',
							
							)
						);
			
		$query->from('#__act_route AS a')
		      ->join('LEFT', '#__act_trigger_calc AS t ON t.id = a.id') // VIEW TABLE
			  ->join('LEFT', '#__act_line AS l ON l.id = a.line')
			  ->join('LEFT', '#__act_sector AS s ON s.id = l.sector')
			 // ->join('LEFT', '#__act_route AS r ON r.line = a.line')
			  ->where('a.state IN (1,-1)'); // Status Veröffentlicht und in Planung

		// Filtering sector
		$filter_sector = $this->state->get("filter.sector");
			if ($filter_sector != '')
			{
				//$query->where($db->qn('s.id') . '=' . (int) $filter_sector);
				JArrayHelper::toInteger($filter_sector);
                $query->where($db->qn('s.id') . 'IN (' . implode(',', $filter_sector).')');
			}

		// Filtering building
        $filter_building = $this->state->get("filter.building");
            if ($filter_building != '') {
               $query->where($db->qn('s.building') .'=' . (int) $filter_building);
            }

	 return $query;
	}


	/**
	 * Liste von Routen welche zum Rausschrauben vorgemerkt sind
	 *
	 * @return  mixed Array
	 */
    public function getReplaceRoutes()
    { 
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select(array('r.id, r.name, l.line, s.sector, g.uiaa, c.color, r.extend_sql'))
              ->from('#__act_route AS r')
			  ->join('LEFT', '#__act_trigger_calc AS t ON t.id = r.id') // VIEW TABLE
			  ->join('LEFT', '#__act_grade AS g ON g.id = t.calc_grade_round')
			  ->join('LEFT', '#__act_line AS l ON l.id = r.line')
			  ->join('LEFT', '#__act_sector AS s ON s.id = l.sector')
			  ->join('LEFT', '#__act_color AS c ON c.id = r.color')
              ->where('r.state = -1')
              ->order('r.line');
		
		// Filtering sector	  
		$filter_sector = $this->state->get("filter.sector");
			if ($filter_sector != '')
			{
				JArrayHelper::toInteger($filter_sector);
                $query->where($db->qn('s.id') . 'IN (' . implode(',', $filter_sector).')');
			}
 
        $db->setQuery($query);
        $result = $db->loadObjectList();
        
        return $result;
    }


	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();
		

		return $items;
	}


}
