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
	 *  Routen Ist-Bestand
	 * Zusammengefasst nach genauen Grad inkl. Zwischengrade
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		$params       = JComponentHelper::getParams('com_act');
		$grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

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
							 'COUNT(CASE WHEN t.calc_grade_round = 37 then 1 ELSE NULL END) as  ist_grade_37',
							 'COUNT(CASE WHEN t.calc_grade_round = 38 then 1 ELSE NULL END) as  ist_grade_38',
							 'COUNT(CASE WHEN t.calc_grade_round = 39 then 1 ELSE NULL END) as  ist_grade_39',
							 'COUNT(CASE WHEN t.calc_grade_round = 40 then 1 ELSE NULL END) as  ist_grade_40',
							 'COUNT(CASE WHEN t.calc_grade_round = 41 then 1 ELSE NULL END) as  ist_grade_41',
							 'COUNT(CASE WHEN t.calc_grade_round = 42 then 1 ELSE NULL END) as  ist_grade_42',
							 'COUNT(CASE WHEN t.calc_grade_round = 43 then 1 ELSE NULL END) as  ist_grade_43',
							 'COUNT(CASE WHEN t.calc_grade_round = 44 then 1 ELSE NULL END) as  ist_grade_44',
							 'COUNT(CASE WHEN t.calc_grade_round = 45 then 1 ELSE NULL END) as  ist_grade_45',
							 'COUNT(CASE WHEN t.calc_grade_round = 46 then 1 ELSE NULL END) as  ist_grade_46',
							 'COUNT(CASE WHEN t.calc_grade_round = 47 then 1 ELSE NULL END) as  ist_grade_47',
							 'COUNT(CASE WHEN t.calc_grade_round = 48 then 1 ELSE NULL END) as  ist_grade_48',
							 'COUNT(CASE WHEN t.calc_grade_round = 49 then 1 ELSE NULL END) as  ist_grade_49',
							 'COUNT(CASE WHEN t.calc_grade_round = 50 then 1 ELSE NULL END) as  ist_grade_50',
							 'COUNT(CASE WHEN t.calc_grade_round = 51 then 1 ELSE NULL END) as  ist_grade_51',
							 'COUNT(CASE WHEN t.calc_grade_round = 52 then 1 ELSE NULL END) as  ist_grade_52',
							 'COUNT(CASE WHEN t.calc_grade_round = 53 then 1 ELSE NULL END) as  ist_grade_53',
							 'COUNT(a.state) as  totalroutes',
							)
						);
			
		$query->from('#__act_route AS a')
			  ->join('LEFT', '#__act_trigger_calc AS t  ON t.id        = a.id')  
              ->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round') // Convertierter Grad cg = C-Grade
			  ->join('LEFT', '#__act_line 		  AS l  ON l.id        = a.line')
			  ->join('LEFT', '#__act_sector       AS s  ON s.id        = l.sector')
			  ->where('a.state IN (1, -1)');

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
	 * Routen Ist-Bestand
	 * Zusammengefasst auf ganze Grade 
	 * Keine ZWG und keine + und - Grade
	 * 
	 * @return  mixed Array
	 */

	 public function getRoutesIstGradeTotal()
	 {
		$params       = JComponentHelper::getParams('com_act');
		$grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select(array('COUNT(CASE WHEN cg.filter = 3  then 1 ELSE NULL END) as  ist_gradetotal3',
		 					 'COUNT(CASE WHEN cg.filter = 4  then 1 ELSE NULL END) as  ist_gradetotal4',
		 					 'COUNT(CASE WHEN cg.filter = 5  then 1 ELSE NULL END) as  ist_gradetotal5',
		 					 'COUNT(CASE WHEN cg.filter = 6  then 1 ELSE NULL END) as  ist_gradetotal6',
		 					 'COUNT(CASE WHEN cg.filter = 7  then 1 ELSE NULL END) as  ist_gradetotal7',
		 					 'COUNT(CASE WHEN cg.filter = 8  then 1 ELSE NULL END) as  ist_gradetotal8',
		 					 'COUNT(CASE WHEN cg.filter = 9  then 1 ELSE NULL END) as  ist_gradetotal9',
		 					 'COUNT(CASE WHEN cg.filter = 10 then 1 ELSE NULL END) as  ist_gradetotal10',
		 					 'COUNT(CASE WHEN cg.filter = 11 then 1 ELSE NULL END) as  ist_gradetotal11',
		 					 'COUNT(CASE WHEN cg.filter = 12 then 1 ELSE NULL END) as  ist_gradetotal12',
							 'COUNT(CASE WHEN cg.filter = 0  then 1 ELSE NULL END) as  comes_out_undefined',
							 )
						 );
			 
	    $query->from('#__act_route AS a')
		       ->join('LEFT', '#__act_trigger_calc AS t ON t.id = a.id') // VIEW TABLE
				// Convertierter Grad cg = C-Grade
				->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round')
				->join('LEFT', '#__act_line AS l ON l.id = a.line')
				->join('LEFT', '#__act_sector AS s ON s.id = l.sector')
				->where('a.state IN (1, -1)'); // Wichtig Status Vorgemerkt und Freigegeben

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
 
		$db->setQuery($query);

		$result = $db->loadRowList();
		return $result;
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
        
		$query->select(array('SUM(JSON_EXTRACT(routessoll_ind, "$.g10")) as grade_id10',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g11")) as grade_id11',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g12")) as grade_id12',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g13")) as grade_id13',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g14")) as grade_id14',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g15")) as grade_id15',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g16")) as grade_id16',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g17")) as grade_id17',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g18")) as grade_id18',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g19")) as grade_id19',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g20")) as grade_id20',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g21")) as grade_id21',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g22")) as grade_id22',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g23")) as grade_id23',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g24")) as grade_id24',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g25")) as grade_id25',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g26")) as grade_id26',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g27")) as grade_id27',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g28")) as grade_id28',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g29")) as grade_id29',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g30")) as grade_id30',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g31")) as grade_id31',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g32")) as grade_id32',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g33")) as grade_id33',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g34")) as grade_id34',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g35")) as grade_id35',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g36")) as grade_id36',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g37")) as grade_id37',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g38")) as grade_id38',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g39")) as grade_id39',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g40")) as grade_id40',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g41")) as grade_id41',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g42")) as grade_id42',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g43")) as grade_id43',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g44")) as grade_id44',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g45")) as grade_id45',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g46")) as grade_id46',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g47")) as grade_id47',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g48")) as grade_id48',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g49")) as grade_id49',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g50")) as grade_id50',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g51")) as grade_id51',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g52")) as grade_id52',
	                         'SUM(JSON_EXTRACT(routessoll_ind, "$.g53")) as grade_id53',
							),
					  );
        $query->from('#__act_sector')
			  ->where($db->qn('state') .'= 1');


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
        
		$query->select(array('ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g3")),1) as grade3',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g4")),1) as grade4',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g5")),1) as grade5',
							 'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g6")),1) as grade6',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g7")),1) as grade7',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g8")),1) as grade8',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g9")),1) as grade9',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g10")),1) as grade10',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g11")),1) as grade11',
	                         'ROUND(SUM(JSON_EXTRACT(a.routessoll, "$.g12")),1) as grade12',
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
	 * Routen Vorgemerkt zum herausschrauben 
	 * Zusammengefasst auf ganze Grade 6-, 6, 6+
	 * ZWG werden zum unteren Grad zugwiesen
	 * 
	 * !!!!Ausschlagebend ist welcher Filter in der Tabelle unter id_grade_filter steht!!!!!!!!!
	 *
	 * @return  mixed Array
	 */

	 public function getRoutesComesOut()
	 {
		 $params       = JComponentHelper::getParams('com_act');
		 $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade
 
		 $db    = $this->getDbo();
		 $query = $db->getQuery(true);
 
		 $query->select(array('COUNT(CASE WHEN cg.id_grade_filter = 10  then 1 ELSE NULL END) as comes_out_10',
							  'COUNT(CASE WHEN cg.id_grade_filter = 11 then 1 ELSE NULL END) as  comes_out_11',
							  'COUNT(CASE WHEN cg.id_grade_filter = 12 then 1 ELSE NULL END) as  comes_out_12',
							  'COUNT(CASE WHEN cg.id_grade_filter = 13 then 1 ELSE NULL END) as  comes_out_13',
							  'COUNT(CASE WHEN cg.id_grade_filter = 14 then 1 ELSE NULL END) as  comes_out_14',
							  'COUNT(CASE WHEN cg.id_grade_filter = 15 then 1 ELSE NULL END) as  comes_out_15',
							  'COUNT(CASE WHEN cg.id_grade_filter = 16 then 1 ELSE NULL END) as  comes_out_16',
							  'COUNT(CASE WHEN cg.id_grade_filter = 17 then 1 ELSE NULL END) as  comes_out_17',
							  'COUNT(CASE WHEN cg.id_grade_filter = 18 then 1 ELSE NULL END) as  comes_out_18',
							  'COUNT(CASE WHEN cg.id_grade_filter = 19 then 1 ELSE NULL END) as  comes_out_19',
							  'COUNT(CASE WHEN cg.id_grade_filter = 20 then 1 ELSE NULL END) as  comes_out_20',
							  'COUNT(CASE WHEN cg.id_grade_filter = 21 then 1 ELSE NULL END) as  comes_out_21',
							  'COUNT(CASE WHEN cg.id_grade_filter = 22 then 1 ELSE NULL END) as  comes_out_22',
							  'COUNT(CASE WHEN cg.id_grade_filter = 23 then 1 ELSE NULL END) as  comes_out_23',
							  'COUNT(CASE WHEN cg.id_grade_filter = 24 then 1 ELSE NULL END) as  comes_out_24',
							  'COUNT(CASE WHEN cg.id_grade_filter = 25 then 1 ELSE NULL END) as  comes_out_25',
							  'COUNT(CASE WHEN cg.id_grade_filter = 26 then 1 ELSE NULL END) as  comes_out_26',
							  'COUNT(CASE WHEN cg.id_grade_filter = 27 then 1 ELSE NULL END) as  comes_out_27',
							  'COUNT(CASE WHEN cg.id_grade_filter = 28 then 1 ELSE NULL END) as  comes_out_28',
							  'COUNT(CASE WHEN cg.id_grade_filter = 29 then 1 ELSE NULL END) as  comes_out_29',
							  'COUNT(CASE WHEN cg.id_grade_filter = 30 then 1 ELSE NULL END) as  comes_out_30',
							  'COUNT(CASE WHEN cg.id_grade_filter = 31 then 1 ELSE NULL END) as  comes_out_31',
							  'COUNT(CASE WHEN cg.id_grade_filter = 32 then 1 ELSE NULL END) as  comes_out_32',
							  'COUNT(CASE WHEN cg.id_grade_filter = 33 then 1 ELSE NULL END) as  comes_out_33',
							  'COUNT(CASE WHEN cg.id_grade_filter = 34 then 1 ELSE NULL END) as  comes_out_34',
							  'COUNT(CASE WHEN cg.id_grade_filter = 35 then 1 ELSE NULL END) as  comes_out_35',
							  'COUNT(CASE WHEN cg.id_grade_filter = 36 then 1 ELSE NULL END) as  comes_out_36',
							  'COUNT(CASE WHEN cg.id_grade_filter = 36 then 1 ELSE NULL END) as  comes_out_36',
							  'COUNT(CASE WHEN cg.id_grade_filter = 37 then 1 ELSE NULL END) as  comes_out_37',
							  'COUNT(CASE WHEN cg.id_grade_filter = 38 then 1 ELSE NULL END) as  comes_out_38',
							  'COUNT(CASE WHEN cg.id_grade_filter = 39 then 1 ELSE NULL END) as  comes_out_39',
							  'COUNT(CASE WHEN cg.id_grade_filter = 40 then 1 ELSE NULL END) as  comes_out_40',
							  'COUNT(CASE WHEN cg.id_grade_filter = 41 then 1 ELSE NULL END) as  comes_out_41',
							  'COUNT(CASE WHEN cg.id_grade_filter = 42 then 1 ELSE NULL END) as  comes_out_42',
							  'COUNT(CASE WHEN cg.id_grade_filter = 43 then 1 ELSE NULL END) as  comes_out_43',
							  'COUNT(CASE WHEN cg.id_grade_filter = 44 then 1 ELSE NULL END) as  comes_out_44',
							  'COUNT(CASE WHEN cg.id_grade_filter = 45 then 1 ELSE NULL END) as  comes_out_45',
							  'COUNT(CASE WHEN cg.id_grade_filter = 46 then 1 ELSE NULL END) as  comes_out_46',
							  'COUNT(CASE WHEN cg.id_grade_filter = 47 then 1 ELSE NULL END) as  comes_out_47',
							  'COUNT(CASE WHEN cg.id_grade_filter = 48 then 1 ELSE NULL END) as  comes_out_48',
							  'COUNT(CASE WHEN cg.id_grade_filter = 49 then 1 ELSE NULL END) as  comes_out_49',
							  'COUNT(CASE WHEN cg.id_grade_filter = 50 then 1 ELSE NULL END) as  comes_out_50',
							  'COUNT(CASE WHEN cg.id_grade_filter = 51 then 1 ELSE NULL END) as  comes_out_51',
							  'COUNT(CASE WHEN cg.id_grade_filter = 52 then 1 ELSE NULL END) as  comes_out_52',
							  'COUNT(CASE WHEN cg.id_grade_filter = 53 then 1 ELSE NULL END) as  comes_out_53',
							  
							  'COUNT(a.state) as  totalroutes',
							 )
						 );
			 
		 $query->from('#__act_route AS a')
			   ->join('LEFT', '#__act_trigger_calc AS t ON t.id = a.id') // VIEW TABLE
			   // Convertierter Grad cg = C-Grade
			   ->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round')
			   ->join('LEFT', '#__act_line AS l ON l.id = a.line')
			   ->join('LEFT', '#__act_sector AS s ON s.id = l.sector')
			   ->where('a.state = -1');
 
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
	 
		 $db->setQuery($query);
		 $result = $db->loadObjectList();
 
		 return $result;
 
	 }
 
 
	 /**
	  * Routen Vorgemerkt zum herausschrauben 
	  * Zusammengefasst auf ganze Grade
	  * 
	  * @return  mixed Array
	  */
 
	  public function getRoutesComesOutGradeTotal()
	  {
		 $params       = JComponentHelper::getParams('com_act');
		 $grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade
 
		 $db    = $this->getDbo();
		 $query = $db->getQuery(true);
		 $query->select(array('COUNT(CASE WHEN cg.filter = 3  then 1 ELSE NULL END) as  comes_out_gradetotal3',
							  'COUNT(CASE WHEN cg.filter = 4  then 1 ELSE NULL END) as  comes_out_gradetotal4',
							  'COUNT(CASE WHEN cg.filter = 5  then 1 ELSE NULL END) as  comes_out_gradetotal5',
							  'COUNT(CASE WHEN cg.filter = 6  then 1 ELSE NULL END) as  comes_out_gradetotal6',
							  'COUNT(CASE WHEN cg.filter = 7  then 1 ELSE NULL END) as  comes_out_gradetotal7',
							  'COUNT(CASE WHEN cg.filter = 8  then 1 ELSE NULL END) as  comes_out_gradetotal8',
							  'COUNT(CASE WHEN cg.filter = 9  then 1 ELSE NULL END) as  comes_out_gradetotal9',
							  'COUNT(CASE WHEN cg.filter = 10 then 1 ELSE NULL END) as  comes_out_gradetotal10',
							  'COUNT(CASE WHEN cg.filter = 11 then 1 ELSE NULL END) as  comes_out_gradetotal11',
							  'COUNT(CASE WHEN cg.filter = 12 then 1 ELSE NULL END) as  comes_out_gradetotal12',
							  'COUNT(CASE WHEN cg.filter = 0  then 1 ELSE NULL END) as  comes_out_undefined',
							  )
						  );
			  
		 $query->from('#__act_route AS a')
				->join('LEFT', '#__act_trigger_calc AS t ON t.id = a.id') // VIEW TABLE
				 // Convertierter Grad cg = C-Grade
				 ->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round')
				 ->join('LEFT', '#__act_line AS l ON l.id = a.line')
				 ->join('LEFT', '#__act_sector AS s ON s.id = l.sector')
				 ->where('a.state = -1'); // State -1 = Vorgemerkt
 
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
  
		 $db->setQuery($query);
 
		 $result = $db->loadRowList();
		 return $result;
	  }

 

	/**
	 * Liste von Routen welche zum Rausschrauben vorgemerkt sind
	 * Ausgabe des Routennamen, Linie, Sektor usw
	 *
	 * @return  mixed Array
	 */
    public function getReplaceRoutes()
    { 
		$params       = JComponentHelper::getParams('com_act');
		$grade_table = $params['grade_table'];  // Welche Tabelle für Schwierigkeitsgrade

        $db    = $this->getDbo();
        $query = $db->getQuery(true);
        
        $query->select(array('r.id, r.name, l.line, s.sector,  c.color, r.extend_sql', 't.calc_grade_round', 'cg.grade AS c_grade',))
              ->from('#__act_route AS r')
			  ->join('LEFT', '#__act_trigger_calc AS t ON t.id = r.id') // VIEW TABLE
			  // Convertierter Grad cg = C-Grade
 			  ->join('LEFT', '#__'.$grade_table.' AS cg ON cg.id_grade = t.calc_grade_round')
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
			};
			
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
