<?php
/**
 * @package    Com_Routes_planning
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class Routes_planningViewPlannings extends \Joomla\CMS\MVC\View\HtmlView
{
    protected $state;
    protected $params;

    // Daten aus dem Model
    protected $sollRoutesInd;     // Objekt g{id} => int
    protected $routesComesOut;    // [(object) comes_out_{id} => int]
    protected $items;             // hier leer (Model liefert leere Liste)

    // RAW
    public $labels_raw = [];
    public $ist_raw    = [];
    public $soll_raw   = [];
    public $vmg_raw    = [];
    public $todo_raw   = [];
    public $sumIst     = 0;
    public $sumSoll    = 0;
    public $sumVmg     = 0;

    // MERGED
    public $labelsMerged = [];
    public $istM  = [];
    public $sollM = [];
    public $vmgM  = [];
    public $todoM = [];

    public function display($tpl = null)
    {
        $this->state  = $this->get('State');
        $this->params = $this->state->get('params');

        $this->filterForm     = $this->get('FilterForm');
        $this->activeFilters  = $this->get('ActiveFilters');

        // Datenquellen (Model)
        $this->items          = $this->get('Items');
        $this->sollRoutesInd  = $this->get('SollRoutesInd');
        $this->routesComesOut = $this->get('RoutesComesOut');
        $this->replaceRoutes  = $this->get('ReplaceRoutes'); // Liste von Vorgemerkte Routen

        $this->prepareGradesChartData();

        if ($errors = $this->get('Errors')) {
            throw new \Exception(implode("\n", $errors));
        }

        // --- Tabelle: alle Grade einzeln, unabhÃ¤ngig vom Chart-Merge ---
        \JLoader::import('helpers.grade', JPATH_SITE . '/components/com_act');
        $allGrades = \GradeHelpersGrade::getSettergradeList(); // [{grade,id_grade}, ...]

        // Labels fÃ¼r die Tabelle
        $this->tableLabels = [];
        foreach ((array)$allGrades as $g) {
            $this->tableLabels[] = is_object($g) ? (string)$g->grade : (string)$g['grade'];
        }

        // Helper: beliebige Quelle -> indexbasiertes Array
        $toList = static function($src): array {
            if ($src === null) return [];
            if (is_array($src)) return array_values($src);
            return array_values((array)$src); // stdClass
        };

        // Quelle wÃ¤hlen (bevorzugt RAW, sonst MERGED, sonst leer)
        $srcSoll = $this->soll_raw ?? $this->sollM ?? [];
        $srcIst  = $this->ist_raw  ?? $this->istM  ?? [];
        $srcVmg  = $this->vmg_raw  ?? $this->vmgM  ?? [];

        $listSoll = $toList($srcSoll);
        $listIst  = $toList($srcIst);
        $listVmg  = $toList($srcVmg);

        $fullLen = count($this->tableLabels);

        // Auf volle LÃ¤nge der Grade bringen (abschneiden/auffÃ¼llen)
        $padTo = static function(array $arr, int $len): array {
            $arr = array_slice($arr, 0, $len);
            if (count($arr) < $len) $arr = array_pad($arr, $len, 0);
            return array_map('intval', $arr);
        };

        $this->tableSoll = $padTo($listSoll, $fullLen);
        $this->tableIst  = $padTo($listIst,  $fullLen);
        $this->tableVmg  = $padTo($listVmg,  $fullLen);

        // ToDo fÃ¼r Tabelle (ohne Deckelung; Darstellung -1/rot macht das Template falls gewÃ¼nscht)
        $this->tableTodo = [];
        for ($i = 0; $i < $fullLen; $i++) {
            $raw = $this->tableSoll[$i] - $this->tableIst[$i] + $this->tableVmg[$i]; // Soll âˆ’ Ist + Vorg.
            $this->tableTodo[$i] = $raw;
        }

        parent::display($tpl);
    }

    /**
     * Chart-Daten strikt entlang GradeHelpersGrade::getSettergradeList()
     */
    protected function prepareGradesChartData()
    {
        // 0) Label-/ID-Liste aus Helper
        \JLoader::import('helpers.grade', JPATH_SITE . '/components/com_act');
        $setter = \GradeHelpersGrade::getSettergradeList(); // [{grade,id_grade}, ...]
        $setter = is_array($setter) ? $setter : [];

        $this->labels_raw = [];
        $idList = [];
        foreach ($setter as $row) {
            $this->labels_raw[] = (string) ($row->grade ?? '');
            $idList[]           = (int)    ($row->id_grade ?? 0);
        }
        // 0 (Unbekannt) anhÃ¤ngen
        $this->labels_raw[] = '0';
        $idList[] = 0;

        // 1) IST (robust per DB, ID oder Text), Unknown separat
        $params     = \JComponentHelper::getParams('com_act');
        $gradeTable = $params['grade_table'];

        $db = Factory::getDbo();

        $q1 = $db->getQuery(true)
            ->select($db->qn('g.id_grade', 'id_grade'))
            ->select('COUNT(DISTINCT ' . $db->qn('a.id') . ') AS ' . $db->qn('totalroutes'))
            ->from($db->qn('#__' . $gradeTable, 'g'))
            ->join(
                'LEFT',
                $db->qn('#__act_trigger_calc', 't') . ' ON (' .
                $db->qn('t.calc_grade_round') . ' = ' . $db->qn('g.id_grade') .
                ' OR ' . $db->qn('t.calc_grade') . ' = ' . $db->qn('g.grade') . ')'
            )
            ->join(
                'LEFT',
                $db->qn('#__act_route', 'a') . ' ON ' .
                $db->qn('a.id') . ' = ' . $db->qn('t.id') .
                ' AND ' . $db->qn('a.state') . ' IN (1,-1)'
            )
            ->join('LEFT', $db->qn('#__act_line', 'l') . ' ON ' . $db->qn('l.id') . ' = ' . $db->qn('a.line'))
            ->join('LEFT', $db->qn('#__act_sector', 's') . ' ON ' . $db->qn('s.id') . ' = ' . $db->qn('l.sector'))
            ->group($db->qn('g.id_grade'));

        $filterSector   = (array) ($this->state->get('filter.sector') ?? []);
        $filterBuilding = (int)   ($this->state->get('filter.building') ?? 0);
        if (!empty($filterSector)) {
            \Joomla\Utilities\ArrayHelper::toInteger($filterSector);
            $q1->where($db->qn('s.id') . ' IN (' . implode(',', $filterSector) . ')');
        }
        if ($filterBuilding > 0) {
            $q1->where($db->qn('s.building') . ' = ' . (int) $filterBuilding);
        }

        $db->setQuery($q1);
        $rows1 = (array) $db->loadAssocList();
        $byIst = [];
        foreach ($rows1 as $r) {
            $byIst[(int) $r['id_grade']] = (int) $r['totalroutes'];
        }

        $q2 = $db->getQuery(true)
            ->select('COUNT(DISTINCT ' . $db->qn('a2.id') . ') AS ' . $db->qn('cnt'))
            ->from($db->qn('#__act_route', 'a2'))
            ->join('INNER', $db->qn('#__act_trigger_calc', 't2') . ' ON ' . $db->qn('t2.id') . ' = ' . $db->qn('a2.id'))
            ->join(
                'LEFT',
                $db->qn('#__' . $gradeTable, 'g2') . ' ON (' .
                $db->qn('t2.calc_grade_round') . ' = ' . $db->qn('g2.id_grade') .
                ' OR ' . $db->qn('t2.calc_grade') . ' = ' . $db->qn('g2.grade') . ')'
            )
            ->join('LEFT', $db->qn('#__act_line', 'l2') . ' ON ' . $db->qn('l2.id') . ' = ' . $db->qn('a2.line'))
            ->join('LEFT', $db->qn('#__act_sector', 's2') . ' ON ' . $db->qn('s2.id') . ' = ' . $db->qn('l2.sector'))
            ->where($db->qn('a2.state') . ' IN (1,-1)')
            ->where($db->qn('g2.id_grade') . ' IS NULL');

        if (!empty($filterSector)) {
            $q2->where($db->qn('s2.id') . ' IN (' . implode(',', $filterSector) . ')');
        }
        if ($filterBuilding > 0) {
            $q2->where($db->qn('s2.building') . ' = ' . (int) $filterBuilding);
        }

        $db->setQuery($q2);
        $byIst[0] = (int) $db->loadResult();

        $this->ist_raw = [];
        foreach ($idList as $gid) {
            $this->ist_raw[] = $byIst[$gid] ?? 0;
        }

        // 2) SOLL (Objekt g{id} => int)
        if (!is_object($this->sollRoutesInd) || empty((array) $this->sollRoutesInd)) {
            $this->sollRoutesInd = $this->get('SollRoutesInd');
        }
        $sollObj = is_object($this->sollRoutesInd) ? $this->sollRoutesInd : (object) [];
        $this->soll_raw = [];
        foreach ($idList as $gid) {
            $this->soll_raw[] = (int) ($sollObj->{'g' . $gid} ?? 0);
        }

        // 3) VMG (comes_out_{id})
        if (empty($this->routesComesOut) || !isset($this->routesComesOut[0])) {
            $this->routesComesOut = $this->get('RoutesComesOut');
        }
        $vmgArr = isset($this->routesComesOut[0]) ? (array) $this->routesComesOut[0] : [];
        $this->vmg_raw = [];
        foreach ($idList as $gid) {
            $this->vmg_raw[] = (int) ($vmgArr['comes_out_' . $gid] ?? 0);
        }

        // 4) Summen & ToDo  (ToDo jetzt ohne Clamping!)
        $this->sumIst  = array_sum($this->ist_raw);
        $this->sumSoll = array_sum($this->soll_raw);
        $this->sumVmg  = array_sum($this->vmg_raw);

        $this->todo_raw = [];
        foreach ($this->soll_raw as $i => $s) {
            $t = $this->ist_raw[$i] ?? 0;
            $v = $this->vmg_raw[$i] ?? 0;
            $this->todo_raw[$i] = ($s - $t + $v); //  Soll âˆ’ Ist + Vorgemerkt
        }


        // ===================================================================
        // MERGE-SCHEMA: Lade dynamisches Schema aus Datenbank
        // ===================================================================
        // Die getMergeScheme() Funktion liest aus der Grade-Tabelle:
        // - alle Grades mit routes_planning=1 und merge_group IS NOT NULL
        // - gruppiert nach merge_group
        // Ergebnis: ["5b/5b+" => ["5b", "5b+"], "6a/6a+" => ["6a", "6a+"], ...]
        \JLoader::import('helpers.grade', JPATH_SITE . '/components/com_act');
        $mergeScheme = \GradeHelpersGrade::getMergeScheme();

        // Fallback fuer leeres Schema oder wenn getMergeScheme() fehlschlaegt
        if (empty($mergeScheme)) {
            $mergeScheme = [];
        }

        // Index fuer schnellen Lookup: label => position in labels_raw
        $rawIndex = array_flip(array_values($this->labels_raw));

        // ===================================================================
        // EINZELNE GRADES: Ergaenze Grades ohne merge_group zum Schema
        // ===================================================================
        // Alle RAW-Labels, die nicht in einer merge_group sind, 
        // werden als 1:1 Mapping hinzugefuegt (z.B. "4a" => ["4a"])
        $covered = [];
        foreach ($mergeScheme as $mergeLabel => $sourceLabels) {
            foreach ($sourceLabels as $src) { 
                $covered[$src] = true; 
            }
        }
        
        foreach ($this->labels_raw as $label) {
            if (!isset($covered[$label])) {
                $mergeScheme[$label] = [$label];
            }
        }

        // ===================================================================
        // SORTIERUNG: Nach Schwierigkeit (Position in labels_raw)
        // ===================================================================
        // Das Merge-Schema wird nach der niedrigsten Position der Source-Labels
        // sortiert, damit die Merge-Ansicht die Grades in aufsteigender
        // Schwierigkeit zeigt (wie in der RAW-Ansicht)
        $schemeOrder = [];
        foreach ($mergeScheme as $mergeLabel => $sourceLabels) {
            $minPos = PHP_INT_MAX;
            foreach ($sourceLabels as $src) {
                if (isset($rawIndex[$src])) {
                    $minPos = min($minPos, $rawIndex[$src]);
                }
            }
            // Nur Merge-Labels aufnehmen, deren Source-Labels existieren
            if ($minPos < PHP_INT_MAX) {
                $schemeOrder[$mergeLabel] = $minPos;
            }
        }
        
        // Sortiere nach Position (aufsteigend)
        asort($schemeOrder);

        // ===================================================================
        // MERGED-DATEN: Aggregiere Werte nach Schema
        // ===================================================================
        $this->labelsMerged = [];
        $this->sollM = [];
        $this->istM  = [];
        $this->vmgM  = [];
        $this->todoM = [];

        // Helper-Funktion: Summiere Werte fuer alle Source-Labels einer Gruppe
        $sumSourceLabels = function (array $sourceLabels, array $rawData) use ($rawIndex) {
            $sum = 0;
            foreach ($sourceLabels as $srcLabel) {
                if (isset($rawIndex[$srcLabel])) {
                    $idx = $rawIndex[$srcLabel];
                    $sum += (int) ($rawData[$idx] ?? 0);
                }
            }
            return $sum;
        };

        // Durchlaufe sortiertes Schema und aggregiere Daten
        foreach ($schemeOrder as $mergeLabel => $position) {
            $sourceLabels = $mergeScheme[$mergeLabel];
            
            $this->labelsMerged[] = $mergeLabel;
            
            $sollSum = $sumSourceLabels($sourceLabels, $this->soll_raw);
            $istSum  = $sumSourceLabels($sourceLabels, $this->ist_raw);
            $vmgSum  = $sumSourceLabels($sourceLabels, $this->vmg_raw);
            
            $this->sollM[] = $sollSum;
            $this->istM[]  = $istSum;
            $this->vmgM[]  = $vmgSum;
            $this->todoM[] = ($sollSum - $istSum + $vmgSum); // Soll - Ist + Vorgemerkt (ohne Clamping)
        }

        // --- Aliase fÃ¼r Templates, damit Tabelle exakt die Chart-Gruppierung nutzt ---
        $this->labelsM = $this->labelsMerged;   // Tabellen-Labels = Chart-Merged-Labels
        $this->sollM   = array_values($this->sollM);
        $this->istM    = array_values($this->istM);
        $this->vmgM    = array_values($this->vmgM);
        // $this->todoM ist bereits korrekt (ohne Clamping)
    }

    /**
     * KompatibilitÃ¤ts-Helfer â€“ erlaubt $this->getState('filter.building') im Template
     */
    public function getState($key)
    {
        return isset($this->state->$key) ? $this->state->$key : null;
    }
}