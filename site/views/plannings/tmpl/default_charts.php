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
    labels:  <?php echo $this->label_grade; ?>,
    datasets: [{
      label: "<?php echo Text::_('COM_ROUTES_PLANNING_SHOULD'); ?>",
      backgroundColor: "#98c920",
		  data: <?php echo $this->soll_routes_data; ?>
    }, 
    {
      label: "<?php echo Text::_('COM_ROUTES_PLANNING_IS'); ?>",
      backgroundColor: "#019abc",
      data: <?php echo $this->ist_routes_data; ?>
    },
    {
      label: "<?php echo Text::_('Vorgemerkt'); ?>",
      backgroundColor: "#fab903",
      data: <?php echo $this->comes_out_routes_data; ?>
    }
  ]
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
    animation: {duration: 0 },
    hover: { animationDuration: 0 },
    responsiveAnimationDuration: 0 ,
    scales: {
      xAxes: [{
        ticks: {
          callback: function(value, index, values) {
            return  value + '.Grad';
          }
        }
      }],
      yAxes: [{
          ticks: {display: false}
        }]
    }
  }
});
</script>


           