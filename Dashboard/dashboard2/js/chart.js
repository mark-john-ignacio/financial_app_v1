//begin::Total Sales line chart
//Total sales line chart on dashboard. This is on the first widget
// Define a function to render the line chart
function renderTotalSalesLineChart(data) {
    var totalSalesLineChart = {
        series: [{
            name: 'Net Profit',
            data: data.series[0].data.map((value, index) => ({
                x: data.xaxis.categories[index],
                y: value
            }))
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
                    return 'Month-Year: ' + data.xaxis.categories[val-1];
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
            categories: data.xaxis.categories,
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

    var chart = new ApexCharts(document.querySelector(".total-sales-chart"), totalSalesLineChart);
    chart.render();
}

// Fetch the data from the PHP script
fetch('analytics/total_sales_line_chart.php')
    .then(response => response.json())
    .then(data => {
        renderTotalSalesLineChart(data);
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
                    return "₱" + val.toLocaleString();
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
                name: "Last Year Gross",
                data: data.map(item => parseFloat(item.last_year_gross).toFixed(4))
            }, {
                name: "This Year Gross",
                data: data.map(item => parseFloat(item.this_year_gross).toFixed(4))
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
                    formatter: function(val) {
                        return "₱" + val.toLocaleString();
                    }
                }
            },
            colors: ["#9e9e9e", chartColorValue],
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


//begin::Sale per item bar chart
function renderSalesPerItemBarChart(data) {
    let chartElement = document.getElementById("sale-per-item-bar");
    let chartHeight = parseInt(KTUtil.css(chartElement, "height"));
    let gray500 = KTUtil.getCssVariableValue("--bs-gray-500");
    let primary = KTUtil.getCssVariableValue("--bs-primary");

    if (chartElement) {
        let itemCodes = data.map(item => item.item_number);
        let itemDesc = data.map(item => item.item_description);
        let amountData = data.map(item => item.total_sales);

        new ApexCharts(chartElement, {
            series: [{
                name: "Total Sales",
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
                categories: itemCodes.map((item, index) => item !== null ? item : `N/A (${index + 1})`),
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
                x: {
                    formatter: function(val) {
                        let index = itemCodes.indexOf(val);
                        if (index !== -1) {
                            return itemDesc[index] !== null ? itemDesc[index] : "N/A";
                        }
                        return val;
                    }
                },
                y: {
                    formatter: function(e) {
                        return "₱" + e.toLocaleString();
                    }
                }
            },
            colors: [primary],
            grid: {
                borderColor: gray500,
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            }
        }).render();
    }
}

// Usage
fetch('analytics/sale_per_item_bar.php')
    .then(response => response.json())
    .then(data => {
        renderSalesPerItemBarChart(data);
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });

//end::Sale per item bar chart

//begin::Purchase per item bar chart

fetch('analytics/purchase_per_item_bar.php')
    .then(response => response.json())
    .then(data => {
        let chartElement = document.getElementById("purchase-per-item-bar");
        let chartHeight = parseInt(KTUtil.css(chartElement, "height"));
        let gray500 = KTUtil.getCssVariableValue("--bs-gray-500");
        let gray200 = KTUtil.getCssVariableValue("--bs-gray-200");
        let primary = KTUtil.getCssVariableValue("--bs-primary");
        let gray300 = KTUtil.getCssVariableValue("--bs-gray-300");

        if (chartElement) {
            let itemCodes = data.map(item => item.item_code);
            let itemDesc = data.map(item => item.item_description);
            let amountData = data.map(item => item.total_amount);

            new ApexCharts(chartElement, {
                series: [{
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
                    x: {
                        formatter: function(val) {
                            let index = itemCodes.indexOf(val);
                            if (index !== -1) {
                                return itemDesc[index];
                            }
                            return val;
                        }
                    },
                    y: {
                        formatter: function(e) {
                            return "₱" + e.toLocaleString();
                        }
                    }
                },
                colors: [primary],
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


//begin::Purchase per supplier
am4core.ready(function() {
    // Themes begin
    am4core.useTheme(am4themes_dataviz);
    am4core.useTheme(am4themes_animated);
    // Themes end

    fetch('analytics/purchase_per_supplier_pie.php')
        .then(response => response.json())
        .then(data => {
            var chartData = data.map(row => ({
                country: row.country,
                value: parseFloat(row.value) // Convert value to number
            }));

            // Create chart
            var chart = am4core.create('supplier-pie', am4charts.PieChart);
            chart.hiddenState.properties.opacity = 0; // this creates initial fade-in

            chart.data = chartData;

            var series = chart.series.push(new am4charts.PieSeries());
            series.dataFields.value = 'value';
            series.dataFields.radiusValue = 'value';
            series.dataFields.category = 'country';
            series.slices.template.cornerRadius = 6;


            series.ticks.template.disabled = true;
            series.alignLabels = false;
            series.labels.template.text = "{value.percent.formatNumber('#.0')}%";
            series.labels.template.radius = am4core.percent(-40);
            series.labels.template.fill = am4core.color("white");

            // Set pastel colors
            series.colors.list = [
                am4core.color("#fa31bc"),
                am4core.color("#2956f7"),
                am4core.color("#0ad1e7"),
                am4core.color("#05d005"),
                am4core.color("#ff8f0b"),
                am4core.color("#e709e7"),
                am4core.color("#f0f008"),
                am4core.color("#ca0a87")
            ];

            series.hiddenState.properties.endAngle = -90;

            chart.legend = new am4charts.Legend();
            chart.legend.position = "right";
            chart.legend.valign = "top";

            chart.legend.labels.template.maxWidth = 150;
            chart.legend.labels.template.truncate = true;

            
        })
        .catch(error => {
            console.error('Error fetching data:', error);
        });
});


//end::Purchase per supplier