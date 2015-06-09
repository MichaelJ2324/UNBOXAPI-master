<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>UNBOX API</title>
    <?php echo Asset::css('bootstrap.min.css'); ?>
    <?php echo Asset::css('font-awesome.css'); ?>
    <?php echo Asset::css('datepicker.css'); ?>
    <?php echo Asset::css('select2.css'); ?>
    <?php echo Asset::css('select2-bootstrap.css'); ?>
    <?php echo Asset::css('unbox_api.css'); ?>

    <meta name="google-signin-clientid" content="105682255558-0660ol2o50kbflotg0t6dnejpp3cd922.apps.googleusercontent.com" />
    <meta name="google-signin-scope" content="https://www.googleapis.com/auth/plus.login" />
    <meta name="google-signin-requestvisibleactions" content="http://schema.org/AddAction" />
    <meta name="google-signin-cookiepolicy" content="single_host_origin" />
    <meta name="google-signin-callback" content="googleLoginWrapper" />
    <link rel="icon"
          type="image/png"
          href="<?php echo Uri::base(false).Asset::find_file('logo_dark_ico.png', 'img'); ?>">
    <style>
    </style>
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div>
        <div class="navbar-header pull-left">
            <a href="<?php echo Uri::base(false)."/"; ?>" >
                <span class="navbar-brand pull-left logo" style="color: #ffffff;padding: 10px 15px 0px 15px;">
                    UNBOX
                    <span class='header-icon'>
                        <?php echo Asset::img("logo_light_small.png"); ?>
                    </span>
                    <span style="color:#985A55;"><b>API</b></span>
                </span>
            </a>
        </div>
        <div class="collapse navbar-collapse pull-left">
            <ul class="nav navbar-nav" id="mainNav">

            </ul>
        </div>
        <div class="collapse navbar-collapse pull-right">
            <ul class="nav navbar-nav" id="rightNav">

            </ul>
        </div>
    </div>
</div>
<div id="notices">

</div>
<div id="layout">

</div>
<?php echo Asset::js('jquery-1.11.1.min.js'); ?>
<?php echo Asset::js('underscore-min.js'); ?>
<?php echo Asset::js('backbone-min.js'); ?>
<?php echo Asset::js('bootstrap.min.js'); ?>
<?php echo Asset::js('bootstrap-datepicker.js'); ?>
<?php echo Asset::js('select2.min.js'); ?>
<script src="https://www.google.com/recaptcha/api.js?&render=explicit" async defer></script>
<!--<script src="https://plus.google.com/js/client:platform.js" async defer></script>-->
<?php echo Asset::js('unbox_api.js'); ?>

<script type="text/javascript">
    UNBOX = new UNBOXAPI.App({
		//id: <?php //echo $session; ?>,
        user: <?php echo ($user===null?"null":"'$user'"); ?>
    });
    UNBOX.start();

</script>

</body>
</html>
