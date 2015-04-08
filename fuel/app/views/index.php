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
    <script src="https://www.google.com/recaptcha/api.js?&render=explicit" async defer></script>
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
<div id="drawer" class="hidden">
    <div id="drawerContent"></div>
    <a href='#' class='drawer-close-btn'><span class='glyphicon glyphicon-chevron-up'></span></a>
</div>
<div class='un-color1 opaque un-panel un-panel-closed hidden' id='panel1'>
    <a href='#' data-panel='1' class='un-panel-toggle un-open-panel opaque un-color1' id='panel1_toggle'>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <span class='glyphicon glyphicon-chevron-right'></span>
    </a>
    <div class='un-panel-content hidden' id='panel1_content'>

    </div>
</div>
<div class='un-color2 opaque un-panel un-panel-closed hidden' id='panel2'>
    <a href='#' data-panel='2' class='un-panel-toggle un-open-panel opaque un-color2' id='panel2_toggle'>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <span class='glyphicon glyphicon-chevron-right'></span>
    </a>
    <div class='un-panel-content hidden' id='panel2_content'>

    </div>
</div>
<div class='un-color3 opaque un-panel un-panel-closed hidden' id='panel3'>
    <a href='#' data-panel='3' class='un-panel-toggle un-open-panel opaque un-color3' id='panel3_toggle'>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <span class='glyphicon glyphicon-chevron-right'></span>
    </a>
    <div class='un-panel-content hidden' id='panel3_content'>

    </div>
</div>
<div class='un-panel' id='main' style="width: 100%;">
    <div class='un-panel-content' style="padding-right: 15px;">

    </div>
</div>

<script type="text/template" id="navBtns">
    <li id='homeLi' class='dropdown active'>
        <a href='<%= current.get('link') %>' style='float: left;'>
        <%= current.get('icon') %>
        <%= current.escape('name') %>
        </a>
        <% if (modules.length>1){ %>
        <a href='#' class='dropdown-toggle' data-toggle='dropdown' style='float: right; padding-left: 0px;'>
            <span class='caret'></span>
        </a>
        <ul class='dropdown-menu' role='menu'>
            <% _.each(modules,function(module){
            if (module.get('name')!==current.get('name')){
            %>
            <li><a href='<%= module.get('link') %>'><%= module.get('icon') %><%= module.escape('name') %></a></li>
            <%      }
            }) %>
        </ul>
        <% } %>
    </li>
    <% _.each(links,function(link){ %>
    <li>
        <a href='<%= link.link %>'><%= link.icon %><%= _.escape(link.name) %></a>
    </li>
    <% }) %>
</script>
<script type="text/template" id="panel">
    <a href='#' data-panel='<%= num %>' class='un-panel-toggle un-open-panel opaque un-color<%= num %>' id='panel<%= num %>_toggle'>
        <span class='glyphicon glyphicon-chevron-right'></span>
        <span class='glyphicon glyphicon-chevron-right'></span>
    </a>
    <div class='un-panel-content' id='panel<%= num %>_content'>
    </div>
</script>
<script type="text/template" id="notice">
    <span class='notice notice-<%= notice.get('type') %> alert-dismissible' role='alert' id='notice_<%= id %>' >
        <button type='button' class='notice-close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
        <span class='notice-text'><%= notice.get('message') %></span>
    </span>
</script>
<?php echo Asset::js('jquery-1.11.1.min.js'); ?>
<?php echo Asset::js('underscore-min.js'); ?>
<?php echo Asset::js('backbone-min.js'); ?>
<?php echo Asset::js('bootstrap.min.js'); ?>
<?php echo Asset::js('bootstrap-datepicker.js'); ?>
<?php echo Asset::js('select2.min.js'); ?>
<script src="https://plus.google.com/js/client:platform.js" async defer></script>
<?php echo Asset::js('unbox_api.js'); ?>

</body>
</html>
