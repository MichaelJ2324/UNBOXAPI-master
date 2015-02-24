<script type="text/tempalte" id="TestParameters">
    <div class="parameter-setup">
        <form id='ParameterForm'>
            <div class="panel-group" id="ep_params_accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#ep_params_accordion" href="#EP_urlParams">
                                URL Parameters
                            </a>
                        </h4>
                    </div>
                    <div class="panel-collapse collapse in" id="EP_urlParams">
                        <div class="panel-body" id="ep_url_params" >

                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#ep_params_accordion" href="#EP_requestParams">
                                Request Parameters
                            </a>
                        </h4>
                    </div>
                    <div class="panel-collapse collapse in" id="EP_requestParams">
                        <div class="panel-body" id="ep_request_params">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class='ep-actions' id="ep_action2">
    </div>

</script>
<script type="text/template" id="Parameters">
    <% _.each(parameters,function(param){ %>
        <label><%= param.escape('name') %> <span class='text-muted'><%= param.escape('type') %></span></label>
        <%= param.get('html') %>
        <span class='help-block'><%= param.escape('description') %></span>
    <% }) %>
</script>