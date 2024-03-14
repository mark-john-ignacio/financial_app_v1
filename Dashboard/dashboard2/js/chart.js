var options = {
    series: [{
        name: 'Net Profit',
        data: [] // Empty array to be populated with data from the PHP script
    }],
    chart: {
        height: 100,
        type: 'line',
        toolbar: {
            show: false
        },
        zoom: {
            enabled: false
        },
        sparkline: {
            enabled: true
        }
    },
    stroke: {
        curve: 'smooth',
        width: 3,
        colors: ['#FFFFFF']
    },
    tooltip: {
        enabled: true,
        x: {
            formatter: function(val) {
                return 'Month-Year: ' + options.xaxis.categories[val];
            }
        },
        y: {
            formatter: function(val) {
                return '₱' + val.toLocaleString();
            }
        }
    },
    dataLabels: {
        enabled: false
    },
    grid: {
        show: false
    },
    xaxis: {
        categories: [], // Empty array to be populated with month-year values from the PHP script
        labels: {
            show: false
        },
        axisTicks: {
            show: false
        },
        axisBorder: {
            show: false
        }
    },
    yaxis: {
        labels: {
            show: false
        }
    }
};

// Fetch the data from the PHP script
fetch('data/sales_trend_data.php')
    .then(response => response.json())
    .then(data => {
        options.series[0].data = data.series[0].data.map((value, index) => ({
            x: data.xaxis.categories[index],
            y: value
        }));

        options.xaxis.categories = data.xaxis.categories;

        var chart = new ApexCharts(document.querySelector(".total-sales-chart"), options);
        chart.render();
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });