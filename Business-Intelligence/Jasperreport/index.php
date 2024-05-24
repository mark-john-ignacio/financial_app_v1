
<script src="http://localhost:8080/jrio-client/scripts/jrio/jrio.js"></script>

<div id="reportContainer"></div>

<script>

    jrio.config({
        server : "http://localhost:8080/jrio-docs",
        theme: {
            href: "http://localhost:8080/jrio-client/themes/default"
        },
        locale: "en_US"
    });

    jrio(function(jrioClient) {
        jrioClient.report({
            resource: "/samples/reports/highcharts/HighchartsChart",
            container: "#reportContainer",
            error: function(err) { alert(err); },
        });
    });
</script>