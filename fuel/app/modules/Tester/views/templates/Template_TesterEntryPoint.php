<script type='text/template' id='EntryPointOverview'>
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
</script>
<script type='text/template' id='EntryPointActions'>
    <% if (hasParams==true && step==2) { %>
        <button type='button' class='btn btn-primary pull-right' id='setupParams' >Setup Params</button>
    <% }else{ %>
        <button type='button' class='btn btn-primary pull-right' id='sendAPI' disabled='disabled' >Test EntryPoint</button>
        <button type='button' class='btn btn-primary pull-right' id='generateScript'>Generate Script</button>
    <% } %>
</script>
<script type='text/template' id='EntryPointMain'>
    <table class='table table-responsive'>
        <tr>
            <td class='row-head'>Name:</td>
            <td class='ep-info'><%= _.escape(entryPoint.get('name')) %></td>
        </tr>
        <tr>
            <td class='row-head'>Method:</td>
            <td class='ep-info'><%= _.escape(entryPoint.get('method_name')) %></td>
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
</script>
<script type='text/template' id='EntryPointParameters'>
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
                <%= _.escape(p.get('type')) %>
            </div>
            <div class='col-sm-5'>
                <%= _.escape(p.get('name')) %>
            </div>
            <div class='col-sm-3'>
                <%= _.escape(p.get('required')) %>
            </div>
        </div>
     <% }); %>
</script>
<script type='text/template' id='EntryPointExamples'>
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
</script>
<script type='text/template' id='EntryPointExceptions'>
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
</script>