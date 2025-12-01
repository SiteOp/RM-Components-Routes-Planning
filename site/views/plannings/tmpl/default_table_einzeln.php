<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

// JavaScript bekommt beide Datensätze und rendert die Tabelle dynamisch
?>

<div class="table-responsive mt-5">
  <!-- DataTables-Style Buttons -->
  <div class="dt-buttons mb-2">
    <button class="dt-button buttons-copy buttons-html5" type="button" tabindex="0" onclick="copyTableToClipboard()">
      <span>Kopieren</span>
    </button>
    <button class="dt-button buttons-csv buttons-html5" type="button" tabindex="0" onclick="exportTableToCSV()">
      <span>CSV</span>
    </button>
    <button class="dt-button buttons-excel buttons-html5" type="button" tabindex="0" onclick="exportTableToExcel()">
      <span>Excel</span>
    </button>
    <button class="dt-button buttons-print" type="button" tabindex="0" onclick="printTable()">
      <span>Drucken</span>
    </button>
  </div>
  
  <div class="table-responsive">
    <table id="compare_table" class="table table-sm table-striped table-bordered text-center" style="width:100%">
      <thead>
        <tr>
          <th><?php echo Text::_('Grad'); ?></th>
          <!-- Spalten werden dynamisch von JavaScript eingefügt -->
        </tr>
      </thead>
      <tbody>
        <tr id="row-soll">
          <th class="text-nowrap">Soll</th>
          <!-- Daten werden dynamisch von JavaScript eingefügt -->
        </tr>
        <tr id="row-ist">
          <th class="text-nowrap">Ist</th>
        </tr>
        <tr id="row-vmg">
          <th class="text-nowrap">Vorg.</th>
        </tr>
        <tr id="row-todo">
          <th class="text-nowrap">ToDo</th>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script>
// Einfache Export-Funktionen ohne DataTables
function copyTableToClipboard() {
  var table = document.getElementById('compare_table');
  if (!table) return;
  
  var text = '';
  var rows = table.querySelectorAll('tr');
  rows.forEach(function(row) {
    var cols = row.querySelectorAll('th, td');
    var rowText = [];
    cols.forEach(function(col) {
      rowText.push(col.textContent.trim());
    });
    text += rowText.join('\t') + '\n';
  });
  
  navigator.clipboard.writeText(text).then(function() {
    alert('Tabelle in Zwischenablage kopiert!');
  }).catch(function(err) {
    console.error('Fehler beim Kopieren:', err);
    // Fallback für ältere Browser
    var textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.opacity = '0';
    document.body.appendChild(textArea);
    textArea.select();
    try {
      document.execCommand('copy');
      alert('Tabelle in Zwischenablage kopiert!');
    } catch(e) {
      alert('Kopieren fehlgeschlagen');
    }
    document.body.removeChild(textArea);
  });
}

function exportTableToCSV() {
  var table = document.getElementById('compare_table');
  if (!table) return;
  
  var csv = [];
  var rows = table.querySelectorAll('tr');
  
  rows.forEach(function(row) {
    var cols = row.querySelectorAll('th, td');
    var rowData = [];
    cols.forEach(function(col) {
      var text = col.textContent.trim();
      // Escape Kommas und Anführungszeichen
      if (text.indexOf(',') !== -1 || text.indexOf('"') !== -1) {
        text = '"' + text.replace(/"/g, '""') + '"';
      }
      rowData.push(text);
    });
    csv.push(rowData.join(','));
  });
  
  var csvContent = '\uFEFF' + csv.join('\n'); // BOM für Excel
  var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  var link = document.createElement('a');
  var url = URL.createObjectURL(blob);
  
  link.setAttribute('href', url);
  link.setAttribute('download', 'grade_comparison.csv');
  link.style.visibility = 'hidden';
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

function exportTableToExcel() {
  var table = document.getElementById('compare_table');
  if (!table) return;
  
  // Prüfe ob SheetJS (XLSX) verfügbar ist
  if (typeof XLSX === 'undefined') {
    // Fallback: Verwende HTML-Table-Export
    var html = table.outerHTML;
    var blob = new Blob([html], { type: 'application/vnd.ms-excel' });
    var link = document.createElement('a');
    var url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'grade_comparison.xls');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    return;
  }
  
  // Mit SheetJS
  var wb = XLSX.utils.table_to_book(table, { sheet: "Grades" });
  XLSX.writeFile(wb, 'grade_comparison.xlsx');
}

function printTable() {
  var table = document.getElementById('compare_table');
  if (!table) return;
  
  var printWindow = window.open('', '', 'height=600,width=800');
  printWindow.document.write('<html><head><title>Grade Comparison</title>');
  printWindow.document.write('<style>');
  printWindow.document.write('table { border-collapse: collapse; width: 100%; margin: 20px 0; }');
  printWindow.document.write('th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }');
  printWindow.document.write('th { background-color: #f2f2f2; font-weight: bold; }');
  printWindow.document.write('.text-danger { color: #dc3545; }');
  printWindow.document.write('.text-success { color: #28a745; }');
  printWindow.document.write('</style>');
  printWindow.document.write('</head><body>');
  printWindow.document.write('<h2>Grade Comparison</h2>');
  printWindow.document.write(table.outerHTML);
  printWindow.document.write('</body></html>');
  printWindow.document.close();
  
  printWindow.onload = function() {
    printWindow.print();
    printWindow.close();
  };
}

// Daten für die Tabelle (werden vom grades-app.js genutzt)
window.gradesTableData = {
  raw: {
    labels: <?php echo json_encode(array_values((array)($this->labels_raw ?? []))); ?>,
    soll:   <?php echo json_encode(array_values(array_map('intval', (array)($this->soll_raw ?? [])))); ?>,
    ist:    <?php echo json_encode(array_values(array_map('intval', (array)($this->ist_raw ?? [])))); ?>,
    vmg:    <?php echo json_encode(array_values(array_map('intval', (array)($this->vmg_raw ?? [])))); ?>
  },
  merged: {
    labels: <?php echo json_encode(array_values((array)($this->labelsMerged ?? []))); ?>,
    soll:   <?php echo json_encode(array_values(array_map('intval', (array)($this->sollM ?? [])))); ?>,
    ist:    <?php echo json_encode(array_values(array_map('intval', (array)($this->istM ?? [])))); ?>,
    vmg:    <?php echo json_encode(array_values(array_map('intval', (array)($this->vmgM ?? [])))); ?>
  }
};

// Initial die Tabelle mit RAW-Daten befüllen
(function() {
  function initTableContent() {
    var table = document.getElementById('compare_table');
    if (!table || !window.gradesTableData) return;
    
    // Prüfe ob mergePlus aus LocalStorage geladen werden soll
    var mergePlus = false;
    try {
      var saved = localStorage.getItem('grades_mergePlus');
      mergePlus = (saved === 'true');
    } catch(e) {}
    
    var data = mergePlus ? window.gradesTableData.merged : window.gradesTableData.raw;
    var labels = data.labels || [];
    var soll = data.soll || [];
    var ist = data.ist || [];
    var vmg = data.vmg || [];
    
    if (labels.length === 0) return;
    
    // Header befüllen
    var thead = table.querySelector('thead tr');
    if (thead) {
      var headerHTML = '<th>Grad</th>';
      
      for (var i = 0; i < labels.length; i++) {
        headerHTML += '<th class="text-center text-nowrap">' + labels[i] + '</th>';
      }
      thead.innerHTML = headerHTML;
    }
    
    // Soll-Zeile
    var rowSoll = document.getElementById('row-soll');
    if (rowSoll) {
      var sollHTML = '<th class="text-nowrap">Soll</th>';
      for (var i = 0; i < soll.length; i++) {
        sollHTML += '<td class="text-center">' + soll[i] + '</td>';
      }
      rowSoll.innerHTML = sollHTML;
    }
    
    // Ist-Zeile
    var rowIst = document.getElementById('row-ist');
    if (rowIst) {
      var istHTML = '<th class="text-nowrap">Ist</th>';
      for (var i = 0; i < ist.length; i++) {
        istHTML += '<td class="text-center">' + ist[i] + '</td>';
      }
      rowIst.innerHTML = istHTML;
    }
    
    // Vorgemerkt-Zeile
    var rowVmg = document.getElementById('row-vmg');
    if (rowVmg) {
      var vmgHTML = '<th class="text-nowrap">Vorg.</th>';
      for (var i = 0; i < vmg.length; i++) {
        vmgHTML += '<td class="text-center">' + vmg[i] + '</td>';
      }
      rowVmg.innerHTML = vmgHTML;
    }
    
    // ToDo-Zeile
    var rowTodo = document.getElementById('row-todo');
    if (rowTodo) {
      var todoHTML = '<th class="text-nowrap">ToDo</th>';
      for (var i = 0; i < soll.length; i++) {
        var todo = (soll[i] || 0) - (ist[i] || 0) + (vmg[i] || 0);
        var cls = todo < 0 ? 'text-danger' : 'text-success';
        todoHTML += '<td class="text-center ' + cls + '">' + todo + '</td>';
      }
      rowTodo.innerHTML = todoHTML;
    }
  }
  
  // Sofort ausführen wenn DOM bereit ist
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTableContent);
  } else {
    initTableContent();
  }
})();
</script>