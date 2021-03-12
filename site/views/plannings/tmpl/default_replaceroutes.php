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


?> 
<div class="table-responsive">
    <table id="replaceroutes_table" class="table table-striped table-bordered" style="width:100%"  >
        <thead>
            <tr>
                <th>Sektor</th> <?php // TODO ?>
                <th>Linie</th>
                <th>Name</th>
                <th>Grad</th>
                <th>Farbe</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($this->replaceRoutes AS $routes) : ?>
                <tr>
                    <td><?php echo $routes->sector; ?></td>
                    <td><?php echo $routes->line; ?></td>
                    <td><?php echo $routes->name; ?></td>
                    <td><?php echo $routes->uiaa; ?></td>
                    <td><?php echo $routes->color; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



