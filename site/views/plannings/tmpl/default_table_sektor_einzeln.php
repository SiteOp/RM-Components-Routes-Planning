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
use Joomla\CMS\HTML\HTMLHelper;

$grade_start = $this->grade_start_individually; 
$grade_end = $this->grade_end_individually;

?> 
    <div class="table-responsive mt-5">
        <table id="compare_table" class="display table table-sm table-striped table-bordered text-center" style="width:100%"  >
            <thead>
                <tr>
                    <th><?php echo Text::_('COM_ROUTES_PLANNING_GRADE'); ?></th>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <th> <?php echo Text::_('COM_ROUTES_PLANNING_GRADE_OPTION_'.$i) ; ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_SHOULD'); ?></td>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <?php  $grade = "grade$i"; ?>
                        <td><?php echo $this->sollRoutesInd[0]->$grade; ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <?php  $ist = "ist_grade_$i"; ?>
                        <td><?php echo $this->items[0]->$ist; ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_DIFF'); ?></td>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <?php  $grade = "grade$i"; ?>
                        <?php  $ist = "ist_grade_$i"; ?>
                        <?php 
                        if(($this->items[0]->$ist - $this->sollRoutesInd[0]->$grade) < 0) {
                            echo '<td class="diff_minus">';
                        } 
                        elseif (($this->items[0]->$ist - $this->sollRoutesInd[0]->$grade) > 0) {
                            echo '<td class="diff_plus">';
                        }
                        else {
                            echo '<td>';
                        }; ?>
                        <?php echo ($this->items[0]->$ist - $this->sollRoutesInd[0]->$grade); ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            </tbody>
        </table>
    </div>
