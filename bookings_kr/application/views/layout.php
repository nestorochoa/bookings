<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Kite Republic</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
  </script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    type="text/css" />
  <link href="/front/css/custom.css" rel="stylesheet">
  <script src="https://unpkg.com/htmx.org@1.9.8"></script>
</head>

<body>

  <div class="container">

    <?php include('include/newmenu.inc.php') ?>
    <?php include("include/$content"); ?>

  </div>
</body>