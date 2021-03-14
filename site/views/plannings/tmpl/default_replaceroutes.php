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
            </tr>
        </thead>
        <tbody>
            <?php foreach($this->replaceRoutes AS $route) : ?>
                <tr>
                    <td><?php echo $route->sector; ?></td>
                    <td><?php echo $route->line; ?></td>
                    <td><?php echo $route->name; ?></td>
                    <td class="text-center"><?php echo $route->uiaa; ?></td>
                    <td><?php echo $route->color; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>