<?php
return array(
    'label' => "<label for='<%= _.escape(field['name']) %>'><%= _.escape(field['label']) %></label>",
    'help' => "<span class='help-block'><%= _.escape(field['help']) %></span>",
    'text' => "<input type='text'
                    class='form-control <%= _.escape(field['class']) %>'
                    placeholder='<%= _.escape(field['placeholder']) %>'
                    value='<%= _.escape(field['value']) %>'
                    id='<%= _.escape(typeof field['id'] !== 'undefined' ? field['id'] : field['name']) %>'
                    name='<%= _.escape(field['name']) %>'
                    <%= _.escape(field['disabled']) %>
                    />",
    'select' => "<select class='form-control <%= _.escape(field['class']) %>'
                    id='<%= _.escape(typeof field['id'] !== 'undefined' ? field['id'] : field['name']) %>'
                    name='<%= _.escape(field['name']) %>'
                    <%= _.escape(field['disabled']) %> >
                        <% _.each(options,function(option){ %>
                            <option value='<%= option.get('key') %>'
                               <%= (field['value']==option.get('key')?'selected':'') %> ><%= option.escape('value') %></option>
                        <% }) %>
                  </select>",
    'checkbox' => "<input type='hidden' value='0'
                        id='<%= _.escape(typeof field['id'] !== 'undefined' ? field['id'] : field['name']) %>'
                        name='<%= _.escape(field['name']) %>'>
                    <input type='checkbox' value='1'
                        id='<%= _.escape(typeof field['id'] !== 'undefined' ? field['id'] : field['name']) %>'
                        name='<%= _.escape(field['name']) %>'
                        <%= _.escape(field['disabled']) %>
                        class='form-control <%= _.escape(field['class']) %>' >",
    'textarea' => "<textarea rows='5'
                    class='form-control <%= _.escape(field['class']) %>'
                    placeholder='<%= _.escape(field['placeholder']) %>'
                    id='<%= _.escape(typeof field['id'] !== 'undefined' ? field['id'] : field['name']) %>'
                    name='<%= _.escape(field['name']) %>'
                    <%= _.escape(field['disabled']) %>><%= _.escape(field['value']) %></textarea>",
    'navBtns' => "
        <li id='homeLi' class='dropdown active'>
            <a href='<%= current.get('link') %>' style='float: left;'>
                <%= current.get('icon') %>
                <%= current.escape('name') %>
            </a>
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
            </li>
            <% _.each(links,function(link){ %>
            <li><a href='<%= link.link %>'><%= link.icon %><%= _.escape(link.name) %></a></li>
        <% }) %>",
    "panel" => "
            <a href='#' data-panel='<%= num %>' class='un-panel-toggle un-open-panel opaque un-color<%= num %>' id='panel<%= num %>_toggle'>
                <span class='glyphicon glyphicon-chevron-right'></span>
                <span class='glyphicon glyphicon-chevron-right'></span>
            </a>
            <div class='un-panel-content' id='panel<%= num %>_content'>

            </div>",
    "notice" => "
            <span class='notice notice-<%= notice.get('type') %> alert-dismissible' role='alert' id='notice_<%= id %>' >
                <button type='button' class='notice-close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button>
                <span class='notice-text'><%= notice.get('message') %></span>
            </span>
    "
);