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

use Joomla\CMS\Language\Text;

// Start und Ende beziehen sich auf den ganzen Grad 3.Grade, 4.Grade usw
$grade_start = Routes_planningHelpersRoutes_planning::getFilterUiaa($this->grade_start_individually); // Wandelt den startwert um den Grad (z.B Start = 10 entspricht 3.Grad)
$grade_end = Routes_planningHelpersRoutes_planning::getFilterUiaa($this->grade_end_individually); 

// JSON für Soll-Werte
$sollgrade = [];
for ($i = $grade_start; $i <= $grade_end; $i++) {
  $soll = "gradetotal$i";
  $varname = 'soll_';
 array_push($sollgrade,  $this->sollRoutesInd[0]->$soll);
};
$soll_routes_data = json_encode($sollgrade);


// JSON für Label (Grad wird innerhalb Charts hinzugefügt)
$label_grade = [];
for ($i = $grade_start; $i <= $grade_end; $i++) {
  array_push($label_grade, $i);
}
$label_grade = json_encode($label_grade);


// JSON für IST-Werte 
$ist_routes_data = [];
for ($i = $grade_start; $i <= $grade_end; $i++) {
  $ist = "ist_gradetotal$i";
  $varname = 'ist_';
  array_push($ist_routes_data,$this->items[0]->$ist);
}
$ist_routes_data = json_encode($ist_routes_data);


?> 
<script>
Chart.helpers.merge(Chart.defaults.global.plugins.datalabels, {
  align: 'end',
  anchor: 'end',
  color: '#555',
  offset: 0,
  font: {
    size: 16,
    weight: 'bold'
  },
  
});

new Chart(document.getElementById("bar-chart-grouped"), {
  type: 'bar',
  data: {
    labels:  <?php echo $label_grade; ?>,
    datasets: [{
      label: "<?php echo Text::_('COM_ROUTES_PLANNING_SHOULD'); ?>",
      backgroundColor: "#98c920",
		  data: <?php echo $soll_routes_data; ?>
    }, 
    {
      label: "<?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?>",
      backgroundColor: "#019abc",
      data: <?php echo $ist_routes_data; ?>
    }]
  },
  // Abstand von Legend nach unten 3.Grade ...
  plugins: [{
    beforeInit: function(chart, options) {
      chart.legend.afterFit = function() {
        this.height = this.height + 20;
      };
    }
  }],
  options: {
    scales: {
      xAxes: [{
        ticks: {
          callback: function(value, index, values) {
            return  value + '.Grad';
          }
        }
      }]
    }
  }
});
</script>