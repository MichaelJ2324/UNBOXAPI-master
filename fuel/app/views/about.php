<div class="container-fluid">
    <div class="row-fluid">
        <span class="span8">The REST v10 Script Builder is designed to help you quickly create PHP scripts that leverage the REST v10 API built into Sugar 7.  A breakdown of how the Script Builder works is available in the <a href="./README.txt">Read Me</a>.  For feature requests or assistance, please email <a href="mailto:dkallish@sugarcrm.com">dkallish@sugarcrm.com</a></span>
    </div>
    <br/>
    <div class="row-fluid">
      <span class="span8">Not all Endpoints are available at this time, but more will be added in the future.  Custom Endpoints are not supported, but could be added manually.  More information about Custom Endpoints is available in the Sugar Developer Documentation: <a href="http://support.sugarcrm.com/02_Documentation/04_Sugar_Developer/Sugar_Developer_Guide_7.1/70_API/Web_Services/15_Extending_Web_Services/">Extending Web Services</a>  The Script Builder includes all of the following endpoints:<br/>
      &nbsp;&nbsp;&nbsp;&nbsp;<b>Note:</b> These are visible by entering '&#60;Your Instance's URL&#62;/rest/v10/help' in your browser's Address Bar.</span>
    </div>
    <br/>
    <div class="span10 well">
        <ul style="list-style-type: none;">
            <li>GET Endpoints:</li>
            <ul style="list-style-type: none;">
                <li><b>filterList</b> (List of Records) - Filtered list of all records in this module</li>
                <li><b><a href="#retrieveRecord_modal" data-toggle="modal" data-target="#retrieveRecord_modal">retrieveRecord</a></b> (Retreive Record) - Returns a single record</li>
                <li><b><a href="#viewChangeLog_modal" data-toggle="modal" data-target="#viewChangeLog_modal">viewChangeLog</a></b> (View Change Log) - View change log in record view</li>
                <li><b><a href="#getFileList_modal" data-toggle="modal" data-target="#getFileList_modal">getFileList</a></b> (List of Related Files) - Lists all populated fields of type "file" or of type "image" for a record.</li>
                <li><b><a href="#getRecordActivities_modal" data-toggle="modal" data-target="#getRecordActivities_modal">getRecordActivities</a></b> (Record's Activities) - This method retrieves a record's activities</li>
                <li><b>getModuleActivities</b> (Module's Activities) - This method retrieves a module's activities</li>
                <li><b><a href="#config_modal" data-toggle="modal" data-target="#config_modal">config</a></b> (Module Config) - Retrieves the config settings for a given module (Forecasts)</li>
                <li><b><a href="#getMostActiveUsers_modal" data-toggle="modal" data-target="#getMostActiveUsers_modal">getMostActiveUsers</a></b> (User with Most Activities) - This endpoint is used to most active users for Meetings, Calls, and Emails.</li>
                <li><b><a href="#requestPassword_modal" data-toggle="modal" data-target="#requestPassword_modal">requestPassword</a></b> (Email Password Request) - This method sends a Reset Password request email to the Username</li>
                <li><b><a href="#listConfigurations_modal" data-toggle="modal" data-target="#listConfigurations_modal">listConfigurations</a></b> (Outbound Email Accounts) - A list of outbound email configurations</li>
                <li><b><a href="#ping_modal" data-toggle="modal" data-target="#ping_modal">ping</a></b> (Check Login) - Responds with 'pong' to confirm that the access token is valid.</li>
                <li><b><a href="#whattimeisit_modal" data-toggle="modal" data-target="#whattimeisit_modal">whattimeisit</a></b> (Current Server Time) - Responds with the current time in server format.</li>
                <li><b><a href="#getRecentlyViewed_modal" data-toggle="modal" data-target="#getRecentlyViewed_modal">getRecentlyViewed</a></b> (My Recently Viewed Records) - Returns all of the current users recently viewed records.</li>
                <li><b><a href="#globalSearch_modal" data-toggle="modal" data-target="#globalSearch_modal">search</a></b> (Search Globally) - Globally search records</li>
            </ul>
            <br/>
            <li>POST Endpoints:</li>
            <ul style="list-style-type: none;">
                <li><b>createRecord</b> (Create Record) - This method creates a new record of the specified type</li>
                <li><b>saveFilePost</b> (Save a File) - Saves a file. The file can be a new file or a file override.</li>
                <li><b>subscribeToRecord</b> (Subscribe to Record) - This method subscribes the user to the current record, for activity stream updates.</li>
                <li><b>configSave</b> (Save Forecast Config) - Creates the config entries for the given module (Forecasts)</li>
                <li><b>checkForDuplicates</b> (Check for Duplicates) - Check for duplicate records within a module</li>
                <li><b>createMail</b> (Send an Email) - Create Mail Item</li>
                <li><b>validateEmailAddresses</b> (Validate Email Address) - Validate an Email Address</li>
                <li><b>logout</b> (Logout using OAuth2.0) - Expires the token portion of the OAuth 2.0 specification.</li>
                <li><b>token</b> (Login using OAuth2.0) - Retrieves the token portion of the OAuth 2.0 specification (Logs In).</li>
            </ul>
            <br/>
            <li>PUT Endpoints:</li>
            <ul style="list-style-type: none;">
                <li><b>updateRecord</b> - This method updates a record of the specified type</li>
                <li><b>setFavorite</b> - This method sets a record of the specified type as a favorite</li>
                <li><b>unsetFavorite</b> - This method unsets a record of the specified type as a favorite</li>
                <li><b>configSave</b> - Updates the config entries for given module (Forecasts)</li>
                <li><b>updatePassword</b> - Updates current user's password</li>
            </ul>
            <br/>
            <li>DELETE Endpoints:</li>
            <ul style="list-style-type: none;">
                <li><b>deleteRecord</b> - This method deletes a record of the specified type</li>
                <li><b>unsetFavorite</b> - This method unsets a record of the specified type as a favorite</li>
                <li><b>removeFile</b> - Removes a file from a field.</li>
                <li><b>unsubscribeFromRecord</b> - This method unsubscribes the user from the current record, for activity stream updates.</li>
            </ul>
    </div>
    <div class="row-fluid">
        <span class="span12"><b>REST v10 Script Builder</b> uses the following third party tools:</span>
    </div>
    <div class="row-fluid">
        <span class="span12"><a href="http://jquery.com/">jQuery</a> - jQuery is a fast and concise JavaScript Library that simplifies HTML document traversing, event handling, animating, and Ajax interactions for rapid web development.</span>
    </div>
    <div class="row-fluid">
        <span class="span12"><a href="http://getbootstrap.com/">Twitterbootstrap</a> - HTML, CSS, and JS toolkit from Twitter.</span>
    </div>
</div>