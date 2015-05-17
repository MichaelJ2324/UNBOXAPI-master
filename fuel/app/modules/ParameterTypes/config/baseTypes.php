<?php
return array(
    'data_types' => array(
        'string',
        'text',
        'integer',
        'boolean',
        'array',
        'date',
        'datetime'
    ),
    'api_types' => array(
        'password'
    ),
    'templates' => array(
        'string' => "<input type='text'
                        class='form-control'
                        value='<%= _.escape(field['value']) %>'
                        id='parameter_<%= _.escape(field['name']) %>'
                        name='<%= _.escape(field['name']) %>'
                        required='<%= _.escape(field['required']) %>'
                    />",
        'text' => "<textarea rows='5'
                    class='form-control'
                    id='parameter_<%= _.escape(field['name']) %>'
                    name='<%= _.escape(field['name']) %>'
                    required='<%= _.escape(field['required']) %>'
                    ><%= _.escape(field['value']) %></textarea>",
        'integer' => "<input type='number'
                        class='form-control'
                        value='<%= _.escape(field['value']) %>'
                        id='parameter_<%= _.escape(field['name']) %>'
                        name='<%= _.escape(field['name']) %>'
                        required='<%= _.escape(field['required']) %>'
                    />",
        'boolean' => "<input type='hidden' value='0'
                        id='<%= _.escape(field['name']) %>'
                        name='<%= _.escape(field['name']) %>' >
                    <input type='checkbox' value='1'
                        id='parameter_<%= _.escape(field['name']) %>'
                        name='<%= _.escape(field['name']) %>'
                        required='<%= _.escape(field['required']) %>'
                        class='form-control' >",
        'array' => "<textarea rows='5'
                    class='form-control'
                    id='parameter_<%= _.escape(field['name']) %>'
                    name='<%= _.escape(field['name']) %>'
                    required='<%= _.escape(field['required']) %>'
                    ><%= _.escape(field['value']) %></textarea>",
        'date' => "<input type='text'
                        class='form-control datepicker'
                        value='<%= _.escape(field['value']) %>'
                        id='parameter_<%= _.escape(field['name']) %>'
                        name='<%= _.escape(field['name']) %>'
                        required='<%= _.escape(field['required']) %>'
                    />",
        //TODO::DateTime picker template
        'datetime' => "<input type='text'
                        class='form-control datepicker'
                        value='<%= _.escape(field['value']) %>'
                        id='parameter_<%= _.escape(field['name']) %>'
                        name='<%= _.escape(field['name']) %>'
                        required='<%= _.escape(field['required']) %>'
                    />",
        'password' => "<input type='password'
                        class='form-control'
                        value='<%= _.escape(field['value']) %>'
                        id='parameter_<%= _.escape(field['name']) %>'
                        name='<%= _.escape(field['name']) %>'
                        required='<%= _.escape(field['required']) %>'
                    />",
    )
);