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

//  Params 
$params      = JComponentHelper::getParams( 'com_routes_planning' );
$holds_manufacturer   = $params['holds_manufacturer']; // Soll die Spalte Griffhersteller angezeigt werden?

?> 
<div class="table-responsive">
    <table id="replaceroutes_table" class="table table-striped table-bordered" style="width:100%"  >
        <thead>
            <tr>
                <th><?php echo Text::_('COM_ROUTES_PLANNING_SECTOR'); ?></th>
                <th><?php echo Text::_('COM_ROUTES_PLANNING_LINE'); ?></th>
                <th><?php echo Text::_('COM_ROUTES_PLANNING_ROUTES_NAME'); ?></th>
                <th><?php echo Text::_('COM_ROUTES_PLANNING_GRADE'); ?></th>
                <th><?php echo Text::_('COM_ROUTES_PLANNING_ROUTES_COLOR'); ?></th>
                <?php if (1 == $holds_manufacturer) : ?>
                    <th><?php echo Text::_('COM_ROUTES_PLANNING_HOLDS_MANUFACTURER'); ?></th> 
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($this->replaceRoutes AS $route) : ?>
                <tr>
                    <td><?php echo $this->escape($route->sector); ?></td>
                    <td><?php echo $this->escape($route->line); ?></td>
                    <td>
                        <a href="<?php echo Route::_('index.php?option=com_act&view=route&id='.(int) $route->id); ?>"><?php echo $this->escape($route->name); ?></a>
                    </td>
                   
                    <td class="text-center" data-order="<?php echo $this->escape($route->calc_grade_round); ?>"><?php echo $this->escape($route->c_grade); ?></td>
                    <td><?php echo $this->escape($route->color); ?></td>
                    <?php if (1 == $holds_manufacturer) : ?>
                        <td><?php echo Routes_planningHelpersRoutes_planning::getHoldManufacturer($this->escape($route->extend_sql)); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php 
