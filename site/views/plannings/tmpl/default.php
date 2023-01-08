<?php
/**
 * @version    CVS: 1.1.3
 * @package    Com_Act
 * @author     Richard Gebhard <gebhard@site-optimierer.de>
 * @copyright  2019 Richard Gebhard
 * @license    GNU General Public License Version 2 oder später; siehe LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use \Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

// CSS Datatables Variante Bootstrap 4
$doc = Factory::getDocument();
//$doc->addStyleSheet('node_modules/datatables.net-dt/css/jquery.dataTables.min.css');
$doc->addStyleSheet('https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css');
//$doc->addStyleSheet('node_modules/datatables.net-buttons-dt/css/buttons.dataTables.min.css');
$doc->addStyleSheet('https://cdn.datatables.net/buttons/2.3.3/css/buttons.dataTables.min.css');

// Add Script Charts
$doc->addScript('node_modules/chart.js/dist/Chart.bundle.min.js');
$doc->addScript('node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js');

// Add Script Charts Databables
//$doc->addScript("node_modules/datatables.net/js/jquery.dataTables.min.js"); 
$doc->addScript("https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"); 
//$doc->addScript("node_modules/datatables.net-buttons/js/dataTables.buttons.min.js");
$doc->addScript("https://cdn.datatables.net/buttons/2.3.3/js/dataTables.buttons.min.js");
//$doc->addScript("node_modules/jszip/dist/jszip.min.js");
$doc->addScript("https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js");
$doc->addScript("node_modules/pdfmake/build/pdfmake.min.js");
$doc->addScript("node_modules/pdfmake/build/vfs_fonts.js");
//$doc->addScript("node_modules/datatables.net-buttons/js/buttons.html5.min.js");
$doc->addScript("https://cdn.datatables.net/buttons/2.3.2/js/buttons.html5.min.js");

// Menüparameter - Titel usw
$app = Factory::getApplication();
$menu = $app->getMenu();
$active = $menu->getActive();
$itemId = $active->id;
$menuparams = $menu->getParams($itemId);

$canEdit = Factory::getUser()->authorise('core.edit', 'com_act');

?> 

<div id="routes_planning">
    <?php // Page-Header ?>
    <?php if ($menuparams['show_page_heading']) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($menuparams['page_heading']); ?></h1> 
        </div>
    <?php else : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($active->title); ?></h1>
        </div>
    <?php endif; ?>

	<form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post" name="adminForm" id="adminForm">

        <?php echo LayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>

        <div class="row mt-3">
            <div class="col">
                <div class="card">
                    <div class="card-body">
                            <canvas id="bar-chart-grouped" width="" height="60"></canvas>
                            <div class="text-center">
                           <span class="mr-2"> Soll: <?php echo $this->totalsoll; ?> </span> |
                           <span class="mr-2 ml-2 "> Ist: <?php echo ($this->items[0]->totalroutes) ; ?> </span> |
                           <span class="ml-2"> Vorgemerkt: <?php echo count($this->replaceRoutes); ?></span> 
                            </div>
                    </div>
                </div>
            </div> 
        </div>
      
        <?php if (1 == $this->record_sector_or_building) :  ?>
            <?php  if(0 == $this->record_type) { 
                        echo 'Gebäude Einzelwerterfassung fehlt'; // Gebäude Einzelweretrfassung
                    } else {
                        echo $this->loadTemplate('table_building_prozent'); // Geböude Prozenterfassung
                    }; ?>
        <?php else : ?>
            <?php  if(0 == $this->record_type) {
                        echo $this->loadTemplate('table_sektor_einzeln'); // Sektor Einzelwerterfassung
                    } else {
                        echo 'Sektor Prozenterfassung fehlt '; // Sektor Prozenterfassung
                    }; ?>
        <?php endif; ?>


        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>

    <div class="row mt-5">
        <div class="col">
            <p class="mb-3"><b><?php echo Text::_('COM_ROUTES_PLANNING_PLANNED_TO_REPLACE'); ?></b></p>
            <?php echo $this->loadTemplate('replaceroutes'); ?>
        </div>
    </div>
</div>


<script>
$.extend( true, $.fn.dataTable.defaults, {
    dom: 'lBtip',
    searching: false,
    "language": {
        "lengthMenu":   "_MENU_",
        "zeroRecords":  "Nothing found",
        "infoEmpty":     "  ",
        "infoFiltered":  "( _MAX_ )",
        "infoPostFix":   " ",
        "emptyTable":    "No Data",
        "paginate": {
            "first":     "First",
            "last":      "Last",
            "previous":  "<i class='fas fa-backward'></i>",
            "next":      "<i class='fas fa-forward'></i>"
        }
    }
});
</script>


<?php 
// Wenn Einzelerfassung für Sektoren dann kann der PDF-Button und Excel in der Vergleichstabelle erstellt werden
// Bei Prozentwerten nicht, da Datatables in der Tabelle nicht mit <td colspan""></td> funktioniert

if((0 == $this->record_type) && (2 == $this->record_sector_or_building)) {
    echo $this->loadTemplate('compare_table_js');
}

?>

<script>
$(document).ready( function () {
    $('#replaceroutes_table').DataTable({
        "language": {
             "info":        " _TOTAL_ <?php echo Text::_('COM_ROUTES_PLANNING_ROUTES'); ?>",
             "emptyTable":  "<?php echo Text::_('COM_ROUTES_PLANNING_NO_PLANNED_TO_REPLACE'); ?>",
            },
        "lengthMenu": [[10, 15, 20, -1], [10, 15, 20, "All"]],
        buttons: [
            {
                extend: 'excelHtml5',
                columns: ":visible",
                title: "<?php echo Text::_('COM_ROUTES_PLANNING_PLANNED_TO_REPLACE'); ?>"
            },
            {
                extend: 'pdfHtml5',
                orientation: 'portrait',
                title: '',
                messageTop: "<?php echo Text::_('COM_ROUTES_PLANNING_PLANNED_TO_REPLACE'); ?>"
            }
        ]
    });
} );
</script>

<?php // Das PHP und JS für das Chart ?>
<?php echo $this->loadTemplate('charts'); ?>
