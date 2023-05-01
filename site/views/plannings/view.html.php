<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Routes_planning
 * @author     Birgit Gebhard <info@routes-manager.de>
 * @copyright  2021 Birgit Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;


/**
 * View class for a list of Routes_planning.
 *
 * @since  1.6
 */
class Routes_planningViewPlannings extends \Joomla\CMS\MVC\View\HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;
	

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();

		$this->state 		  = $this->get('State');
		$this->items   		  = $this->get('Items'); // Ist-Bestand Status 1 + -1 

		$this->filterForm     = $this->get('FilterForm');
		$this->activeFilters  = $this->get('ActiveFilters');

		$this->routesComesOut = $this->get('RoutesComesOut'); 
		$this->routesComesOutTotal = $this->get('RoutesComesOutGradeTotal'); 
		$this->replaceRoutes  = $this->get('ReplaceRoutes'); // Liste von Vorgemerkte Routen (Routenname, Linie, Sektor....)

		$this->sollRoutesInd  = $this->get('SollRoutesInd'); // Individuell = Einzelwert
		$this->SollRoutesPercentBuidling = $this->get('SollRoutesPercentBuidling');

		
		// Params
		$this->params 	   = $this->state->get('params');
		$this->record_type = $this->params['record_type'];
		$this->record_sector_or_building = $this->params['record_sector_or_building']; // Sollen die Sollwerte im Sektor oder Gebäude erfasst werden? 1=Gebäude 2=Sektor

		// Import Helper aus ACT 
		// JSON String der Grade
		JLoader::import('helpers.grade', JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_act');
		$this->gradeList   = GradeHelpersGrade::getGradeListPlanning(); // JSON String der Grade


		// Liste der zusammengefassten Routengrade aus [3, 3+, 4-...] wird [3,4]
		foreach($this->gradeList as $value) {
   			$gradeListShort[] = intval($value->grade);
		}
		$this->$gradeListShort = array_unique($gradeListShort);

		###################### CHARTS ###########################
		/**
		 * Soll-Bestand Total
		 * Soll-Bestand aus Sektoren kommt mit grade_id3, grade_id4
		 * Anhand des Filters für die jeweilige grade_id wird dann die Summe 
		 * des Soll-Bestandes des Totalen-Grade berechnet
		 * Anschließend wird ein neues Array erstellt
		 * Output JSON für Charts
		*/

		// Sollbestand Einzelwerte record_type = 0
		if(0 == $this->record_type) {
			foreach($this->sollRoutesInd[0] AS $grade_id => $value) {
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) ==  3) {$grade3  = $grade3  +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) ==  4) {$grade4  = $grade4  +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) ==  5) {$grade5  = $grade5  +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) ==  6) {$grade6  = $grade6  +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) ==  7) {$grade7  = $grade7  +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) ==  8) {$grade8  = $grade8  +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) ==  9) {$grade9  = $grade9  +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) == 10) {$grade10 = $grade10 +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) == 11) {$grade11 = $grade11 +$value;}
				if(Routes_planningHelpersRoutes_planning::getGradeFilter($grade_id) == 12) {$grade12 = $grade12 +$value;}
			}
			$this->soll_routes_data = array($grade3,$grade4,$grade5,$grade6,$grade7,$grade8,$grade9,$grade10,$grade11,$grade12);
			$this->totalsoll = array_sum($this->soll_routes_data); // Gesamtsummer des Soll-Bestandes
			$this->soll_routes_data = json_encode($this->soll_routes_data);
		}
		// Soll-Bestand für Prozentwerte  record_type = 1
		if(1 == $this->record_type) {
			$this->soll_routes_data = array();
			foreach($this->$gradeListShort as $grade) {
				$grade = "grade$grade";
				array_push($this->soll_routes_data, $this->SollRoutesPercentBuidling[0]->$grade);
			}
			$this->totalsoll = array_sum($this->soll_routes_data);
			$this->soll_routes_data = json_encode($this->soll_routes_data);
		}

		/**
		 * Routen Vorgemerkt zum herausschrauben
		 * Zusammengefasst auf ganze Grade 
		 * Output JSON für Charts
		*/
		$this->routesComesOutGradeTotal = $this->get('RoutesComesOutGradeTotal');

		$this->comes_out_routes_data = json_encode($this->routesComesOutGradeTotal[0]);

		/**
		 * Routen Ist-Bestand 
		 * Zusammengefasst auf ganze Grade
		 * Output JSON für Charts
		*/
		$this->routesIstGradeTotal = $this->get('RoutesIstGradeTotal'); 
		$this->ist_routes_data = json_encode($this->routesIstGradeTotal[0]);

		/**
		 * JSON für Label (Grad wird innerhalb Charts hinzugefügt
		*/ 
		$label_grade   = GradeHelpersGrade::getRoutesgradeFilter(); 
		array_push($label_grade, 0); // Fügt den Grad 0 (undefiniert hinzu)
		$this->label_grade = json_encode($label_grade);


		// Sollwerte abhängig von Auswahl Gebäude / Sektor -- Einzelwerte / Prozentwerte
		//if (1 == $this->record_sector_or_building) {
          //  if(0 == $this->record_type) { 
            //     echo 'Gebäude Einzelwerterfassung fehlt'; // Gebäude Einzelwerterfassung
            //} else {
                // Geböude Prozenterfassung
				// JSON für Soll-Werte Erfassung als Prozentwerte/Gebäude (pb = percent building)
			//	print_R($this->SollRoutesPercentBuidling);
			//	$sollgrade = [];
			//	for ($i = 3; $i <= 10; $i++)
			//	{
			//		$soll = "grade$i";
			//		$varname = 'soll_';
			//		array_push($sollgrade,  $this->SollRoutesPercentBuidling[0]->$soll);
			//	};
					//$this->totalsoll = array_sum($sollgrade);
					//$this->soll_routes_data = json_encode($sollgrade);


            //}; 
        //} else {
          //  if(0 == $this->record_type) {
                // Sektor Einzelwerterfassung
				// JSON für Soll-Werte Erfassung als Einzelwerte/ Sektoren



				//$sollgrade = [];
				// foreach($gradeList as $value){
				//	$soll = "grade$value->id_grade"; 
				//	$varname = 'soll_';
				//	array_push($sollgrade,  $this->sollRoutesInd[0]->$soll);
				// }

				// $this->totalsoll = array_sum($sollgrade);
				// $this->soll_routes_data = json_encode($sollgrade);
				 //echo 'JSON-Data für die Soll-Liste in Charts';


            //} else {
              //       echo 'Sektor Prozenterfassung fehlt '; // Sektor Prozenterfassung
           //};
		//}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();
		parent::display($tpl);
	}




	/**
	 * Prepares the document
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_ROUTES_PLANNING_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}
}
