<script type="text/template" id="TesterOutput">
    <div class="panel panel-default" id="request_panel">
        <div class="panel-heading">
            Request
            <select class="form-control pull-right" id="view_style" style="width: 80px; height: 20px;">
                <option value="pretty" selected="selected">Pretty</option>
                <option value="raw">Raw</option>
                <option value="encode">Encoded</option>
            </select>
        </div>
        <div class="panel-body" id="request">
            <% if (request.get('request')==""||request.get('request')==null) {
                }else{
                    if (style.get('name')=='pretty'){ %>
                        <%= "<pre>"+_.escape(decodeURI(request.get('request')))+"</pre>" %>
            <%      }else if (style.get('name')=='raw') { %>
                        <%= "<pre>"+request.escape('request')+"</pre>" %>
            <%      }
                } %>
        </div>
    </div>
    <div class="panel panel-default" id="response_panel">
        <div class="panel-heading">
            Response
        </div>
        <div class="panel-body" id="response">
            <% if (request.get('response')==""||request.get('response')==null) {
                }else{
                    if (style.get('name')=='pretty'){ %>
                    <%= "<pre>"+_.escape(JSON.stringify(jQuery.parseJSON(request.get('response')),undefined,2))+"</pre>" %>
                <%      }else if (style.get('name')=='raw') { %>
                    <%= "<pre>"+request.escape('response')+"</pre>" %>
                <%      }
                } %>
        </div>
    </div>
</script>