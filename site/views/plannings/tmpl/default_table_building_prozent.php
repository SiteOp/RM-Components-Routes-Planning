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


$ist_total_grade = json_decode($this->ist_routes_data, true);

// Liste der zusammengefassten Routengrade aus [3, 3+, 4-...] wird [3,4]
foreach($this->gradeList as $value) {
    $gradeListShort[] = intval($value->grade);
}
$gradeListShort = array_unique($gradeListShort);

?> 

    <div class="table-responsive mt-5">
        <table id="compare_table" class="display table table-sm  table-bordered text-center" style="width:100%"  >
            <thead>
                <tr>
                    <th><?php echo Text::_('COM_ROUTES_PLANNING_GRADE'); ?></th>
                     <?php foreach($this->gradeList AS $value) : ?>
                        <th><?php echo $value->grade; ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>

                <tr> <?php // Sollbestand Erfassung innerhalb der Gebäude als Prozentwerte ?>
                     <td><?php echo Text::_('COM_ROUTES_PLANNING_SHOULD'); ?></td>
                    <?php foreach($gradeListShort as $grade) : ?>
                        <td colspan= 
                            <?php 
                            if ($grade == 3) {
                                echo "'2'";
                            } elseif ($grade == 12) {
                                echo "'1'";
                            } else {
                                echo "'3'";
                            }
                            ?>
                        >
                        <?php  $grade = "grade$grade"; ?>
                        <?php echo $this->SollRoutesPercentBuidling[0]->$grade; ?>
                    <?php endforeach; ?>
                </tr>

                <tr> <?php // Ist-Bestand als Gesamtwerte/Grade  ?>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                    <?php $i=0; ?>
                    <?php foreach($gradeListShort as $grade) : ?>
                        <td colspan= 
                            <?php 
                            if ($grade == 3) {
                                echo "'2'";
                            } elseif ($grade == 12) {
                                echo "'1'";
                            } else {
                                echo "'3'";
                            }
                            ?>
                        >
                        <?php echo $this->routesIstGradeTotal[0][$i]; ?>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tr>

                <tr><?php // Ist-Bestand als Einzelwerte/Grade ?>
                <td><?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?></td>
                    <?php foreach($this->gradeList as $value) : ?>
                        <?php  $ist = "ist_grade_$value->id_grade"; ?>
                        <td><?php echo $this->items[0]->$ist; ?></td>
                    <?php endforeach; ?>
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
                    <?php $i=0; ?>
                    <?php foreach($gradeListShort as $grade) : ?>
                        <td colspan= 
                            <?php 
                            if ($grade == 3) {
                                echo "'2'";
                            } elseif ($grade == 12) {
                                echo "'1'";
                            } else {
                                echo "'3'";
                            }
                            ?>
                        >
                        <?php $comes_out = "comes_out_gradetotal$i"; ?>
                        <?php echo $this->routesComesOutTotal[0][$i]; ?>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tr>

                <tr> <?php // Vorgemerkt zum Heruasschrauben - Comes Out || Einzelwerte/Grade ?>
                    <td><?php echo Text::_('COM_ROUTES_PLANNING_COMES_OUT_ABK'); ?></td>
                    <?php foreach($this->gradeList as $value) : ?>
                        <?php $comes_out = "comes_out_$value->id_grade"; ?>
                        <td><?php echo $this->routesComesOut[0]->$comes_out; ?></td>
                    <?php endforeach; ?>
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
                    <?php $i=0; ?>
                    <?php foreach($gradeListShort as $grade) : ?>
                        <td colspan= 
                            <?php 
                            if ($grade == 3) {
                                echo "'2'";
                            } elseif ($grade == 12) {
                                echo "'1'";
                            } else {
                                echo "'3'";
                            }
                            ?>
                         <?php  $grade = "grade$grade"; ?>
                         <?php $comes_out = "comes_out_gradetotal$i"; ?>
                         <?php $wert = ($this->SollRoutesPercentBuidling[0]->$grade - ($this->routesIstGradeTotal[0][$i] - $this->routesComesOutTotal[0][$i])) ; ?> 

                        <?php 
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
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tr>
            </tbody>
        </table>
    </div>