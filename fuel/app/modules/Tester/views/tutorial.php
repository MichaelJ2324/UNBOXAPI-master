<p>This tool is designed to help you quickly build REST v10 web service scripts, based on the endpoints built into Sugar 7.x and up.  The steps below provide a generic walkthrough of the tool's capabilities:</p>
<div class="well">
    <div class="row-fluid">
        <p>
            1. Enter the SugarCRM URL, Username, and Password you'll be using when running the script you're building:<br />
        <center><img class="img-polaroid" src="./tutorialImages/credentials.png" style="width: 400px; height: 110px;" /></center>
        </p>
    </div>
    <div class="row-fluid">
        <p>
            2. Sugar uses one of four HTTP Request Methods, select one to begin:<br />
        <center><img class="img-polaroid" src="./tutorialImages/httpMethods.png" /></center>
        <a href="http://en.wikipedia.org/wiki/Hypertext_Transfer_Protocol#Request_methods" target="_blank?" style="padding-left: 7%;">What's an HTTP Request Method?</a>
        </p>
    </div>
    <div class="row-fluid">
        <p>
            3. Based on your selected Method Type, a new list of Endpoints is generated.  Select an Endpoint to continue:<br />
        <center><img class="img-polaroid" src="./tutorialImages/selectEndpoint.png" /></center>
        </p>
    </div>
    <div class="row-fluid">
        <p>
            4. When you select an Endpoint, additional fields may display.  These fields represent some (or all) of the required information that the Endpoint needs before you attempt to run the web service script.  Enter your data into the corresponding fields:<br />
        <center><img class="img-polaroid" src="./tutorialImages/inputFields.png" /></center>
        </p>
    </div>
    <div class="row-fluid">
        <p>
            5. After filling out all of the fields, click the 'Create Script' button to update the 'Script Builder Result' textarea field:<br />
        <center><img class="img-polaroid" src="./tutorialImages/createScript.png" /></center>
        </p>
    </div>
    <div class="row-fulid">
        <p>
            The 'Script You've Built' TextArea field will update and display all the PHP code necessary for running the script you built using the 'Build Your Script' fields.  If the Endpoint requires additional updates before running, the tool will prompt you to explain where these changes need to be made.
        </p>
    </div>
</div>
<p>
    For a more advanced tutorial and detailed information about the available Endpoints, please visit the <a href="../../../views/about.php">About</a> page.
</p>