<?php
return array(
    'module' => "<input type='text'
                    value='<%= _.escape(field['value']) %>'
                    name='<%= _.escape(field['name']) %>'
                    id='<%= _.escape(field['name']) %>'
                    placeholder='<%= _.escape(field['name']) %>'
                    class='form-control'
                    required='<%= _.escape(field['required']) %>' />",
    'record_id' => "<div class='input-group'>
                            <span class='input-group-btn'>
                                <button class='btn btn-default' type='button'>
                                    <span class='glyphicon glyphicon-search'></span>
                                </button>
                            </span>
                            <input type='text'
                                value='<%= _.escape(field['value']) %>'
                                name='<%= _.escape(field['name']) %>'
                                id='<%= _.escape(field['name']) %>'
                                placeholder='<%= _.escape(field['name']) %>'
                                class='form-control'
                                required='<%= _.escape(field['required']) %>' />
                        </div>",
    'grant_type' => "<select id='<%= _.escape(field['name']) %>' name='<%= _.escape(field['name']) %>' class='form-control select2' <%= _.escape(field['required']) %> >
                            <option value='password'>Password</option>
                            <option value='refresh_token'>Refresh Token</option>
                        </select>",
    'client_id' => "<select id='<%= _.escape(field['name']) %>' name='<%= _.escape(field['name']) %>' class='form-control select2' <%= _.escape(field['required']) %> >
                            <option value='sugar'>Sugar</option>
                            <option value='support_portal'>Sugar Portal</option>
                        </select>",
    'platform' => "<select id='<%= _.escape(field['name']) %>' name='<%= _.escape(field['name']) %>' class='form-control select2' <%= _.escape(field['required']) %> >
                            <option value='base'>Base</option>
                            <option value='mobile'>Mobile</option>
                            <option value='portal'>Portal</option>
                            <option value='api'>API</option>
                        </select>",
    'filter' => "<textarea id='<%= _.escape(field['name']) %>' name='<%= _.escape(field['name']) %>' <%= _.escape(field['required']) %> class='form-control'></textarea>",
);

?>