<script src="http://localhost:8080/jasperserver-pro/client/visualize.js"></script>
<div id="container"></div>
<script>
    visualize(
        {auth:
            {
                name: "jasperadmin",
                password: "jasperadmin"
            }
        },
        /*
        please uncomment and use your credentials for testing
        {auth: {
        name: "******",
        password: "*******"
        }},
        */
        function (v) {
            v("#container").report({
                resource: "/public/MyxFin/MyxFin",
                error: function(e) {
                    alert(e);
                }
            });
        });
</script>