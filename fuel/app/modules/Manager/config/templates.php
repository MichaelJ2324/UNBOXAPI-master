<?php
return array(
    'Actions' => "
        <div class='panel-group' id='admin_actions_accordion'>
            <% _.each(modules,function(module){ %>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <h4 class='panel-title'>
                        <a data-toggle='collapse' data-parent='#admin_actions_accordion' href='#<%= module.get('name') %>_actions'>
                            <%= module.get('name') %>
                        </a>
                    </h4>
                </div>
                <div class='panel-collapse collapse <%  if (default_module===false){
                                                            var options = module.get('options');
                                                            if (options!=null) {
                                                                var manager_default = typeof options['manager_default'] !== 'undefined' ? options['manager_default'] : '';
                                                                if (manager_default==true){
                                                                    var open='in';
                                                                }else{
                                                                    var open='';
                                                                }
                                                            }
                                                        }else{
                                                            if (module.get('name')==default_module){
                                                                var open='in';
                                                            }
                                                        }%>
                               <%= open %> ' id='<%= module.get('name') %>_actions'>
                    <div class='panel-body'>
                        <div class='col-xs-4 text-center'>
                            <a class='btn btn-primary ' href='#manage/<%= module.get('name') %>/list' >List <span class='glyphicon glyphicon-th-list'></span></a>
                        </div>
                        <div class='col-xs-4 text-center'>
                            <a class='btn btn-primary ' href='#manage/<%= module.get('name') %>/create' >Create <span class='glyphicon glyphicon-plus'></span></a>
                        </div>
                        <div class='col-xs-4 text-center'>
                            <a class='btn btn-primary ' href='#manage/<%= module.get('name') %>/import' >Import <span class='glyphicon glyphicon-upload'></span></a>
                        </div>
                    </div>
                </div>
            </div>
            <% }) %>
            <div class='panel panel-default'>
                <div class='panel-heading'>
                    <b>System</b>
                </div>
                <div class='panel-body'>
                    <div class='col-lg-12'>
                        <div class='row btn-row'>
                            <button class='btn btn-primary btn-block'>Load Package (.zip)</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>",
    'Output' => "
        <div class='panel panel-default' id='filter_panel'>
            <div class='panel-heading'>
                Filter List
            </div>
            <div class='panel-body' id='filters_panel_body'>
                <div class='col-lg-12'>
                    <form id='filters' class='form-inline'>
                        <div class='form-group col-md-3'>
                            <label>Name</label>
                            <input type='text' id='name' name='name' class='form-control' placeholder='EntryPoint Name' />
                        </div>
                        <div class='form-group col-md-3'>
                            <label>Application</label>
                            <select id='application' name='application' class='form-control select2' placeholder='Choose Application' >
                                <option value='NULL'></option>
                            </select>
                        </div>
                        <div class='form-group col-md-3'>
                            <label>API</label>
                            <select id='api' name='api' class='form-control select2' placeholder='Choose API' >
                                <option value='NULL'></option>
                            </select>
                        </div>
                        <div class='form-group col-md-3'>
                            <label>HTTP Method</label>
                            <select id='httpMethod' name='httpMethod' class='form-control select2' placeholder='Choose Method' >
                                <option value='NULL'></option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class='panel panel-default' id='list_panel'>
            <div class='panel-body' id='list_panel_body'>
                <table class='table table-responsive table-striped table-bordered table-hover' id='ep_table'>
                    <thead>
                    <tr>
                        <th style='width: 25px;'>
                            <div class='input-group'  style='width: 25px;'>
                                <span class='input-group-addon'>
                                    <input type='checkbox' name='select_all' id='select_all' />
                                </span>
                                <div class='input-group-btn'>
                                    <button type='button' class='btn btn-default dropdown-toggle' data-toggle='dropdown'><span class='caret'></span></button>
                                    <ul class='dropdown-menu'>
                                        <li><a href='#'>Delete</a></li>
                                        <li><a href='#'>Mass Update</a></li>
                                        <li><a href='#'>Export</a></li>
                                    </ul>
                                </div><!-- /btn-group -->
                            </div><!-- /input-group -->
                        </th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Method</th>
                        <th style='border-right: 0px;'>URL</th>
                        <th style='border-left: 0px;'>
                            <span class='pull-right'>
                                <button class='btn btn-link' id='prevBtn'><span class='glyphicon glyphicon-chevron-left'></span></button>
                                <span id='offset'>1</span>-<span id='next_offset'>20</span>
                                <button class='btn btn-link' id='nextBtn'><span class='glyphicon glyphicon-chevron-right'></span></button>
                            </span>
                        </th>
                    </tr>
                    </thead>
                    <tbody id='list'>
    
                    </tbody>
                </table>
            </div>
        </div>",
    'List' => "
        <% _.each(entryPoints,function(entryPoint){ %>
            <tr>
                <td><input type='checkbox' value='<%= entryPoint.escape('id') %>' /></td>
                <td><%= entryPoint.escape('name') %></td>
                <td><%= entryPoint.escape('description') %></td>
                <td><%= entryPoint.escape('method_name') %></td>
                <td colspan='2'><%= entryPoint.escape('url') %></td>
            </tr>
        <% }) %>",
    'Record' => "
        <div class='panel panel-default'>
            <div class='panel-heading'>
                <h4 class='panel-title'> <%= currentModule.get('label') %></h4>
            </div>
            <div class='panel-body'>
                <div class='col-lg-12'>
                    <form id='quickRecord'>
                        <div class='row'>
                            <%= actions %>
                        </div>
                        <% _.each(fields, function(field){ %>
                            <div class='row'>
                                <%= field %>
                            </div>
                        <% }) %>
                    </form>
                </div>
            </div>
        </div>
    ",
    "RecordActions" => "
        <span class='pull-right'>
            <div class='btn-group'>
              <button type='button' class='btn btn-primary' id='save'>Save</button>
              <button type='button' class='btn btn-primary dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                <span class='caret'></span>
              </button>
              <ul class='dropdown-menu' role='menu' style='right: 0px;'>
                <li><a href='#' id='fullForm'>Full Form</a></li>
                <li><a href='#' id='clear'>Clear Form</a></li>
                <li class='divider'></li>
                <li><a href='#' id=''>Quick Relate</a></li>
              </ul>
            </div>
        </span>
    "
);
?>