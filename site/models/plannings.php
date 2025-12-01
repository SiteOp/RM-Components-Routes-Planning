<?php
/**
 * @package    Com_Routes_planning
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;

class Routes_planningModelPlannings extends ListModel
{
    /**
     * Filter/Params in den Model-State holen
     */
    protected function populateState($ordering = null, $direction = null)
    {
        parent::populateState($ordering, $direction);

        $app    = Factory::getApplication();
        $params = $app->getParams();
        $this->setState('params', $params);

        // Filter aus der URL ?filter[building]=.. & ?filter[sector][]=..
        $filter = (array) $app->input->get('filter', [], 'array');

        $sector = isset($filter['sector']) ? (array) $filter['sector'] : [];
        ArrayHelper::toInteger($sector);
        $sector = array_values(array_unique(array_filter($sector, static function ($v) {
            return (int) $v !== 0;
        })));
        $this->setState('filter.sector', $sector);

        $building = isset($filter['building']) ? (int) $filter['building'] : 0;
        $this->setState('filter.building', $building);
    }

    /**
     * Liste braucht hier nichts (alle Zahlenreihen werden separat ermittelt).
     * Liefere bewusst ein leeres Resultset, um Overhead zu sparen.
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $q  = $db->getQuery(true)
            ->select('1')
            ->from($db->qn('#__users'));
        $q->where('1=0');
        return $q;
    }

    /**
     * SOLL (Einzelwerte je Sektor) -> summiert Objekt: { g10: int, g11: int, ... }
     */
    public function getSollRoutesInd()
    {
        $db = $this->getDbo();
        $q  = $db->getQuery(true);

        $q->select('routessoll_ind')
          ->from($db->qn('#__act_sector'))
          ->where($db->qn('state') . ' = 1');

        $filter_sector = (array) $this->state->get('filter.sector', []);
        if (!empty($filter_sector)) {
            ArrayHelper::toInteger($filter_sector);
            $q->where($db->qn('id') . ' IN (' . implode(',', $filter_sector) . ')');
        }

        $filter_building = (int) $this->state->get('filter.building', 0);
        if ($filter_building > 0) {
            $q->where($db->qn('building') . ' = ' . (int) $filter_building);
        }

        $db->setQuery($q);
        $rows = (array) $db->loadColumn();

        $totals = [];
        foreach ($rows as $json) {
            $data = json_decode($json, true);
            if (is_array($data)) {
                foreach ($data as $key => $val) {
                    if (!isset($totals[$key])) {
                        $totals[$key] = 0;
                    }
                    $totals[$key] += (int) $val;
                }
            }
        }

        return (object) $totals;
    }

    /**
     * VMG (Vorgemerkt, state = -1), robust:
     * gid = COALESCE(t.calc_grade_round, gtxt.id_grade, 0)
     * -> Rückgabe: [(object){ comes_out_{id}: int, ..., comes_out_0: int }]
     */
    public function getRoutesComesOut()
    {
        $params      = \JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table']; // z. B. act_grade_franz

        $db = $this->getDbo();

        // Basis: nur vorgemerkte Routen (state = -1)
        $qBase = $db->getQuery(true)
            ->select('COALESCE(' .
                     $db->qn('t.calc_grade_round') . ', ' .
                     $db->qn('gtxt.id_grade')      . ', 0) AS gid')
            ->from($db->qn('#__act_route', 'a'))
            ->join('LEFT', $db->qn('#__act_trigger_calc', 't') . ' ON ' . $db->qn('t.id') . ' = ' . $db->qn('a.id'))
            ->join('LEFT', $db->qn('#__' . $grade_table, 'gtxt') . ' ON ' . $db->qn('gtxt.grade') . ' = ' . $db->qn('t.calc_grade'))
            ->join('LEFT', $db->qn('#__act_line', 'l') . ' ON ' . $db->qn('l.id') . ' = ' . $db->qn('a.line'))
            ->join('LEFT', $db->qn('#__act_sector', 's') . ' ON ' . $db->qn('s.id') . ' = ' . $db->qn('l.sector'))
            ->where($db->qn('a.state') . ' = -1');

        // Filter
        $filterSector   = (array) $this->state->get('filter.sector', []);
        $filterBuilding = (int)   $this->state->get('filter.building', 0);

        if (!empty($filterSector)) {
            ArrayHelper::toInteger($filterSector);
            $qBase->where($db->qn('s.id') . ' IN (' . implode(',', $filterSector) . ')');
        }
        if ($filterBuilding > 0) {
            $qBase->where($db->qn('s.building') . ' = ' . (int) $filterBuilding);
        }

        // Gruppiert zählen
        $q = $db->getQuery(true)
            ->select($db->qn('gid'))
            ->select('COUNT(*) AS ' . $db->qn('total'))
            ->from('(' . $qBase . ') AS ' . $db->qn('x'))
            ->group($db->qn('gid'));

        $db->setQuery($q);
        $rows = (array) $db->loadAssocList();

        $byId = [];
        foreach ($rows as $r) {
            $byId[(int) $r['gid']] = (int) $r['total'];
        }

        // comes_out_{id} entsprechend Setterliste mappen
        \JLoader::import('helpers.grade', JPATH_SITE . '/components/com_act');
        $setter = \GradeHelpersGrade::getSettergradeList();
        $setter = is_array($setter) ? $setter : [];

        $result = [];
        foreach ($setter as $row) {
            $gid = (int) ($row->id_grade ?? 0);
            $result['comes_out_' . $gid] = $byId[$gid] ?? 0;
        }
        // Unbekannt
        $result['comes_out_0'] = $byId[0] ?? 0;

        return [(object) $result];
    }

    /**
     * Liste vorgemerkter Routen (für Tabellenansicht), inkl. Filter
     */
    public function getReplaceRoutes()
    {
        $params      = \JComponentHelper::getParams('com_act');
        $grade_table = $params['grade_table'];

        $db = $this->getDbo();
        $q  = $db->getQuery(true);

        $q->select([
                'r.id', 'r.name', 'l.line', 's.sector',
                'c.color', 'r.extend_sql',
                't.calc_grade_round', 'cg.grade AS c_grade'
            ])
          ->from($db->qn('#__act_route', 'r'))
          ->join('LEFT', $db->qn('#__act_trigger_calc', 't') . ' ON ' . $db->qn('t.id') . ' = ' . $db->qn('r.id'))
          ->join('LEFT', $db->qn('#__' . $grade_table, 'cg') . ' ON ' . $db->qn('cg.id_grade') . ' = ' . $db->qn('t.calc_grade_round'))
          ->join('LEFT', $db->qn('#__act_line',   'l') . ' ON ' . $db->qn('l.id') . ' = ' . $db->qn('r.line'))
          ->join('LEFT', $db->qn('#__act_sector', 's') . ' ON ' . $db->qn('s.id') . ' = ' . $db->qn('l.sector'))
          ->join('LEFT', $db->qn('#__act_color',  'c') . ' ON ' . $db->qn('c.id') . ' = ' . $db->qn('r.color'))
          ->where($db->qn('r.state') . ' = -1')
          ->order($db->qn('r.line') . ' ASC');

        $filter_sector = (array) $this->state->get('filter.sector', []);
        if (!empty($filter_sector)) {
            ArrayHelper::toInteger($filter_sector);
            $q->where($db->qn('s.id') . ' IN (' . implode(',', $filter_sector) . ')');
        }

        $filter_building = (int) $this->state->get('filter.building', 0);
        if ($filter_building > 0) {
            $q->where($db->qn('s.building') . ' = ' . (int) $filter_building);
        }

        $db->setQuery($q);
        return (array) $db->loadObjectList();
    }
}
