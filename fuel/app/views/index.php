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
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" id="modalDiaglog">
        <div class="modal-content" id="modalContent">
            <div class="modal-header">
                <button type="button" class="close" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalHead">
                </h4>
            </div>
            <div class="modal-body" id="modalBody">
            </div>
            <div class="modal-footer" id="modalFoot">
            </div>
        </div>
    </div>
</div>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div>
        <div class="navbar-header pull-left">
            <a href="<?php echo Uri::base(false)."/"; ?>" class="dropdown-toggle" data-toggle="dropdown">
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
            <ul class="nav navbar-nav" id="main-nav">

            </ul>
        </div>
        <div class="navbar-header pull-right">
            <ul class="nav navbar-nav">
                <li>
                    <a href="#tutorial">
                        <span class="glyphicon glyphicon-info-sign"></span>
                    </a>
                </li>
                <li>
                    <a href="#about">
                        <span class="glyphicon glyphicon-question-sign"></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<div id="notices">

</div>
<div class='un-color1 opaque un-panel un-panel-closed hidden' id='panel1'>
    <span data-panel='1' class='un-panel-toggle un-open-panel opaque un-color1' id='panel1_toggle'>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <span class='glyphicon glyphicon-chevron-right'></span>
    </span>
    <div class='un-panel-content hidden' id='panel1_content'>

    </div>
</div>
<div class='un-color2 opaque un-panel un-panel-closed hidden' id='panel2'>
    <span data-panel='2' class='un-panel-toggle un-open-panel opaque un-color2' id='panel2_toggle'>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <span class='glyphicon glyphicon-chevron-right'></span>
    </span>
    <div class='un-panel-content hidden' id='panel2_content'>

    </div>
</div>
<div class='un-color3 opaque un-panel un-panel-closed hidden' id='panel3'>
    <span data-panel='3' class='un-panel-toggle un-open-panel opaque un-color3' id='panel3_toggle'>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <span class='glyphicon glyphicon-chevron-right'></span>
    </span>
    <div class='un-panel-content hidden' id='panel3_content'>

    </div>
</div>
<div class='un-panel' id='main' style="width: 100%;">
    <div class='un-panel-content' style="padding-right: 15px;">

    </div>
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

</body>
</html>
