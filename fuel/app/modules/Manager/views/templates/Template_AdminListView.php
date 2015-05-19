<script type="text/template" id="AdminOutput">
    <div class="panel panel-default" id="filter_panel">
        <div class="panel-heading">
            Filter List
        </div>
        <div class="panel-body" id="filters_panel_body">
            <div class="col-lg-12">
                <form id='filters' class="form-inline">
                    <div class="form-group col-md-3">
                        <label>Name</label>
                        <input type='text' id="name" name="name" class="form-control" placeholder="Entrypoint Name" />
                    </div>
                    <div class="form-group col-md-3">
                        <label>Application</label>
                        <select id="application" name="application" class="form-control select2" placeholder="Choose Application" >
                            <option value="NULL"></option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>API</label>
                        <select id="api" name="api" class="form-control select2" placeholder="Choose API" >
                            <option value="NULL"></option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label>HTTP Method</label>
                        <select id="httpMethod" name="httpMethod" class="form-control select2" placeholder="Choose Method" >
                            <option value="NULL"></option>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="panel panel-default" id="list_panel">
        <div class="panel-body" id="list_panel_body">
            <table class="table table-responsive table-striped table-bordered table-hover" id="ep_table">
                <thead>
                <tr>
                    <th style="width: 25px;">
                        <div class="input-group"  style="width: 25px;">
                            <span class="input-group-addon">
                                <input type="checkbox" name="select_all" id="select_all" />
                            </span>
                            <div class="input-group-btn">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Delete</a></li>
                                    <li><a href="#">Mass Update</a></li>
                                    <li><a href="#">Export</a></li>
                                </ul>
                            </div><!-- /btn-group -->
                        </div><!-- /input-group -->
                    </th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Method</th>
                    <th style="border-right: 0px;">URL</th>
                    <th style="border-left: 0px;">
                        <span class="pull-right">
                            <button class="btn btn-link" id="prevBtn"><span class="glyphicon glyphicon-chevron-left"></span></button>
                            <span id="offset">1</span>-<span id="next_offset">20</span>
                            <button class="btn btn-link" id="nextBtn"><span class="glyphicon glyphicon-chevron-right"></span></button>
                        </span>
                    </th>
                </tr>
                </thead>
                <tbody id="ep_list">

                </tbody>
            </table>
        </div>
    </div>
</script>
<script type="text/template" id="EpList">
    <% _.each(entrypoints,function(entrypoint){ %>
        <tr>
            <td><input type='checkbox' value='<%= entrypoint.escape('id') %>' /></td>
            <td><%= entrypoint.escape('name') %></td>
            <td><%= entrypoint.escape('description') %></td>
            <td><%= entrypoint.escape('method_name') %></td>
            <td colspan="2"><%= entrypoint.escape('url') %></td>
        </tr>
    <% }) %>
</script>