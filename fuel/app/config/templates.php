<?php
return array(
    'label' => "<label for='<%= _.escape(name) %>'><%= _.escape(text) %></label>",
    'help' => "<span class='help-block'><%= _.escape(text) %></span>",
    'text' => "<input type='text'
                    class='form-control <%= input.escape('class') %>'
                    placeholder='<%= input.escape('placeholder') %>'
                    value='<%= (value||input.escape('value')) %>'
                    id='<%= input.escape('id')  %>'
                    name='<%= input.escape('name')  %>'
                    <%= (input.get('disabled') == 'disabled' ? input.escape('disabled') : (input.escape('disabled')==true ? 'disabled' : '' )) %>
                    <%= (input.get('required') == 'required' ? input.escape('required') : (input.escape('required')==true ? 'required' : '' )) %>
                    />",
    'select' => "<select
                    class='form-control <%= input.escape('class') %>'
                    placeholder='<%= input.escape('placeholder') %>'
                    id='<%= input.escape('id')  %>'
                    name='<%= input.escape('name')  %>'
                    <%= (input.get('disabled') == 'disabled' ? input.escape('disabled') : (input.get('disabled')==true ? 'disabled' : '' )) %>
                    <%= (input.get('required') == 'required' ? input.escape('required') : (input.get('required')==true ? 'required' : '' )) %>
                    >
                    <% if (input.options!==false){
                            _.each(input.options.models,function(option){ %>
                            <option value='<%= option.get('key') %>'
                               <%= (value==option.get('key')?'selected':((input.get('value')==option.get('key')&&value=='')?'selected':'')) %> ><%= _.escape(option.get('value')) %></option>
                        <% })
                    }%>
                  </select>",
    'relate' => "<input type='hidden'
                    class='form-control select2 relate'
                    id='<%= input.escape('id')  %>'
                    name='<%= input.escape('name')  %>'
                    data-module='<%= input.escape('module') %>'
                    >",
    'checkbox' => "<input type='hidden' value='0'
                        id='<%= input.escape('id')  %>'
                        name='<%= input.escape('name')  %>'
                    />
                    <input type='checkbox' value='1'
                        class='form-control <%= input.escape('class') %>'
                        placeholder='<%= input.escape('placeholder') %>'
                        id='<%= input.escape('id')  %>'
                        name='<%= input.escape('name')  %>'
                        <%= ((value==true||value==1)?'checked':(((input.get('value')==true||input.get('value')==1)&&value=='') ? 'checked' : '')) %>
                    />",
    'textarea' => "<textarea rows='5'
                        class='form-control <%= input.escape('class') %>'
                        placeholder='<%= input.escape('placeholder') %>'
                        id='<%= input.escape('id')  %>'
                        name='<%= input.escape('name')  %>'
                        <%= (input.get('disabled') == 'disabled' ? input.escape('disabled') : (input.escape('disabled')==true ? 'disabled' : '' )) %>
                        <%= (input.get('required') == 'required' ? input.escape('required') : (input.escape('required')==true ? 'required' : '' )) %>
                    ><%= (value||input.escape('value')) %></textarea>",
    'navBtns' => "
            <li id='homeLi' class='dropdown active'>
            <a href='<%= current.get('link') %>' style='float: left;'>
            <%= current.get('icon') %>
            <%= current.escape('label') %>
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
            <% }) %>",
    "panel" => "
            <div class='un-color<%= num %> opaque un-panel un-panel-closed hidden' id='panel<%= num %>'>
                <span data-panel='<%= num %>' class='un-panel-toggle un-open-panel opaque un-color<%= num %>' id='panel<%= num %>_toggle'>
                    <span class='glyphicon glyphicon-chevron-right'></span>
                    <span class='glyphicon glyphicon-chevron-right'></span>
                </span>
                <div class='un-panel-content' id='panel<%= num %>_content'>

                </div>
            </div>",
    "main" => "
        <div class='un-panel' id='main' style='width: 100%;'>
            <div class='un-panel-content' style='padding-right: 10px;'>

            </div>
        </div>
    ",
    "notice" => "
            <span class='notice notice-<%= notice.get('type') %> alert-dismissible' role='alert' id='notice_<%= id %>' >
                <button type='button' class='notice-close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                <span class='notice-text'><%= notice.get('message') %></span>
            </span>
    ",

);