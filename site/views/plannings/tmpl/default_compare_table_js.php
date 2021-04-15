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
let sectornames= jQuery('#filter_sector option:selected').toArray().map(item => item.text).join();
    $(document).ready( function () {
        $('#compare_table').DataTable({
            paging: false,
            ordering:  false,
            "language": {
                "info": "",
            },
            buttons: [
            {
                extend: 'excelHtml5',
                messageTop: sectornames,
                exportOptions: { 
                    format: {
                        header: function ( data, column, row ){
                            return data.substring(data.indexOf("value")+9,data.indexOf("</option"));
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                orientation: 'landscape',
                messageTop: sectornames,
                title: "<?php echo Text::_('COM_ROUTES_PLANNING_SHOULD_IS_COMPARISON'); ?>",
                exportOptions: { 
                    format: {
                        header: function ( data, column, row ){
                            return data.substring(data.indexOf("value")+9,data.indexOf("</option"));
                        }
                    }
                }     
            }
            ]
        });
    });
    </script>