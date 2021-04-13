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
$gs = $this->grade_start_percent;
$ge = $this->grade_end_percent;

$ist_total_grade = json_decode($this->ist_routes_data, true);

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
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <?php  $ist = "ist_grade_$i"; ?>
                        <td><?php echo $this->items[0]->$ist; ?></td>
                    <?php endfor; ?>
                </tr>
                <tr>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                  <?php $index = 0; ?>
                    <?php for ($i= $gs; $i <=$ge; $i++) : ?>
                        <td colspan= 
                            <?php
                            // Wenn nicht 3 oder 12 Grad muss Tabellenzelle über 3 zellen gehen
                            if ($i == 3) {
                                echo "'2'";
                            } elseif ($i == 12) {
                                echo "'1'";
                            } else {
                                echo "'3'";
                            }
                            ?>
                        >
                            <?php echo $ist_total_grade[$index]; ?>
                        </td>
                        <?php ++$index; ?>
                    <?php endfor; ?>
                    <td></td>
                </tr>
                <tr>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_SHOULD'); ?></td>
                    <?php for ($i = $gs; $i <= $ge; $i++) : ?> 
                        <?php  $grade = "grade$i"; ?>
                        <td colspan= 
                            <?php 
                            if ($i == 3) {
                                echo "'2'";
                            } elseif ($i == 12) {
                                echo "'1'";
                            } else {
                                echo "'3'";
                            }
                            ?>
                        >
                        <?php echo $this->SollRoutesPercentBuidling[0]->$grade; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
               
                <tr>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_DIFF'); ?></td>
                    <?php $index = '0'; ?>
                   <?php for ($i= $gs; $i <=$ge; $i++) : ?>
                    <td colspan= 
                            <?php
                            if ($i == 3) {
                                echo "'2'";
                            } elseif ($i == 12) {
                                echo "'1'";
                            } else {
                                echo "'3'";
                            }
                            ?>
                             <?php 
                            if(($this->SollRoutesPercentBuidling[0]->$grade -$ist_total_grade[$index]) < 0) {
                                echo 'class="diff_minus"';
                            } 
                            elseif (($this->SollRoutesPercentBuidling[0]->$grade -$ist_total_grade[$index]) > 0) {
                                echo 'class="diff_plus"';
                            }
                            else {
                                echo '';
                            }; ?>
                        >
                        <?php  $grade = "grade$i"; ?>
                        <?php echo ($this->SollRoutesPercentBuidling[0]->$grade -$ist_total_grade[$index]); ?>
                        <?php ++$index; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            </tbody>
        </table>
    </div>

