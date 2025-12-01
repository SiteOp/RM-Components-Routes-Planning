<?php
/**
 * @package    Com_Routes_planning
 * @subpackage Views
 * @version    1.0.0
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$doc = Factory::getDocument();
$componentPath = Uri::root() . 'components/com_routes_planning/assets/';

// ========================================
// DataTables 2 (vanilla) + Buttons 3
// ========================================
$doc->addStyleSheet('//cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css');
$doc->addStyleSheet('//cdn.datatables.net/buttons/3.2.5/css/buttons.dataTables.min.css');

$doc->addScript('//cdn.datatables.net/2.3.4/js/dataTables.min.js');
$doc->addScript('//cdn.datatables.net/buttons/3.2.5/js/dataTables.buttons.min.js');
$doc->addScript('//cdn.datatables.net/buttons/3.2.5/js/buttons.html5.min.js');
$doc->addScript('//cdn.datatables.net/buttons/3.2.5/js/buttons.print.min.js');



// === Excel (excelHtml5) benÃ¶tigt JSZip ===
$doc->addScript('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js');

// === SheetJS für manuellen Excel-Export ===
$doc->addScript('https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js');

// CSS
$doc->addStyleSheet($componentPath . 'css/charts.css', ['version' => '1.5.0']);

// JavaScript
$doc->addScript($componentPath . 'js/datatables-init.js', ['version' => '1.5.0'], ['defer' => false]);

// Menu Parameters
$app = Factory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$itemId = $active ? $active->id : 0;
$menuparams = $menu->getParams($itemId);
?>

<div id="routes_planning">
  <?php if ($menuparams->get('show_page_heading')) : ?>
    <div class="page-header">
      <h1><?php echo $this->escape($menuparams->get('page_heading')); ?></h1>
    </div>
  <?php else : ?>
    <div class="page-header">
      <h1><?php echo $this->escape($active ? $active->title : ''); ?></h1>
    </div>
  <?php endif; ?>

  <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">
    <?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
    <?php //- Chart Section ?>
    <div class="row mt-3">
      <div class="col">
        <?php echo $this->loadTemplate('chart'); ?>
      </div>
    </div>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
    <?php echo HTMLHelper::_('form.token'); ?>
  </form>
  <?php // Einzelwerterfassung Tabelle ?>
  <?php echo $this->loadTemplate('table_einzeln'); ?>

 <?php //echo $this->loadTemplate('table_prozent'); // GebÃ¶ude Prozenterfassung ?>

  <?php //Vorgemerkte Routen ?>
  <div class="row mt-5">
    <div class="col">
      <p class="mb-3">
        <b><?php echo Text::_('COM_ROUTES_PLANNING_PLANNED_TO_REPLACE'); ?></b>
      </p>
      <?php echo $this->loadTemplate('replaceroutes'); ?>
    </div>
  </div>
</div>