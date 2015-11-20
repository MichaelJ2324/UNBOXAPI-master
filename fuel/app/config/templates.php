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
    'mainNav' => "
            <li id='homeLi' class='dropdown active'>
                <a href='<%= links[0].link %>' style='float: left;'>
                    <%= links[0].icon %>
                    <%= UNBOX.Translator.translate(links[0].label) %>
                </a>
            <% if (layouts.length>2){ %>
                <a href='#' class='dropdown-toggle' data-toggle='dropdown' style='float: right; padding-left: 0px;'>
                    <span class='caret'></span>
                </a>
                <ul class='dropdown-menu ' role='menu'>
                <% _.each(layouts,function(layout){
                        if (layout.get('name')!==current){
                            var llinks = layout.get('links');
                %>
                    <li><a href='<%= llinks[0].link %>'><%= llinks[0].icon %><%= UNBOX.Translator.translate(llinks[0].label,layout) %></a></li>
                <%        }
                    })
                %>
                </ul>
            <% } %>
            </li>
            <%
                if (links.length > 1 ){
                    _.each(links,function(link){
                        if (link.label !== 'LBL_MODULE'){
            %>
            <li>
                <a href='<%= link.link %>'><%= link.icon %><%= UNBOX.Translator.translate(link.label) %></a>
            </li>
            <%      }})
                }
            %>",
    'rightNav' => "
            <li class='dropdown'>
                <% if (user.loggedIn()){ %>
                <a href='#profile' style='float: left;'>
                    <i class='fa fa-user'></i>
                    <%= user.escape('user_name') %>
                </a>
                <% } else { %>
                <a href='#login' style='float: left;'>
                  <i class='glyphicon glyphicon-log-in'></i>
                   Login
                </a>
                <% } %>
                <a href='#' class='dropdown-toggle' data-toggle='dropdown' style='float: right; padding-left: 0px;'>
                    <span class='caret'></span>
                </a>
                <ul class='dropdown-menu dropdown-menu-right' role='menu'>
                    <li>
                        <a href='#tutorial'>
                            <span class='glyphicon glyphicon-question-sign'></span>
                            Tutorial
                        </a>
                    </li>
                    <li>
                        <a href='#about'>
                            <span class='glyphicon glyphicon-info-sign'></span>
                            About
                        </a>
                    </li>
                    <% if (user.loggedIn()){ %>
                        <li role='presentation' class='divider'></li>
                        <li><a href='#logout'><i class='glyphicon glyphicon-log-out'></i>Logout</a></li>
                    <% } %>
                </ul>
            </li>
    ",
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
            <div class='un-panel-content' id='main_content' style='padding-right: 10px;'>

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