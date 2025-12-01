<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

$doc = Factory::getDocument();

// Core-JS
JHtml::_('behavior.core');

// Chart.js (v4)
$doc->addScript(
    'https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js',
    ['version' => 'auto'],
    ['defer' => true]
);

// Zahlen/Prozent vorbereiten
$sumSoll = (int) ($this->sumSoll ?? 0);
$sumIst  = (int) ($this->sumIst  ?? 0);
$sumVmg  = (int) ($this->sumVmg  ?? 0);

$percent = function(int $value, int $base): float {
    if ($base <= 0) return 0.0;
    return ($value / $base) * 100.0;
};
$pctIst = max(0.0, min(100.0, $percent($sumIst, $sumSoll)));
$pctVmg = max(0.0, min(100.0, $percent($sumVmg, $sumSoll)));

$fmtPct = fn(float $p) => number_format($p, 1, '.', '');
$fmtNum = fn($v) => number_format((int)$v, 0, ',', '.');

$erfuellung = ($sumSoll > 0) ? (int) round(($sumIst / $sumSoll) * 100) : 0;
$diff = $sumIst - $sumSoll;

// ScriptOptions für das App-JS
$payload = [
  // RAW
  'labels_raw' => $this->labels_raw,
  'soll_raw'   => $this->soll_raw,
  'ist_raw'    => $this->ist_raw,
  'vmg_raw'    => $this->vmg_raw,
  'todo_raw'   => $this->todo_raw,

  // MERGED (vom View korrekt gebaut)
  'labels' => $this->labelsMerged,
  'soll'   => $this->sollM,
  'ist'    => $this->istM,
  'vmg'    => $this->vmgM,
  'todo'   => $this->todoM,

  // Summen
  'sumSoll' => $sumSoll,
  'sumIst'  => $sumIst,
  'sumVmg'  => $sumVmg,
];
$doc->addScriptOptions('gradesData', $payload);

// grades-app.js laden
$componentPath = Uri::root() . 'components/com_routes_planning/assets/';
$doc->addScript(
    $componentPath . 'js/grades-app.js',
    ['version' => 'auto'],
    ['defer' => true]
);
?>

<section class="summary row">
  <div class="col-12 col-md-4 d-flex">
    <div class="card flex-fill">
      <div class="card-body d-flex flex-column">
        <h3>Soll gesamt</h3>
        <div class="info"><div class="big" id="sumSoll"><?= $fmtNum($sumSoll) ?></div></div>
         <div class="mt-auto">
          <div class="progress">
            <div id="barSoll" class="progress-bar progress-bar-striped bg-success" role="progressbar"
                style="width: 100%;"
                aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
          </div>
          </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4 d-flex">
    <div class="card flex-fill">
      <div class="card-body d-flex flex-column">
        <h3>Ist gesamt</h3>
        <div class="info">
          <div class="big" id="sumIst"><?= $fmtNum($sumIst) ?></div>
          <p class="meta" id="diffText">Differenz: <?= ($diff >= 0 ? '+' : '') . $fmtNum($diff) ?></p>
        </div>
        <div class="mt-auto">
          <div class="progress">
            <div id="barIst" class="progress-bar progress-bar-striped"
                role="progressbar"
                style="width: <?= $fmtPct($pctIst) ?>%; background-color:#2da7ff;"
                aria-valuenow="<?= (int)round($pctIst) ?>" 
                aria-valuemin="0" 
                aria-valuemax="100">
              <span id="fulfillmentPercent" style="font-weight:bold; color:#fff;"><?= $erfuellung ?>%</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-4 d-flex">
    <div class="card flex-fill">
      <div class="card-body d-flex flex-column">
        <h3>Vorgemerkt gesamt</h3>
        <div class="info"><div class="big" id="sumVmg"><?= $fmtNum($sumVmg) ?></div></div>
        <div class="mt-auto">
          <div class="progress">
            <div id="barVmg" class="progress-bar progress-bar-striped"
                role="progressbar"
                style="width: <?= $fmtPct($pctVmg) ?>%; background-color:#ffa42b;"
                aria-valuenow="<?= (int)round($pctVmg) ?>" 
                aria-valuemin="0" 
                aria-valuemax="100"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="my-3 d-flex mt-5">
  <button class="btn btn-outline-secondary mr-2" id="btnMergePlus" aria-pressed="false">Varianten zusammenfassen</button>
  <button class="btn btn-outline-secondary" id="btnToggleTodo" aria-pressed="false">ToDo anzeigen</button>
</div>

<div class="chart-outer card">
  <div class="chart-inner">
    <canvas id="gradesChart"></canvas>
  </div>
</div>
