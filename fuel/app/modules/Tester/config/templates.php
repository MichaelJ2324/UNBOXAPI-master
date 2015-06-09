<?php
return array(
    "Panel1" => "
        <div class='panel panel-default'>
            <div class='panel-heading'>
                Build Test
            </div>
            <div class='panel-body'>
                <div class='col-lg-12'>
                    <form id='Tester_Setup'>
                        <div class='row'>
                            <label>Application</label>
                            <input type='hidden' class='form-control select2 relate' id='application' name='application_id' data-module='Applications'>
                        </div>
                        <div class='row'>
                            <label>API</label>
                            <input type='hidden' class='form-control select2 relate' id='api' name='api_id' data-module='Apis'>
                        </div>
                        <div class='row'>
                            <label>HTTP Method</label>
                            <input type='hidden' class='form-control select2 relate' id='http_method' name='http_method_id' data-module='HttpMethods'>
                        </div>
                        <div class='row'>
                            <label>Entry Point</label>
                            <input type='hidden' class='form-control select2 relate' id='entry_point' name='entry_point_id' data-module='Entrypoints'>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class='panel panel-default'>
            <div class='panel-heading'>
                API Login
            </div>
            <div class='panel-body'>
                <label for='login_id'>Login Method</label>
                <input type='hidden' class='form-control select2 relate' id='login' name='login_id' data-module='Logins'>
                <div class='col-md-12' id='api_login'>

                </div>
            </div>
        </div>
    ",
    "LoginInfo" => "
        <ul class='nav nav-tabs'>
            <li role='presentation' class='active'><a href='#login_form_container'>Form</a></li>
            <li role='presentation' class='disabled'><a href='#login_info'>Login Info</a></li>
        </ul>
        <div class='tab-content'>
            <div role='tabpanel' class='tab-pane' id='login_form_container'>
                <form id='login_form' class='form-horizontal hidden' role='form'>
                </form>
            </div>
            <div role='tabpanel' class='tab-pane' id='token_info'>
            </div>
        </div>
    ",
    "LoginForm" => "
        <div id='login_normal'>
        </div>
        <div id='login_advanced' class='panel-collapse collapse'>
        </div>
        <a id='login_advanced_btn' data-toggle='collapse' data-target='#login_advanced'>Advanced+</a>
        <br>
        <button type='button' id='loginBtn' class='btn btn-primary'>Login</button>
    ",
    'TokenInfo' => "
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
    'Panel2' => "
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
        <div class='ep-actions' id='ep_actions'>
			<button type='button' class='btn btn-primary pull-right' id='setupRequest'>Setup Request</button>
        </div>
    ",
    "EntrypointMain" => "
        <table class='table table-responsive'>
            <tr>
                <td class='row-head'>Name:</td>
                <td class='ep-info'><%= _.escape(entrypoint.get('name')) %></td>
            </tr>
            <tr>
                <td class='row-head'>Method:</td>
                <td class='ep-info'><%= _.escape(entrypoint.get('method')) %></td>
            </tr>
            <tr>
                <td class='row-head'>URL:</td>
                <td class='ep-info'><%= _.escape(entrypoint.get('url')) %></td>
            </tr>
        </table>
        <div>
            <span class='row-head'>Description:</span>
            <p class='ep-info' id='ep_description'><%= _.escape(entrypoint.get('description')) %></p>
        </div>
    ",
    "EntrypointParameters" => "
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
    "EntrypointExamples" => "
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
    "EntrypointExceptions" => "
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
	"Panel3" => "
        <div class='request-setup' style='height: 93%'>
			<div class='panel-group' id='ep_request_accordion'>
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<h4 class='panel-title'>
							<a data-toggle='collapse' data-parent='#ep_request_accordion' href='#EP_requestSetup'>
								Request Setup
							</a>
						</h4>
					</div>
					<div class='panel-collapse collapse in' id='EP_requestSetup'>
						<div class='panel-body' id='ep_request_info' >
						</div>
					</div>
				</div>
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<h4 class='panel-title'>
							<a data-toggle='collapse' data-parent='#ep_request_accordion' href='#EP_urlParams'>
								EntryPoint URL
							</a>
						</h4>
					</div>
					<div class='panel-collapse collapse' id='EP_urlParams'>
						<form id='URLParams'>
							<div class='panel-body' id='ep_url_params' >
							</div>
						</form>
					</div>
				</div>
				<div class='panel panel-default'>
					<div class='panel-heading'>
						<h4 class='panel-title'>
							<a data-toggle='collapse' data-parent='#ep_request_accordion' href='#EP_requestParams'>
								Request Payload
							</a>
						</h4>
					</div>
					<div class='panel-collapse collapse' id='EP_requestParams'>
						<form id='RequestPayload'>
							<div class='panel-body' id='ep_request_params'>
							</div>
						</form>
					</div>
				</div>
			</div>
        </div>
        <div class='test-actions' id='test_actions'>
        	<button type='button' class='btn btn-primary pull-right' id='sendAPI' disabled='disabled' >Send Request</button>
            <button type='button' class='btn btn-primary pull-right' id='generateScript'>Generate Script</button>
        </div>
    ",
	"RequestInfo" => "
		<label for='web_address'>Base URL</label>
		<input type='text' class='form-control' id='web_address' name='web_address' value='<%= web_address.getValue() %>' />
	",
    "Parameters" => "
        <% _.each(parameters,function(param){ %>
            <label><%= param.escape('name') %> <span class='text-muted'><%= param.escape('type') %></span></label>
            <%= param.get('html') %>
            <span class='help-block'><%= param.escape('description') %></span>
        <% }) %>
    ",
    "Main" => "
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
                        <% if (request==''||request==null) {
                        }else{
                            if (style.get('name')=='pretty'){ %>
                                <%= '<pre>'+_.escape(JSON.stringify(jQuery.parseJSON(request),undefined,2))+'</pre>' %>
                            <%      }else if (style.get('name')=='raw') { %>
                                <%= '<pre>'+_.escape(request)+'</pre>' %>
                            <%      }
                        } %>
                    </div>
                    <div role='tabpanel' class='tab-pane active' id='response'>
                        <% if (response==''||response==null) {
                            }else{
                                if (style.get('name')=='pretty'){ %>
                                    <%= '<pre>'+_.escape(JSON.stringify(jQuery.parseJSON(response),undefined,2))+'</pre>' %>
                                <%      }else if (style.get('name')=='raw') { %>
                                    <%= '<pre>'+_.escape(response)+'</pre>' %>
                                <%      }
                            } %>
                    </div>
                </div>
            </div>
        </div>
    ",
);
?>