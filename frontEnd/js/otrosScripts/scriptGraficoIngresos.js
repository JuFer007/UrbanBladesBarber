fetch('../../backEnd/controladores/controladorGrafico2.php') 
  .then(res => res.json())
  .then(data => {
    const ctxIngresos = document.getElementById('chartIngresos').getContext('2d');
    new Chart(ctxIngresos, {
      type: 'line', 
      data: {
        labels: data.labels,
        datasets: data.datasets.map(ds => ({
          label: ds.label,
          data: ds.data,
          borderColor: ds.borderColor || 'rgba(75, 192, 192, 1)',
          backgroundColor: ds.backgroundColor || 'rgba(75, 192, 192, 0.2)',
          fill: ds.fill
        }))
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            precision: 0,
            ticks: {
              callback: function(value) {
                return 'S/. ' + value.toFixed(2); 
              }
            }
          }
        },
        plugins: {
          legend: {
            display: true,
            position: 'top'
          }
        }
      }
    });
  })
  .catch(err => console.error('Error cargando ingresos:', err));