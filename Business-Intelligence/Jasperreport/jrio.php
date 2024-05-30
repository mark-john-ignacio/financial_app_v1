
<script src="http://localhost:8080/jrio-client/scripts/jrio/jrio.js"></script>

<div>
    <div id="reportContainer"></div>
    <div id="chartContainer"></div>
</div>

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
            resource: "/reports/test",
            container: "#reportContainer",
            error: function(err) { alert(err); },
        });
    });

    jrio(function(jrioClient) {
        jrioClient.report({
            resource: "/reports/accounts_category_chart",
            container: "#chartContainer",
            error: function(err) { alert(err); },
        });
    });
</script>