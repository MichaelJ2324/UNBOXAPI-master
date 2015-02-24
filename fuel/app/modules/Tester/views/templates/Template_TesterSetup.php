<script type="text/template" id="TesterSetup">
    <div class="panel panel-default">
        <div class="panel-heading">
            Choose Entry Point
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <form id='Tester_Setup'>
                    <div class="row">
                        <label>Application</label>
                        <select id="application" name="application" class="form-control select2" placeholder="Choose Application" >
                            <option value="NULL"></option>
                        </select>
                    </div>
                    <div class="row">
                        <label>API</label>
                        <select id="api" name="api" class="form-control select2" placeholder="Choose API" disabled="disabled" >
                            <option value="NULL"></option>
                        </select>
                    </div>
                    <div class="row">
                        <label>HTTP Method</label>
                        <select id="httpMethod" name="httpMethod" class="form-control select2" placeholder="Choose Method" disabled="disabled" >
                            <option value="NULL"></option>
                        </select>
                    </div>
                    <div class="row">
                        <label>Entry Point</label>
                        <select id="entryPoint_select" name="entryPoint_select" class="form-control select2" placeholder="Choose EntryPoint" disabled="disabled" >
                            <option value="NULL"></option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            API Login
        </div>
        <div class="panel-body" id="api_login">
            <div class="flipper">
                <div class="front">
                    <button type="button" class="btn btn-link pull-right hidden" id="logged_in_info">View Login Token</button>
                    <label for='web_address'>Web Address</label>
                    <input id="web_address" name="web_address" class="form-control" placeholder="http://www.example.com" >
                    <label for='login_method'>Login Method</label>
                    <select id="login_method" name="login_method" class="form-control select2" placeholder="Choose Login Method" disabled="disabled" >
                        <option value="NULL"></option>
                    </select>
                    <form id='API_Login_form' class="form-horizontal hidden" role="form">
                        <div id='login_normal'>
                        </div>
                        <div id='login_advanced' class='panel-collapse collapse'>
                        </div>
                        <a id="login_advanced_btn" data-toggle="collapse" data-target="#login_advanced">Advanced+</a>
                        <br>
                        <button type='button' id='loginBtn' class='btn btn-primary'>Login</button>
                        <button type='button' id='logOutBtn1' class='btn btn-default logout' disabled='true'>Logout</button>
                    </form>
                </div>
                <div class="back" id="api_login_info">

                </div>
            </div>
        </div>
    </div>
</script>