<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Kite Republic</title>
  <meta name="description" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->

  <script type="text/javascript" src="<?php echo base_url() ?>front/js/jquery-1.10.2.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url() ?>front/js/bootstrap.min.js"></script>
  <script type="text/javascript" src="<?php echo base_url() ?>front/js/ladda.js"></script>
  <script type="text/javascript" src="<?php echo base_url() ?>front/js/bootstrap-datepicker.js"></script>

  <link href="<?php echo base_url() ?>front/css/bootstrap.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url() ?>front/css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url() ?>front/css/ladda.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url() ?>front/css/datepicker.css" rel="stylesheet" type="text/css" />
</head>

<body>

  <div class="container">
    <?php $this->load->view('include/menu.inc.php');  ?>
    <?php $this->load->view('include/calendar-read.inc.php');  ?>
  </div>


</body>

</html>