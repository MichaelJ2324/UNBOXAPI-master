var UNBOXAPI = UNBOXAPI || {};

UNBOXAPI.App = Backbone.Router.extend({
    routes: {
        "help": "help",
        "about": "about",
        "manage": "manager",
        "manage/:module/:action": "quickRecord",
        "manage/:module/:action/:id": "quickRecord",
        "test": "tester",
        "login": "home",
        "logout": "logout",
        "register": "register",
        "profile": "profile",
        "home": "home",
        '*path': 'defaultRoute'
    },
    initialize: function (options) {
        this.options = options || {};
        this.id = this.options.id;
        //TODO::Setup $.AJAX with jsClient ID in header
        this.user = new UNBOXAPI.Models.User;
        this.metadata = new UNBOXAPI.Collections.MetaData({
            user: this.user
        });
        this.loggedIn = false;
        if (!(this.options.user==null||typeof this.options.user=='undefined')){
            this.loggedIn = true;
            this.user.set({
                id: this.options.user
            });
        }
        this.view = null;
        this.layout = null;
        _.bindAll(this,"start","setupView","userStateChange");
        this.user.on("change:id",this.userStateChange);
    },
    start: function(){
        this.setupLang();
        var functions = [
            {
                f: this.metadata.fetchAll,
                p: {}
            },
            {
                f: UNBOXAPI.Models.Utils.fetch,
                p: {
                    model: this.user
                }
            },
        ];
        var queue = new UNBOXAPI.Queue(functions,function(){
            Backbone.history.start({
                root: window.location.pathname
            });
        });
        queue.process();
    },
    setupLang: function(){
        var lang = UNBOXAPI.Global.Cookie.get('lang');
        if (lang==null){
            lang = 'en';
            UNBOXAPI.Global.Cookie.set('lang',lang,null);
        }
        UNBOX.Translator.setLang(lang);
    },
    setupView: function(layout) {
        var ready = false;
        var oldLayout = this.metadata.layouts.current;
        if (this.metadata.layouts.setCurrent(layout) == true) {
            var layoutObj = this.metadata.layouts.findWhere({name: layout});
            if ((layoutObj.config.getValue("login") == true && this.user.loggedIn() == true) || (layoutObj.config.getValue('login') == false)) {
                if (this.view == null) {
                    this.view = new UNBOXAPI.Views.AppView({
                        el: $("body"),
                        metadata: this.metadata,
                        user: this.user
                    });
                } else {
                    if (oldLayout!=layout) {
                        this.view.changeLayout();
                    }
                }
                ready = true;
            } else {
                this.navigate("login", {trigger: true});
            }
        }else{
            this.navigate("login", {trigger: true});
        }
        return ready;
    },
    userStateChange: function(){
        var loggedIn = this.user.loggedIn();
        if (this.loggedIn==false && loggedIn==true){
            this.metadata.fetchAll();
            this.loggedIn = loggedIn;
            var module = this.user.get('default_module');
            if (!(module==null||module==""||typeof module=='undefined')){
                this.navigate(module,{trigger:true});
            }else{
                this.navigate("Home",{trigger: true});
            }
        }else{
            if (this.loggedIn==true && loggedIn==false){
                this.loggedIn = false;
                this.metadata.fetchAll();
            }
            this.navigate("Home",{trigger: true});
        }
    },
    //Route functions
    help: function () {

    },
    about: function () {

    },
    manager: function (module) {
        if (this.setupView("Manager")) {
            this.view.layout.menu(module);
        }
    },
    quickRecord: function(module,action,id){
        if (this.metadata.layouts.current !== "Manager") {
            this.manager(module);
        }
        if (!(module == "" || typeof module == 'undefined' || module == null)) {
            if (this.metadata.modules.current !== module) {
                this.metadata.modules.setCurrent(module);
            }
            if (!(action == "" || typeof action == 'undefined' || action == null)) {
                if (action == "create" || action == "view") {
                    this.view.layout.quickRecord(module,action,id);
                    this.view.layout.relateRecord();
                } else if (action == "list") {

                } else if (action == "import") {

                } else {
                    console.log("Invalid Action");
                }
            }
        }
    },
    tester: function () {
        this.setupView("Tester");
    },
    login: function(){
        if (this.metadata.layouts.current !== "Home") {
            if (this.setupView("Home")){
                return false;
            }
        }
        if (!this.user.loggedIn()) {
            this.view.layout.login();
        }else{
            this.navigate("home",{trigger: true});
        }
    },
    home: function(){
        if (this.setupView("Home")) {
            if (!this.user.loggedIn()) {
                this.navigate("login");
                this.login();
            } else {
                this.view.layout.home()
            }
        }
    },
    register: function(){
        if (this.metadata.layouts.current !== "Home") {
            if (!this.setupView("Home")){
                return false;
            }
        }
        if (!this.user.loggedIn()){
            this.view.layout.register();
        }else{
            this.navigate("profile");
            this.profile();
        }
    },
    profile: function(){
        if (this.metadata.layouts.current !== "Home") {
            if (!this.setupView("Home")){
                return false;
            }
        }
        if (this.user.loggedIn()){
            this.view.layout.profile();
        }else{
            this.navigate("login");
            this.login();
        }
    },
    logout: function(){
        this.user.logout();
    },
    defaultRoute: function () {
        this.home();
    }
});
UNBOXAPI.Views = {
    AppView: Backbone.View.extend({
        events: {
        },
        initialize: function(options) {
            this.options = options || {};
            this.metadata = this.options.metadata;
            this.user = this.options.user;

            this.notices = new UNBOXAPI.Collections.Notices;

            //Dom elements
            this.$mainNav = $("#mainNav");
            this.$rightNav = $("#rightNav");
            this.$notices = $("#notices");
            this.$layout = $("#layout");

            //build nav
            this.mainNav = new UNBOXAPI.Views.MainNav({
                el: this.$mainNav,
                collection: this.metadata.layouts,
                model: this.metadata.layouts.findWhere({ name: this.metadata.layouts.current }),
                template: this.metadata.templates.getTemplate("mainNav")
            });
            this.rightNav = new UNBOXAPI.Views.RightNav({
                el: this.$rightNav,
                model: this.user,
                template: this.metadata.templates.getTemplate("rightNav")
            });
            //build notice
            this.notice = new UNBOXAPI.Views.Notice({
                el: this.$notices,
                collection: this.notices,
                template: this.metadata.templates.getTemplate("notice")
            });

            _.bindAll(this,"render","changeLayout","setupLayout");

            this.render();
        },
        render: function() {
            this.setupLayout();
            return this;
        },
        setupLayout: function(){
            var layoutName = this.metadata.layouts.current;
            var Layout = UNBOXAPI.Views[layoutName];
            this.layout = new Layout.Layout({
                el: this.$layout,
                templates: this.metadata.templates,
                metadata: this.metadata.layouts.findWhere({name: layoutName }),
                modules: this.metadata.modules,
                user: this.user
            });
        },
        changeLayout: function(){
            if (!(this.layout==null||typeof this.layout=='undefined')){
                this.layout.reset().done(this.setupLayout).resolve();
            }
        }
    }),
    MainNav: Backbone.View.extend({
        events: {
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;
            this.user = this.options.user;

            _.bindAll(this,"render");
            this.collection.on("reset",this.render);
            this.model.on("change",this.render);
            this.render();
        },
        render: function(){
            UNBOX.Translator.setContext(this.model);
            this.html = _.template(this.template,{
                current: this.model.get('name'),
                layouts: this.collection.models,
                links: this.model.get("links")
            });
            this.$el.html(this.html);
            return this;
        }
    }),
    RightNav: Backbone.View.extend({
        events: {
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;

            _.bindAll(this,"render");
            this.model.on("change",this.render);
            this.render();
        },
        render: function(){
            this.html = _.template(this.template,{
                user: this.model
            });
            this.$el.html(this.html);
            return this;
        }
    }),
    Layout: Backbone.View.extend({
        events: {
            "click .un-close-panel": "closePanel",
            "click .un-open-panel": "openPanel"
        },
        initialize: function(options){
            this.options = options || {};
            this.gTemplates = this.options.templates;
            this.modules = this.options.modules;
            this.metadata = this.options.metadata;
            this.user = this.options.user;

            //Global Models object for Layout
            this.models = {};
            //Global collection object for Layout
            this.collections = {};

            //Setup Panels
            this.collection = new UNBOXAPI.Collections.Panels;
            this.model = new UNBOXAPI.Models.MainPanel({
                panels: this.collection
            });
            _.bindAll(this,"buildLayout","start","bootstrap","setContent","reset","render","_init","close","openPanel","closePanel");
            this.buildLayout();
            this.start();
        },
        buildLayout: function(){
            this.panels = [];
            var panel_template = this.gTemplates.getTemplate("panel");
            var panels = this.metadata.config.getValue("panels");
            panels = (panels>3?3:panels);
            for (var x=1; x<=panels; x++){
                var panelModel = new UNBOXAPI.Models.Panels({
                    number: x
                });
                this.panels[x] = new UNBOXAPI.Views.Panel({
                    el: $("#panel"+x),
                    model: panelModel,
                    template: panel_template
                })
                this.collection.add(panelModel);
            }
            this.main = new UNBOXAPI.Views.Main({
                el: $("#main"),
                model: this.model,
                template: this.gTemplates.getTemplate("main")
            });
        },
        start: function(){
            //TODO: Removing Bootstrap until further review
            //var models = this.metadata.config.getValue("bootstrap");
            //if (!(models==null||typeof models=='undefined')){
            //    this.collections = {};
            //    this.bootstrap(models,this.render)
            //}else{
                this.render();
            //}
        },
        bootstrap: function(models,callback){
            var functions = [];
            for (var x = 0; x < models.length; x++) {
                this.collections[models[x]] = new UNBOXAPI.Collections.Records({
                    module: this.modules.findWhere({ name: models[x] })
                });
                functions[x] = {
                    f: UNBOXAPI.Collections.Utils.fetch,
                    p: {
                        collection: this.collections[models[x]],
                        options: {
                            name: models[x]
                        }
                    }
                }
            }
            var queue = new UNBOXAPI.Queue(functions,callback);
            return queue.process();
        },
        render: function(){
            this._init();
            return this;
        },
        _init: function(){
            //override function
        },
        setContent: function(area,view,view_options,state){
            state = state || 'hide';
            if (area=='main'){
                var pane = this.model;
            }else{
                var pane = this.collection.getPanel(area);
            }
            view_options = view_options || {};
            pane.set({
                content: new view(view_options)
            });
            if (area!=='main') {
                switch(state){
                    case 'open':
                        pane.set({
                            open: true
                        });
                        break;
                    case 'close':
                        pane.set({
                            open: false,
                            hidden: false
                        });
                        break;
                    default:
                        pane.set({
                            hidden: true
                        });
                        break;
                }
            }
        },
        reset: function(){
            //cascading effect
            var dfd = $.Deferred();
            dfd.done(this.main.reset);
            for (var x=1; x<this.panels.length; x++){
                dfd.done(this.panels[x].reset);
            }
            dfd.done(this.close);
            return dfd;
        },
        openPanel: function(e){
            var panel = $(e.currentTarget).data("panel");
            panel = this.collection.getPanel(panel);
            panel.set({
                open: true
            });
        },
        closePanel: function(e){
            var panel = $(e.currentTarget).data("panel");
            panel = this.collection.getPanel(panel);
            panel.set({
                open: false
            });
        }
    }),
    Main: Backbone.View.extend({
        events: {
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;

            this.$content = null;
            _.bindAll(this,"render","resize","setContent","reset","setupDOMPointers");
            this.model.on("change:width",this.resize);
            this.model.on("change:content",this.setContent);
            this.render();
        },
        render: function(){
            this.html = _.template(this.template);
            $("#layout").append(this.html);
            this.setupDOMPointers();
            return this;
        },
        setupDOMPointers: function(){
            this.$content = $("#main_content");
        },
        setContent: function(){
            var previousContent = this.model.previous("content");
            if (!(previousContent==null||typeof previousContent=='undefined')){
                previousContent.close();
            }
            var view = this.model.get("content");
            view.el = this.$content;
            view.setElement(this.$content);
            view.render();
        },
        resize: function(){
            //FIXME::Hack to make resizing work
            //this.$el used to work, but doesn't now?
            $("#main").css("width", this.model.get("width")+"%");
        },
        reset: function(){
            this.$content.html("");
        }
    }),
    Panel: Backbone.View.extend({
        events: {
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;
            this.$content = null;
            this.$panel = null;
            this.$panel_toggle = null;

            _.bindAll(this,"state","visible","open","close","show","hide","render","setContent","reset");
            this.model.on("change:open",this.state);
            this.model.on("change:hidden",this.visible);
            this.model.on("change:content",this.setContent);

            this.render();
        },
        render: function(){
            this.html = _.template(this.template,{
                num: this.model.get("number")
            });
            $("#layout").append(this.html);
            this.setupDOMPointers();
            return this;
        },
        setupDOMPointers: function(){
            this.$content = $("#panel"+this.model.get('number')+"_content");
            this.$panel = $("#panel"+this.model.get('number'));
            this.$panel_toggle = $("#panel"+this.model.get('number')+"_toggle");
        },
        setContent: function(){
            var previousContent = this.model.previous("content");
            if (!(previousContent==null||typeof previousContent=='undefined')){
                previousContent.close();
                this.render();
            }
            var view = this.model.get("content");
            view.el = this.$content;
            view.setElement(this.$content);
            view.render();
        },
        close: function(){
            this.$panel.addClass("un-panel-closed").removeClass("un-panel-shadow");
            this.$content.addClass("hidden");
            this.$panel_toggle.removeClass("un-panel-close").addClass("un-open-panel un-panel-toggle-shadow").html(this.togglePanelIcon("open"));
        },
        open: function(){
            this.$panel.removeClass("un-panel-closed").addClass("un-panel-shadow");
            this.$content.removeClass("hidden");
            this.$panel_toggle.removeClass("un-open-panel un-panel-toggle-shadow").addClass("un-close-panel").html(this.togglePanelIcon("close"));
        },
        state: function(){
            if (this.model.get("open")===true) {
                if (this.model.get("hidden")===true){
                    this.model.set({
                        hidden: false
                    },{
                        silent: true
                    });
                    this.show();
                    setTimeout(this.open,100);
                }else{
                    this.open();
                }
            }else{
                this.close()
            }
        },
        visible: function(){
            if (this.model.get("hidden")===true) {
                if (this.model.get("open")===true){
                    this.model.set({
                        open: false
                    },{
                        silent: true
                    });
                    this.close();
                    setTimeout(this.hide,1000);
                }else{
                    this.hide();
                }
            }else{
                this.show();
            }
        },
        hide: function(){
            this.$panel.addClass("hidden");
            this.$content.addClass("hidden");
        },
        show: function(){
            this.$panel.removeClass("hidden");
            this.$content.removeClass("hidden");
        },
        togglePanelIcon: function(state){
            if (state=="open"){ state = "right"; }
            else if (state=="close"){ state = "left"; }
            return "<span class='glyphicon glyphicon-chevron-"+state+"'></span><span class='glyphicon glyphicon-chevron-"+state+"'></span>";
        },
        reset: function(){
            this.$content.html("");
        }
    }),
    Notice: Backbone.View.extend({
        events: {
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;

            _.bindAll(this,"render","notify","denotify");
            this.collection.on("show",this.notify);
            this.collection.on("unshow",this.denotify);

            this.$content = this.$el.children(".un-panel-content");
        },
        render: function(){
            return this;
        },
        notify: function(model){
            if (model.get("type")!=="loading") {
                var notice = _.template(this.template, {
                    notice: model,
                    id: model.cid
                });
            }else{
                var loading = model.clone();
                loading.set({
                    message: "Loading..."
                });
                var notice = _.template(this.template, {
                    notice: loading,
                    id: model.cid
                });
            }
            this.$el.append(notice);
        },
        denotify: function(model){
            $("#notice_"+model.cid).remove();
        }
    })
}
UNBOXAPI.Views.Home = {
    Layout: UNBOXAPI.Views.Layout.extend({
        setup: function(){
            _.bindAll(this,"profile","login","home","register");
        },
        profile: function(){
            this.setContent(
                1,
                UNBOXAPI.Views.Home.Profile,
                {
                    model: this.user
                },
                "open"
            );
        },
        login: function(){
            this.setContent(
                1,
                UNBOXAPI.Views.Home.Login,
                {
                    model: this.user,
                    template: this.metadata.templates.getTemplate("Login")
                },
                "open"
            );
        },
        home: function(){
            this.setContent(
                1,
                UNBOXAPI.Views.Home.Profile,
                {
                    model: this.user,
                    templates: this.metadata.templates
                },
                "close"
            );
            this.setContent(
                'main',
                UNBOXAPI.Views.Home.Home,
                {
                    templates: this.metadata.templates
                }
            );
        },
        register: function(){
            this.setContent(
                1,
                UNBOXAPI.Views.Home.Register,
                {
                    model: this.user,
                    template: this.metadata.templates.getTemplate("Register")
                },
                "open"
            );
        }
    }),
    Login: Backbone.View.extend({
        events: {
            "click #submit": "login",
            "focusout input": "updateModel",
            "keyup input": "keyPressHandler"
        },
        initialize: function(options) {
            this.options = options || {};
            this.template = this.options.template;

            this.$username = null;
            this.$password = null;
            this.model = this.model || new UNBOXAPI.Models.User;

            this.username = null;
            this.password = null;
            _.bindAll(this,"login","updateModel","keyPressHandler");
        },
        render: function() {
            this.html = _.template(this.template);
            this.$el.html(this.html);
            this.setup();
            return this;
        },
        setup: function(){
            this.$username = $("#username");
            this.$password = $("#password");
            //setup Google+ button
            //UNBOXAPI.Global.Login.Google.loginButton();
        },
        login: function(){
            var p = this.model.get("password");
            var u = this.model.get("username");
            if ((typeof p == 'undefined' || typeof u == 'undefined')||(p == '' || u == '')||(p == null || u == null)){
                this.model.set({
                    usernmane: this.$username.val(),
                    password: btoa(this.$password.val())
                });
            }
            this.model.login();
        },
        keyPressHandler : function(e){
            if(event.keyCode == 13){
                this.updateModel(e);
                this.login();
            }
        },
        updateModel: function(e) {
            var changed = e.currentTarget;
            var value = $(e.currentTarget).val();
            if (changed.name =='password'){
                value = btoa(value);
            }
            var obj = {};
            obj[changed.name] = value;
            this.model.set(obj);
        }
    }),
    Register: Backbone.View.extend({
        events: {
            "click #Register": "submit",
            "focusout input": "updateModel"
        },
        initialize: function(options) {
            this.options = options || {};
            this.template = this.options.template;

            this.captcha = null;

            _.bindAll(this,"submit","updateModel");
        },
        render: function() {
            this.html = _.template(this.template);
            this.$el.html(this.html);
            this.setup();

            return this;
        },
        setup: function(){
            this.captcha = UNBOXAPI.Global.Login.Google.ReCaptcha.render('captcha');
        },
        submit: function(){
            this.model.url = "user/register";
            if (typeof grecaptcha !== 'undefined') {
                this.model.set('captcha', grecaptcha.getResponse(this.captcha));
            }
            UNBOXAPI.Models.Utils.save({
                model: this.model,
                success: function(model,response,options){
                    //UNBOXAPI.Global.Utils.notice("User Created. Please login to access system.","success");
                }
            });
        },
        updateModel: function(e) {
            var changed = e.currentTarget;
            var value = $(e.currentTarget).val();
            if (changed.name =='password'){
                value = btoa(value);
            }
            var obj = {};
            obj[changed.name] = value;
            this.model.set(obj);
        }
    }),
    Home: Backbone.View.extend({
        initialize: function(options){
            this.options = options || {};
            this.templates = this.options.templates;
            this.render();
        },
        render: function(){

        }
    }),
    Profile: Backbone.View.extend({
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;
            this.render();
        },
        render: function(){

        }
    })
}
UNBOXAPI.Views.Tester = {
    Layout: UNBOXAPI.Views.Layout.extend({
        _init: function(){
            this.models.application = new UNBOXAPI.Models.Record({
                module: this.modules.findWhere({ name: "Applications" })
            });
            this.models.api = new UNBOXAPI.Models.Record({
                module: this.modules.findWhere({ name: "Apis" })
            });
            this.models.login = new UNBOXAPI.Models.Record({
                module: this.modules.findWhere({ name: "Logins" })
            });
            this.models.httpMethod = new UNBOXAPI.Models.Record({
                module: this.modules.findWhere({ name: "HttpMethods" })
            });
            this.models.entryPoint = new UNBOXAPI.Models.Record({
                module: this.modules.findWhere({ name: "Entrypoints" })
            });
            this.models.entryPoint.parameters = new UNBOXAPI.Collections.Records({
                module: this.modules.findWhere({ name: "Parameters" })
            });
            this.models.web_address = new UNBOXAPI.Models.Data({
                key: 'web_address',
                value: ""
            });
            this.models.login.parameters = new UNBOXAPI.Collections.Records({
                module: this.modules.findWhere({ name: "Parameters" })
            });
            this.models.token = new UNBOXAPI.Models.Tokens();
            this.models.test = new UNBOXAPI.Models.Record({
                module: this.modules.findWhere({ name: "Tests" })
            });


            _.bindAll(this,"testSetup","epDetail","requestSetup","output","fetchEntrypoint","resetTester","submitTest");
            this.models.entryPoint.on("sync",this.resetTester);
            this.models.test.on("test",this.submitTest);
            this.output();
            this.testSetup();
            this.epDetail();
            this.requestSetup();
        },
        testSetup: function(){
            this.setContent(
                1,
                UNBOXAPI.Views.Tester.Setup.Panel,
                {
                    application: this.models.application,
                    api: this.models.api,
                    login: this.models.login,
                    httpMethod: this.models.httpMethod,
                    entryPoint: this.models.entryPoint,
                    web_address: this.models.web_address,
                    token: this.models.token,
                    templates: this.metadata.templates
                },
                'open'
            );
        },
        epDetail: function(){
            this.setContent(
                2,
                UNBOXAPI.Views.Tester.EntrypointDetail.Panel,
                {
                    model: this.models.entryPoint,
                    panel: this.collection.getPanel(2),
                    templates: this.metadata.templates
                }
            );
        },
        requestSetup: function(){
            this.setContent(
                3,
                UNBOXAPI.Views.Tester.RequestSetup.Panel,
                {
                    collection: this.models.entryPoint.parameters,
                    panel: this.collection.getPanel(3),
                    web_address: this.models.web_address,
                    token: this.models.token,
                    test: this.models.test,
                    templates: this.metadata.templates
                }
            );
        },
        output: function(){
            this.setContent(
                'main',
                UNBOXAPI.Views.Tester.Output.Panel,
                {
                    model: this.models.test,
                    template: this.metadata.templates.getTemplate("Main")
                }
            );
        },
        submitTest: function(){
            this.models.test.set({
                application_id: this.models.application.get("id"),
                api_id: this.models.api.get("id"),
                entrypoint_id: this.models.entryPoint.get("id"),
                login_id: this.models.login.get("id"),
                web_address: this.models.web_address.getValue()
            });
            this.models.test.url = UNBOXAPI.Global.ajaxURL+"/tester/test";
            UNBOXAPI.Models.Utils.fetch({
                model: this.models.test
            });
        },
        resetTester: function(){
            var panel = this.collection.getPanel(3);
            panel.set({
                hidden: true
            });
            this.models.entryPoint.parameters.url = UNBOXAPI.Global.ajaxURL+"Entrypoints/"+this.models.entryPoint.get("id")+"/related/Parameters";
            UNBOXAPI.Models.Utils.fetch({
                model: this.models.entryPoint.parameters
            });
            this.output();
        },
        fetchEntrypoint: function(){
            UNBOXAPI.Models.Utils.fetch({
                model: this.models.entryPoint,
                options: {},
                success: this.resetTester
            });
        }
    }),
    Setup: {
        Panel: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.templates = this.options.templates;

                this.application = this.options.application;
                this.api = this.options.api;
                this.login = this.options.login;
                this.httpMethod = this.options.httpMethod;
                this.entryPoint = this.options.entryPoint;
                this.web_address = this.options.web_address;

                //prepare dom references
                this.$appSelect = null;
                this.$apiSelect = null;
                this.$loginSelect = null;
                this.$httpMethodSelect = null;
                this.$entryPointSelect = null;
                this.$api_login_panel = null;

                //setup models
                this.token = this.options.token || new UNBOXAPI.Models.Tokens;

                this.template = this.templates.getTemplate("Panel1");
            },
            render: function() {
                this.html = _.template(this.template);
                this.$el.html(this.html);

                this.setup();

                return this;
            },
            setup: function(){
                //setup DOM
                this.$appSelect = $("#application");
                this.$apiSelect = $("#api");
                this.$loginSelect = $("#login");
                this.$httpMethodSelect = $("#http_method");
                this.$entryPointSelect = $("#entry_point");
                this.$api_login_panel = $("#api_login");

                this.applicationSelect = new UNBOXAPI.Views.Global.RelateField({
                    el: this.$appSelect,
                    model: this.application
                });
                this.apiSelect = new UNBOXAPI.Views.Global.DependentRelateField({
                    el: this.$apiSelect,
                    model: this.api,
                    parent: this.application,
                    fetchOnSelect: true
                });
                this.loginSelect = new UNBOXAPI.Views.Global.DependentRelateField({
                    el: this.$loginSelect,
                    model: this.login,
                    disable: true,
                    parent: this.api,
                    fetchOnSelect: true
                });
                this.httpMethodSelect = new UNBOXAPI.Views.Global.RelateField({
                    el: this.$httpMethodSelect,
                    model: this.httpMethod
                });
                this.entryPointSelect = new UNBOXAPI.Views.Global.DependentRelateField({
                    el: this.$entryPointSelect,
                    model: this.entryPoint,
                    parent: this.api,
                    filters: {
                        method: this.httpMethod
                    },
                    fetchOnSelect: true
                });

                //Setup Login Form
                this.loginSubPanel = new UNBOXAPI.Views.Tester.Setup.LoginPanel({
                    el: this.$api_login_panel,
                    model: this.login,
                    api: this.api,
                    token: this.token,
                    web_address: this.web_address,
                    templates: this.templates
                });

            }
        }),
        LoginPanel: Backbone.View.extend({
            events: {
                "click #loginBtn": "login",
                "click #logoutBtn": "logout"
            },
            initialize: function(options) {
                this.options = options|| {};
                this.api = this.options.api;
                this.web_address = this.options.web_address;
                this.token = this.options.token;
                this.templates = this.options.templates || {};
                this.template = this.templates.getTemplate("LoginInfo");

                _.bindAll(this,"render");
                this.model.on("change",this.render);
                this.api.on("change", this.render);
            },
            render: function(){
                if (this.api.get('login_required')) {
                    this.html = _.template(this.template);
                    this.$el.html(this.html);
                    this.setup();
                }else{
                    this.html = "Login not required for API.";
                    this.$el.html(this.html);
                }
                return this.$el;
            },
            setup: function(){
                this.$normalParams = $("#login_normal");
                this.$advancedParams = $("#login_advanced");
                this.$token_info = $("#token_info");
                this.normalParams = new UNBOXAPI.Views.Tester.Setup.LoginParams({
                    el: this.$normalParams,
                    collection: this.model.parameters.where({ type: "normal" }),
                    template: this.templates.getTemplate("LoginParam")
                });
                this.advancedParams = new UNBOXAPI.Views.Tester.Setup.LoginParams({
                    el: this.$advancedParams,
                    collection: this.model.parameters.where({ type: "advanced" }),
                    template: this.templates.getTemplate("LoginParam")
                });
                this.tokenInfo = new UNBOXAPI.Views.Tester.Setup.TokenInfo({
                    el: this.$token_info,
                    model: this.token,
                    template: this.templates.getTemplate("TokenInfo")
                });
            },
            login: function(){
                console.log("Login test");
            },
            logout: function(){
                console.log("Logout test");
            }
        }),
        LoginParams: Backbone.View.extend({
            initialize: function(options) {
                this.options = options || {};
                this.template = this.options.template || {};

                _.bindAll(this,"render");
                this.render();
            },
            render: function(){
                this.html = _.template(this.template,{
                    parameters: this.collection
                });
                this.$el.html(this.html);
                return this;
            }
        }),
        TokenInfo: Backbone.View.extend({
            initialize: function(options) {
                this.options = options || {};
                this.token = this.options.token || new UNBOXAPI.Models.Tokens;
                this.template = this.options.template;

                _.bindAll(this,"render");
                this.render();
            },
            render: function(){
                this.html = _.template(this.template,{
                    token: this.model
                });
                this.$el.html(this.html);
                return this;
            }
        })
    },
    EntrypointDetail: {
        Panel: Backbone.View.extend({
            events: {
                "click #setupRequest": "setupRequest"
            },
            initialize: function(options) {
                this.options = options || {};
                this.panel = this.options.panel || {};
                this.token = this.options.token || {};
                this.templates = this.options.templates || {};

                this.template = this.templates.getTemplate("Panel2");

                this.$ep_main = null;
                this.$ep_parameters = null;
                this.$ep_examples = null;
                this.$ep_exceptions = null;

                _.bindAll(this,"render","panelState","setup");
                this.model.bind("sync",this.panelState);
            },
            render: function(){
                this.html = _.template(this.template);
                this.$el.html(this.html);

                this.setup();

                return this;
            },
            setup: function(){
                this.$ep_main = $("#ep_main");
                this.$ep_parameters = $("#ep_parameters");
                this.$ep_examples = $("#ep_examples");
                this.$ep_exceptions = ("#ep_exceptions");

                this.entryPointDetail = new UNBOXAPI.Views.Tester.EntrypointDetail.MainDetail({
                    el: this.$ep_main,
                    model: this.model,
                    template: this.templates.getTemplate("EntrypointMain")
                });
                this.parameterPanel = new UNBOXAPI.Views.Tester.EntrypointDetail.Parameters({
                    el: this.$ep_parameters,
                    collection: this.model.parameters,
                    template: this.templates.getTemplate("EntrypointParameters")
                });
                /*
                this.examplePanel = new UNBOXAPI.Views.Tester.EntrypointDetail.Examples({
                    el: this.$ep_examples,
                    collection: this.examples,
                    template: this.layout.templates.getTemplate("EntrypointExamples")
                });
                this.exceptionPanel = new UNBOXAPI.Views.Tester.EntrypointDetail.Exceptions({
                    el: this.$ep_exceptions,
                    collection: this.exceptions,
                    template: this.layout.templates.getTemplate("EntrypointExceptions")
                });*/
            },
            setupRequest: function(){
                this.collection.trigger("setupRequest");
            },
            panelState: function(){
                if (this.model.get('name')!==null&&this.model.get('name')!==""){
                    this.panel.set({
                        open: true
                    });
                }else{
                    this.panel.set({
                        hidden: true
                    });
                }
            }
        }),
        MainDetail: Backbone.View.extend({
            events: {
            },
            initialize: function(options){
                this.options = options || {};
                this.template = this.options.template || {};
                _.bindAll(this,"render");
                this.model.bind("sync",this.render);
            },
            render: function(){
                this.html = _.template(this.template,{
                    entrypoint: this.model
                });
                this.$el.html(this.html);
                return this;
            }
        }),
        Parameters: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.template = this.options.template || {};
                _.bindAll(this,"render");
                this.collection.bind("sync",this.render);
            },
            render: function(){
                this.html = _.template(this.template, {
                    parameters: this.collection.where({ url_param: "0"})
                });
                this.$el.html(this.html);
                return this;
            }
        }),
        Examples: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.template = this.options.template || {};
                _.bindAll(this,"render");
                this.collection.bind("sync",this.render);
            },
            render: function(){
                if (this.collection.length>0) {
                    this.html = _.template(this.template, {
                        examples: this.collection
                    });
                    this.$el.html(this.html);
                }
                return this;
            }
        }),
        Exceptions: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.template = this.options.template || {};
                _.bindAll(this,"render");
                this.collection.bind("sync",this.render);
            },
            render: function(){
                if (this.collection.length>0) {
                    this.html = _.template(this.template, {
                        exceptions: this.collection
                    });
                    this.$el.html(this.html);
                }
                return this;
            }
        })
    },
    RequestSetup: {
        Panel: Backbone.View.extend({
            events: {
                "click #sendRequest": "sendRequest",
                "click #generateScript": "generateScript"
            },
            initialize: function(options) {
                this.options = options || {};
                this.panel = this.options.panel || {};
                this.token = this.options.token || {};
                this.templates = this.options.templates || {};
                this.web_address = this.options.web_address || {};
                this.test = this.options.test;

                this.template = this.templates.getTemplate("Panel3");

                this.$request_info = null;
                this.$url_parameters = null;
                this.$request_parameters = null;

                _.bindAll(this,"render","openPanel","submitBtnState","sendRequest","generateScript");
                this.collection.on("sync",this.render);
                this.collection.on("setupRequest",this.openPanel);
                this.collection.bind("sync",this.render);
                this.token.bind("change:access_token",this.submitBtnState);
            },
            render: function(){
                this.html = _.template(this.template);
                this.$el.html(this.html);

                this.setup();
                this.submitBtnState();
                return this;
            },
            setup: function(){
                //setup dom references
                this.$request_info = $("#ep_request_info");
                this.$url_parameters = $("#ep_url_params");
                this.$request_parameters = $("#ep_request_params");

                this.requestInfo = new UNBOXAPI.Views.Tester.RequestSetup.Info({
                    el: this.$request_info,
                    model: this.web_address,
                    template: this.templates.getTemplate("RequestInfo")
                });
                this.urlParams = new UNBOXAPI.Views.Tester.RequestSetup.Parameters({
                    el: this.$url_parameters,
                    parameters: this.collection.where({ url_param: "1" }),
                    template: this.templates.getTemplate("Parameters")
                });
                this.requestPayload = new UNBOXAPI.Views.Tester.RequestSetup.Parameters({
                    el: this.$request_parameters,
                    parameters: this.collection.where({ url_param: "0" }),
                    template: this.templates.getTemplate("Parameters")
                });
            },
            submitBtnState: function() {
                if (!(this.token.get("access_token")==null||typeof this.token.get("access_token")=='undefined')) {
                    $("#sendAPI").removeAttr("disabled");
                }else{
                    $("#sendAPI").attr("disabled",true);
                }
            },
            sendRequest: function(e){
                var payload = $("#RequestPayload").serializeArray();
                var url_params = $("#URLParams").serializeArray();
                this.test.set({
                    payload: payload,
                    url_params: url_params
                });
                this.test.trigger("test");
            },
            generateScript: function(e){

            },
            openPanel: function(){
                this.panel.set({
                    open: true
                });
            }
        }),
        Info: Backbone.View.extend({
            events: {
                "change #web_address": "updateRequestURL"
            },
            initialize: function(options){
                this.options = options || {};
                this.template = this.options.template || {};
                _.bindAll(this,"render","updateRequestURL");
                this.render();
            },
            render: function(){
                this.html = _.template(this.template,{
                    web_address: this.model
                });
                this.$el.html(this.html);
                return this;
            },
            updateRequestURL: function(e){
                var value = $(e.currentTarget).val();
                this.model.set({
                    value: value
                });
            }
        }),
        Parameters: Backbone.View.extend({
            initialize: function(options) {
                this.options = options || {};
                this.parameters = this.options.parameters || [];
                this.template = this.options.template || {};
                _.bindAll(this,"render");
                this.render();
            },
            render: function(){
                var html = "";
                var params = [];
                for (var x=0;x<this.parameters.length;x++) {
                    var type = (!(this.parameters[x].get("api_type_name")==null||this.parameters[x].get("api_type_name")=="")?this.parameters[x].get("api_type"):this.parameters[x].get("data_type"));
                    var field = {
                        name: this.parameters[x].get("name"),
                        required: this.parameters[x].get("required"),
                        value: ""
                    };
                    params[x].set({
                        html: _.template(type.template,{
                            field: field
                        }),
                        type: type.name
                    });
                }
                this.html = _.template(this.template,{
                    parameters: params
                });
                this.$el.html(this.html);
                return this;
            }
        })
    },
    Output: {
        Panel: Backbone.View.extend({
            events: {
                "click .format_type": "changeStyle"
            },
            initialize: function (options) {
                this.options = options || {};
                this.template = this.options.template;

                var Style = Backbone.Model.extend({
                    defaults: {
                        name: "pretty"
                    }
                });

                _.bindAll(this, "render","changeStyle");
                this.model.bind("sync", this.render);

                this.style = new Style();
                this.style.bind("change", this.render);
            },
            render: function () {
                this.html = _.template(this.template,{
                    request: this.model.get("request"),
                    response: this.model.get("response"),
                    style: this.style
                });
                this.$el.html(this.html);

                return this;
            },
            changeStyle: function(e){
                this.style.set({
                    "name": $(e.currentTarget).data("format")
                });
            }
        })
    }
}
UNBOXAPI.Views.Manager = {
    Layout: UNBOXAPI.Views.Layout.extend({
        _init: function(){
            _.bindAll(this,"menu","quickRecord","relateRecord","listView");
        },
        menu: function(module){
            module = module || this.metadata.config.getValue("default");
            this.setContent(
                1,
                UNBOXAPI.Views.Manager.Menu,
                {
                    model: this.metadata,
                    collection: this.modules,
                    template: this.metadata.templates.getTemplate("Menu"),
                    module: module
                },
                'open'
            );
        },
        quickRecord: function(module,action,id){
            if (action=='view'){
                if (!(typeof id == 'undefined' || id == "" || id == null)) {
                    if (!(typeof this.models.current =='Object')) {
                        this.models.current = new UNBOXAPI.Models.Record({
                            module: this.modules.findWhere({ name: module })
                        });
                    }
                    if (id !== this.models.current.get("id")) {
                        this.models.current.clear();
                        this.models.current.set({id: id});
                    }
                } else {
                    this.models.current = new UNBOXAPI.Models.Record({
                        module: this.modules.findWhere({ name: module })
                    });
                }
            }else{
                this.models.current = new UNBOXAPI.Models.Record({
                    module: this.modules.findWhere({ name: module })
                });
            }
            console.log("Test");
            this.setContent(
                2,
                UNBOXAPI.Views.Manager.Record.Panel,
                {
                    modules: this.modules,
                    model: this.models.current,
                    templates: this.metadata.templates,
                    gTemplates: this.gTemplates,
                    panel: 2
                },
                'open'
            );
        },
        relateRecord: function(){
            this.setContent(
                3,
                UNBOXAPI.Views.Manager.Record.Panel,
                {
                    related: true,
                    modules: this.modules,
                    model: this.models.current,
                    templates: this.metadata.templates,
                    panel: 3
                }
            );
        },
        listView: function(){
            //TODO: Add the ListView back in
        }
    }),
    Menu: Backbone.View.extend({
        events: {
            "click .btn": "action"
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;
            this.defaultModule = this.options.defaultModule;
            this.module = this.options.module || false;
        },
        render: function(){
            UNBOX.Translator.setContext(this.model);
            this.html = _.template(this.template,{
                modules: this.collection.models,
                current: this.module
            });
            this.$el.html(this.html);
        },
        action: function(e){
            var module = $(e.currentTarget).data('module');
            var action = $(e.currentTarget).data('action');
        }
    }),
    Record: {
        Panel: Backbone.View.extend({
            initialize: function(options){
                this.options = options || {};
                this.templates = this.options.templates;
                this.gTemplates = this.options.gTemplates;
                this.modules = this.options.modules;
                this.module = this.modules.findWhere({name: this.modules.current});
                this.related = this.options.related || false;

                this.number = this.options.panel;

                this.relate = {};
                this.detail = {};
                this.actions = {};

                this.template = this.templates.getTemplate("Record");

                _.bindAll(this,"setup","setupDOMPointers","render");
                this.module.on("reset",this.setup)
            },
            render: function() {
                this.html = _.template(this.template,{
                    number: this.number
                });
                this.$el.html(this.html);
                this.setupDOMPointers();
                this.setup();
                return this;
            },
            setupDOMPointers: function(){
                this.$relate = $("#Relate_"+this.number);
                this.$actions = $("#RecordActions_"+this.number);
                this.$detail = $("#RecordDetail_"+this.number);
            },
            setup: function(){
                if (this.related){
                    this.relate = new UNBOXAPI.Views.Manager.Record.RelateTo({
                        el: this.$relate,
                        module: this.module,
                        modules: this.modules,
                        template: this.templates.getTemplate("RelateTo")
                    });
                }else{
                    this.actions = new UNBOXAPI.Views.Manager.Record.Actions({
                        el: this.$actions,
                        model: this.model,
                        module: this.module,
                        number: this.number,
                        template: this.templates.getTemplate("RecordActions")
                    });
                    this.detail = new UNBOXAPI.Views.Manager.Record.Detail({
                        el: this.$detail,
                        model: this.model,
                        module: this.module,
                        modules: this.modules,
                        number: this.number,
                        template: this.templates.getTemplate("RecordDetail")
                    });
                }
            }
        }),
        Detail: Backbone.View.extend({
            events: {
                "focusout input": "updateModel",
                "focusout textarea": "updateModel",
                "change select": "updateModel",
                "change .select2": "updateModel"
            },
            initialize: function(options){
                this.options = options || {};
                console.log("Init Detail");
                this.module = this.options.module || {};
                this.modules = this.options.modules || null;
                this.template = this.options.template || {};
                this.number = this.options.number || 2;

                this.$form = null;

                _.bindAll(this,"render","clearForm","updateModel","syncForm");
                this.model.on("save",this.syncForm);
                this.model.on("clear",this.clearForm);
                this.model.on("sync",this.render);

                var modelID = this.model.get('id');
                var modelName = this.model.get('name');
                if ((!(modelID==""||modelID==null||typeof modelID=='undefined'))
                    &&(typeof modelName=='undefined'||modelName==null||modelName=="")){
                    UNBOXAPI.Models.Utils.fetch({
                        model: this.model
                    })
                }else {
                    this.render();
                }
            },
            render: function(){
                this.html = _.template(this.template, {
                    model: this.model,
                    fields: this.module.fields.models,
                    number: this.number
                });
                this.$el.html(this.html);
                this.setupDOMPointers();
                this.setupRelateFields();
                return this;
            },
            setupDOMPointers: function(){
                this.$form = $("#recordDetail_2");
            },
            setupRelateFields: function(){
                var relateFields = this.$el.find(".select2.relate");
                if (!(relateFields==null||typeof relateFields=='undefined'||relateFields.length==0)) {
                    for(var x=0;x<relateFields.length;x++){
                        var moduleName = $(relateFields[x]).data("module");
                        var model = new UNBOXAPI.Models.Record({
                            module: this.modules.findWhere({ name: moduleName })
                        });
                        var relatedField = new UNBOXAPI.Views.Global.RelateField({
                            el: $(relateFields[x]),
                            model: model
                        });
                    }
                }
            },
            clearForm: function(){
                this.$el.trigger('reset');
                this.model.clear();
            },
            syncForm: function(){
                var inputs = this.$form.find(":input");
                for(var x=0;x<inputs.length;x++) {
                    var $input = $(inputs[x]);
                    console.log($input);
                    if ($input.attr('id').indexOf("autogen")==-1) {
                        console.log($input.val());
                    }
                }

            },
            updateModel: function(e) {
                var changed = e.currentTarget;
                var value = $(e.currentTarget).val();
                var obj = {};
                if (changed.name!=="") {
                    obj[changed.name] = value;
                    this.model.set(obj);
                }
            }
        }),
        Actions: Backbone.View.extend({
            events: {
                "click .save-record": "save",
                "click .clear-record": "clear",
                "click .version-record": "version",
                "click .relate-record": "relate"
            },
            initialize: function(options){
                this.options = options || {};
                this.template = this.options.template || {};
                this.module = this.options.module || {};

                _.bindAll(this,"save","clear","render","relate","version");
                this.number = this.options.number || 2;
                this.render();
            },
            render: function(){
                var isNew = (this.model.get('id')==null||this.model.get('id')==""||typeof this.model.get('id')=="undefined");
                this.html = _.template(this.template, {
                    isNew: isNew,
                    module: this.module,
                    number: this.number
                });
                this.$el.html(this.html);
            },
            clear: function(e){
                if ($(e.currentTarget).data('panel')==this.number) {
                    this.model.trigger("clear");
                }
            },
            save: function(e){
                if ($(e.currentTarget).data('panel')==this.number) {
                    this.model.trigger("save");
                }
            },
            version: function(e){
                if ($(e.currentTarget).data('panel')==this.number) {
                    this.model.trigger("version");
                }
            },
            relate: function(e){
                if ($(e.currentTarget).data('panel')==this.number) {
                    this.model.trigger("relate");
                }
            }
        }),
        RelateTo: Backbone.View.extend({
            events: {
                "click .related_module": "relate"
            },
            initialize: function(options){
                this.options = options || {};
                this.module = this.options.module;
                this.modules = this.options.modules;
                this.collection = this.module.relationships;

                this.template = this.options.template || {};
            },
            render: function(){
                this.html = _.template(this.template, {
                    modules: this.collection.models
                });
                this.$el.html(this.html);
                return this;
            },
            relate: function(e){
                var $selected = $(e.currentTarget);
                var moduleName = $selected.data('relationship');
                var action = $selected.parent().id;
                this.module.reset(this.modules.findWhere({ name: moduleName }).toJSON());
            }

        })
    }
}
UNBOXAPI.Models = {
    Utils: {
        fetch: function(object){
            var model = object.model || null;
            var options = object.options || {};
            var success = object.success || function () {};
            var error = object.error || function () {};
            if (model!==null) {
                //var notice = UNBOXAPI.Global.Utils.Loading.start("Loading " + model.name + " at " + model.url);
                options.success = function (model, response, options) {
                    success(model,response,options);
                    //UNBOXAPI.Global.Utils.Loading.done(notice);
                }
                options.error = function (model, response, options) {
                    error(model,response,options);
                    //UNBOXAPI.Global.Utils.Loading.done(notice);
                    //UNBOXAPI.Global.Utils.log(response);
                }
                return model.fetch(options);
            }else{
                console.log("No model passed to fetch");
            }
        },
        save: function(object){
            var model = object.model || null;
            var attributes = object.attributes || null;
            var options = object.options || {};
            var success = object.success || function () {};
            var error = object.error || function () {};
            //var notice = UNBOXAPI.Global.Utils.Loading.start("Loading "+model.name+" at "+model.url);
            if (model!==null) {
                options.success = function (model, response, options) {
                    success(model,response,options);
                    //UNBOXAPI.Global.Utils.Loading.done(notice);
                }
                options.error = function (model, response, options) {
                    error(model,response,options);
                    //UNBOXAPI.Global.Utils.Loading.done(notice);
                    //UNBOXAPI.Global.Utils.log(response);
                }
                model.save(attributes, options);
            }else{
                console.log("No model passed to save");
            }
        }
    },
    //Modules
    Record: Backbone.Model.extend({
        initialize: function(options){
            this.options = options || {};
            this.urlRoot = this.options.urlRoot || null;
            this.module = this.options.module || this.collection.module;

            this.name = this.module.get("name");

            if (this.urlRoot == null){
                this.urlRoot = UNBOXAPI.Global.ajaxURL + this.name;
            }else{
                this.urlRoot = UNBOXAPI.Global.ajaxURL + this.urlRoot;
            }

            _.bindAll(this, 'getValue');
        },
        getValue: function(){
            var value = "";
            var valueSetup = this.module.config.get('value');
            if(valueSetup==null||typeof valueSetup == 'undefined'){
                value = this.get('name');
            }else{
                for(var x=0;x<valueSetup.length;x++){
                    if (this.has(valueSetup[x])){
                        value += this.get(valueSetup[x]);
                    }else{
                        value += valueSetup[x];
                    }
                    if ((x+1)<valueSetup.length){
                        value += " ";
                    }
                }
            }
            return value;
        }
    }),
    Tokens: Backbone.Model.extend({
        name: "Token",
        defaults: {
            access_token: null,
            refresh_token: null,
            expires_in: null,
            token_type: null
        }
    }),
    Requests: Backbone.Model.extend({
        name: "Request",
        defaults: {
            request: "",
            response: ""
        }
    }),
    //View Models
    Panels: Backbone.Model.extend({
        initialize: function(){
        },
        defaults: {
            open: false,
            hidden: true,
            number: 0,
            content: null
        }
    }),
    MainPanel: Backbone.Model.extend({
        initialize: function(options){
            this.options = options || {};
            this.panels = this.options.panels;

            _.bindAll(this,"resize");
            this.panels.on("change:open",this.resize);
        },
        defaults: {
            width: 100,
            content: null
        },
        resize: function(){
            console.log("Resizing");
            var openPanels = this.panels.where({
                open: true
            });
            this.set({
                width: (4 - openPanels.length) * 25
            });
        }
    }),
    Notices: Backbone.Model.extend({
        defaults: {
            type: null,
            level: null,
            show: false,
            state: null,
            message: null
        }
    }),
    //Metadata Models
    Data: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        default: {
            key: null,
            value: null
        },
        getValue: function(){
            return this.get('value');
        }
    }),
    Input: Backbone.Model.extend({
        initialize: function(){
            this.templates = null;
            this.html = null;
            this.options = null;

            _.bindAll(this,"getHTML");
        },
        default: {
            type: null,
            placeholder: "",
            help: "",
            disabled: false,
            class: "",
            name: "",
            id: "",
            value: null,
            required: false
        },
        getHTML: function(value){
            if (typeof value=='undefined'){
                value='';
            }
            if (this.template!==null) {
                if (this.html == null) {
                    this.html = _.template(this.template, {
                        value: value,
                        input: this
                    });
                }
                return this.html;
            }
            return false;
        }
    }),
    Fields: Backbone.Model.extend({
        initialize: function(){
            this.input = null;
            this.html = null;
            _.bindAll(this,"buildInput","getHTML");
            //this.on("change",this.buildInput);
        },
        default: {
            name: null,
            data_type: null,
            label: null,
            validation: [],
            form: []
        },
        buildInput: function(){
            var formMeta = this.get("form");
            if (!(formMeta==false||formMeta==null||typeof formMeta =='undefined')){
                var validation = this.get("validation");
                if (!formMeta.hasOwnProperty('id')){
                    formMeta['id'] = this.get("name");
                }
                if (!formMeta.hasOwnProperty('name')){
                    formMeta['name'] = this.get("name");
                }
                if (validation.hasOwnProperty('required')){
                    formMeta['required'] = validation['required'];
                }
                this.input = new UNBOXAPI.Models.Input(formMeta);
                this.input.template = this.templates.getTemplate(formMeta['type']);
                if (formMeta['type']=='select') {
                    var options = new UNBOXAPI.Collections.Data;
                    options.reset(formMeta['options']);
                    this.input.options = options;
                }
            }else{
                this.input = false;
            }
        },
        getHTML: function(value){
            if (this.input==null){
                this.buildInput();
            }
            if (typeof value=='undefined'){
                value='';
            }
            if (this.input!==false) {
                if (this.html == null) {
                    this.html = _.template(this.templates.getTemplate("label"), {
                        text: this.get('label'),
                        name: this.input.get('name')
                    });
                    this.html += this.input.getHTML(value);
                    this.html += _.template(this.templates.getTemplate("help"), {
                        text: this.input.get('help')
                    });
                }
                return this.html;
            }
            return false;
        }
    }),
    Relationships: Backbone.Model.extend({
        default: {
            type: null,
            module: null,
            model: null
        }
    }),
    Modules: Backbone.Model.extend({
        initialize: function(options){
            this.templates = {};
            this.config = new UNBOXAPI.Collections.Config;
            this.fields = new UNBOXAPI.Collections.Fields;
            this.relationships = new UNBOXAPI.Collections.Relationships;
            this.labels = new UNBOXAPI.Collections.Labels;

            _.bindAll(this,"setup","setupConfig","setupFields","setupRelationships","setupLabels");
            this.on("change:fields",this.setupConfig);
            this.on("change:config",this.setupTemplates);
            this.on("change:relationships",this.setupRelationships);
            this.on("change:labels",this.setupLabels);
        },
        urlRoot: UNBOXAPI.Global.ajaxURL+"metadata/",
        default:{
            name: "",
            label: null,
            label_plural: null,
            fields: null,
            relationships: null,
            labels: {}
        },
        setup: function(){
            this.setupConfig();
            this.setupFields();
            this.setupRelationships();
            this.setupLabels();
        },
        setupFields: function(){
            var fields = this.get('fields');
            var models = [];
            var c = 0;
            for (var field in fields){
                fields[field].name = field;
                var model = new UNBOXAPI.Models.Fields(fields[field]);
                model.templates = this.templates;
                models[c] = model;
                c++;
            };
            this.fields.reset(models);
        },
        setupConfig: function(){
            this.config.refresh(this.get('config'));
        },
        setupRelationships: function(){
            this.relationships.reset(this.get('relationships'));
        },
        setupLabels: function(){
            this.labels.refresh(this.get('labels'));
        }
    }),
    Layouts: Backbone.Model.extend({
        initialize: function(){
            this.templates = new UNBOXAPI.Collections.Templates;
            this.config = new UNBOXAPI.Collections.Config;
            this.labels = new UNBOXAPI.Collections.Labels;

            _.bindAll(this,"setup","setupTemplates","setupConfig","setupLabels");
            this.on("change:config",this.setupConfig);
            this.on("change:templates",this.setupTemplates);
            this.on("change:labels",this.setupLabels)

            this.setup();
        },
        urlRoot: UNBOXAPI.Global.ajaxURL+"metadata/",
        default:{
            name: "",
            label: null,
            label_plural: null,
            icon: "",
            link: null,
            links: null,
            labels: {},
            templates: {},
            config: {}
        },
        setup: function(){
            this.setupTemplates();
            this.setupConfig();
            this.setupLabels();
        },
        setupTemplates: function(){
            this.templates.refresh(this.get('templates'));
        },
        setupConfig: function(){
            this.config.refresh(this.get('config'));
        },
        setupLabels: function(){
            this.labels.refresh(this.get('labels'));
        }
    }),
    //User Model
    User: Backbone.Model.extend({
        initialize: function(){
            this.checkin = null;
            _.bindAll(this, 'getValue',"loggedIn","login","logout");
        },
        urlRoot: UNBOXAPI.Global.ajaxURL+"users/me",
        default: {
            id: "",
            name: "",
            username: "",
            first_name: "",
            last_name: "",
            email: "",
            password: ""
        },
        getValue: function(){
            return this.get('first_name')+" "+this.get('last_name');
        },
        login: function(){
            //var loading = UNBOXAPI.Global.Utils.Loading.start("Logging In");
            $.ajax({
                url: UNBOXAPI.Global.ajaxURL+'users/login',
                type: "POST",
                context: this,
                data: {
                    username: this.get('username'),
                    password: this.get('password')
                },
                success: function(data){
                    this.fetch();
                    this.checkin = setInterval(function(model) {
                        model.fetch();
                    }, 600000,this);
                },
                error: function(data){
                    console.log(data);
                    //UNBOXAPI.Global.Utils.log(data);
                },
                dataType: 'json'
            }).done(function() {
                //UNBOXAPI.Global.Utils.Loading.done(loading);
            });
        },
        loggedIn: function(){
            var id = this.get('id');
            return !(id == null || typeof id == 'undefined' || id == false || id=="");
        },
        logout: function() {
            $.ajax({
                url: UNBOXAPI.Global.ajaxURL+'users/logout',
                type: "POST",
                context: this,
                success: function(data){
                    this.clear();
                    window.clearInterval(this.checkin);
                    this.checkin = null;
                },
                error: function(data){
                    console.log(data);
                    //FIXME::Wierd error on logout. Everything gets removed properly in backend, but it still throws error.
                    this.clear();
                    window.clearInterval(this.checkin);
                    this.checkin = null;
                },
                dataType: 'json'
            }).done(function() {
                //UNBOXAPI.Global.Utils.Loading.done(loading);
            });
        }
    })
}
UNBOXAPI.Collections = {
    Utils: {
        fetch: function (object) {
            var options = object.options || {};
            var success = object.success || function () {};
            var error = object.error || function () {};
            var collection = object.collection || null;
            if (collection !== null) {
                //var notice = UNBOXAPI.Global.Utils.Loading.start("Loading " + collection.name + " at " + collection.url);
                options.success = function (model, response, options) {
                    success(model,response,options);
                    //UNBOXAPI.Global.Utils.Loading.done(notice);
                }
                options.error = function (model, response, options) {
                    error(model,response,options);
                    //UNBOXAPI.Global.Utils.Loading.done(notice);
                    //UNBOXAPI.Global.Utils.log(response);
                }
                return collection.fetch(options);
            }else{
                console.log("No Collection passed to fetch method");
            }
        }
    },
    Records: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.module = this.options.module || new UNBOXAPI.Models.Modules;

            this.name = this.module.get("name");

            if (this.url == null){
                this.url = UNBOXAPI.Global.ajaxURL + this.name;
            }else{
                this.url = UNBOXAPI.Global.ajaxURL + this.urlRoot;
            }
        },
        model: UNBOXAPI.Models.Record
    }),
    Data: Backbone.Collection.extend({
        model: UNBOXAPI.Models.Data
    }),
    Panels: Backbone.Collection.extend({
        initialize: function(options){
            _.bindAll(this,"getPanel");
        },
        model: UNBOXAPI.Models.Panels,
        getPanel: function(number){
            return this.findWhere({
                number: number
            });
        }
    }),
    Notices: Backbone.Collection.extend({
        initialize: function(){
            _.bindAll(this,"log","appLoading","done");
        },
        model: UNBOXAPI.Models.Notices,
        log: function(model){
            this.add(model);
            switch (model.get("type")){
                case "loading":
                    if (!this.appLoading()){
                        model.set({
                            state: "showing"
                        });
                        this.trigger("show",model);
                    }
                    break;
                default:
                    if (model.get("show")==true){
                        this.trigger("show",model);
                        setTimeout(this.done,10000,model);
                    }
            }
        },
        appLoading: function(){
            var model = this.findWhere({
                type: "loading",
                show: true,
                state: "showing"
            });
            return typeof model == 'object';
        },
        done: function(model){
            model = this.get(model);
            model.set({
                state: "done"
            });
            switch (model.get("type")){
                case "loading":
                    if (!this.appLoading()){
                        this.trigger("unshow",model);
                    }
                    break;
                default:
                    if (model.get("show")==true){
                        this.trigger("unshow",model)
                    }
            }
        }
    }),
    //Metadata Handling
    MetaData: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            _.bindAll(this,"fetchAll","setup","setupConfig","setupModules","setupLayouts","setupTemplates");
            this.config = new UNBOXAPI.Collections.Config;
            this.templates = new UNBOXAPI.Collections.Templates;
            this.modules = new UNBOXAPI.Collections.Modules;
            this.layouts = new UNBOXAPI.Collections.Layouts;
            this.on("reset",this.setup);
        },
        name: "Application Metadata",
        url: UNBOXAPI.Global.ajaxURL + "metadata/",
        model: UNBOXAPI.Models.Data,
        fetchAll: function(){
            return UNBOXAPI.Collections.Utils.fetch({
                collection: this,
                options: {
                    reset: true
                },
                success: function(collection,response,options) {
                    //
                },
                fail: function(collection,response,options){
                    //TODO: Failure handling on Metadata fetch
                    console.log('Broken.')
                }
            });
        },
        setup: function(){
            this.setupConfig();
            this.setupTemplates();
            this.setupModules();
            this.setupLayouts();
        },
        setupConfig: function(){
            var config = this.findWhere({
                key: "config"
            });
            this.config.refresh(config.get('value'));
        },
        setupModules: function(){
            var moduleConfig = this.findWhere({
                key: "modules"
            });
            var modules = moduleConfig.get('value');
            var models = [];
            var c = 0;
            if (modules.length>0) {
                for (var module in modules){
                    var model = new UNBOXAPI.Models.Modules(modules[module]);
                    model.templates = this.templates;
                    model.setup();
                    models[c] = model;
                    c++;
                }
            }
            this.modules.reset(models);
        },
        setupLayouts: function(){
            var layoutConfig = this.findWhere({
                key: "layouts"
            });
            this.layouts.reset(layoutConfig.get('value'));
        },
        setupTemplates: function(){
            var templates = this.findWhere({
                key: "templates"
            });
            this.templates.refresh(templates.get('value'));
        }
    }),
    Templates: Backbone.Collection.extend({
        initialize: function(options){
            _.bindAll(this,"refresh","getTemplate");
        },
        model: UNBOXAPI.Models.Data,
        refresh: function(templates){
            templates = typeof templates !== "undefined" ? templates : false;
            if (templates!==false){
                this.reset();
                for(var key in templates){
                    this.add({
                        key: key,
                        value: templates[key]
                    });
                }
            }
        },
        getTemplate: function(template){
            var model = this.findWhere({key: template});
            if (!(model==null||typeof model == 'undefined')){
                return model.getValue();
            }else{
                return null;
            }
        }
    }),
    Layouts: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.current = new UNBOXAPI.Models.Layouts;

            _.bindAll(this,"setCurrent");
        },
        model: UNBOXAPI.Models.Layouts,
        setCurrent: function(layout) {
            var model = this.findWhere({name: layout});
            if (model==null||typeof model=='undefined'){
                return false;
            }
            this.current = layout;
            return true;
        }
    }),
    Modules: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.current = new UNBOXAPI.Models.Modules;
            _.bindAll(this,"setCurrent");
        },
        model: UNBOXAPI.Models.Modules,
        setCurrent: function(module) {
            var model = this.findWhere({name: module});
            if (model==null||typeof model=='undefined'){
                return false;
            }
            this.current = module;
            return true;
        }
    }),
    Fields: Backbone.Collection.extend({
        model: UNBOXAPI.Models.Fields
    }),
    Relationships: Backbone.Collection.extend({
        model: UNBOXAPI.Models.Relationships
    }),
    Config: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            _.bindAll(this,"refresh","getValue");
        },
        url: UNBOXAPI.Global.ajaxURL + "metadata/config/",
        model: UNBOXAPI.Models.Data,
        refresh: function(config){
            config = typeof config !== "undefined" ? config : false;
            if (config!==true){
                this.reset();
                for(var key in config){
                    this.add({
                        key: key,
                        value: config[key]
                    });
                }
            }
        },
        getValue: function(key){
            var model = this.findWhere({key: key});
            if (!(model==null||typeof model == 'undefined')){
                return model.getValue();
            }else{
                return null;
            }
        }
    })
}
UNBOXAPI.Collections.Labels = UNBOXAPI.Collections.Config.extend({
    url: UNBOXAPI.Global.ajaxURL + "metadata/labels",
});










