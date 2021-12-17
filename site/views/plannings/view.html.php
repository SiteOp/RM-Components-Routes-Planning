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

		$this->state = $this->get('State');

		$this->items   = $this->get('Items'); // Ist-Bestand Status 1 + -1 
		$this->routesComesOut = $this->get('RoutesComesOut');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		$this->replaceRoutes = $this->get('ReplaceRoutes'); // Liste von Vorgemerkte Routen (Name, Linie, Sektor....)
		$this->sollRoutesInd = $this->get('SollRoutesInd'); // Individuell = Einzelwert
		$this->SollRoutesPercentBuidling = $this->get('SollRoutesPercentBuidling');

		// Params
		$this->params = $this->state->get('params');
		$this->record_type = $this->params['record_type'];
		$this->record_sector_or_building = $this->params['record_sector_or_building']; // Sollen die Sollwerte im Sektor oder Gebäude erfasst werden? 1=Gebäude 2=Sektor
		$this->grade_start_individually = $this->params['grade_start_individually'];   // Einzelwerte - Niedrigster Schwierigkeitsgrad
		$this->grade_end_individually = $this->params['grade_end_individually'];       // Einzelwerte - Höchster  Schwierigkeitsgrad
		$this->grade_start_percent = $this->params['grade_start_percent'];       // Prozentwerte - Höchster  Schwierigkeitsgrad
		$this->grade_end_percent = $this->params['grade_end_percent'];       // Prozentwerte - Höchster  Schwierigkeitsgrad

		$grade_start = Routes_planningHelpersRoutes_planning::getFilterUiaa($this->grade_start_individually); // Wandelt den startwert um den Grad (z.B Start = 10 entspricht 3.Grad)
		$grade_end = Routes_planningHelpersRoutes_planning::getFilterUiaa($this->grade_end_individually); 

		// CHARTS
		$undefined = $this->items[0]->ist_undefined; // Routen ohne Routengrad z. B Speedrouten 

		// JSON für IST-Werte (Status 1 und -1)
		$ist_routes_data = [];
		for ($i = $grade_start; $i <= $grade_end; $i++) {
			$ist = "ist_gradetotal$i";
			$varname = 'ist_';
			array_push($ist_routes_data,$this->items[0]->$ist);
		}

		if(!empty($undefined)) {
			array_push($ist_routes_data, $undefined );
		};

		$this->ist_routes_data = json_encode($ist_routes_data);



		// JSON für Vorgemerkte Routen
		$comes_out_routes_data = [];
		for ($i = $grade_start; $i <= $grade_end; $i++) {
			$comes_out = "comes_out_gradetotal$i";
			$varname = 'comes_out_';
			array_push($comes_out_routes_data,$this->routesComesOut[0]->$comes_out);
		}

		if(!empty($undefined)) {
			array_push($comes_out_routes_data, $undefined );
		};

		$this->comes_out_routes_data = json_encode($comes_out_routes_data);


		// JSON für Label (Grad wird innerhalb Charts hinzugefügt)
		$label_grade = [];
			for ($i = $grade_start; $i <= $grade_end; $i++) {
			array_push($label_grade, $i);
		}
		if(!empty($undefined)) {
			array_push($label_grade, '?' );
		};
		$this->label_grade = json_encode($label_grade);



		// Sollwerte abhängig von Auswahl Gebäude / Sektor -- Einzelwerte / Prozentwerte

		if (1 == $this->record_sector_or_building) {
            if(0 == $this->record_type) { 
                 echo 'Gebäude Einzelwerterfassung fehlt'; // Gebäude Einzelwerterfassung
            } else {
                // Geböude Prozenterfassung
				// JSON für Soll-Werte Erfassung als Prozentwerte/Gebäude (pb = percent building)
				$sollgrade = [];
				for ($i = $this->grade_start_percent; $i <= $this->grade_end_percent; $i++)
				{
					$soll = "grade$i";
					$varname = 'soll_';
					array_push($sollgrade,  $this->SollRoutesPercentBuidling[0]->$soll);
				};
					$this->totalsoll = array_sum($sollgrade);
					$this->soll_routes_data = json_encode($sollgrade);
            }; 
        } else {
            if(0 == $this->record_type) {
                // Sektor Einzelwerterfassung
				// JSON für Soll-Werte Erfassung als Einzelwerte/ Sektoren
				$sollgrade = [];
				for ($i = $grade_start; $i <= $grade_end; $i++) 
				{
					$soll = "gradetotal$i";
					$varname = 'soll_';
					array_push($sollgrade,  $this->sollRoutesInd[0]->$soll);
				};
				$this->totalsoll = array_sum($sollgrade);
				$this->soll_routes_data = json_encode($sollgrade);

            } else {
                     echo 'Sektor Prozenterfassung fehlt '; // Sektor Prozenterfassung
            };
		}

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
