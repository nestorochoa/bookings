

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<title>Your Message Subject or Title</title>
	<style type="text/css">

		
		
		/* Client-specific Styles */
		#outlook a {padding:0;} /* Force Outlook to provide a "view in browser" menu link. */
		body{width:100% !important; -webkit-text-size-adjust:100%; -ms-text-size-adjust:100%; margin:0; padding:0;background-color: beige;}
		/* Prevent Webkit and Windows Mobile platforms from changing default font sizes, while not breaking desktop design. */
		.ExternalClass {width:100%;} /* Force Hotmail to display emails at full width */
		.ExternalClass, .ExternalClass p, .ExternalClass span, .ExternalClass font, .ExternalClass td, .ExternalClass div {line-height: 100%;} /* Force Hotmail to display normal line spacing.  More on that: http://www.emailonacid.com/forum/viewthread/43/ */
		#backgroundTable {margin:0; padding:0; width:100% !important; line-height: 100% !important;}
		/* End reset */


		img {outline:none; text-decoration:none; -ms-interpolation-mode: bicubic;}
		a img {border:none;}
		.image_fix {display:block;}

		p {margin: 1em 0;}

		h1, h2, h3, h4, h5, h6 {color: black !important;}

		h1 a, h2 a, h3 a, h4 a, h5 a, h6 a {color: blue !important;}

		h1 a:active, h2 a:active,  h3 a:active, h4 a:active, h5 a:active, h6 a:active {
			color: red !important; /* Preferably not the same color as the normal header link color.  There is limited support for psuedo classes in email clients, this was added just for good measure. */
		 }

		h1 a:visited, h2 a:visited,  h3 a:visited, h4 a:visited, h5 a:visited, h6 a:visited {
			color: purple !important; /* Preferably not the same color as the normal header link color. There is limited support for psuedo classes in email clients, this was added just for good measure. */
		}

		table td {border-collapse: collapse;}

		table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }


		a {color: orange;}

		
		a:link { color: orange; }
		a:visited { color: blue; }
		a:hover { color: green; }
		


		@media  (max-width: 480px) {
			
			.m_logo > img{
				width:320px;
			}
			.message{
				line-height: 20px;
			}
			.contact{
				float:left;
				margin-left: 25px;
				font-family: sans-serif;
				line-height: 39px;
				font-size : 8px;
			}
			.address{
				float:right;
				margin-right: 25px;
				font-family: sans-serif;
				line-height: 20px;
				font-size : 8px;
			}
			a[href^="tel"], a[href^="sms"] {
						text-decoration: none;
						color: black; /* or whatever your want */
						pointer-events: none;
						cursor: default;
					}

			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
						text-decoration: default;
						color: orange !important; /* or whatever your want */
						pointer-events: auto;
						cursor: default;
					}
		}

		/* More Specific Targeting */

		@media only screen and (min-device-width: 768px) and (max-device-width: 1024px) {
			/* You guessed it, ipad (tablets, smaller screens, etc) */

			/* Step 1a: Repeating for the iPad */
			a[href^="tel"], a[href^="sms"] {
						text-decoration: none;
						color: blue; /* or whatever your want */
						pointer-events: none;
						cursor: default;
					}

			.mobile_link a[href^="tel"], .mobile_link a[href^="sms"] {
						text-decoration: default;
						color: orange !important;
						pointer-events: auto;
						cursor: default;
					}
		}

		@media only screen and (-webkit-min-device-pixel-ratio: 2) {
			/* Put your iPhone 4g styles in here */
		}

		/* Following Android targeting from:
		http://developer.android.com/guide/webapps/targeting.html
		http://pugetworks.com/2011/04/css-media-queries-for-targeting-different-mobile-devices/  */
		@media only screen and (-webkit-device-pixel-ratio:.75){
			/* Put CSS for low density (ldpi) Android layouts in here */
		}
		@media only screen and (-webkit-device-pixel-ratio:1){
			/* Put CSS for medium density (mdpi) Android layouts in here */
		}
		@media only screen and (-webkit-device-pixel-ratio:1.5){
			/* Put CSS for high density (hdpi) Android layouts in here */
		}
		/* end Android targeting */
		.message{
			background-color: honeydew;
			padding:45px;
			
		}
		.m_logo{
			margin-bottom: 20px;
		}
		.contact{
			float:left;
			margin-left: 25px;
			font-family: sans-serif;
			line-height: 46px;
		}
		.address{
			float:right;
			margin-right: 25px;
			font-family: sans-serif;
			line-height: 20px;
		}
		.footer{
			height:92px;
			border:3px solid rgb(162, 189, 189);;
			border-radius:20px;
		}
		
	</style>

	<!-- Targeting Windows Mobile -->
	<!--[if IEMobile 7]>
	<style type="text/css">

	</style>
	<![endif]-->

	<!-- ***********************************************
	****************************************************
	END MOBILE TARGETING
	****************************************************
	************************************************ -->

	<!--[if gte mso 9]>
	<style>
		/* Target Outlook 2007 and 2010 */
	</style>
	<![endif]-->
</head>
<body>
	<!-- Wrapper/Container Table: Use a wrapper table to control the width and the background color consistently of your email. Use this approach instead of setting attributes on the body tag. -->
	<table cellpadding="0" cellspacing="0" border="0" id="backgroundTable">
	<tr>
		<td>

		<!-- Tables are the most common way to format your email consistently. Set your table widths inside cells and in most cases reset cellpadding, cellspacing, and border to zero. Use nested tables as a way to space effectively in your message. -->
		<table cellpadding="0" cellspacing="0" border="0" align="center">
			<tr>
				<td width="200" valign="top"></td>
				<td width="200" valign="top" class="message">
				<div class="m_logo"><img src="<? echo $basic_var['company_logo'] ?>"></div>
				<div><?echo $message?></div>
				<div class="footer">
			<div class="contact">
			
			<a href="www.kiterepublic.com.au">Kite Republic</a>
			
			
			<br/>Shop: 03 9537 0644
			
			</div>
			<div class="address"><p>
			St Kilda Seabaths Complex<br/>
			4/10-18 Jacka Bvd,<br/>
			St Kilda, 3182</p></div>
		</div>
				</td>
				<td width="200" valign="top"></td>
			</tr>
		</table>
		

		</td>
	</tr>
	</table>
	<!-- End of wrapper table -->
</body>
</html>
