<!DOCTYPE html>
<html lang="en">
<head>
    <title>Home</title>
    <meta charset="utf-8">
    <meta name = "format-detection" content = "telephone=no" />
    <link rel="icon" href="<?php echo $this->webroot; ?>images/favicon.ico" type="image/x-icon">
    <link rel="shortcut icon" href="<?php echo $this->webroot; ?>images/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="<?php echo $this->webroot; ?>css/reset.css">
    <link rel="stylesheet" href="<?php echo $this->webroot; ?>css/style.css">

    <!--[if lt IE 8]>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->webroot; ?>css/ie.css">
    <link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400italic' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans:700' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans:800' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans+Condensed:300' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Sanchez' rel='stylesheet' type='text/css'>
    <div style=' clear: both; text-align:center; position: relative;'>
        <a href="http://windows.microsoft.com/en-US/internet-explorer/products/ie/home?ocid=ie6_countdown_bannercode">
            <img src="http://storage.ie6countdown.com/assets/100/images/banners/warning_bar_0000_us.jpg" border="0" height="42" width="820" alt="You are using an outdated browser. For a faster, safer browsing experience, upgrade for free today." />
        </a>
    </div>
    <![endif]-->
    <!--[if lt IE 9]>
    <script src="<?php echo $this->webroot; ?>js/html5shiv.js"></script>
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo $this->webroot; ?>css/ie.css">
    <![endif]-->

    <style>
        html,
        body {
            background: transparent;
        }

        body {
            background: url(http://setupcase.com/app/webroot/uploads/1/46/Paintings/Rivière-à-Simon-24X30.jpg) no-repeat top left;
            background-size: 100%;
        }

    </style>

</head>

<body id="splash">



<div class="splash-wrapper">
    <img src="<?php echo $this->webroot; ?>images/logo.png" alt="" class="logo"></a> </h1>
    <?php echo $this->Session->flash(); ?>
    <?php echo $this->fetch('content'); ?>
</div>

</body>
</html>