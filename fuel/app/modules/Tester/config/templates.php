<?php
return array(
    'LoginInfo' => "
        <div class='row'>
            <button type='button' class='btn btn-primary pull-left logout' id='logOutBtn2'>Logout</button>
            <button type='button' class='btn btn-link pull-right' id='return_LoginForm'>Return to Form</button>
        </div>
        <div class='row'>
            <table class='table table-responsive pull-left'>
                <% var attribute %>
                <% for(attribute in token.attributes) { %>
                    <tr>
                        <td>
                            <%= attribute %>:
                        </td>
                        <td>
                            <%= token.get(attribute) %>
                        </td>
                    </tr>
                <% } %>
            </table>
        </div>",
    'LoginParam' => "
        <% _.each(parameters,function(param){ %>
            <label class='control-label'><%= param.escape('name') %></label>
            <%= param.get('html') %>
        <% }) %>",
    'EntryPointOverview' => "
        <div class='entry-point-overview'>
            <div class='ep-logo circle'>
                <span class='ep-logo-text'>
                    Ep
                </span>
            </div>
            <div class='ep-main-detail' id='ep_main'>

            </div>
            <div class='entry-point-detail'>
                <div class='panel-group' id='ep_detail_accordion'>
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            <h4 class='panel-title'>
                                <a data-toggle='collapse' data-parent='#ep_detail_accordion' href='#EPparameters'>
                                    Parameters
                                </a>
                            </h4>
                        </div>
                        <div class='panel-collapse collapse in' id='EPparameters'>
                            <div class='panel-body' id='ep_parameters' >

                            </div>
                        </div>
                    </div>
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            <h4 class='panel-title'>
                                <a data-toggle='collapse' data-parent='#ep_detail_accordion' href='#EPexamples'>
                                    Examples
                                </a>
                            </h4>
                        </div>
                        <div class='panel-collapse collapse' id='EPexamples'>
                            <div class='panel-body' id='ep_examples'>
                            </div>
                        </div>
                    </div>
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            <h4 class='panel-title'>
                                <a data-toggle='collapse' data-parent='#ep_detail_accordion' href='#EPexceptions'>
                                    Exceptions
                                </a>
                            </h4>
                        </div>
                        <div class='panel-collapse collapse' id='EPexceptions'>
                            <div class='panel-body' id='ep_exceptions'>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class='ep-actions' id='ep_action1'>

        </div>
    ",
    "EntryPointActions" => "
        <% if (hasParams==true && panelNumber==2) { %>
            <button type='button' class='btn btn-primary pull-right' id='setupParams' >Setup Params</button>
        <% }else{ %>
            <button type='button' class='btn btn-primary pull-right' id='sendAPI' disabled='disabled' >Test EntryPoint</button>
            <button type='button' class='btn btn-primary pull-right' id='generateScript'>Generate Script</button>
        <% } %>
    ",
    "EntryPointMain" => "
        <table class='table table-responsive'>
            <tr>
                <td class='row-head'>Name:</td>
                <td class='ep-info'><%= _.escape(entryPoint.get('name')) %></td>
            </tr>
            <tr>
                <td class='row-head'>Method:</td>
                <td class='ep-info'><%= _.escape(entryPoint.getHttpMethod()) %></td>
            </tr>
            <tr>
                <td class='row-head'>URL:</td>
                <td class='ep-info'><%= _.escape(entryPoint.get('url')) %></td>
            </tr>
        </table>
        <div>
            <span class='row-head'>Description:</span>
            <p class='ep-info' id='ep_description'><%= _.escape(entryPoint.get('description')) %></p>
        </div>
    ",
    "EntryPointParameters" => "
        <div class='row'>
            <div class='col-sm-4'>
                <b>Type</b>
            </div>
            <div class='col-sm-5'>
                <b>Name</b>
            </div>
            <div class='col-sm-3'>
                <b>Req</b>
            </div>
        </div>
        <% _.each(parameters,function(p){ %>
            <div class='row'>
                <div class='col-sm-4'>
                    <%= _.escape(p.get('data_type').name) %>
                </div>
                <div class='col-sm-5'>
                    <%= _.escape(p.get('name')) %>
                </div>
                <div class='col-sm-3'>
                    <%= _.escape(p.get('required')) %>
                </div>
            </div>
        <% }); %>
    ",
    "EntryPointExamples" => "
        <div class='row'>
            <div class='col-sm-2'></div>
            <div class='col-sm-4'>
                <b>Name</b>
            </div>
            <div class='col-sm-4'>
                <b>Example</b>
            </div>
            <div class='col-sm-2'></div>
        </div>
        <% _.each(examples,function(example){ %>
            <div class='row'>
                <div class='col-sm-2'></div>
                <div class='col-sm-4'>
                    <%= _.escape(example.get('name')) %>
                </div>
                <div class='col-sm-4'>
                    <button type='button' class='btn-sm btn-primary ep-example-btn'><%= _.escape('</Example>') %></button>
                </div>
                <div class='col-sm-2'></div>
            </div>
        <% }); %>
    ",
    "EntryPointExceptions" => "
        <div class='row'>
            <div class='col-sm-4'>
                <b>Type</b>
            </div>
            <div class='col-sm-2'>
                <b>Code</b>
            </div>
            <div class='col-sm-6'>
                <b>Default Error</b>
            </div>
        </div>
        <% _.each(exceptions,function(exception){ %>
            <div class='row'>
                <div class='col-sm-4'>
                    <%= _.escape(exception.get('type')) %>
                </div>
                <div class='col-sm-5'>
                    <%= _.escape(exception.get('http_code')) %>
                </div>
                <div class='col-sm-3'>
                    <%= _.escape(exception.get('error')) %>
                </div>
            </div>
        <% }); %>
    ",
    "Output" => "
        <div class='panel panel-default' style='height: 100%; display: block;'>
            <div class='panel-body'>
                <ul class='nav nav-tabs' role='tablist'>
                    <li role='presentation' class=''><a href='#request' aria-controls='request' role='tab' data-toggle='tab'>Request</a></li>
                    <li role='presentation' class='active'><a href='#response' aria-controls='response' role='tab' data-toggle='tab'>Response</a></li>
                    <li role='presentation' class='dropdown'>
                        <a class='dropdown-toggle' data-toggle='dropdown' href='#' role='button' aria-expanded='false'>
                          Format <span class='caret'></span>
                        </a>
                        <ul class='dropdown-menu' role='menu'>
                            <li><a href='#' class='format_type' data-format='pretty'>Pretty</a></li>
                            <li><a href='#' class='format_type' data-format='raw'>Raw</a></li>
                        </ul>
                    </li>
                </ul>
                <div class='tab-content'>
                    <div role='tabpanel' class='tab-pane' id='request'>
                        <% if (request.get('request')==''||request.get('request')==null) {
                        }else{
                            if (style.get('name')=='pretty'){ %>
                                <%= '<pre>'+_.escape(JSON.stringify(jQuery.parseJSON(request.get('request')),undefined,2))+'</pre>' %>
                            <%      }else if (style.get('name')=='raw') { %>
                                <%= '<pre>'+request.escape('request')+'</pre>' %>
                            <%      }
                        } %>
                    </div>
                    <div role='tabpanel' class='tab-pane active' id='response'>
                        <% if (request.get('response')==''||request.get('response')==null) {
                            }else{
                                if (style.get('name')=='pretty'){ %>
                                    <%= '<pre>'+_.escape(JSON.stringify(jQuery.parseJSON(request.get('response')),undefined,2))+'</pre>' %>
                                <%      }else if (style.get('name')=='raw') { %>
                                    <%= '<pre>'+request.escape('response')+'</pre>' %>
                                <%      }
                            } %>
                    </div>
                </div>
            </div>
        </div>
    ",
    "ParameterForm" => "
        <div class='parameter-setup'>
            <form id='ParameterForm'>
                <div class='panel-group' id='ep_params_accordion'>
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            <h4 class='panel-title'>
                                <a data-toggle='collapse' data-parent='#ep_params_accordion' href='#EP_urlParams'>
                                    URL Parameters
                                </a>
                            </h4>
                        </div>
                        <div class='panel-collapse collapse in' id='EP_urlParams'>
                            <div class='panel-body' id='ep_url_params' >

                            </div>
                        </div>
                    </div>
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            <h4 class='panel-title'>
                                <a data-toggle='collapse' data-parent='#ep_params_accordion' href='#EP_requestParams'>
                                    Request Parameters
                                </a>
                            </h4>
                        </div>
                        <div class='panel-collapse collapse in' id='EP_requestParams'>
                            <div class='panel-body' id='ep_request_params'>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class='ep-actions' id='ep_action2'>
        </div>
    ",
    "Parameters" => "
        <% _.each(parameters,function(param){ %>
            <label><%= param.escape('name') %> <span class='text-muted'><%= param.escape('type') %></span></label>
            <%= param.get('html') %>
            <span class='help-block'><%= param.escape('description') %></span>
        <% }) %>
    ",
    "Setup" => "
        <div class='panel panel-default'>
            <div class='panel-heading'>
                Choose Entry Point
            </div>
            <div class='panel-body'>
                <div class='col-lg-12'>
                    <form id='Tester_Setup'>
                        <div class='row'>
                            <label>Application</label>
                            <select id='application' name='application' class='form-control select2' placeholder='Choose Application' >
                                <option value='NULL'></option>
                            </select>
                        </div>
                        <div class='row'>
                            <label>API</label>
                            <select id='api' name='api' class='form-control select2' placeholder='Choose API' disabled='disabled' >
                                <option value='NULL'></option>
                            </select>
                        </div>
                        <div class='row'>
                            <label>HTTP Method</label>
                            <select id='httpMethod' name='httpMethod' class='form-control select2' placeholder='Choose Method' disabled='disabled' >
                                <option value='NULL'></option>
                            </select>
                        </div>
                        <div class='row'>
                            <label>Entry Point</label>
                            <select id='entryPoint_select' name='entryPoint_select' class='form-control select2' placeholder='Choose EntryPoint' disabled='disabled' >
                                <option value='NULL'></option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class='panel-heading'>
                API Login
            </div>
            <div class='panel-body' id='api_login'>
                <div class='flipper'>
                    <div class='front'>
                        <button type='button' class='btn btn-link pull-right hidden' id='logged_in_info'>View Login Token</button>
                        <label for='web_address'>Web Address</label>
                        <input id='web_address' name='web_address' class='form-control' placeholder='http://www.example.com' >
                        <label for='login_method'>Login Method</label>
                        <select id='login_method' name='login_method' class='form-control select2' placeholder='Choose Login Method' disabled='disabled' >
                            <option value='NULL'></option>
                        </select>
                        <form id='API_Login_form' class='form-horizontal hidden' role='form'>
                            <div id='login_normal'>
                            </div>
                            <div id='login_advanced' class='panel-collapse collapse'>
                            </div>
                            <a id='login_advanced_btn' data-toggle='collapse' data-target='#login_advanced'>Advanced+</a>
                            <br>
                            <button type='button' id='loginBtn' class='btn btn-primary'>Login</button>
                            <button type='button' id='logOutBtn1' class='btn btn-default logout' disabled='true'>Logout</button>
                        </form>
                    </div>
                    <div class='back' id='api_login_info'>

                    </div>
                </div>
            </div>
        </div>
    "
);
?>