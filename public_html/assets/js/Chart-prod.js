// Chart-prod.js (puro JS)
(function() {
  // Carga Google Charts
  google.charts.load('current', { packages: ['corechart'] });
  // Cuando Google Charts esté listo, llamamos a nuestra función
  google.charts.setOnLoadCallback(loadDataAndDraw);
  function loadDataAndDraw() {
    // Ruta al JSON estático (ajusta si tu carpeta es otra)
    const url = '/assets/data/chart-data.json';
    fetch(url)
      .then(resp => {
        if (!resp.ok) throw new Error('HTTP error ' + resp.status);
        return resp.json();
      })
      .then(jsonData => {
        // jsonData esperado: [ ["Producto A",12], ["Producto B",8], ... ]
        const rows = [['Articulo', 'Stock']].concat(jsonData);
        const data = google.visualization.arrayToDataTable(rows);
        const options = {
          pieHole: 0.4,
          title: 'Stock por producto',
          width: '100%',
          height: 400
        };
        const el = document.getElementById('piechart');
        if (!el) {
          console.error('No se encontró el contenedor #piechart en el DOM');
          return;
        }
        const chart = new google.visualization.PieChart(el);
        chart.draw(data, options);
      })
      .catch(err => {
        console.error('Error al cargar datos para el gráfico:', err);
        // puedes mostrar un mensaje en el DOM en lugar del chart
        const el = document.getElementById('piechart');
        if (el) el.innerHTML = '<div style="padding:1rem;color:#b00">No se pudieron cargar los datos del gráfico</div>';
      });
  }
})();
