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

?>
    <div class="table-responsive mt-5">
        <table id="compare_table" class="display table table-sm  table-bordered text-center" style="width:100%"  >
            <thead>
                <tr><?php // Liste der Routengrade ohne Zwischengrade ?>
                    <th><?php echo Text::_('COM_ROUTES_PLANNING_GRADE'); ?></th>
                    <?php foreach($this->gradeList AS $value) : ?>
                        <th><?php echo $value->grade; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <tr><?php // Sollbestand ?>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_SHOULD'); ?></td> 
                    <?php foreach($this->gradeList as $value) : ?>
                        <?php  $grade = "grade_id$value->id_grade"; ?>
                        <td><?php echo $this->sollRoutesInd[0]->$grade; ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr><?php // Istbestand ?>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                    <?php foreach($this->gradeList as $value) : ?>
                        <?php  $ist = "ist_grade_$value->id_grade"; ?>
                        <td><?php echo $this->items[0]->$ist; ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr> <?php // Vorgemerkt zum Heruasschrauben - Comes Out ?>
                    <td class="table_border_bottom">
                        <a class="" rel="popover" 
                           data-placement="right" 
                           data-html="true" 
                           data-trigger="hover" 
                           title="" 
                           data-content="<?php echo Text::_('COM_ROUTES_PLANNING_COMES_OUT_DESC'); ?>" 
                           data-original-title="<?php echo Text::_('COM_ROUTES_PLANNING_COMES_OUT_INFO'); ?>">
                           <i class="fas fa-info-circle"></i>
                         </a>
                         <?php echo Text::_('COM_ROUTES_PLANNING_COMES_OUT_ABK'); ?>
                    </td>
                    <?php foreach($this->gradeList as $value) : ?>
                        <?php $comes_out = "comes_out_$value->id_grade"; ?>
                        <td><?php echo $this->routesComesOut[0]->$comes_out; ?></td>
                    <?php endforeach; ?>
                </tr>
                <tr class="table-light"> <?php // ToDo-Liste aus Sollbestand Minus (Ist-Bestand Minus Kommt raus) ?>
                    <td>
                      <a class="" rel="popover" 
                           data-placement="right" 
                           data-html="true" 
                           data-trigger="hover" 
                           title="" 
                           data-content="<?php echo Text::_('COM_ROUTES_PLANNING_TODO_DESC'); ?>" 
                           data-original-title="<?php echo Text::_('COM_ROUTES_PLANNING_TODO_INFO'); ?>">
                           <i class="fas fa-info-circle"></i>
                         </a>
                        <?php echo Text::_('COM_ROUTES_PLANNING_TODO'); ?>
                    </td>
                    <?php foreach($this->gradeList as $value) : ?>
                        <?php  $grade = "grade_id$value->id_grade"; ?>
                        <?php  $ist = "ist_grade_$value->id_grade"; ?>
                        <?php  $comes_out = "comes_out_$value->id_grade"; ?>
                        <?php 
                        if($this->sollRoutesInd[0]->$grade - ($this->items[0]->$ist -$this->routesComesOut[0]->$comes_out) < 0) {
                            echo '<td class="diff_minus">';
                        } 
                        elseif ($this->sollRoutesInd[0]->$grade - ($this->items[0]->$ist -$this->routesComesOut[0]->$comes_out) > 0) {
                            echo '<td class="diff_plus">';
                        }
                        else {
                            echo '<td>';
                        }; ?>
                        <?php echo ($this->sollRoutesInd[0]->$grade - ($this->items[0]->$ist -$this->routesComesOut[0]->$comes_out)); ?>
                        </td>
                        <?php endforeach; ?>
                </tr>
            </tbody>
            <tfoot>
            </tfoot>
        </table>
    </div>
