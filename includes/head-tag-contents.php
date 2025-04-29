<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="">

<link rel="shortcut icon" href="/images/logo_conabio.png" type="image/x-icon">

<title>SNIB MX</title>

<!-- Bootstrap core CSS -->
<link href="/snibmx/css/bootstrap.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="/snibmx/css/navbar-top-fixed.css" rel="stylesheet">
<link href="/snibmx/css/conabio.css" rel="stylesheet">
<?php 
 if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
 $ip=$_SERVER['HTTP_CLIENT_IP'];}
 elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
 $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];} else {
 $ip=$_SERVER['REMOTE_ADDR'];}
?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-8226401-20"></script>
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-8226401-20', 'auto');
    ga('send', 'pageview', {
      'dimension1':  '<?=$ip;?>'
    });
/*window.dataLayer = window.dataLayer || [];
function gtag(){dataLayer.push(arguments);}
gtag('js', new Date());

gtag('config', 'UA-8226401-20');*/
</script>
