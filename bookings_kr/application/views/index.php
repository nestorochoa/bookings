<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    
    <!-- Le JS -->
    <script type="text/javascript" src="<?php echo base_url() ?>front/js/jquery-1.10.2.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>front/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>front/js/ladda.js"></script>
    <script type="text/javascript" src="<?php echo base_url() ?>front/js/login.js"></script>

    <!-- Le styles -->
    <link href="<?php echo base_url() ?>front/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>front/css/login.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>front/css/bootstrap-responsive.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url() ?>front/css/ladda.css" rel="stylesheet" type="text/css" />

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="Scripts/html5shiv.js"></script>
    <![endif]-->
    </head>

    <body>
        <div class="container">
            <div class="form-signin">
	    <?if ($this->session->flashdata('warning') !='' || isset($_GET['reg'])){
	    	$value_e = $this->session->flashdata('warning');
		if(isset($_GET['reg']) ){
			if($_GET['reg'] == 99){
				$value_e =  'You have registered your coupon, please check your email for further instructions'  ;	
			}
		}
		
	    ?>
	    	<div class="alert alert-danger"><?echo $value_e?></div>
		<? }?>
                <img src="<? echo $basic_var['company_logo'] ?>">
                <div id="form-signin">
		<form action="<?php echo base_url()?>validate_user" method="post">
                    <input type="text" class="input-block-level" placeholder="Email" id="username" name="username">
                    <input type="password" class="input-block-level" placeholder="Password" id="password" name="password">
                    <button id="btnLogOn" class="btn btn-large btn-primary btn-block ladda-button zoom-out">
                        <span class="ladda-label">Login</span>
                        <span class="ladda-spinner"></span>
                        <div class="ladda-progress" style="width: 0px;"></div>
                    </button>
		    </form>
                </div>

                <div id="form-coupon" style="display: none">
		<form action="<?php echo base_url()?>validate_coupon" method="post">
                    <input id="coupon" class="input-block-level" name="coupon" type="text" placeholder="Voucher Code">
                    <button id="btnRegisterCancel" class="btn btn-inverse" type="button">Cancel</button>
                    <button id="btnRegisterSend" class="btn btn-success ladda-button zoom-out">
                        <span class="ladda-label">Register</span>
                        <span class="ladda-spinner"></span>
                        <div class="ladda-progress" style="width: 0px;"></div>
                    </button>
		</form>
                </div>

                <div id="form-forgot" style="display: none">
		<form action="<?php echo base_url()?>forgot_password" method="post">
                    <input id="rec-email" name="rec-email"class="input-block-level" name="Email" type="text" placeholder="Email">
                    <button id="btnForgotCancel" class="btn btn-inverse" type="button">Cancel</button>
                    <button id="btnForgotSend" class="btn btn-danger ladda-button zoom-out">
                        <span class="ladda-label">Recover</span>
                        <span class="ladda-spinner"></span>
                        <div class="ladda-progress" style="width: 0px;"></div>
                    </button>
		    <form/>
                </div>
            </div>
            <div id="well" class="well well-small">
                <p style="margin: 0">Do you have a voucher? <strong><a id="btnCoupon">Redeem voucher!</a></strong></p>
            	<p style="margin: 0">Get a Voucher: <strong><a href="http://www.kiterepublic.com.au/product-category/kiteboarding/kiteboarding-lessons/" target="_blank">Buy lessons</a></strong></p>
	    </div>
            <p id="copyright">&copy; <? echo $basic_var['company_name']?> - <a id="btnForgot">Forgot your password?</a></p>
        </div> <!-- /container -->

        <script>
            // Bind normal buttons
            Ladda.bind( '.ladda-button', { timeout: 1000 } );
        </script>

    </body>
</html>




