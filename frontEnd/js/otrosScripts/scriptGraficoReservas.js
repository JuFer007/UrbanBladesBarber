fetch('../../backEnd/controladores/controladorGrafico1.php')
  .then(res => res.json())
  .then(data => {
    const ctx = document.getElementById('chartReservas').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Cantidad de Reservas',
          data: data.totales,
          backgroundColor: 'rgba(255, 159, 64, 0.7)',
          borderColor: 'rgba(255, 159, 64, 0.7)',
          borderWidth: 1,
          barPercentage: 0.2, 
          categoryPercentage: 1.5
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            min: 0,
            max: 50,
            precision: 0
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
  .catch(err => console.error('Error cargando datos:', err));
