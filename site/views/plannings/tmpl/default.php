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
$doc->addStyleSheet('https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.css');
$doc->addStyleSheet('https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css');

// Add Script 
$doc->addScript('node_modules/chart.js/dist/Chart.bundle.min.js');
$doc->addScript('node_modules/chartjs-plugin-datalabels/dist/chartjs-plugin-datalabels.min.js');


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
                    </div>
                </div>
            </div> 
        </div>

        <div class="table-responsive mt-5">
            <table id="compare_table" class="display table table-sm table-striped table-bordered text-center" style="width:100%"  >
            
                <thead>
                    <tr>
                        <th>Grad</th>
                        <?php for($i = 10; $i <= 36; $i++) : ?>
                                <th> <?php echo Text::_('COM_ROUTES_PLANNING_GRADE_OPTION_'.$i) ; ?></th>
                            <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Soll</td>
                        <?php foreach($this->sollRoutes[0] AS $sollRoutes) : ?>
                            <td><?php echo $sollRoutes[0]; ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <tr>
                        <td>Ist</td>
                        <?php for($i = 10; $i <= 36; $i++) : ?>
                            <?php  $ist = "ist_grade_$i"; ?>
                            <td><?php echo $this->items[0]->$ist; ?></td>
                        <?php endfor; ?>
                    </tr>
                    <tr>
                        <td>Diff</td>
                            <?php for($i = 10; $i <= 36; $i++) : ?>
                                <?php  $soll = "soll_g_$i"; ?>
                                <?php  $ist = "ist_grade_$i"; ?>
                                <?php if(($this->items[0]->$ist - $this->sollRoutes[0]->$soll) < 0) {
                                    echo '<td class="diff_minus">';
                                } 
                                elseif (($this->items[0]->$ist - $this->sollRoutes[0]->$soll) > 0) {
                                    echo '<td class="diff_plus">';
                                }
                                else {
                                    echo '<td>';
                                }; ?>
                                <?php echo ($this->items[0]->$ist - $this->sollRoutes[0]->$soll); ?></td>
                            <?php endfor; ?>
                    </tr>
                </tbody>
            </table>
        </div>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
        <?php echo HTMLHelper::_('form.token'); ?>
    </form>

    <div class="row mt-5">
        <div class="col-6">
            <p class="mb-3"><b><?php echo Text::_('COM_ROUTES_PLANNING_PLANNED_TO_REPLACE'); ?></b></p>
            <?php echo $this->loadTemplate('replaceroutes'); ?>
        </div>
    </div>
</div>



<?php // https://datatables.net/download ?>
<script src="https://cdn.datatables.net/v/bs4/dt-1.10.23/datatables.min.js" ></script>
<?php // Script für Buttons ?>
<script src="node_modules/datatables.net-buttons/js/dataTables.buttons.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" ></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js" ></script>

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

<script>

let sectornames= jQuery('#filter_sector option:selected').toArray().map(item => item.text).join();

    $(document).ready( function () {
        $('#compare_table').DataTable({
            paging: false,
            ordering:  false,
            "language": {
                "info": "",
            },
            buttons: [
            {
                extend: 'excelHtml5',
                messageTop: sectornames,
                exportOptions: { 
                    format: {
                        header: function ( data, column, row ){
                            return data.substring(data.indexOf("value")+9,data.indexOf("</option"));
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                messageTop: sectornames,
                title: "<?php echo Text::_('COM_ROUTES_PLANNING_SHOULD_IS_COMPARISON'); ?>",
                exportOptions: { 
                    format: {
                        header: function ( data, column, row ){
                            return data.substring(data.indexOf("value")+9,data.indexOf("</option"));
                        }
                    }
                }     
            }
            ]
        });
    });
</script>

<script>
$(document).ready( function () {
    $('#replaceroutes_table').DataTable({
        "language": {
             "info":        " _TOTAL_ <?php echo Text::_('COM_ROUTES_PLANNING_ROUTES'); ?>",
             "emptyTable":  "<?php echo Text::_('COM_ROUTES_PLANNING_NO_PLANNED_TO_REPLACE'); ?>",
            },
        "lengthMenu": [[5, 10, -1], [5, 10, "All"]],
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


