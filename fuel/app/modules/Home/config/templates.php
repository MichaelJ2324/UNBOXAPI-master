<?php
return array(
    'Login' => "
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <div class='row' style='text-align: center;'><h4>Login</h4></div>
            </div>
            <div class='panel-body'>
                <form class='form-login'>
                    <label for='username'>Username</label>
                    <input type='text' id='username' name='username' class='form-control' placeholder='Username' required='true' autofocus='true' value=''>
                    <label for='password'>Password</label>
                    <input type='password' id='password' name='password' class='form-control' placeholder='Password' required='true'>
                    <div class='checkbox'>
                      <label>
                        <input type='checkbox' value='remember' id='remember' name='remember'> Remember Me
                      </label>
                    </div>
                    <div class='col-md-4'>
                        <button class='btn btn-primary btn-block' type='button' id='login'>Login</button>
                    </div>
                    <div class='col-md-4'>
                        <button class='btn btn-default btn-block' type='button' id='register'>Register</button>
                    </div>
                    <div class='col-md-4'>
                        <div id='gLoginWrapper'>
                            <div id='gLogin' class=''></div>
                        </div>
                    </div>
              </form>
          </div>
      </div>
    ",
    'Register' => "
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <div class='row' style='text-align: center;'><h4>Register</h4></div>
            </div>
            <div class='panel-body'>
                <div class='col-lg-12'>
                    <form class='form-register'>
                        <label for='first_name'>First Name</label>
                        <input type='text' id='first_name' name='first_name' class='form-control' placeholder='First Name' required='true' autofocus='true'>
                        <label for='last_name'>Last Name</label>
                        <input type='text' id='last_name' name='last_name' class='form-control' placeholder='Last Name' required='true' autofocus='true'>
                        <label for='email'>Email</label>
                        <input type='text' id='email' name='email' class='form-control' placeholder='email@example.com' required='true' autofocus='true'>
                        <label for='username'>Username</label>
                        <input type='text' id='username' name='username' class='form-control' placeholder='Username' required='true' autofocus='true'>
                        <label for='password'>Password</label>
                        <input type='password' id='password' name='password' class='form-control' placeholder='Password' required='true'>
                        <div>
                            <div id='captcha'></div>
                        </div>
                        <button class='btn btn-primary' type='button' id='Register'>Submit</button>
                  </form>
              </div>
          </div>
      </div>",
    "Profile" => "
        <div class='profile-overview'>
            <div class='ep-logo circle'>
                <span class='ep-logo-text'>
                    Pr
                </span>
            </div>
            <div class='profile-main-detail' id='profile_main'>

            </div>
            <div class='profile-settings'>

            </div>
        </div>
    ",
    //TODO::Add Handlebar templates for Profile data
    "ProfileMain" => "",
    "ProfileSettings" => "",
    //TODO::Add Activity Stream template for Home Page main stuff
    "RecentActivity" => ""
);

?>