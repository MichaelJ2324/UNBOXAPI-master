<div class="col-lg-12" style="padding: 15px;">
    <div class="col-lg-6">
        <form id="entryPoint">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="row">
                            <label>Name</label>
                            <input type="text" name="name" id="name" placeholder="Name" required="true" class="form-control" value="<?php echo $entryPoint->name; ?>"/>
                            <input type="hidden" name="id" id="id" value="<?php echo $entryPoint->get_Id(); ?>"/>
                        </div>
                        <div class="row">
                            <label>Method</label>
                            <select id="httpMethod" name="httpMethod" class="form-control" placeholder="Choose HTTP Method">
                                <option value="NULL"></option>
                                <?php
                                $http_method = $entryPoint->get_HttpMethod();
                                foreach ($form_data['http_methods'] as $row){
                                    echo "<option value='{$row['id']}'".($http_method==$row['id']?"SELECTED":"").">{$row['value']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <label>URL</label>
                            <input type="text" name="url" id="url" placeholder="URL" required="true" class="form-control" value="<?php echo $entryPoint->url; ?>"/>
                        </div>
                        <div class="row">
                            <label>Description</label>
                            <textarea name="description" id="description" placeholder="Description of Entry Point" class="form-control"><?php echo $entryPoint->description; ?></textarea>
                        </div>
                        <div class="row">
                            <label>API Versions</label>
                            <select id="versions[]" name="versions[]" multiple class="form-control select2" placeholder="Choose Versions">
                                <?php
                                $versions=$entryPoint->get_ApiVersions();
                                foreach ($form_data['versions'] as $row){
                                    echo "<option value='{$row['id']}' ".(in_array($row['id'],$versions)?"SELECTED":"").">{$row['value']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="row">
                            <br>
                            <button type="button" onclick="APITester.admin.entryPoint.save();" class="btn btn-primary">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="col-lg-6">
        <form id="entryPoint_params">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b>Input Params</b>
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-primary" style="padding: 2px 10px;"><span class="glyphicon glyphicon-plus"></span></button>
                        <button type="button" class="btn btn-default" style="padding: 2px 10px;"><span class="glyphicon glyphicon-search"></span></button>
                    </div><!-- /input-group -->
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class="row row-heading row-border">
                            <div class="col-lg-2">
                                <b>Required</b>
                            </div>
                            <div class="col-lg-4">
                                <b>Name</b>
                            </div>
                            <div class="col-lg-4">
                                <b>Param Type</b>
                            </div>
                            <div class="col-lg-2">
                            </div>
                        </div>
                        <?php
                        $params = $entryPoint->get_Input_Params();
                        foreach ($params as $input_param){
                            $type = $input_param['param']->paramType->get_type();
                            echo "<div class='row row-border'>
                                    <div class='col-lg-2'>
                                        <input type='checkbox' name='required_{$input_param['id']}' id='required_{$input_param['id']}'".($input_param['param']->required==true?"CHECKED":"")." value='1' disabled />
                                        <input type='hidden' name='param_{$input_param['id']}' id='param_{$input_param['id']}' value='{$input_param['id']}' />
                                        <input type='hidden' name='relationship_{$input_param['id']}' id='relationship_{$input_param['id']}' value='{$input_param['relationship']}' />
                                    </div>
                                    <div class='col-lg-4'>
                                        {$input_param['param']->name}
                                        <input type='hidden' name='param_name_{$input_param['id']}' id='param_name_{$input_param['id']}' value='{$input_param['param']->name}' />
                                    </div>
                                    <div class='col-lg-3'>
                                        $type
                                        <input type='hidden' name='param_type_{$input_param['id']}' id='param_name_{$input_param['id']}' value='$type' />
                                    </div>
                                    <div class='col-lg-3'>
                                        <div class='btn-group pull-right'>
                                            <button type='button' class='btn btn-default' style='padding: 2px 10px;'><span class='glyphicon glyphicon-remove'></span></button>
                                            <button type='button' class='btn btn-default' style='padding: 2px 10px;'><span class='glyphicon glyphicon-eye-open'></span></button>
                                        </div>
                                    </div>
                                </div>
                                <div class='row row-border hidden'>
                                    <div class='col-lg-12'>
                                        {$input_param['param']->description}
                                    </div>
                                </div>
                            ";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>