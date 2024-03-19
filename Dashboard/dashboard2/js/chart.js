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
fetch('analytics/total_sales_line_chart.php')
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

fetch('analytics/top_selling_item_bar_chart.php')
    .then(response => response.json())
    .then(data => {
        topSellingBarChart(data);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

//end::Top Selling Item bar chart

//begin::Sales Progress bar chart
function fetchSalesProgressDataAndRenderChart() {
    fetch('analytics/sales_progress_bar_chart.php')
        .then(response => response.json())
        .then(data => renderSalesProgressChart(data))
        .catch(error => console.error('Error fetching data:', error));
}

function renderSalesProgressChart(data) {
    var chartElements = document.querySelectorAll(".sales-progress-bar-chart");
    [].slice.call(chartElements).map(function(chartElement) {
        var chartColor = chartElement.getAttribute("data-kt-color");
        var chartHeight = parseInt(KTUtil.css(chartElement, "height"));
        var chartColorValue = KTUtil.getCssVariableValue("--bs-" + chartColor);

        new ApexCharts(chartElement, {
            series: [{
                name: "Net Profit",
                data: data.map(item => item.net_profit)
            }, {
                name: "Revenue",
                data: data.map(item => item.revenue)
            }],
            chart: {
                fontFamily: "inherit",
                type: "bar",
                height: chartHeight,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: ["50%"],
                    borderRadius: 4
                }
            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 2,
                colors: ["transparent"]
            },
            xaxis: {
                categories: data.map(item => item.month),
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
                labels: {
                    style: {
                        colors: chartColor,
                        fontSize: "12px"
                    }
                }
            },
            yaxis: {
                y: 0,
                offsetX: 0,
                offsetY: 0,
                labels: {
                    style: {
                        colors: chartColor,
                        fontSize: "12px"
                    }
                }
            },
            fill: {
                type: "solid"
            },
            states: {
                normal: {
                    filter: {
                        type: "none",
                        value: 0
                    }
                },
                hover: {
                    filter: {
                        type: "none",
                        value: 0
                    }
                },
                active: {
                    allowMultipleDataPointsSelection: false,
                    filter: {
                        type: "none",
                        value: 0
                    }
                }
            },
            tooltip: {
                style: {
                    fontSize: "12px"
                },
                y: {
                    formatter: function(e) {
                        return "$" + e + " revenue";
                    }
                }
            },
            colors: [chartColorValue, "#9e9e9e"],
            grid: {
                padding: {
                    top: 10
                },
                borderColor: "ffffff",
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            }
        }).render();
    });
}

// Usage
fetchSalesProgressDataAndRenderChart();
//end::Sales Progress bar chart

//begin::daterangepicker
$("#kt_daterangepicker_1").daterangepicker();
//end::daterangepicker


//begin::Purchase per item bar chart

fetch('analytics/purchase_per_item_bar.php')
    .then(response => response.json())
    .then(data => {
        let chartElement = document.getElementById("purchase-per-item-bar");
        let chartHeight = parseInt(KTUtil.css(chartElement, "height"));
        let gray500 = "#9e9e9e";
        let gray200 = "#9e9e9e";
        let primary = "#1e1f22";
        let gray300 = "#9e9e9e";

        if (chartElement) {
            let itemCodes = data.map(item => item.item_code);
            let quantityData = data.map(item => item.total_quantity);
            let amountData = data.map(item => item.total_amount);

            new ApexCharts(chartElement, {
                series: [{
                    name: "Total Quantity",
                    data: quantityData
                }, {
                    name: "Total Amount",
                    data: amountData
                }],
                chart: {
                    fontFamily: "inherit",
                    type: "bar",
                    height: chartHeight,
                    toolbar: {
                        show: false
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: ["30%"],
                        borderRadius: 4
                    }
                },
                legend: {
                    show: false
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ["transparent"]
                },
                xaxis: {
                    categories: itemCodes,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: gray500,
                            fontSize: "12px"
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: gray500,
                            fontSize: "12px"
                        }
                    }
                },
                fill: {
                    opacity: 1
                },
                states: {
                    normal: {
                        filter: {
                            type: "none",
                            value: 0
                        }
                    },
                    hover: {
                        filter: {
                            type: "none",
                            value: 0
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: "none",
                            value: 0
                        }
                    }
                },
                tooltip: {
                    style: {
                        fontSize: "12px"
                    },
                    y: {
                        formatter: function(e) {
                            return "₱" + e;
                        }
                    }
                },
                colors: [primary, gray300],
                grid: {
                    borderColor: gray200,
                    strokeDashArray: 4,
                    yaxis: {
                        lines: {
                            show: true
                        }
                    }
                }
            }).render();
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });
//end::Purchase per item bar chart