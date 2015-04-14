Backbone.View.prototype.close = function(){
    this.$el.html("");
    this.unbind();
}

var UNBOXAPI = UNBOXAPI || {};

UNBOXAPI.Global = {
    ajaxURL: "api/",
    Utils: {
        //Not using
        Loading: {
            start: function (data) {
                var notice = new UNBOXAPI.Models.Notices;
                notice.set({
                    type: "loading",
                    level: "info",
                    show: true,
                    message: data
                });
                UNBOX.collections.notices.log(notice);
                return notice;
            },
            done: function (model){
                var notice = UNBOX.collections.notices.done(model);
                $("body").css("cursor", "auto");
            }
        },
        log: function(response){
            var notice = new UNBOXAPI.Models.Notices;
            notice.set({
                type: "warning",
                level: "debug",
                show: true,
                message: response.status + ": " + response.statusText
            });
            UNBOX.collections.notices.log(notice);
        },
        notice: function(message,type){
            var notice = new UNBOXAPI.Models.Notices;
            notice.set({
                type: type,
                level: "info",
                show: true,
                message: message
            });
            UNBOX.collections.notices.log(notice);
        }
    },
    Login: {
        Google: {
            loginButton: function(authResult) {
                if (typeof gapi !== 'undefined') {
                    if (typeof authResult == 'undefined') {
                        var params = {
                            'theme': 'light',
                        };
                        gapi.signin.render('gLogin', params);
                    } else {
                        if (authResult['status']['signed_in']) {
                            document.getElementById('gLoginWrapper').setAttribute('style', 'display: none');
                            gapi.client.load('plus', 'v1', UNBOXAPI.Global.Login.Google.apiClientLoaded);
                        } else {
                            console.log(authResult);
                        }
                    }
                }
            },
            apiClientLoaded: function () {
                var request = gapi.client.plus.people.get({
                    'userId': 'me'
                });
                request.execute(UNBOXAPI.Global.Login.Google.handleProfile);
            },
            handleProfile: function (resp) {
                console.log(resp);
                console.log('Retrieved profile for:' + resp.displayName);
            },
            ReCaptcha: {
                render: function(element) {
                    if (typeof grecaptcha !== 'undefined') {
                        return grecaptcha.render(element, {
                            'sitekey': '6Ldh2QMTAAAAAIYd2mUJBSek7MaBWc3X8yYPv6bE',
                            'theme': 'light'
                        });
                    }
                }
            }
        }
    }
}
UNBOXAPI.App = Backbone.Router.extend({
    routes: {
        "help": "help",
        "about": "about",
        "manage": "manager",
        "manage/:module/:action": "quickRecord",
        "manage/:module/:action/:id": "quickRecord",
        "test": "tester",
        "login": "home",
        "register": "register",
        '*path': 'defaultRoute'
    },
    initialize: function (options) {
        this.options = options || {};
        this.metadata = new UNBOXAPI.Collections.MetaData;
        this.user = new UNBOXAPI.Models.User;
        this.view = null;
        this.layout = null;

        _.bindAll(this,"start","setupView");
        this.user.on("reset",this.metadata.fetchAll);
    },
    start: function(){
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
            }
        ];
        var queue = new UNBOXAPI.Queue(functions,function(){
            Backbone.history.start({
                root: window.location.pathname
            });
        });
        queue.process();
    },
    setupView: function(layout) {
        this.metadata.layouts.setCurrent(layout);
        if (this.metadata.layouts.current.config.getValue("enabled")){
            if ((this.metadata.layouts.current.config.getValue("login")&&this.user.loggedIn())||(this.metadata.layouts.current.config.getValue('login')==false)){
                if (this.view == null) {
                    this.view = new UNBOXAPI.Views.AppView({
                        el: $("body"),
                        metadata: this.metadata
                    });
                }else {
                    this.view.changeLayout(layout);
                }
            }else{
                this.navigate("login",{ trigger: true });
            }
        }else{
            this.navigate("",{ trigger: true});
        }
    },
    //Route functions
    help: function () {

    },
    about: function () {

    },
    manager: function (module) {
        this.setupView("Manager");

        this.view.models.request = new UNBOXAPI.Models.Requests;
        this.view.setContent(
            1,
            UNBOXAPI.Views.Manager.Actions,
            {
                collection: this.metadata.modules,
                module: module
            },
            'open'
        );
        this.view.setContent(
            'main',
            UNBOXAPI.Views.Manager.ListView.Panel,
            {
                model: this.view.models.request
            }
        );
            /*
             UNBOX.views.manager.applicationSelect = new UNBOXAPI.Views.Manager.ListView.ApplicationSelect({
             el: $("#application"),
             collection: UNBOX.collections.applications
             });
             UNBOX.views.manager.apiSelect = new UNBOXAPI.Views.Manager.ListView.APISelect({
             el: $("#api"),
             collection: UNBOX.collections.apis
             });
             UNBOX.views.manager.httpMethodSelect = new UNBOXAPI.Views.Manager.ListView.HttpMethodSelect({
             el: $("#httpMethod"),
             collection: UNBOX.collections.httpMethods
             });
             UNBOX.views.manager.listView = new UNBOXAPI.Views.Manager.ListView.List({
             el: $("#list"),
             collection: UNBOX.collections.entryPoints,
             model: UNBOX.models.entryPoint
             });
             //Setup cascading dropdowns, and linked models
             UNBOX.views.manager.applicationSelect.apiSelect = UNBOX.views.manager.apiSelect;
             UNBOX.views.manager.applicationSelect.entryPointList = UNBOX.views.manager.entryPointList;
             UNBOX.views.manager.apiSelect.httpMethodSelect = UNBOX.views.manager.httpMethodSelect;
             UNBOX.views.manager.apiSelect.entryPointList = UNBOX.views.manager.entryPointList;
             UNBOX.views.manager.httpMethodSelect.entryPointList = UNBOX.views.manager.entryPointList;
             UNBOX.views.output.listView = UNBOX.views.manager.entryPointList;
             */
    },
    quickRecord: function(module,action,id){
        if (this.metadata.layouts.current.get('name') !== "Manager") {
            this.setupView("Manager");
        }
        if (!(module == "" || typeof module == 'undefined' || module == null)) {
            if (this.metadata.modules.current.get("name") !== module) {
                this.metadata.modules.setCurrent(module);
            }
            if (!(action == "" || typeof action == 'undefined' || action == null)) {
                if (action == "create" || action == "view") {
                    if (typeof this.view.current == 'Object') {
                        if (model == UNBOX.modules.current) {
                            if (!(typeof id == 'undefined' || id == "" || id == null)) {
                                if (id !== this.view.current.get("id")) {
                                    this.view.current.clear();
                                    this.view.current.set({id: id});
                                }
                            } else {
                                this.view.current.clear();
                            }
                        } else {
                            this.view.current = new UNBOXAPI.Models[module];
                        }
                    } else {
                        this.view.current = new UNBOXAPI.Models[module];
                        if (id !== this.view.current.get("id")) {
                            this.view.current.set({id: id});
                        }
                    }
                    this.view.setContent(
                        2,
                        UNBOXAPI.Views.Manager.QuickRecord,
                        {
                            module: this.metadata.modules.current,
                            model: this.view.current
                        },
                        'open'
                    );
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
        this.view.models.entryPoint = new UNBOXAPI.Models.EntryPoints;
        this.view.models.token = new UNBOXAPI.Models.Tokens;
        this.view.collections.parameters = new UNBOXAPI.Collections.Parameters({
            entryPoint: this.view.models.entryPoint
        });
        this.view.models.request = new UNBOXAPI.Models.Requests;
        this.view.setContent(
            1,
            UNBOXAPI.Views.Tester.Setup.Panel,
            {
                entryPoint: this.view.models.entryPoint,
                token: this.view.models.token
            },
            'open'
        );
        this.view.setContent(
            2,
            UNBOXAPI.Views.Tester.EntryPointDetail.Panel,
            {
                model: this.view.models.entryPoint,
                collection: this.view.collections.parameters,
                token: this.view.models.token
            }
        );
        this.view.setContent(
            3,
            UNBOXAPI.Views.Tester.Parameters.Panel,
            {
                collection: this.view.collections.parameters,
                token: this.view.models.token,
                request: this.view.models.request
            }
        );
        this.view.setContent(
            'main',
            UNBOXAPI.Views.Tester.Output.Panel,
            {
                model: this.view.models.request
            }
        );
    },
    login: function(){
        if (this.metadata.layouts.current.get('name') !== "Home") {
            this.setupView("Home");
        }
        this.view.setContent(
            1,
            UNBOXAPI.Views.Home.Login,
            {
                model: this.user
            },
            "open"
        );
    },
    home: function(){
        this.setupView("Home");
        if (!this.user.loggedIn()){
            this.login();
        }else{

        }
    },
    register: function(){
        if (this.metadata.layouts.current.get('name') !== "Home") {
            this.setupView("Home");
        }
        if (!this.user.loggedIn()){
            this.view.setContent(
                1,
                UNBOXAPI.Views.Home.Register,
                {
                    model: this.user
                },
                "open"
            );
        }else{
            this.navigate("profile");
            this.profile();
        }
    },
    profile: function(){
        if (this.metadata.layouts.current.get('name') !== "Home") {
            this.setupView("Home");
        }
        if (this.user.loggedIn()){
            this.view.setContent(1,UNBOXAPI.Views.Home.Profile,null,'open');
        }else{
            this.navigate("login");
            this.login();
        }
    },
    defaultRoute: function () {
        this.home();
    }
});
UNBOXAPI.Views = {
    DynamicOption: Backbone.View.extend({
        tagName: "option",

        initialize: function(){
            _.bindAll(this, 'render');
        },
        render: function(){
            $(this.el).attr('value', this.model.get('id')).html(_.escape(this.model.getValue()));
            return this;
        }
    }),
    DynamicSelect: Backbone.View.extend({
        events: {
            "change": "changeSelected"
        },
        initialize: function(){
            _.bindAll(this, 'addOne', 'addAll');
            this.collection.bind('reset', this.addAll);
            if (this.collection.length>0){
                this.addAll();
            }
        },
        addOne: function(model){
            var selectView = new UNBOXAPI.Views.DynamicOption({ model: model });
            this.selectViews.push(selectView);
            $(this.el).append(selectView.render().el);
        },
        addAll: function(){
            $(this.el).select2('destroy');
            _.each(this.selectViews, function(selectView) {
                selectView.remove();
            });
            this.selectViews = [];
            this.collection.each(this.addOne);
            if (this.selectedId) {
                $(this.el).val(this.selectedId);
            }
            $(this.el).select2();
        },
        changeSelected: function(){
            this.setSelectedId($(this.el).val());
        },
        populateFrom: function(url) {
            this.collection.url = UNBOXAPI.Global.ajaxURL + url;
            UNBOXAPI.Collections.Utils.fetch({
                collection: this.collection,
                options: {
                    reset: true
                }
            });
            this.setDisabled(false);
        },
        setDisabled: function(disabled) {
            $(this.el).attr('disabled', disabled);
        }
    }),
    Drawer: Backbone.View.extend({
        el: $("#drawer"),
        events: {
            "click .drawer-close-btn":  "close"
        },
        size: "sm",
        backdrop: true,
        exit_key_exit: true,
        initialize: function(options) {
            this.$drawer = $("#drawerContent");
            this.render();
        },
        render: function(){
            this.$drawer.html(this.options.data);
            this.$el.addClass("drawer-open");
            this.$el.removeClass("hidden");
            return this;
        },
        close: function () {
            this.$drawer.html(this.options.data);
            this.$el.removeClass("drawer-open");
            this.$el.addClass("hidden");
        }
    }),
    Modal: Backbone.View.extend({
        el: $("#modal"),
        events: {
            "click .close":  "close"
        },
        size: "sm",
        backdrop: true,
        exit_key_exit: true,
        initialize: function(options) {
            this.options = options || {};
            this.size = this.options.size !== typeof 'undefined' ? this.options.size : this.size;
            this.backdrop = this.options.backdrop !== typeof 'undefined' ? this.options.backdrop : this.backdrop;
            this.exit_key_exit = this.options.exit_key_exit !== typeof 'undefined' ? this.options.exit_key_exit : this.exit_key_exit;

            this.modalDiag = $('#modalDiaglog');
            this.modalHead = $('#modalHead');
            this.modalBody = $('#modalBody');
            this.modalFoot = $('#modalFoot');
            this.render();
        },
        render: function(){
            this.modalDiag.addClass("modal-"+this.size);
            this.modalHead.html(this.options.head);
            this.modalBody.html(this.options.body);
            this.modalFoot.html(this.options.foot);
            this.$el.modal({
                backdrop: this.backdrop,
                keyboard: this.exit_key_exit
            });
            return this;
        },
        close: function(){
            this.$el.modal('hide');
            this.modalHead.html('');
            this.modalBody.html('');
            this.modalFoot.html('');
            this.modalDiag.removeClass("modal-sm");
            this.modalDiag.removeClass("modal-lg");
        }
    }),
    AppView: Backbone.View.extend({
        events: {
        },
        initialize: function(options) {
            this.options = options || {};
            this.metadata = this.options.metadata || null;
            this.models = {};
            this.collections = {};
            this.collection = new UNBOXAPI.Collections.Panels;
            this.collection.add([
                new UNBOXAPI.Models.Panels({
                    number: 1
                }),
                new UNBOXAPI.Models.Panels({
                    number: 2
                }),
                new UNBOXAPI.Models.Panels({
                    number: 3
                })
            ]);
            this.model = new UNBOXAPI.Models.MainPanel({
                panels: this.collection
            });
            this.notices = new UNBOXAPI.Collections.Notices;

            //Dom elements
            this.$panel1 = $("#panel1");
            this.$panel2 = $("#panel2");
            this.$panel3 = $("#panel3");
            this.$main = $('#main');
            this.$navBar = $("#main-nav");
            this.$notices = $("#notices");

            //build nav
            this.nav = new UNBOXAPI.Views.NavBar({
                el: this.$navBar,
                collection: this.metadata.layouts,
                model: this.metadata.layouts.current,
                template: this.metadata.templates.getTemplate("navBtns")
            });
            //build notice
            this.notice = new UNBOXAPI.Views.Notice({
                el: this.$notices,
                collection: this.notices,
                template: this.metadata.templates.getTemplate("notice")
            });
            //build main
            this.main = new UNBOXAPI.Views.Main({
                el: this.$main,
                model: this.model
            });
            //build panels
            var panel_template = this.metadata.templates.getTemplate("panel");
            this.panel1 = new UNBOXAPI.Views.Panel({
                el: this.$panel1,
                model: this.collection.getPanel(1),
                template: panel_template
            });
            this.panel2 = new UNBOXAPI.Views.Panel({
                el: this.$panel2,
                model: this.collection.getPanel(2),
                template: panel_template
            });
            this.panel3 = new UNBOXAPI.Views.Panel({
                el: this.$panel3,
                model: this.collection.getPanel(3),
                template: panel_template
            });
            _.bindAll(this,"reset","render","changeLayout","setContent");

            this.start();
        },
        render: function() {
            return this;
        },
        start: function(){
            var models = this.metadata.layouts.current.config.getValue("bootstrap");
            if (!(models==null||typeof models=='undefined')){
                this.collections = {};
                this.bootstrap(models,this.render)
            }else{
                this.render();
            }
        },
        bootstrap: function(models,callback){
            var functions = [];
            for (var x = 0; x < models.length; x++) {
                this.collections[models[x]] = new UNBOXAPI.Collections[models[x]];
                functions[x] = {
                    f: UNBOXAPI.Collections.Utils.fetch,
                    p: {
                        collection: this.collections[models[x]]
                    }
                }
            }
            var queue = new UNBOXAPI.Queue(functions,callback);
            return queue.process();
        },
        changeLayout: function(layout){
            this.models = {};
            this.collections = {};
            this.reset();
            this.start();
        },
        setContent: function(area,view,view_options,state){
            state = state || 'hide';
            if (area=='main'){
                var pane = this.model;
            }else{
                var pane = this.collection.getPanel(area);
            }
            view_options.panel = pane;
            view_options.layout = this.metadata.layouts.current;
            pane.set({
                content: new view(view_options)
            });
            if (area!=='main') {
                pane.trigger(state);
            }
        },
        reset: function(panel1,panel2,panel3){
            this.panel1.reset(panel1);
            this.panel2.reset(panel2);
            this.panel3.reset(panel3);
            this.main.reset();
        }
    }),
    NavBar: Backbone.View.extend({
        events: {
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;

            _.bindAll(this,"render");
            this.collection.on("change",this.render);
            this.model.on("reset",this.render);
            this.render();
        },
        render: function(){
            this.html = _.template(this.template,{
                current: this.model,
                modules: this.collection.models,
                links: this.model.get("links")
            });
            this.$el.html(this.html);
            return this;
        }
    }),
    Main: Backbone.View.extend({
        events: {
        },
        initialize: function(options){
            this.options = options || {};

            _.bindAll(this,"render","resize","setContent");
            this.model.on("change:width",this.resize);
            this.model.on("change:content",this.setContent);

            this.template = this.$el.html();
            this.setupDOMPointers();
        },
        render: function(){
            this.html = _.template(this.template);
            this.$el.html(this.html);
            this.setupDOMPointers();
            return this;
        },
        setupDOMPointers: function(){
            this.$content = this.$el.children(".un-panel-content");
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
        resize: function(){
            this.$el.css("width", this.model.get("width")+"%");
        },
        reset: function(){
            this.render();
        }
    }),
    Panel: Backbone.View.extend({
        events: {
            "click .un-close-panel": "closePanel",
            "click .un-open-panel": "openPanel"
        },
        initialize: function(options){
            this.options = options || {};
            this.template = this.options.template;
            this.$content = null;
            this.$panel = null;
            this.$panel_toggle = null;

            _.bindAll(this,"openPanel","closePanel","showPanel","hidePanel","render","convert","setContent");
            this.model.on("change:number",this.convert);
            this.model.on("open",this.openPanel);
            this.model.on("close",this.closePanel);
            this.model.on("show",this.showPanel);
            this.model.on("hide",this.hidePanel);
            this.model.on("change:content",this.setContent);

            this.setupDOMPointers();
        },
        render: function(){
            this.html = _.template(this.template,{
                num: this.model.get("number")
            });
            this.$el.html(this.html);
            this.setupDOMPointers();
            return this;
        },
        setupDOMPointers: function(){
            this.$content = $("#panel"+this.model.get('number')+"_content");
            this.$panel = $("#panel"+this.model.get('number'));
            this.$panel_toggle = $("#panel"+this.model.get('number')+"_toggle");
        },
        convert: function() {

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
        closePanel: function(event){
            this.$panel.addClass("un-panel-closed").removeClass("un-panel-shadow");
            this.$content.addClass("hidden");
            this.$panel_toggle.removeClass("un-panel-close").addClass("un-open-panel un-panel-toggle-shadow").html(this.togglePanelIcon("open"));

            this.model.set({
                open: false
            });
        },
        openPanel: function(event){
            if (this.model.get("hidden")==false) {
                this.model.set({
                    open: true
                });
                this.$panel.removeClass("un-panel-closed").addClass("un-panel-shadow");
                this.$content.removeClass("hidden");
                this.$panel_toggle.removeClass("un-open-panel un-panel-toggle-shadow").addClass("un-close-panel").html(this.togglePanelIcon("close"));
            }else{
                this.model.trigger("show");
                setTimeout(this.openPanel,200);
            }
        },
        hidePanel: function(){
            if (this.model.get("open")==false) {
                this.$panel.addClass("hidden");
                this.$content.addClass("hidden");
                this.model.set({
                    hidden: true
                }, {
                    silent: true
                });
            }else{
                this.model.trigger("close");
                setTimeout(this.hidePanel,200);
            }
        },
        showPanel: function(){
            this.$panel.removeClass("hidden");
            this.model.set({
                hidden: false
            },{
                silent: true
            });
        },
        togglePanelIcon: function(state){
            if (state=="open"){ state = "right"; }
            else if (state=="close"){ state = "left"; }
            return "<span class='glyphicon glyphicon-chevron-"+state+"'></span><span class='glyphicon glyphicon-chevron-"+state+"'></span>";
        },
        reset: function(hide){
            hide = hide||false;
            this.render();
            if (hide==true) this.hidePanel();
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
    Login: Backbone.View.extend({
        events: {
            "click #login": "login",
            "focusout input": "updateModel"
        },
        initialize: function(options) {
            this.options = options || {};
            this.layout = this.options.layout || {};
            this.panel = this.options.panel || {};

            this.model = this.model || new UNBOXAPI.Models.User;

            this.username = null;
            this.password = null;
            this.template = this.layout.templates.getTemplate("Login");
            _.bindAll(this,"login","updateModel");
        },
        render: function() {
            this.html = _.template(this.template);
            this.$el.html(this.html);
            this.setup();
            return this;
        },
        setup: function(){
            //setup Google+ button
            UNBOXAPI.Global.Login.Google.loginButton();
        },
        login: function(){
            this.model.login();
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
            this.layout = this.options.layout || {};
            this.panel = this.options.panel || {};
            this.captcha = null;
            this.template = this.layout.templates.getTemplate("Register");

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
    Main: Backbone.View.extend({

    }),
    Profile: Backbone.View.extend({

    })
}
UNBOXAPI.Views.Tester = {
    Setup: {
        Panel: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.panel = this.options.panel || {};
                this.layout = this.options.layout || {};
                this.template = this.layout.templates.getTemplate("Setup");

                //prepare dom references
                this.$application = null;
                this.$api = null;
                this.$loginMethod = null;
                this.$httpMethod = null;
                this.$entryPoint = null;
                this.$api_login = null;

                //setup models
                this.application = new UNBOXAPI.Models.Applications;
                this.api = new UNBOXAPI.Models.APIs;
                this.loginMethod = new UNBOXAPI.Models.Logins;
                this.httpMethod = new UNBOXAPI.Models.HttpMethods;
                this.entryPoint = this.options.entryPoint || new UNBOXAPI.Models.EntryPoints;
                this.token = this.options.token || new UNBOXAPI.Models.Tokens;

                //setup collections
                this.applications = new UNBOXAPI.Collections.Applications;
                this.apis = new UNBOXAPI.Collections.APIs;
                this.loginMethods = new UNBOXAPI.Collections.Logins;
                this.httpMethods = new UNBOXAPI.Collections.HttpMethods;
                this.entryPoints = new UNBOXAPI.Collections.EntryPoints;

            },
            render: function() {
                this.html = _.template(this.template);
                this.$el.html(this.html);

                this.setup();

                return this;
            },
            setup: function(){
                //setup DOM
                $(".select2").select2();
                this.$application = $("#application");
                this.$api = $("#api");
                this.$loginMethod = $("#login_method");
                this.$httpMethod = $("#httpMethod");
                this.$entryPoint = $("#entryPoint_select");
                this.$api_login = $("#api_login");
                this.$api_login_form = $("#API_Login_form");

                //setup views
                this.applicationSelect = new UNBOXAPI.Views.Tester.Setup.ApplicationSelect({
                    el: this.$application,
                    collection: this.applications,
                    model: this.application
                });
                this.apiSelect = new UNBOXAPI.Views.Tester.Setup.APISelect({
                    el: this.$api,
                    collection: this.apis,
                    model: this.api
                });
                this.loginSelect = new UNBOXAPI.Views.Tester.Setup.LoginSelect({
                    el: this.$loginMethod,
                    collection: this.loginMethods,
                    model: this.loginMethod
                });
                this.httpMethodSelect = new UNBOXAPI.Views.Tester.Setup.HttpMethodSelect({
                    el: this.$httpMethod,
                    collection: this.httpMethods,
                    model: this.httpMethod
                });
                this.entryPointSelect = new UNBOXAPI.Views.Tester.Setup.EntryPointSelect({
                    el: this.$entryPoint,
                    collection: this.entryPoints,
                    model: this.entryPoint
                });
                //Setup cascading dropdowns
                this.applicationSelect.apiSelect = this.apiSelect;
                this.apiSelect.httpMethodSelect = this.httpMethodSelect;
                this.apiSelect.loginSelect = this.loginSelect;
                this.apiSelect.entryPointSelect = this.entryPointSelect;
                this.httpMethodSelect.entryPointSelect = this.entryPointSelect;

                //Setup Login Form
                this.loginSubPanel = new UNBOXAPI.Views.Tester.Setup.LoginPanel({
                    el: this.$api_login,
                    model: this.loginMethod,
                    token: this.token,
                    layout: this.layout
                });

                //load first dropdown
                this.applications.fetch({ reset: true });
            }
        }),
        ApplicationSelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(applicationId) {
                this.apiSelect.selectedId = null;
                this.apiSelect.setApplicationId(applicationId);
                this.model = this.collection.get(applicationId);
                UNBOXAPI.tester.models.application = this.model;
            }
        }),
        APISelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(apiID) {
                this.httpMethodSelect.selectedId = null;
                this.httpMethodSelect.setApiID(apiID);
                this.loginSelect.selectedId = null;
                this.loginSelect.setApiID(apiID);
                this.entryPointSelect.selectedId = null;
                this.entryPointSelect.setApiID(apiID);
                var model = this.collection.get(apiID);
                this.model.set({
                   id: model.get('id'),
                   name: model.get('name'),
                   version: model.get('version'),
                   value: model.get('value')
                });
            },
            setApplicationId: function(applicationID) {
                this.populateFrom("applications/" + applicationID + "/apis");
            }
        }),
        HttpMethodSelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(httpMethodID) {
                this.entryPointSelect.selectedId = null;
                this.entryPointSelect.setMethodId(httpMethodID);
                //UNBOXAPI.tester.models.httpMethod = UNBOX.collections.httpMethods.findWhere({id:httpMethodID});
            },
            setApiID: function(apiID) {
                this.populateFrom("apis/" + apiID + "/methods");
            }
        }),
        EntryPointSelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(entryPointID) {
                this.model.clear({
                    silent: true
                });
                this.model.set({
                    id: entryPointID
                },{
                    silent: true
                });
                UNBOXAPI.Models.Utils.fetch({
                    model: this.model
                });
            },
            setApiID: function(apiID){
                this.chosenAPI = apiID;
            },
            setMethodId: function(httpMethodID) {
                this.populateFrom("apis/" + this.chosenAPI + "/entryPoints/"+httpMethodID);
            }
        }),
        LoginSelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(loginID) {
                var model = this.collection.get(loginID);
                this.model.set({
                    id: loginID,
                    login_entryPoint_id: model.get('login_entryPoint_id'),
                    logout_entryPoint_id: model.get('logout_entryPoint_id'),
                    value: model.get('value')
                });
                UNBOXAPI.tester.models.loginMethod = this.model;
            },
            setApiID: function(apiID) {
                this.populateFrom("apis/" + apiID + "/logins/");
            }
        }),
        LoginPanel: Backbone.View.extend({
            events: {
                "click #return_LoginForm": "showForm",
                "change #web_address": "setWebAddress",
                "click #logged_in_info": "render",
                "click .logout": "logout"
            },
            initialize: function(options) {
                this.options = options|| {};
                this.token = this.options.token || new UNBOXAPI.Models.Tokens;
                this.layout = this.options.layout || {};

                _.bindAll(this,"render","showForm","resetLogin","logout");
                this.model.bind("change",this.render);
                this.model.bind("logout",this.resetLogin);
                this.token.on("change:access_token",this.loginCheck);

                this.$web_address = $("#web_address");
                this.$login_method = $("#login_method");
                this.$api_login_form = $("#API_Login_form");
                this.loginForm = new UNBOXAPI.Views.Tester.Setup.LoginForm({
                    el: this.$api_login_form,
                    model: this.model,
                    token: this.token,
                    template: this.layout.templates.getTemplate("LoginForm")
                });

                this.$api_login_info = $("#api_login_info");


                this.render();
            },
            render: function(){
                if (this.token.get("access_token")==null||this.token.get("access_token")==""){
                    this.showForm();
                }else{
                    this.template = this.layout.templates.getTemplate("LoginInfo");
                    this.html = _.template(this.template,{
                        token: this.model
                    });
                    this.$api_login_info.html(this.html);
                    this.showInfo();
                }
                return this.$el;
            },
            loginCheck: function(){

            },
            showForm: function(){
                this.$el.removeClass("flip");
            },
            showInfo: function(){
                this.$el.addClass("flip");
            },
            setWebAddress: function(e){
                UNBOXAPI.tester.web_address = $(e.currentTarget).val();
            },
            resetLogin: function(){
                UNBOX.login_info = "";
                this.$loggedIn_btn.addClass("hidden");
                this.$api_login_form.trigger("reset");
                this.$web_address.value("");
                this.$login_method.value("");
                $(".logout").attr("disabled",true);
            },
            logout: function(){
                var data = {
                    'web_address': UNBOXAPI.tester.web_address,
                    'token': UNBOXAPI.tester.models.token.get("access_token")
                };
                $.ajax({
                    url: UNBOXAPI.Global.ajaxURL+'apis/'+UNBOX.models.api.get('id')+'/test/'+UNBOX.models.login.get("logout_entryPoint_id"),
                    type: "POST",
                    data: data,
                    context: this,
                    success: function(data){
                        this.token.clear();
                        this.token.trigger("logout");
                    },
                    dataType: 'json'
                });
            }
        }),
        LoginForm: Backbone.View.extend({
            events: {
                "click #loginBtn": "login"
            },
            initialize: function(options) {
                this.options = options || {};
                this.token = this.options.token || new UNBOXAPI.Models.Tokens;
                this.template = this.options.template || {};

                _.bindAll(this,"render","login");

                this.$normal_div = $("#login_normal");
                this.$advanced_div = $("#login_advanced");
                this.$advanced_btn = $("#login_advanced_btn");
                this.$loggedIn_btn = $("#logged_in_info");

                this.$logoutBtn = $("#logoutBtn");
                this.$loginBtn = $("#loginBtn");

                this.collection = new UNBOXAPI.Collections.Parameters({
                    loginMethod: this.model
                });
                this.collection.on("sync",this.render);

            },
            render: function(){
                var normal_params = this.collection.where({ login_pane: "normal" });
                var type = "";
                var field = "";
                for (var x=0;x<normal_params.length;x++) {
                    type = "";
                    field = "";
                    type = (!(normal_params[x].get("api_type_name")==null||normal_params[x].get("api_type_name")=="")?normal_params[x].get("api_type"):normal_params[x].get("data_type"));
                    field = {
                        name: normal_params[x].get("name"),
                        required: normal_params[x].get("required"),
                        value: ""
                    };
                    normal_params[x].set({
                        html: _.template(type.template,{
                            field: field
                        }),
                        type: type.name
                    });
                }
                this.html = _.template(this.template,{
                    parameters: normal_params
                });
                this.$normal_div.html(this.html);
                var advanced_params = this.collection.where({ login_pane: "advanced" });
                for (var x=0;x<advanced_params.length;x++) {
                    type = "";
                    field = "";
                    type = (!(advanced_params[x].get("api_type_name")==null||advanced_params[x].get("api_type_name")=="")?advanced_params[x].get("api_type"):advanced_params[x].get("data_type"));
                    if (type.template==null) type = advanced_params[x].get("data_type");
                    field = {
                        name: advanced_params[x].get("name"),
                        required: advanced_params[x].get("required"),
                        value: ""
                    };
                    advanced_params[x].set({
                        html: _.template(type.template,{
                            field: field
                        }),
                        type: type.name
                    });
                }
                this.html = "";
                this.html = _.template(this.template,{
                    parameters: advanced_params
                });
                this.$advanced_div.removeClass('in');
                this.$advanced_div.html(this.html);
                this.$el.removeClass('hidden');
                $(".select2",this.$el).select2();
                return this;
            },
            login: function(){
                var loginForm = this.$el.serializeArray();
                loginForm.push({
                    name: 'web_address',
                    value: UNBOXAPI.tester.web_address
                });
                $.ajax({
                    url: UNBOXAPI.Global.ajaxURL+'apis/'+UNBOXAPI.tester.models.api.get("id")+'/login/'+this.model.get("login_entryPoint_id"),
                    type: "POST",
                    data: loginForm,
                    context: this,
                    success: function(data){
                        this.$loggedIn_btn.removeClass("hidden");
                        UNBOXAPI.tester.login_info = jQuery.parseJSON(data['response']);
                        if (this.token!==null) this.token.clear({silent: true});
                        this.token.set(UNBOXAPI.tester.login_info);
                        this.$logoutBtn.removeAttr("disabled");
                    },
                    dataType: 'json'
                });
            }
        })
    },
    EntryPointDetail: {
        Panel: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.panel = this.options.panel || {};
                this.token = this.options.token || {};
                this.layout = this.options.layout || {};

                this.template = this.layout.templates.getTemplate("EntryPointOverview");

                this.examples = new UNBOXAPI.Collections.Examples({
                    entryPoint: this.model
                });
                this.exceptions = new UNBOXAPI.Collections.Exceptions({
                    entryPoint: this.model
                });

                this.$ep_main = null;
                this.$ep_action1 = null;
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
                this.$ep_action = $("#ep_action1");
                this.$ep_parameters = $("#ep_parameters");
                this.$ep_examples = $("#ep_examples");
                this.$ep_exceptions = ("#ep_exceptions");

                this.entryPointDetail = new UNBOXAPI.Views.Tester.EntryPointDetail.MainDetail({
                    el: this.$ep_main,
                    model: this.model,
                    template: this.layout.templates.getTemplate("EntryPointMain")
                });
                this.actionButtons= new UNBOXAPI.Views.Tester.ActionButtons({
                    el: this.$ep_action,
                    model: this.token,
                    collection: this.collection,
                    panelNumber: this.panel.get("number"),
                    template: this.layout.templates.getTemplate("EntryPointActions")
                });
                this.parameterPanel = new UNBOXAPI.Views.Tester.EntryPointDetail.Parameters({
                    el: this.$ep_parameters,
                    collection: this.collection,
                    template: this.layout.templates.getTemplate("EntryPointParameters")
                });
                this.examplePanel = new UNBOXAPI.Views.Tester.EntryPointDetail.Examples({
                    el: this.$ep_examples,
                    collection: this.examples,
                    template: this.layout.templates.getTemplate("EntryPointExamples")
                });
                this.exceptionPanel = new UNBOXAPI.Views.Tester.EntryPointDetail.Exceptions({
                    el: this.$ep_exceptions,
                    collection: this.exceptions,
                    template: this.layout.templates.getTemplate("EntryPointExceptions")
                });
            },
            panelState: function(){
                if (this.model.get('name')!==null&&this.model.get('name')!==""){
                    this.panel.trigger("open");
                }else{
                    this.panel.trigger("close");
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
                    entryPoint: this.model
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
                    parameters: this.collection.where({url_param: "0"})
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
    Parameters: {
        Panel: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.panel = this.options.panel || {};
                this.token = this.options.token || {};
                this.layout = this.options.layout || {};

                this.template = this.layout.templates.getTemplate("ParameterForm");

                this.$url_parameters = null;
                this.$request_parameters = null;
                this.$actions = null;

                _.bindAll(this,"render","panelState");
                this.collection.on("testerSetup",this.panelState);
            },
            render: function(){
                this.html = _.template(this.template);
                this.$el.html(this.html);

                this.setup();

                return this;
            },
            setup: function(){
                //setup dom references
                this.$url_parameters = $("#ep_url_params");
                this.$request_parameters = $("#ep_request_params");
                this.$actions = $("#ep_action2");

                this.urlParams = new UNBOXAPI.Views.Tester.Parameters.UrlParams({
                    el: this.$url_parameters,
                    collection: this.collection,
                    template: this.layout.templates.getTemplate("Parameters")
                });
                this.requestParams = new UNBOXAPI.Views.Tester.Parameters.RequestParams({
                    el: this.$request_parameters,
                    collection: this.collection,
                    template: this.layout.templates.getTemplate("Parameters")
                });
                this.actionButtons = new UNBOXAPI.Views.Tester.ActionButtons({
                    el: this.$actions,
                    model: this.token,
                    collection: this.collection,
                    panelNumber: this.panel.get("number"),
                    template: this.layout.templates.getTemplate("EntryPointActions")
                });
            },
            panelState: function(){
                if (this.collection.length>0){
                    this.panel.trigger("open");
                }else{
                    this.panel.trigger("hide");
                }
            }
        }),
        UrlParams: Backbone.View.extend({
            initialize: function(options) {
                this.options = options || {};
                this.template = this.options.template || {};
                _.bindAll(this,"render");
                this.collection.on("testerSetup",this.render);
            },
            render: function(){
                var url_params = this.collection.where({ url_param: "1"});
                var html = "";
                for (var x=0;x<url_params.length;x++) {
                    var type = (!(url_params[x].get("api_type_name")==null||url_params[x].get("api_type_name")=="")?url_params[x].get("api_type"):url_params[x].get("data_type"));
                    var field = {
                        name: url_params[x].get("name"),
                        required: url_params[x].get("required"),
                        value: ""
                    };
                    url_params[x].set({
                        html: _.template(type.template,{
                            field: field
                        }),
                        type: type.name
                    });
                }
                this.html = _.template(this.template,{
                    parameters: url_params
                });
                this.$el.html(this.html);
                return this;
            }
        }),
        RequestParams: Backbone.View.extend({
            initialize: function(options) {
                this.options = options || {};
                this.template = this.options.template || {};
                _.bindAll(this,"render");
                this.collection.on("testerSetup",this.render);
            },
            render: function(){
                var url_params = this.collection.where({ url_param: "0"});
                var html = "";
                for (var x=0;x<url_params.length;x++) {
                    var type = (!(url_params[x].get("api_type_name")==null||url_params[x].get("api_type_name")=="")?url_params[x].get("api_type"):url_params[x].get("data_type"));
                    var field = {
                        name: url_params[x].get("name"),
                        required: url_params[x].get("required"),
                        value: ""
                    };
                    url_params[x].set({
                        html: _.template(type.template,{
                            field: field
                        }),
                        type: type.name
                    });
                }
                this.html = _.template(this.template,{
                    parameters: url_params
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
                this.layout = this.options.layout || {};
                this.template = this.layout.templates.getTemplate("Output");

                var Style = Backbone.Model.extend({
                    defaults: {
                        name: "pretty"
                    }
                });

                _.bindAll(this, "render");
                this.model.bind("change", this.render);

                this.style = new Style();
                this.style.bind("change", this.render);

                this.$request = $("#request");
                this.$response = $("#response");
            },
            render: function () {
                this.html = _.template(this.template,{
                    request: this.model,
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
    },
    ActionButtons: Backbone.View.extend({
        events: {
            "click #sendAPI": "testEP",
            "click #generateScript": "generateScript",
            "click #setupParams": "setupParams"
        },
        initialize: function(options){
            this.options = options || {};
            this.panelNumber = this.options.panelNumber || null;
            this.template = this.options.template || {};

            _.bindAll(this,"render","testButtonState","testEP","generateScript","setupParams");
            this.collection.bind("sync",this.render);
            this.model.bind("change:access_token",this.testButtonState);
        },
        render: function(){
            var hasParams = false;
            if (this.collection.length>0){
                hasParams = true;
            }
            this.html = _.template(this.template,{
                hasParams: hasParams,
                panelNumber: this.panelNumber
            });
            this.$el.html(this.html);
            this.testButtonState();
            return this;
        },
        setupParams: function(){
            this.collection.trigger("testerSetup");
        },
        testButtonState: function() {
            if (!(this.model.get("access_token")==null||typeof this.model.get("access_token")=='undefined')) {
                $("#sendAPI").removeAttr("disabled");
            }else{
                $("#sendAPI").attr("disabled",true);
            }
        },
        testEP: function(e){
            var paramForm = $("#ParameterForm").serializeArray();
            paramForm.push(
                {
                    name: 'token',
                    value: UNBOXAPI.tester.models.token.get('access_token')
                },
                {
                    name: 'web_address',
                    value: UNBOXAPI.tester.web_address
                });
            $.ajax({
                url: UNBOXAPI.Global.ajaxURL+'apis/'+UNBOXAPI.tester.models.api.get('id')+'/test/'+UNBOXAPI.tester.models.entryPoint.get("id"),
                type: "POST",
                data: paramForm,
                success: function(data){
                    UNBOXAPI.tester.models.request.clear();
                    UNBOXAPI.tester.models.request.set(data);
                },
                dataType: 'json'
            });
        },
        generateScript: function(e){

        }
    })
}
UNBOXAPI.Views.Manager = {
    Actions: Backbone.View.extend({
        events: {
            "click .btn": "action"
        },
        initialize: function(options){
            this.options = options || {};
            this.layout = this.options.layout || {};
            this.module = this.options.module || false;

            this.template = this.layout.templates.getTemplate("Actions");
        },
        render: function(){
            this.html = _.template(this.template,{
                modules: this.collection.models,
                default_module: this.module
            });
            this.$el.html(this.html);
        },
        action: function(e){
            var module = $(e.currentTarget).data('module');
            var action = $(e.currentTarget).data('action');
        }
    }),
    ListView: {
        Panel: Backbone.View.extend({
            el: $('#output_panel'),
            events: {
                "change #name": "setNameFilter",
                "click #prevBtn": "previous",
                "click #nextBtn": "next"
            },
            initialize: function (options) {
                this.options = options || {};
                this.layout = this.options.layout || {};

                this.template = this.layout.templates.getTemplate("Output");

                _.bindAll(this,"setNameFilter","previous","next");
                this.$output = $("#output");
                this.$nameFilter = $("#name");

                this.render();
            },
            render: function () {
                this.html = _.template(this.template,{
                    request: this.model
                });
                this.$output.html(this.html);
                return this;
            },
            setNameFilter: function(e) {
                this.list.setName($(e.currentTarget).val());
            },
            previous: function(e){
                this.list.previous();
            },
            next: function(e){
                this.list.next();

            }
        }),
        ApplicationSelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(applicationId) {
                this.apiSelect.setApplicationID(applicationId);
                this.entryPointList.setApplicationID(applicationId);
            },
            setDisabled: function(){

            }
        }),
        APISelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(apiID) {
                this.httpMethodSelect.setApiID(apiID);
                this.entryPointList.setApiID(apiID);
            },
            setApplicationID: function(applicationID) {
                this.populateFrom("applications/" + applicationID + "/apis");
            },
            setDisabled: function(){

            }
        }),
        HttpMethodSelect: UNBOXAPI.Views.DynamicSelect.extend({
            setSelectedId: function(httpMethodID) {
                this.entryPointList.setMethodID(httpMethodID);
            },
            setApiID: function(apiID) {
                this.populateFrom("apis/" + apiID + "/methods");
            },
            setDisabled: function(){

            }
        }),
        List: Backbone.View.extend({
            events: {

            },
            initialize: function(options){
                this.options = options || {};
                this.template = this.options.template || {};

                _.bindAll(this,"render","next","previous","setOffset");
                this.collection.on("reset",this.render);
                this.filter = {};

                this.$offset = $("#offset");
                this.$next_offset = $("#next_offset");
                this.$prevBtn = $("#prevBtn");
                this.$nextBtn = $("#nextBtn");

                this.limit = 20;
                this.offset = 0;

                this.populateFrom();
            },
            render: function(){
                this.html = _.template(this.template,{
                    entryPoints: this.collection.models
                });
                this.$el.html(this.html);
                this.configureBtns();
            },
            setApplicationID: function(appID) {
                this.applicationID = appID;
                this.filter['application'] = appID;
                this.setOffset(0);
                this.populateFrom();
            },
            setApiID: function(apiID) {
                this.apiID = apiID;
                this.filter['api'] = apiID;
                this.setOffset(0);
                this.populateFrom();
            },
            setMethodID: function(methodID) {
                this.methodID = methodID;
                this.filter['method'] = methodID;
                this.setOffset(0);
                this.populateFrom();
            },
            setName: function(name) {
                this.nameFilter = name;
                this.filter['name'] = name;
                this.setOffset(0);
                this.populateFrom();
            },
            populateFrom: function() {
                var old_filter = this.filter;
                var filter = jQuery.isEmptyObject(this.filter) == false ? $.param(this.filter) : "";
                if (filter !="") {
                    if (old_filter!==filter){
                        this.offset=0;
                    }
                    this.collection.url = UNBOXAPI.Global.ajaxURL + "entryPoints/filter?" + filter + "&limit=" + this.limit + "&offset=" + this.offset;
                }else{
                    this.collection.url = UNBOXAPI.Global.ajaxURL + "entryPoints?limit=" + this.limit + "&offset=" + this.offset;
                }
                UNBOXAPI.Collections.Utils.fetch({
                    collection: this.collection,
                    options: {
                        reset: true
                    }
                });
            },
            configureBtns: function(){
                var offset = this.offset;
                var next_offset;
                if (this.collection.length<20){
                    next_offset = offset + this.collection.length;
                    this.$nextBtn.attr("disabled","disabled");
                    if (this.collection.length==0){
                        this.$offset.html(0);
                    }
                }else{
                    next_offset = (offset + 20);
                    this.$nextBtn.removeAttr("disabled");
                }
                if (this.offset==0){
                    this.$prevBtn.attr("disabled","disabled");
                }else{
                    this.$prevBtn.removeAttr("disabled");
                }
                this.$next_offset.html(next_offset);
            },
            previous: function(){
                this.setOffset(this.offset - this.limit);
                this.populateFrom();
            },
            next: function(){
                this.setOffset(this.offset + this.limit);
                this.populateFrom();
            },
            setOffset: function(new_offset){
                new_offset = new_offset < 0 ? 0 : new_offset;
                this.offset = new_offset;
                this.$offset.html(this.offset+1);
            }
        })
    },
    RelateRecord: Backbone.View.extend({
        events: {
            "click #save": "save",
            "click #clear": "clear",
            "focusout input": "updateModel",
            "focusout textarea": "updateModel",
            "change select": "updateModel"
        },
        initialize: function(options){
            this.options = options || {};
            this.module = this.options.module || null;
            this.layout = this.options.layout || {};

            this.template = this.layout.templates.getTemplate("");

            _.bindAll(this,"render","getFields","panelState","record","clear","save","updateModel");
            this.model.on("sync",this.render,this);

            this.$step2 = $("#step2");
            this.$panelToggle = $("#panel_toggle2");
            this.quickRecord = $("#quickRecord");

            if (!(typeof this.model.get("id") =='undefined' || this.model.get("id")=="" || this.model.get("id")==null)){
                this.model.fetch();
            }else{
                this.render();
            }
        },
        render: function(){
            if (this.panelState()) {
                this.$step2.html(this.template);
            }
        }
    }),
    QuickRecord: Backbone.View.extend({
        events: {
            "click #save": "save",
            "click #clear": "clear",
            "focusout input": "updateModel",
            "focusout textarea": "updateModel",
            "change select": "updateModel"
        },
        initialize: function(options){
            this.options = options || {};
            this.module = this.options.module || null;
            this.layout = this.options.layout || {};

            this.template = this.layout.templates.getTemplate("Record");

            _.bindAll(this,"render","getFields","clear","save","updateModel");
            this.model.on("sync",this.render);

            this.quickRecord = $("#quickRecord");

            if (!(typeof this.model.get("id") =='undefined' || this.model.get("id")=="" || this.model.get("id")==null)){
                UNBOXAPI.Models.Utils.fetch({
                    model: this.model
                });
            }
        },
        render: function(){
            var fields = this.getFields();
            var actionMenu = this.getActions();
            this.html = _.template(this.template, {
                currentModule: this.module,
                actions: actionMenu,
                fields: fields
            });
            this.$el.html(this.html);
        },
        getActions: function(){
            var template = UNBOXAPI.Global.Utils.getTemplate("RecordActions");
            this.html = _.template(this.template, {
                currentModule: this.module
            });
            return this.html;
        },
        getFields: function(){
            var fields = [];
            var moduleFields = this.module.get("fields");
            var labelTemplate = UNBOXAPI.Global.Utils.getTemplate("label",true);
            var helpTemplate = UNBOXAPI.Global.Utils.getTemplate("help",true);
            for (var fieldName in moduleFields){
                if (moduleFields.hasOwnProperty(fieldName)) {
                    html = "";
                    var field = moduleFields[fieldName];
                    if (field.hasOwnProperty('form')) {
                        var attributes = field['form'];
                        if (attributes.hasOwnProperty("type")) {
                            var fieldProperties = {
                                type: attributes['type'],
                                name: fieldName,
                                label: field['label'],
                                id: attributes['id'],
                                class: attributes['class'],
                                placeholder: attributes['placeholder'],
                                options: new UNBOXAPI.Collections.Data,
                                help: field['help'],
                                disabled: attributes['disabled'],
                                value: this.model.get(fieldName)
                            };
                            var fieldTemplate = UNBOXAPI.Global.Utils.getTemplate(attributes['type'], true);
                            if (attributes['type']=='select'){
                                if (!(typeof attributes['collection']=='undefined' || attributes['collection']==null || attributes['collection']=="")){
                                    for (var x=0; x<UNBOX.collections[attributes['collection']].length;x++){
                                        var model = UNBOX.collections[attributes['collection']].get(x);
                                        if (typeof model!=='undefined') {
                                            fieldProperties.options.add({
                                                key: model.get('id'),
                                                value: model.getValue()
                                            });
                                        }
                                    }
                                }else{
                                    fieldProperties.options.reset(attributes['options']);
                                }
                            }
                            html += _.template(labelTemplate, {
                                field: fieldProperties
                            });
                            html += _.template(fieldTemplate, {
                                field: fieldProperties,
                                options: fieldProperties.options.models
                            });
                            if (field.hasOwnProperty('help')) {
                                html += _.template(helpTemplate, {
                                    field: fieldProperties
                                });
                            }
                            fields.push(html);
                        }
                    }
                }
            }
            return fields;
        },
        updateModel: function(e) {
            var changed = e.currentTarget;
            var value = $(e.currentTarget).val();
            var obj = {};
            obj[changed.name] = value;
            this.model.set(obj);
        },
        save: function(e){
            if (typeof this.model.get("id")=='undefined'||this.model.get('id')==""||this.model.get("id")==null){
                UNBOXAPI.Models.Utils.save({
                    model: this.model,
                    options: {
                        module: this.module
                    },
                    success: function(model,response,options){
                        UNBOX.router.navigate("#manage/"+options.module.get('name')+"/view/"+model.get('id'));
                    }
                });
                /*
                this.model.save(null,{
                    module: this.module,
                    success: function(model,response,options){
                        UNBOX.router.navigate("#manage/"+options.module.get('name')+"/view/"+model.get('id'));
                    }
                });*/
            }else{
                UNBOXAPI.Models.Utils.save({
                    model: this.model
                });
            }
        },
        clear: function(e){
            this.quickRecord.clear();
        }
    }),
    RelatedRecord: {

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
    Module: Backbone.Model.extend({

    }),
    Applications: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "Application",
        urlRoot: UNBOXAPI.Global.ajaxURL+'applications',
        defaults: {
            name: null,
            description: "",
            version: null
        },
        getValue: function(){
            return this.get('name')+" - ("+this.get('version')+")";
        }
    }),
    APIs: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "API",
        urlRoot: UNBOXAPI.Global.ajaxURL+'apis',
        defaults: {
            name: null,
            version: null,
            login_required: false
        },
        getValue: function(){
            return this.get('name')+" - ("+this.get('version')+")";
        }
    }),
    HttpMethods: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "HttpMethod",
        urlRoot: UNBOXAPI.Global.ajaxURL+'httpMethods',
        defaults: {
            method: null
        },
        getValue: function(){
            return this.get('method');
        }
    }),
    EntryPoints: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue','getHttpMethod');
        },
        name: "Entry Point",
        urlRoot: UNBOXAPI.Global.ajaxURL+'entryPoints',
        defaults: {
            name: null,
            method: 1,
            url: null,
            description: null,
            deleted: 0,
            deprecated: 0,
            httpMethod: {}
        },
        getValue: function(){
            return this.get('name');
        },
        getHttpMethod: function(){
            var httpMethod = this.get("httpMethod");
            return httpMethod.method || null;
        }
    }),
    Parameters: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "Parameter",
        urlRoot: UNBOXAPI.Global.ajaxURL+'parameters',
        defaults: {
            data_type: 1,
            api_type: null,
            name: null,
            description: null,
            deprecated: 0,
            deleted: 0
        },
        getValue: function(){
            return this.get('name');
        }
    }),
    ParameterTypes: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "Parameter Type",
        urlRoot: UNBOXAPI.Global.ajaxURL+'parameterTypes',
        defaults: {
            name: null,
            type: 1,
            template: null
        },
        getValue: function(){
            return this.get('name');
        }
    }),
    Examples: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "Example",
        urlRoot: UNBOXAPI.Global.ajaxURL+'examples',
        defaults: {
            type: null,
            name: null,
            example: null
        },
        getValue: function(){
            return this.get('name');
        }
    }),
    Exceptions: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "Exception",
        urlRoot: UNBOXAPI.Global.ajaxURL+'exceptions',
        defaults: {
            type: null,
            name: null,
            code: null,
            description: null
        },
        getValue: function(){
            return this.get('name');
        }
    }),
    Logins: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "Login",
        urlRoot: UNBOXAPI.Global.ajaxURL+'logins',
        defaults: {
            name: null,
            login_entryPoint_id: null,
            logout_entryPoint_id: null
        },
        getValue: function(){
            return this.get('name');
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
            this.panels = this.options.panels || new UNBOXAPI.Collections.Panels;

            _.bindAll(this,"resize");
            this.panels.on("change",this.resize);
        },
        defaults: {
            width: 100,
            content: null
        },
        resize: function(){
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
    Modules: Backbone.Model.extend({
        initialize: function(){
            this.config = new UNBOXAPI.Collections.Config;
            //TODO: Create field and relationship collections for easy access to Module fields and relationships
            //this.fields = new UNBOXAPI.Collections.Fields;
            //this.relationships = new UNBOXAPI.Collections.Relationships;

            _.bindAll(this,"setup","setupConfig");

            this.on("reset",this.setup);
            //this.on("change:fields",this.setupFields);
            //this.on("change:relationships",this.setupRelationships);

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
            //this.setupFields();
            //this.setupRelationships();
        },
        /*setupFields: function(){
            this.templates.refresh(this.get('templates'));
        },*/
        setupConfig: function(){
            this.config.refresh(this.get('config'));
        }
        /*setupRelationships: function(){
            this.relationships.refresh(this.get('relationships'));
        }*/
    }),
    Layouts: Backbone.Model.extend({
        initialize: function(){
            this.templates = new UNBOXAPI.Collections.Templates;
            this.config = new UNBOXAPI.Collections.Config;

            _.bindAll(this,"setup","setupTemplates","setupConfig");

            this.on("reset",this.setup);
            this.on("change:config",this.setupConfig);
            this.on("change:templates",this.setupTemplates);
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
        },
        setupTemplates: function(){
            this.templates.refresh(this.get('templates'));
        },
        setupConfig: function(){
            this.config.refresh(this.get('config'));
        }
    }),
    //User Model
    User: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue','getToken');
        },
        urlRoot: "user/me",
        default: {
            token: [],
            name: "",
            username: "",
            first_name: "",
            last_name: "",
            email: "",
            password: null
        },
        getValue: function(){
            return this.get('first_name')+" "+this.get('last_name');
        },
        login: function(){
            //var loading = UNBOXAPI.Global.Utils.Loading.start("Logging In");
            $.ajax({
                url: 'user/login',
                type: "POST",
                context: this,
                data: {
                    username: this.get('username'),
                    password: this.get('password')
                },
                success: function(data){
                    if (data['err']!==true) {
                        this.fetch({reset: true});
                    }
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
            var access_token = this.getToken();
            if (!(access_token == null || typeof access_token == 'undefined' || access_token == false)) {
                return true;
            }
            return false;
        },
        logout: function() {
            this.reset();
            $.ajaxSetup({
                headers: {}
            });
        },
        getToken: function(){
            var token = this.get('token');
            if (typeof token !== 'undefined') {
                if ("access_token" in token) {
                    return token['access_token'];
                }
            }
            return false
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
    Applications: Backbone.Collection.extend({
        model: UNBOXAPI.Models.Applications,
        url: UNBOXAPI.Global.ajaxURL+"applications/"
    }),
    APIs: Backbone.Collection.extend({
        model: UNBOXAPI.Models.APIs,
        url: UNBOXAPI.Global.ajaxURL+"apis/"
    }),
    Logins: Backbone.Collection.extend({
        model: UNBOXAPI.Models.Logins,
        url: UNBOXAPI.Global.ajaxURL+"logins/"
    }),
    HttpMethods: Backbone.Collection.extend({
        model: UNBOXAPI.Models.HttpMethods,
        url: UNBOXAPI.Global.ajaxURL+"httpMethods/"
    }),
    EntryPoints: Backbone.Collection.extend({
        model: UNBOXAPI.Models.EntryPoints,
        url: UNBOXAPI.Global.ajaxURL+"entryPoints/"
    }),
    Parameters: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.entryPoint = this.options.entryPoint || new UNBOXAPI.Models.EntryPoints;
            this.loginMethod = this.options.loginMethod || new UNBOXAPI.Models.Logins;

            _.bindAll(this,"getEntryPointParams","getLoginParams");
            this.entryPoint.on("change:id",this.getEntryPointParams);
            this.loginMethod.on("change:id",this.getLoginParams);
        },
        model: UNBOXAPI.Models.Parameters,
        url: UNBOXAPI.Global.ajaxURL+"parameters/",
        getEntryPointParams: function(){
            this.url = UNBOXAPI.Global.ajaxURL+"entryPoints/"+this.entryPoint.get("id")+"/link/parameters";
            this.fetch();
        },
        getLoginParams: function(){
            console.log(this.loginMethod);
            this.url = UNBOXAPI.Global.ajaxURL+"entryPoints/"+this.loginMethod.get("login_entryPoint_id")+"/link/parameters";
            this.fetch();
        }
    }),
    DataTypes: Backbone.Collection.extend({
        model: UNBOXAPI.Models.Parameters,
        url: UNBOXAPI.Global.ajaxURL+"parameterTypes/1"
    }),
    ApiTypes: Backbone.Collection.extend({
        model: UNBOXAPI.Models.Parameters,
        url: UNBOXAPI.Global.ajaxURL+"parameterTypes/2"
    }),
    Examples: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.entryPoint = this.options.entryPoint || new UNBOXAPI.Models.EntryPoints;

            _.bindAll(this,"getExamples");
            this.entryPoint.on("change",this.getExamples);
        },
        model: UNBOXAPI.Models.Examples,
        url: UNBOXAPI.Global.ajaxURL+"examples/",
        getExamples: function(){
            this.url = UNBOXAPI.Global.ajaxURL+"entryPoints/"+this.entryPoint.get("id")+"/link/examples";
            /*this.fetch();*/
        }
    }),
    Exceptions: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.entryPoint = this.options.entryPoint || new UNBOXAPI.Models.EntryPoints;

            _.bindAll(this,"getExceptions");
            this.entryPoint.on("change",this.getExceptions);
        },
        model: UNBOXAPI.Models.Exceptions,
        url: UNBOXAPI.Global.ajaxURL+"exceptions/",
        getExceptions: function(){
            this.url = UNBOXAPI.Global.ajaxURL+"entryPoints/"+this.entryPoint.get("id")+"/link/parameters";
            /*this.fetch();*/
        }
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
        initialize: function(){
            _.bindAll(this,"fetchAll","setup","setupConfig","setupModules","setupLayouts","setupTemplates");
            this.config = new UNBOXAPI.Collections.Config;
            this.modules = new UNBOXAPI.Collections.Modules;
            this.layouts = new UNBOXAPI.Collections.Layouts;
            this.templates = new UNBOXAPI.Collections.Templates;
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
                    collection.trigger("fetched");
                },
                fail: function(collection,response,options){
                    //TODO: Failure handling on Metadata fetch
                    console.log('Broken.')
                }
            });
        },
        setup: function(){
            this.setupConfig();
            this.setupModules();
            this.setupLayouts();
            this.setupTemplates();
        },
        setupConfig: function(){
            var config = this.findWhere({
                key: "config"
            });
            this.config.refresh(config.get('value'));
        },
        setupModules: function(){
            var modules = this.findWhere({
                key: "modules"
            });
            this.modules.refresh(modules.get('value'));
        },
        setupLayouts: function(){
            var layouts = this.findWhere({
                key: "layouts"
            });
            this.layouts.refresh(layouts.get('value'));
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
            this.current = typeof this.options.current !== 'undefined' ? this.options.current : new UNBOXAPI.Models.Layouts;

            _.bindAll(this,"refresh","setCurrent");
        },
        model: UNBOXAPI.Models.Layouts,
        refresh: function(layouts){
            layouts = typeof layouts !== "undefined" ? layouts : false;
            if (layouts!==false){
                this.reset(layouts);
                if (typeof this.current !== 'undefined' || this.current == null) {
                    this.setCurrent();
                }
            }
        },
        setCurrent: function(layout) {
            if (typeof layout == 'undefined'){
                var model = this.findWhere({name: this.current.get('name')});
                if (!(typeof model == 'undefined' || model== null)) {
                    model.set({
                        current: true
                    });
                }
            }else {
                this.current.set({
                        current: false
                    },
                    {
                        silent: true
                    }
                );
                var model = this.findWhere({name: layout});
                model.set({
                    current: true
                });
                this.current.set(
                    model.toJSON()
                );
            }
        }
    }),
    Modules: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.current = typeof this.options.current !== 'undefined' ? this.options.current : new UNBOXAPI.Models.Modules;
            _.bindAll(this,"refresh","setCurrent");
        },
        model: UNBOXAPI.Models.Modules,
        refresh: function(modules){
            modules = typeof modules !== "undefined" ? modules : false;
            if (modules!==false){
                this.reset(modules);
                if (typeof this.current !== 'undefined' || this.current == null) {
                    this.setCurrent();
                }
            }
        },
        setCurrent: function(module) {
            if (typeof module == 'undefined'){
                var model = this.findWhere({name: this.current.get('name')});
                if (!(typeof model == 'undefined' || model== null)) {
                    model.set({
                        current: true
                    });
                }
            }else {
                this.current.set({
                        current: false
                    },
                    {
                        silent: true
                    }
                );
                var model = this.findWhere({name: module});
                model.set({
                    current: true
                });
                this.current.set(
                    model.toJSON()
                );
            }
        }
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
UNBOXAPI.Queue = function(functions,callback){
    var running = false;
    var queue = functions;
    var callback = callback;
    var queued = queue.length;
    this.process = function(){
        running = true;
        for(x=0;x<queued;x++){
            queue[x].f(queue[x].p).always(this.down);
        }
    }
    this.down = function(){
        queued--;
        if (queued==0){
            running = false;
            callback();
        }
    }
}

UNBOX = new UNBOXAPI.App;
UNBOX.start();

/*
UNBOX = {
    metadata: new UNBOXAPI.Collections.MetaData,
    config: null,
    modules: null,
    layouts: null,
    user: new UNBOXAPI.Models.User,
    models: {
        mainPanel: null
    },
    collections: {
        httpMethods: new UNBOXAPI.Collections.HttpMethods,
        dataTypes: new UNBOXAPI.Collections.DataTypes,
        apiTypes: new UNBOXAPI.Collections.ApiTypes,
        panels: new UNBOXAPI.Collections.Panels,
        notices: new UNBOXAPI.Collections.Notices
    },
    templates: {},
    view: null,
    nav: null,
    router: new UNBOXAPI.Router
};
//Create objects for global storing of module specific data
UNBOXAPI.tester = {
    test: null,
    web_address: null,
    login_info: null,
    models: {},
    collections: {}
};
UNBOXAPI.manager = {
    models: {},
    collections: {}
};
UNBOXAPI.automater = {};
UNBOXAPI.documenter = {};
UNBOX.collections.panels.add([
    new UNBOXAPI.Models.Panels({
        number: 1
    }),
    new UNBOXAPI.Models.Panels({
        number: 2
    }),
    new UNBOXAPI.Models.Panels({
        number: 3
    })
]);
UNBOX.models.mainPanel = new UNBOXAPI.Models.MainPanel({
    panels: UNBOX.collections.panels
});
UNBOX.config = new UNBOXAPI.Collections.Config({
    metadata: UNBOX.metadata
});
UNBOX.modules = new UNBOXAPI.Collections.Modules({
    metadata: UNBOX.metadata
});
UNBOX.layouts = new UNBOXAPI.Collections.Layouts({
    metadata: UNBOX.metadata
});
UNBOX.view = new UNBOXAPI.Views.AppView({
    el: $("body"),
    model: UNBOX.models.mainPanel,
    collection: UNBOX.collections.panels,
    config: UNBOX.config,
    modules: UNBOX.modules,
    layouts: UNBOX.layouts,
    module: UNBOX.modules.current,
    layout: UNBOX.layouts.current,
    notices: UNBOX.collections.notices
});
UNBOXAPI.Collections.Utils.fetch({
    collection: UNBOX.metadata,
    options: {
        reset: true
    },
    success: function() {
        UNBOX.metadata.trigger("fetched");
        UNBOX.user.fetch();
    }
});*/








