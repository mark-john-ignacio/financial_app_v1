<head>
  <script src="../Bootstrap/js/jquery-3.2.1.min.js"></script>
  <script src="../include/jquery-maskmoney.js" type="text/javascript"></script>
</head>
<body>

  <input type="text" id="currency" />
</body>
<script>
  $(function() {
    $('#currency').maskMoney({prefix:'\u20B1 '});
  })
</script>