//begin::Total Sales line chart
//Total sales line chart on dashboard. This is on the first widget
var totalSalesLineChart = {
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
                return 'Month-Year: ' + totalSalesLineChart.xaxis.categories[val-1];
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
fetch('data/total_sales_line_chart.php')
    .then(response => response.json())
    .then(data => {
        totalSalesLineChart.series[0].data = data.series[0].data.map((value, index) => ({
            x: data.xaxis.categories[index],
            y: value
        }));

        totalSalesLineChart.xaxis.categories = data.xaxis.categories;

        var chart = new ApexCharts(document.querySelector(".total-sales-chart"), totalSalesLineChart);
        chart.render();
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

//end::Total Sales line chart

//begin::Top Selling Item bar chart
function topSellingBarChart(data) {
    var chartElement = document.querySelector(".top-selling-bar-chart");
    var chart = new ApexCharts(chartElement, {
        series: [{
            name: "Revenue",
            data: data.map(item => item.revenue)
        }],
        chart: {
            fontFamily: "inherit",
            height: 100,
            type: "bar",
            toolbar: {
                show: false
            }
        },
        grid: {
            show: false,
            padding: {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0
            }
        },
        colors: ["#ffffff"],
        plotOptions: {
            bar: {
                borderRadius: 2.5,
                dataLabels: {
                    position: "top"
                },
                columnWidth: "20%"
            }
        },
        dataLabels: {
            enabled: false,
            formatter: function (val) {
                return val + "%";
            },
            offsetY: -20,
            style: {
                fontSize: "12px",
                colors: ["#304758"]
            }
        },
        xaxis: {
            labels: {
                show: false
            },
            categories: data.map(item => item.month),
            position: "top",
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            crosshairs: {
                show: false
            },
            tooltip: {
                enabled: false
            }
        },
        yaxis: {
            show: false,
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                show: false,
                formatter: function (val) {
                    return val;
                }
            }
        }
    });

    chart.render();
}

fetch('data/top_selling_item_bar_chart.php')
    .then(response => response.json())
    .then(data => {
        topSellingBarChart(data);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

//end::Top Selling Item bar chart