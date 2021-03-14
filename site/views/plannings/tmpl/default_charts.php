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

// Erstelle Label für Grade 3.Grad usw (Grad wird innerhalb Charts hinzugefügt)
$label_grade = json_encode([3,4,5,6,7,8,9,10,11,12]);

// Erstelle Variablen $ist_10, $ist_11, usw => Istwert
for($i = 10; $i <= 36; $i++) {
  $ist = "ist_grade_$i";
  $varname = 'ist_';
  ${$varname.$i} = $this->items[0]->$ist;
}
// Estelle Gesamtwerte (3,4,5,6) usw aus den Einzelwerten (7, 7-, 7+)
// Gespeichert als JSON [3,4,5,6,7]
$ist_routes_data = json_encode([
  ($ist_grade_3  = ($ist_10 + $ist_11)),               // 3
  ($ist_grade_4  = ($ist_12 + $ist_13 + $ist_14)),     // 4
  ($ist_grade_5  = ($ist_15 + $ist_15 + $ist_17 )),	   // 5
  ($ist_grade_6  = ($ist_18 + $ist_19 + $ist_20 )),	   // 6
  ($ist_grade_7  = ($ist_21 + $ist_22 + $ist_23 )),	   // 7
  ($ist_grade_8  = ($ist_24 + $ist_25 + $ist_26 )),	   // 8
  ($ist_grade_9  = ($ist_27 + $ist_28 + $ist_29 )),	   // 9
  ($ist_grade_10 = ($ist_30 + $ist_31 + $ist_32 )),	   // 10
  ($ist_grade_11 = ($ist_33 + $ist_34 + $ist_35 )),	   // 11
  ($ist_grade_12 = ($ist_36)),	                       // 12
]);	

// Erstelle Variablen $soll_10, $soll_11 usw => Sollwert
for($i = 10; $i <= 36; $i++) {
  $soll = "soll_grade_$i";
  $varname = 'soll_';
  ${$varname.$i} = $this->items[0]->$soll;
}

// Estelle Gesamtwerte (3,4,5,6) usw aus den Einzelwerten (7, 7-, 7+)
// Gespeichert als JSON [3,4,5,6,7]
$soll_routes_data = json_encode([
  ($soll_grade_3  = ($soll_10 + $soll_11)),             // 3
  ($soll_grade_4  = ($soll_12 + $soll_13 + $soll_14)),  // 4
  ($soll_grade_5  = ($soll_15 + $soll_15 + $soll_17 )),	// 5
  ($soll_grade_6  = ($soll_18 + $soll_19 + $soll_20 )),	// 6
  ($soll_grade_7  = ($soll_21 + $soll_22 + $soll_23 )),	// 7
  ($soll_grade_8  = ($soll_24 + $soll_25 + $soll_26 )),	// 8
  ($soll_grade_9  = ($soll_27 + $soll_28 + $soll_29 )),	// 9
  ($soll_grade_10 = ($soll_30 + $soll_31 + $soll_32 )),	// 10
  ($soll_grade_11 = ($soll_33 + $soll_34 + $soll_35 )),	// 11
  ($soll_grade_12 = ($soll_36)),	                      // 12
]);

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