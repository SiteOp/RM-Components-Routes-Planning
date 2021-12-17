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
        <table id="compare_table" class="display table table-sm  table-bordered text-center" style="width:100%"  >
            <thead>
                <tr>
                    <th><?php echo Text::_('COM_ROUTES_PLANNING_GRADE'); ?></th>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <th> <?php echo Text::_('COM_ROUTES_PLANNING_GRADE_OPTION_'.$i) ; ?></th>
                    <?php endfor; ?>
                </tr>
            </thead>
            <tbody>

                <tr> <?php // Sollbestand Erfassung innerhalb der Gebäude als Prozentwerte ?>
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

                <tr> <?php // Ist-Bestand als Gesamtwerte/Grade  ?>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                    <?php for ($i = $gs; $i <= $ge; $i++) : ?> 
                        <?php  $grade = "ist_gradetotal$i"; ?>
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
                        <?php echo $this->items[0]->$grade; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
                
                <tr><?php // Ist-Bestand als Einzelwerte/Grade ?>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <?php  $ist = "ist_grade_$i"; ?>
                        <td>
                            <?php echo $this->items[0]->$ist; ?>
                        </td>
                    <?php endfor; ?>
                </tr>


                 <tr> <?php // Vorgemerkt zum Heruasschrauben - Comes Out || Gesamtwerte/Grade ?>
                    <td>
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
                    <?php for ($i = $gs; $i <= $ge; $i++) : ?> 
                        <?php $comes_out = "comes_out_gradetotal$i"; ?>
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
                        <?php echo $this->routesComesOut[0]->$comes_out; ?>
                        </td>
                    <?php endfor; ?>
                </tr>

                <tr> <?php // Vorgemerkt zum Heruasschrauben - Comes Out || Einzelwerte/Grade ?>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_COMES_OUT_ABK'); ?></td>
                    <?php for($i = $grade_start; $i <= $grade_end; $i++) : ?>
                        <?php $comes_out = "comes_out_grade_$i"; ?>
                        <td>
                        <?php echo $this->routesComesOut[0]->$comes_out; ?>
                        </td>
                    <?php endfor; ?>
                </tr>

                <tr class="table-light"> <?php // ToDo-Liste ; ?>
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

                        <?php  $grade = "grade$i"; ?>
                        <?php  $gradetotal = "ist_gradetotal$i"; ?>
                        <?php $comes_out = "comes_out_gradetotal$i"; ?>
                        <?php $wert = ($this->SollRoutesPercentBuidling[0]->$grade - ($this->items[0]->$gradetotal - $this->routesComesOut[0]->$comes_out)) ; 
                            if($wert > 0) {
                                echo 'class="diff_plus"';
                            } 
                            elseif ($wert < 0) {
                                echo 'class="diff_minus"';
                            }
                            else {
                                echo '';
                            }; ?>
                        >
                        <?php echo $wert; ?>
                        <?php ++$index; ?>
                        </td>
                    <?php endfor; ?>
                </tr>
            </tbody>
        </table>
    </div>
