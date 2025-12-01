/**
 * DataTables 2.x (vanilla) Initialisierung â€“ DT1 wird nicht mehr unterstÃ¼tzt
 * - #replaceroutes_table: Standard (Buttons rechts, Paging an, simple Pagination mit FA-Icons)
 * - #compare_table: kompakt (keine Suche/Info/Paging), Sortierung AUS, Buttons rechts
 */
(function (win) {
  'use strict';

  // DataTables Warnings/Popups global unterdrücken
  if (win.DataTable) {
    win.DataTable.ext.errMode = 'none';
  }

  function initReplaceroutes() {
    var sel = '#replaceroutes_table';
    if (!document.querySelector(sel)) return;

    // falls schon initialisiert, nichts tun
    if (win.replaceroutesTable && win.replaceroutesTable.table) {
      return;
    }

    var table = new win.DataTable(sel, {
      stateSave: true,
      pageLength: 10,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Alle']],

      // Vor / aktuelle Seite / ZurÃ¼ck
      pagingType: 'simple_numbers',

      order: [],
      language: {
        url: (typeof langUrl !== 'undefined'
          ? langUrl
          : 'https://cdn.datatables.net/plug-ins/2.3.4/i18n/de-DE.json'),
        paginate: {
          previous: '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
          next: '<i class="fa fa-chevron-right" aria-hidden="true"></i>'
        }
      },
      layout: {
        topStart: 'pageLength',
        topEnd: { buttons: ['copy', 'csv', 'excelHtml5', 'print'] },
        bottomStart: null,
        bottomEnd: 'paging'
      },
      buttons: ['copy','csv','excelHtml5','print'],
      deferRender: true
    });

    win.replaceroutesTable = table;
  }

  function initCompare() {
    // DEAKTIVIERT: DataTables funktioniert nicht zuverlässig mit dynamischen Spalten
    // Stattdessen verwenden wir nur einfache HTML-Tabelle
    // Die Export-Funktionalität wird später hinzugefügt falls benötigt
    
    /* 
    var sel = '#compare_table';
    var table = document.querySelector(sel);
    if (!table) return;
    
    // Code auskommentiert...
    */
    
    console.log('compare_table: DataTables disabled, using plain HTML table');
  }

  function initAll() {
    initReplaceroutes();
    // initCompare wird manuell vom Table-Script aufgerufen
    // nachdem die Inhalte geladen sind
  }

  // Exportiere initCompare für externe Nutzung
  win.initCompare = initCompare;

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
  } else {
    initAll();
  }

})(window);