<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) && $_SERVER['HTTP_CF_CONNECTING_IP'] == '37.26.61.22') :

?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Database Error</title>
<style type="text/css">

::selection { background-color: #E13300; color: white; }
::-moz-selection { background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	box-shadow: 0 0 8px #D0D0D0;
}

p {
	margin: 12px 15px 12px 15px;
}
</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<?php echo $message; ?>
	</div>
</body>
</html>
<?php else:
 ?>
<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <title>404 Page Not Found</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <style type="text/css">

            ::selection { background-color: #E13300; color: white; }
            ::-moz-selection { background-color: #E13300; color: white; }

            body {
                margin:0;
                font: 13px/20px normal Helvetica, Arial, sans-serif;
            }

            a {
                color: #003399;
                background-color: transparent;
                font-weight: normal;
            }

            code {
                font-family: Consolas, Monaco, Courier New, Courier, monospace;
                font-size: 12px;
                background-color: #f9f9f9;
                border: 1px solid #D0D0D0;
                color: #002166;
                display: block;
                margin: 14px 0 14px 0;
                padding: 12px 10px 12px 10px;
            }

            #container {
                margin: 10px;
                border: 1px solid #D0D0D0;
                box-shadow: 0 0 8px #D0D0D0;
            }

            p {
                margin: 12px 15px 12px 15px;
            }
            .cover_404{
                width:100%;
                display:flex;
                justify-content:flex-start;
            }
            ._left_404{
                height:100vh;
                width:40%;
                background-image:url('/templates/mimelon/assets/img/404/_left_404.png');
                background-repeat:no-repeat;
                background-size:cover;
                position:relative;
                display:flex;
                justify-content:center;
                align-items:center;
            }
            ._right_404{
                height:100vh;
                width:60%;
                background-image:url('/templates/mimelon/assets/img/404/_right_404.jpg');
                background-repeat:no-repeat;
                background-size:cover;
            }
            ._logo_404{
                width:100%;
                text-align:center;
                position:absolute;
                top:45px;
            }
            ._social_404 li{
                display:inline-block;
            }
            ._social_404 li a{
                display:inline-flex;
                justify-content:center;
                align-items:center;
                width:26px;
                height:26px;
                border-radius:50%;
                background:#fff;
                text-decoration:none;
                margin-left:2px;
                margin-right:2px;
                transition:all .4s ease;
                -webkit-transition:all .4s ease;
                -ms-transition:all .4s ease;
                -moz-transition:all .4s ease;
            }
            ._social_404 li a:hover{
                background-color:#000;
            }
            ._social_404 li a:hover i{
                color:#fff;
            }
            ._social_404{
                margin-bottom:20px;
                padding-left:0;
                text-align:center;
            }
            ._social_404:before{
                content:"";
                display:block;
                width:200px;
                height:1px;
                background-color:#f5f5f5;
                margin-bottom:20px;
            }
            ._social_404 li a i{
                color:#000;
            }
            ._content_404 h1{
                color:#ebc733;
                font-size:90px;
                text-align:center;
                margin-bottom:40px;
            }
            ._content_404 h2{
                color:#ebc733;
                font-size:32px;
                padding-top:0;
                margin-top:0;
                text-align:center;
            }
            @media screen and (max-width:992px){
                ._right_404{
                    display:none;
                }
                ._left_404{
                    width:100%;
                }
                ._logo_404{
                    margin-top:80px;
                }
                ._logo_404 img{
                    width:100px;
                    transform:scale(3);
                    -webkit-transform:scale(3);
                    -ms-transform:scale(3);
                    -moz-transform:scale(3);
                }
                ._content_404 h1{
                    font-size: 272px;
                    margin-bottom:150px;
                }
                ._social_404:before{
                    width:100%;
                    margin-bottom:40px;
                }
                ._content_404 h2{
                    font-size:70px;
                }
                ._social_404 li a{
                    width:80px;
                    height:80px;
                }
                ._social_404 li a i{
                    font-size:40px;
                }
                ._social_404 li a{
                    margin-left:8px;
                    margin-right:8px;
                }
                body{
                    overflow:hidden;
                }
                ._left_404{
                    min-height:100vh;
                    height:auto;
                    overflow:auto;
                }
                ._content_404{
                    margin-top:160px;
                    overflow:auto;
                }
            }
            @media screen and (max-width:992px) and (max-height:900px){
                ._content_404 h1{
                    font-size: 90px;
                    margin-bottom:60px;
                }
                ._content_404 h2{
                    font-size:40px;
                }
                ._logo_404 img{
                    width:100px;
                    transform:scale(1);
                    -webkit-transform:scale(1);
                    -ms-transform:scale(1);
                    -moz-transform:scale(1);
                }
                ._social_404 li a{
                    width:40px;
                    height:40px;
                    margin-left:4px;
                    margin-right:4px;
                }
                ._social_404 li a i{
                    font-size:16px;
                }
                ._social_404{
                    margin-bottom:16px;
                }
            }
        </style>
    </head>
    <body>
    <div class="container-fluid cover_404">
        <div class="_left_404">
            <div class="_logo_404">
                <a href="/"><img src="/templates/mimelon/assets/img/icons/logo-mimelon.svg" alt=""></a>
            </div>
            <div class="_content_404">
                <h1>404</h1>
                <h2>Not found</h2>
                <ul class="_social_404">
                    <li><a href="http://" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="http://" target="_blank" rel="noopener noreferrer"><i class="fa fa-instagram"></i></a></li>
                    <li><a href="http://" target="_blank" rel="noopener noreferrer"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="http://" target="_blank" rel="noopener noreferrer"><i class="fa fa-google-plus"></i></a></li>
                </ul>
            </div>
        </div>
        <div class="_right_404"></div>
    </div>
    </body>
    </html>

<?php endif; ?>
