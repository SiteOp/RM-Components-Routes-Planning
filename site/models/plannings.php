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
		$query->select(array('COUNT(CASE WHEN t.calc_grade_round = 10 then 1 ELSE NULL END) as  ist_grade_10',
						     'COUNT(CASE WHEN t.calc_grade_round = 11 then 1 ELSE NULL END) as  ist_grade_11',
							 'COUNT(CASE WHEN t.calc_grade_round = 12 then 1 ELSE NULL END) as  ist_grade_12',
							 'COUNT(CASE WHEN t.calc_grade_round = 13 then 1 ELSE NULL END) as  ist_grade_13',
							 'COUNT(CASE WHEN t.calc_grade_round = 14 then 1 ELSE NULL END) as  ist_grade_14',
							 'COUNT(CASE WHEN t.calc_grade_round = 15 then 1 ELSE NULL END) as  ist_grade_15',
							 'COUNT(CASE WHEN t.calc_grade_round = 16 then 1 ELSE NULL END) as  ist_grade_16',
							 'COUNT(CASE WHEN t.calc_grade_round = 17 then 1 ELSE NULL END) as  ist_grade_17',
							 'COUNT(CASE WHEN t.calc_grade_round = 18 then 1 ELSE NULL END) as  ist_grade_18',
							 'COUNT(CASE WHEN t.calc_grade_round = 19 then 1 ELSE NULL END) as  ist_grade_19',
							 'COUNT(CASE WHEN t.calc_grade_round = 20 then 1 ELSE NULL END) as  ist_grade_20',
							 'COUNT(CASE WHEN t.calc_grade_round = 21 then 1 ELSE NULL END) as  ist_grade_21',
							 'COUNT(CASE WHEN t.calc_grade_round = 22 then 1 ELSE NULL END) as  ist_grade_22',
							 'COUNT(CASE WHEN t.calc_grade_round = 23 then 1 ELSE NULL END) as  ist_grade_23',
							 'COUNT(CASE WHEN t.calc_grade_round = 24 then 1 ELSE NULL END) as  ist_grade_24',
							 'COUNT(CASE WHEN t.calc_grade_round = 25 then 1 ELSE NULL END) as  ist_grade_25',
							 'COUNT(CASE WHEN t.calc_grade_round = 26 then 1 ELSE NULL END) as  ist_grade_26',
							 'COUNT(CASE WHEN t.calc_grade_round = 27 then 1 ELSE NULL END) as  ist_grade_27',
							 'COUNT(CASE WHEN t.calc_grade_round = 28 then 1 ELSE NULL END) as  ist_grade_28',
							 'COUNT(CASE WHEN t.calc_grade_round = 29 then 1 ELSE NULL END) as  ist_grade_29',
							 'COUNT(CASE WHEN t.calc_grade_round = 30 then 1 ELSE NULL END) as  ist_grade_30',
							 'COUNT(CASE WHEN t.calc_grade_round = 31 then 1 ELSE NULL END) as  ist_grade_31',
							 'COUNT(CASE WHEN t.calc_grade_round = 32 then 1 ELSE NULL END) as  ist_grade_32',
							 'COUNT(CASE WHEN t.calc_grade_round = 33 then 1 ELSE NULL END) as  ist_grade_33',
							 'COUNT(CASE WHEN t.calc_grade_round = 34 then 1 ELSE NULL END) as  ist_grade_34',
							 'COUNT(CASE WHEN t.calc_grade_round = 35 then 1 ELSE NULL END) as  ist_grade_35',
							 'COUNT(CASE WHEN t.calc_grade_round = 36 then 1 ELSE NULL END) as  ist_grade_36',

							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 10 AND 11 then 1 ELSE NULL END) as ist_gradetotal3',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 12 AND 14 then 1 ELSE NULL END) as ist_gradetotal4',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 15 AND 17 then 1 ELSE NULL END) as ist_gradetotal5',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 18 AND 20 then 1 ELSE NULL END) as ist_gradetotal6',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 21 AND 23 then 1 ELSE NULL END) as ist_gradetotal7',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 24 AND 26 then 1 ELSE NULL END) as ist_gradetotal8',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 27 AND 29 then 1 ELSE NULL END) as ist_gradetotal9',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 30 AND 32 then 1 ELSE NULL END) as ist_gradetotal10',
							 'COUNT(CASE WHEN t.calc_grade_round BETWEEN 33 AND 35 then 1 ELSE NULL END) as ist_gradetotal11',
							 'COUNT(CASE WHEN t.calc_grade_round = 36 then 1 ELSE NULL END)  as ist_gradetotal12',
							 'COUNT(CASE WHEN t.calc_grade_round NOT BETWEEN 10 AND 36 then 1 ELSE NULL END) as ist_undefined',
							 'COUNT(a.state) as  totalroutes',
							)
						);
			
		$query->from('#__act_route AS a')
		      ->join('LEFT', '#__act_trigger_calc AS t ON t.id = a.id') // VIEW TABLE
			  ->join('LEFT', '#__act_line AS l ON l.id = a.line')
			  ->join('LEFT', '#__act_sector AS s ON s.id = l.sector')
			  ->where('a.state IN (1,-1)'); // Status Veröffentlicht und in Planung

		// Filtering sector
		$filter_sector = $this->state->get("filter.sector");
			if ($filter_sector != '')
			{
				ArrayHelper::toInteger($filter_sector);
                $query->where($db->qn('s.id') . 'IN (' . implode(',', $filter_sector).')');
			}

		// Filtering building
        $filter_building = $this->state->get("filter.building");
            if ($filter_building != '') {
               $query->where($db->qn('s.building') .'=' . (int) $filter_building);
            }
			//echo $query->dump(); exit;
	 return $query;
	}


	/**
	 * Soll Bestand Einzelwerterfassug Sektoren
	 *
	 * @return  mixed Array
	 */
    public function getSollRoutesInd()
    { 
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
		$query->select(array('SUM(JSON_EXTRACT(routessoll_ind, "$.g10")) as grade10',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g11")) as grade11',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g12")) as grade12',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g13")) as grade13',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g14")) as grade14',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g15")) as grade15',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g16")) as grade16',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g17")) as grade17',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g18")) as grade18',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g19")) as grade19',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g20")) as grade20',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g21")) as grade21',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g22")) as grade22',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g23")) as grade23',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g24")) as grade24',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g25")) as grade25',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g26")) as grade26',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g27")) as grade27',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g28")) as grade28',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g29")) as grade29',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g30")) as grade30',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g31")) as grade31',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g32")) as grade32',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g33")) as grade33',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g34")) as grade34',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g35")) as grade35',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g36")) as grade36',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g10") +
								  JSON_EXTRACT(routessoll_ind, "$.g11")) as gradetotal3',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g12") +
								  JSON_EXTRACT(routessoll_ind, "$.g13") +
								  JSON_EXTRACT(routessoll_ind, "$.g14")) as gradetotal4',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g15") +
								  JSON_EXTRACT(routessoll_ind, "$.g16") +
								  JSON_EXTRACT(routessoll_ind, "$.g17")) as gradetotal5',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g18") +
								  JSON_EXTRACT(routessoll_ind, "$.g19") +
								  JSON_EXTRACT(routessoll_ind, "$.g20")) as gradetotal6',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g21") +
								  JSON_EXTRACT(routessoll_ind, "$.g22") +
								  JSON_EXTRACT(routessoll_ind, "$.g23")) as gradetotal7',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g24") +
								  JSON_EXTRACT(routessoll_ind, "$.g25") +
								  JSON_EXTRACT(routessoll_ind, "$.g26")) as gradetotal8',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g27") +
								  JSON_EXTRACT(routessoll_ind, "$.g28") +
								  JSON_EXTRACT(routessoll_ind, "$.g29")) as gradetotal9',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g30") +
								  JSON_EXTRACT(routessoll_ind, "$.g31") +
								  JSON_EXTRACT(routessoll_ind, "$.g32")) as gradetotal10',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g33") +
								  JSON_EXTRACT(routessoll_ind, "$.g34") +
								  JSON_EXTRACT(routessoll_ind, "$.g35")) as gradetotal11',
							 'SUM(JSON_EXTRACT(routessoll_ind, "$.g36")) as gradetotal12',
							),
					  );
        $query->from('#__act_sector');

		// Filtering sector
		$filter_sector = $this->state->get("filter.sector");
			if ($filter_sector != '')
			{
				ArrayHelper::toInteger($filter_sector);
                $query->where($db->qn('id') . 'IN (' . implode(',', $filter_sector).')');
			}

		// Filtering building
        $filter_building = $this->state->get("filter.building");
            if ($filter_building != '') {
               $query->where($db->qn('building') .'=' . (int) $filter_building);
            }
 
        $db->setQuery($query);
        $result = $db->loadObjectList();
        
        return $result;
    }


		/**
	 * Soll Bestand Einzelwerterfassug Sektoren
	 *
	 * @return  mixed Array
	 */
    public function getSollRoutesPercentBuidling()
    { 
        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
		$query->select(array('SUM(JSON_EXTRACT(a.routessoll, "$.g3")) as grade3',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g4")) as grade4',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g5")) as grade5',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g6")) as grade6',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g7")) as grade7',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g8")) as grade8',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g9")) as grade9',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g10")) as grade10',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g11")) as grade11',
	                         'SUM(JSON_EXTRACT(a.routessoll, "$.g12")) as grade12',
							),
					  );
        $query->from('#__act_building AS a');
		     

		
		// Filtering building
        $filter_building = $this->state->get("filter.building");
            if ($filter_building != '') {
               $query->where($db->qn('a.id') .'=' . (int) $filter_building);
            }
 
        $db->setQuery($query);
        $result = $db->loadObjectList();
        
        return $result;
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
				ArrayHelper::toInteger($filter_sector);
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
