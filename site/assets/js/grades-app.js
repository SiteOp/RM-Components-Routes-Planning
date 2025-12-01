(() => {
  const isMobile = () => matchMedia('(max-width:640px)').matches;
  const debounce = (fn, ms=100) => { let t=null; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; };

  // DOM
  const btnMergePlus  = document.getElementById('btnMergePlus');
  const btnToggleTodo = document.getElementById('btnToggleTodo');
  const outer  = document.querySelector('.chart-outer');
  const inner  = document.querySelector('.chart-inner');
  const canvas = document.getElementById('gradesChart');
  if (!canvas || !outer || !inner) return;
  if (typeof Chart === 'undefined') { console.error('Chart.js nicht geladen'); return; }
  const ctx = canvas.getContext('2d');

  // Summary-Elemente
  const elSumSoll = document.getElementById('sumSoll');
  const elSumIst  = document.getElementById('sumIst');
  const elSumVmg  = document.getElementById('sumVmg');
  const barSoll   = document.getElementById('barSoll');
  const barIst    = document.getElementById('barIst');
  const barVmg    = document.getElementById('barVmg');
  const fulfillmentText = document.getElementById('fulfillmentText');
  const diffText        = document.getElementById('diffText');

  // Farben
  const colorSoll="#a9ce5a", colorIst="#2da7ff", colorVmg="#ffa42b";
  const colorTodo = getComputedStyle(document.documentElement).getPropertyValue('--todo-color')?.trim() || '#9aa0a6';

  // Daten aus Joomla Options
  const p = (window.Joomla && Joomla.getOptions) ? (Joomla.getOptions('gradesData') || {}) : {};
  if (!p || (!p.labels && !p.labels_raw)) console.warn('gradesData nicht gefunden oder leer', p);

  // RAW
  const labelsRaw = p.labels_raw || p.labels || [];
  const sollRaw   = p.soll_raw   || p.soll   || [];
  const istRaw    = p.ist_raw    || p.ist    || [];
  const vmgRaw    = p.vmg_raw    || p.vmg    || new Array(labelsRaw.length).fill(0);

  // MERGED
  const labelsMerged = p.labels || [];
  const sollMerged   = p.soll   || [];
  const istMerged    = p.ist    || [];
  const vmgMerged    = p.vmg    || new Array(labelsMerged.length).fill(0);

  // ToDo-Berechnung: exakte Formel (ohne Clamping)
  const calcTodo = (A, B, C) => A.map((a, i) => (+a||0) - (+B[i]||0) + (+C[i]||0));
  const sum = arr => (arr||[]).reduce((a,b)=>a+(+b||0),0);

  // Toggles (LocalStorage) â€“ Default: RAW
  const LS_KEY_MERGE = 'grades_mergePlus';
  const LS_KEY_TODO  = 'grades_showTodo';
  let mergePlus = false, showTodo = false;
  try {
    const savedM = localStorage.getItem(LS_KEY_MERGE);
    mergePlus = (savedM === null) ? false : (savedM === 'true');
    const savedT = localStorage.getItem(LS_KEY_TODO);
    showTodo = (savedT === 'true');
  } catch(e){}

  function currentView(){
    if (mergePlus) {
      return { labels: labelsMerged, A: sollMerged, B: istMerged, C: vmgMerged, T: calcTodo(sollMerged, istMerged, vmgMerged) };
    }
    return { labels: labelsRaw, A: sollRaw, B: istRaw, C: vmgRaw, T: calcTodo(sollRaw, istRaw, vmgRaw) };
  }

  // Summary immer relativ zu Soll
  function updateSummaryFrom(view){
    const sA = sum(view.A); // Soll
    const sB = sum(view.B); // Ist
    const sC = sum(view.C); // Vorgemerkt

    // Zahlen
    elSumSoll && (elSumSoll.textContent = sA.toLocaleString('de-DE'));
    elSumIst  && (elSumIst.textContent  = sB.toLocaleString('de-DE'));
    elSumVmg  && (elSumVmg.textContent  = sC.toLocaleString('de-DE'));

    // Progressbars: Soll 100%, Ist/VMG vs Soll
    const pctIst = sA > 0 ? (sB / sA) * 100 : 0;
    const pctVmg = sA > 0 ? (sC / sA) * 100 : 0;

    setBar(barSoll, 100);
    setBar(barIst,  pctIst);
    setBar(barVmg,  pctVmg);

    const fulfillment = sA > 0 ? Math.round(pctIst) : 0;
    const diff = sB - sA;
    // Prozent IN der Ist-Progressbar anzeigen
    const fulfillmentPercent = document.getElementById('fulfillmentPercent');
    if (fulfillmentPercent) {
      fulfillmentPercent.textContent = fulfillment + '%';
    }
    if (diffText)        diffText.textContent        = `Differenz: ${(diff>=0?'+':'')}${diff.toLocaleString('de-DE')}`;
  }

  function setBar(el, pct){
    if (!el) return;
    const clamped = Math.max(0, Math.min(100, pct));
    el.style.width = clamped.toFixed(1) + '%';
    el.setAttribute('aria-valuenow', String(Math.round(clamped)));
  }

  // Responsive
  let valueLabelFontSize = isMobile()?11:12;
  const perLabel = () => isMobile() ? 46 : 60;
  function desiredInnerSize(labelCount){
    const minW = Math.ceil(labelCount * perLabel());
    const width = Math.max(outer.clientWidth, minW);
    const height = isMobile() ? 360 : 460;
    return {width, height};
  }
  function applySize(labelCount, chart){
    const {width, height} = desiredInnerSize(labelCount);
    inner.style.width  = width + 'px';
    inner.style.height = height + 'px';
    if (chart){
      chart.options.datasets.bar.barThickness = isMobile()?12:14;
      chart.options.scales.x.ticks.font = { size: isMobile()?12:13 };
      chart.options.scales.y.ticks.font = { size: isMobile()?11:12 };
      chart.options.plugins.legend.labels.font = { size: isMobile()?11:12 };
      valueLabelFontSize = isMobile()?11:12;
      chart.resize();
    }
  }

  // Werte Ã¼ber den Balken
  const valueLabelsPlugin = {
    id: 'valueLabels',
    afterDatasetsDraw(c) {
      const {ctx, chartArea:{top}} = c;
      ctx.save();
      ctx.font = `${valueLabelFontSize}px system-ui, -apple-system, Segoe UI, Roboto, Arial`;
      ctx.textAlign = 'center';
      ctx.fillStyle = '#333';
      c.data.datasets.forEach((ds) => {
        if (ds.label === 'ToDo' && !showTodo) return;
        const meta = c.getDatasetMeta(c.data.datasets.indexOf(ds));
        meta.data.forEach((elem, i) => {
          const val = ds.data[i]; if (val == null) return;
          const {x, y} = elem.tooltipPosition();
          let ty = y - 3;
          if (ty < top + valueLabelFontSize + 1) ty = y + 3;
          ctx.textBaseline = (ty < y) ? 'bottom' : 'top';
          ctx.fillText(String(val), x, ty);
        });
      });
      ctx.restore();
    }
  };
  Chart.register(valueLabelsPlugin);

  // Init Chart
  let view = currentView();
  const chart = new Chart(ctx, {
    type:'bar',
    data:{
      labels: view.labels,
      datasets:[
        {label:'Soll',       data:view.A, backgroundColor:colorSoll},
        {label:'Ist',        data:view.B, backgroundColor:colorIst},
        {label:'Vorgemerkt', data:view.C, backgroundColor:colorVmg},
        {label:'ToDo',       data:view.T, backgroundColor:colorTodo, hidden: !showTodo}
      ]
    },
    options:{
      responsive:true, maintainAspectRatio:false,
      layout:{ padding:{ top:14, right:6, bottom:36, left:6 } },
      scales:{
        x:{ stacked:false, ticks:{ maxRotation:0, autoSkip:false, padding:8 }, grid:{ display:false } },
        y:{ beginAtZero:true, ticks:{ padding:6 } }
      },
      datasets:{ bar:{ categoryPercentage:0.95, barPercentage:0.98 } },
      plugins:{
        legend:{ display:false },
        tooltip:{
          callbacks:{
            afterTitle:(items)=>{
              const i = items[0].dataIndex;
              const s = view.A[i] ?? 0;
              const t = view.B[i] ?? 0;
              const v = view.C[i] ?? 0;
              const d = view.T[i] ?? 0;
              return [`Soll: ${s}`, `Ist: ${t}`, `Vorg.: ${v}`, `ToDo: ${d}`];
            }
          }
        }
      }
    }
  });

  applySize(view.labels.length, chart);
  updateSummaryFrom(view);
  updateTable(); // Initial table state

  // Redraw bei Toggles/Resize
  function redraw(){
    view = currentView();
    chart.data.labels           = view.labels.slice();
    chart.data.datasets[0].data = view.A.slice();
    chart.data.datasets[1].data = view.B.slice();
    chart.data.datasets[2].data = view.C.slice();
    chart.data.datasets[3].data = view.T.slice();
    chart.data.datasets[3].hidden = !showTodo;
    chart.options.scales.y.max  = undefined;
    applySize(view.labels.length, chart);
    chart.update();
    updateSummaryFrom(view);
    updateTable();
  }

  // Tabellen-Spalten umschalten zwischen RAW und MERGED
  // Tabelle dynamisch rendern basierend auf mergePlus
  function updateTable(){
    const table = document.getElementById('compare_table');
    if (!table || !window.gradesTableData) return;
    
    const data = mergePlus ? window.gradesTableData.merged : window.gradesTableData.raw;
    const labels = data.labels || [];
    const soll = data.soll || [];
    const ist = data.ist || [];
    const vmg = data.vmg || [];
    
    // Header-Zeile aktualisieren mit String-Konkatenation
    const thead = table.querySelector('thead tr');
    if (thead) {
      var headerHTML = '<th>Grad</th>';
      labels.forEach(function(label) {
        headerHTML += '<th class="text-center text-nowrap">' + label + '</th>';
      });
      thead.innerHTML = headerHTML;
    }
    
    // Helper für ToDo-Farbe
    const getTodoClass = function(todo) {
      return todo < 0 ? 'text-danger' : 'text-success';
    };
    
    // Soll-Zeile
    const rowSoll = document.getElementById('row-soll');
    if (rowSoll) {
      var sollHTML = '<th class="text-nowrap">Soll</th>';
      soll.forEach(function(val) {
        sollHTML += '<td class="text-center">' + val + '</td>';
      });
      rowSoll.innerHTML = sollHTML;
    }
    
    // Ist-Zeile
    const rowIst = document.getElementById('row-ist');
    if (rowIst) {
      var istHTML = '<th class="text-nowrap">Ist</th>';
      ist.forEach(function(val) {
        istHTML += '<td class="text-center">' + val + '</td>';
      });
      rowIst.innerHTML = istHTML;
    }
    
    // Vorgemerkt-Zeile
    const rowVmg = document.getElementById('row-vmg');
    if (rowVmg) {
      var vmgHTML = '<th class="text-nowrap">Vorg.</th>';
      vmg.forEach(function(val) {
        vmgHTML += '<td class="text-center">' + val + '</td>';
      });
      rowVmg.innerHTML = vmgHTML;
    }
    
    // ToDo-Zeile
    const rowTodo = document.getElementById('row-todo');
    if (rowTodo) {
      var todoHTML = '<th class="text-nowrap">ToDo</th>';
      for (var i = 0; i < soll.length; i++) {
        const todo = (soll[i] || 0) - (ist[i] || 0) + (vmg[i] || 0);
        const cls = getTodoClass(todo);
        todoHTML += '<td class="text-center ' + cls + '">' + todo + '</td>';
      }
      rowTodo.innerHTML = todoHTML;
    }
    
    // DataTables nicht mehr verwenden für compare_table
    // Die Tabelle funktioniert jetzt als einfache HTML-Tabelle
  }

  // Buttons
  function syncPressed(btn, on){
    if (!btn) return;
    btn.setAttribute('aria-pressed', on ? 'true' : 'false');
    btn.classList.toggle('active', on);
  }
  syncPressed(btnMergePlus, mergePlus);
  syncPressed(btnToggleTodo, showTodo);

  btnMergePlus?.addEventListener('click', () => {
    mergePlus = !mergePlus;
    try { localStorage.setItem(LS_KEY_MERGE, String(mergePlus)); } catch(e){}
    syncPressed(btnMergePlus, mergePlus);
    redraw();
  });

  btnToggleTodo?.addEventListener('click', () => {
    showTodo = !showTodo;
    try { localStorage.setItem(LS_KEY_TODO, String(showTodo)); } catch(e){}
    syncPressed(btnToggleTodo, showTodo);
    const todoDs = chart.data.datasets.find(d => d.label === 'ToDo');
    if (todoDs){ todoDs.hidden = !showTodo; chart.update(); }
  });

  // Resize
  if ('ResizeObserver' in window){
    const ro = new ResizeObserver(() => applySize((chart.data.labels||[]).length, chart));
    ro.observe(outer);
  }
  window.addEventListener('resize', debounce(() => applySize((chart.data.labels||[]).length, chart), 120));
})();