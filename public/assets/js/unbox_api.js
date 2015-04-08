Backbone.View.prototype.close = function(){
    this.$el.html("");
    this.unbind();
}

var UNBOX = UNBOX || {};
UNBOX.Global = {
    ajaxURL: "api/",
    Bootstrap: {
        total: 0,
        count: 0,
        notice: null,
        start: function () {
            if (UNBOX.Global.Utils.loggedIn()) {
                var models = UNBOX.app.config.findWhere({
                    key: "bootstrapped_models"
                });
                models = models.get('value');
                this.total = models.length;
                for (var x = 0; x < models.length; x++) {
                    UNBOX.Collections.Utils.fetch({
                        collection: UNBOX.app.collections[models[x]],
                        success: function () {
                            UNBOX.Global.Bootstrap.count++;
                            if (UNBOX.Global.Bootstrap.count == UNBOX.Global.Bootstrap.total) {
                                UNBOX.Global.Bootstrap.done();
                            }
                        }
                    })
                }
            }else{
                UNBOX.Global.Bootstrap.done();
            }
        },
        done: function(){
            UNBOX.app.view.start();
        }
    },
    Utils: {
        getTemplate: function(template,global){
            global = typeof global !== 'undefined' ? global : false;
            if (global==false) {
                var templates = UNBOX.app.models.currentLayout.get("templates");
                return templates[template];
            }else{
                var t = document.getElementById(template);
                if (t == null || typeof t == 'undefined') {
                    var templates = UNBOX.app.metadata.findWhere({key: "templates"});
                    var templateArray = templates.get("value");
                    return templateArray[template];
                }
                return t.innerHTML;
            }
        },
        Loading: {
            start: function (data) {
                var notice = new UNBOX.Models.Notices;
                notice.set({
                    type: "loading",
                    level: "info",
                    show: true,
                    message: data
                });
                UNBOX.app.collections.notices.log(notice);
                return notice;
            },
            done: function (model){
                var notice = UNBOX.app.collections.notices.done(model);
                $("body").css("cursor", "auto");
            }
        },
        log: function(response){
            var notice = new UNBOX.Models.Notices;
            notice.set({
                type: "warning",
                level: "debug",
                show: true,
                message: response.status + ": " + response.statusText
            });
            UNBOX.app.collections.notices.log(notice);
        },
        notice: function(message,type){
            var notice = new UNBOX.Models.Notices;
            notice.set({
                type: type,
                level: "info",
                show: true,
                message: message
            });
            UNBOX.app.collections.notices.log(notice);
        },
        loggedIn: function(){
            var access_token = UNBOX.app.user.getToken();
            if (!(access_token == null || typeof access_token == 'undefined')) {
                return true;
            }
            return false;
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
                            gapi.client.load('plus', 'v1', UNBOX.Global.Login.Google.apiClientLoaded);
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
                request.execute(UNBOX.Global.Login.Google.handleProfile);
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
UNBOX.Router = Backbone.Router.extend({
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
    initialize: function () {
        //_.bindAll(this);
    },
    help: function () {
        UNBOX.app.view.tutorial();
    },
    about: function () {
        UNBOX.app.view.about();
    },
    manager: function (module) {
        if (UNBOX.Global.Utils.loggedIn()) {
            UNBOX.app.layouts.setCurrent("Manager");
            UNBOX.app.view.reset(false, true, true);
            //Panel 1
            var panel1 = UNBOX.app.collections.panels.findWhere({
                number: 1
            });
            var options = {
                collection: UNBOX.app.modules,
                module: module,
                panel: panel1
            };
            panel1.set({
                content: new UNBOX.Views.Manager.Actions(options)
            });
            panel1.trigger("open");

            options = {};
            options = {
                model: UNBOX.tester.models.request
            };
            UNBOX.app.models.mainPanel.set({
                content: new UNBOX.Views.Manager.ListView.Panel(options)
            });
            /*
             UNBOX.app.views.manager.applicationSelect = new UNBOX.Views.Manager.ListView.ApplicationSelect({
             el: $("#application"),
             collection: UNBOX.app.collections.applications
             });
             UNBOX.app.views.manager.apiSelect = new UNBOX.Views.Manager.ListView.APISelect({
             el: $("#api"),
             collection: UNBOX.app.collections.apis
             });
             UNBOX.app.views.manager.httpMethodSelect = new UNBOX.Views.Manager.ListView.HttpMethodSelect({
             el: $("#httpMethod"),
             collection: UNBOX.app.collections.httpMethods
             });
             UNBOX.app.views.manager.listView = new UNBOX.Views.Manager.ListView.List({
             el: $("#list"),
             collection: UNBOX.app.collections.entryPoints,
             model: UNBOX.app.models.entryPoint
             });
             //Setup cascading dropdowns, and linked models
             UNBOX.app.views.manager.applicationSelect.apiSelect = UNBOX.app.views.manager.apiSelect;
             UNBOX.app.views.manager.applicationSelect.entryPointList = UNBOX.app.views.manager.entryPointList;
             UNBOX.app.views.manager.apiSelect.httpMethodSelect = UNBOX.app.views.manager.httpMethodSelect;
             UNBOX.app.views.manager.apiSelect.entryPointList = UNBOX.app.views.manager.entryPointList;
             UNBOX.app.views.manager.httpMethodSelect.entryPointList = UNBOX.app.views.manager.entryPointList;
             UNBOX.app.views.output.listView = UNBOX.app.views.manager.entryPointList;
             */
        }else{
            UNBOX.app.router.navigate("login",{trigger: true});
        }
    },
    quickRecord: function(module,action,id){
        if (UNBOX.Global.Utils.loggedIn()) {
            if (UNBOX.app.models.currentLayout.get("name") !== "Manager") {
                this.manager(module);
            }
            if (!(module == "" || typeof module == 'undefined' || module == null)) {
                if (UNBOX.app.models.currentModule.get("name") !== module) {
                    UNBOX.app.modules.setCurrent(module);
                }
                if (!(action == "" || typeof action == 'undefined' || action == null)) {
                    if (action == "create" || action == "view") {
                        if (typeof UNBOX.manager.models.current == 'Object') {
                            if (model == UNBOX.app.models.currentModule) {
                                if (!(typeof id == 'undefined' || id == "" || id == null)) {
                                    if (id !== UNBOX.manager.models.current.get("id")) {
                                        UNBOX.manager.models.current.clear();
                                        UNBOX.manager.models.current.set({id: id});
                                    }
                                } else {
                                    UNBOX.manager.models.current.clear();
                                }
                            } else {
                                UNBOX.manager.models.current = new UNBOX.Models[module];
                            }
                        } else {
                            UNBOX.manager.models.current = new UNBOX.Models[module];
                            if (id !== UNBOX.manager.models.current.get("id")) {
                                UNBOX.manager.models.current.set({id: id});
                            }
                        }
                        var panel2 = UNBOX.app.collections.panels.findWhere({
                            number: 2
                        });
                        var options = {
                            module: UNBOX.app.models.currentModule,
                            model: UNBOX.manager.models.current,
                            panel: panel2
                        };
                        panel2.set({
                            content: new UNBOX.Views.Manager.QuickRecord(options)
                        });
                        panel2.trigger("open");
                    } else if (action == "list") {

                    } else if (action == "import") {

                    } else {
                        console.log("Invalid Action");
                    }
                }
            }
        }else{
            UNBOX.app.router.navigate("login",{trigger: true});
        }
    },
    tester: function () {
        if (UNBOX.Global.Utils.loggedIn()) {
            UNBOX.app.layouts.setCurrent("Tester");
            UNBOX.app.view.reset(false, true, true);

            //Setup UI
            var panel1 = UNBOX.app.collections.panels.findWhere({
                number: 1
            });
            UNBOX.tester.models.entryPoint = new UNBOX.Models.EntryPoints;
            UNBOX.tester.models.token = new UNBOX.Models.Tokens;
            UNBOX.tester.collections.parameters = new UNBOX.Collections.Parameters({
                entryPoint: UNBOX.tester.models.entryPoint
            });
            UNBOX.tester.models.request = new UNBOX.Models.Requests;

            var options = {
                entryPoint: UNBOX.tester.models.entryPoint,
                token: UNBOX.tester.models.token,
                panel: panel1
            };
            panel1.set({
                content: new UNBOX.Views.Tester.Setup.Panel(options)
            });
            panel1.trigger("open");

            //panel 2
            options = {};
            var panel2 = UNBOX.app.collections.panels.findWhere({
                number: 2
            });
            options = {
                model: UNBOX.tester.models.entryPoint,
                collection: UNBOX.tester.collections.parameters,
                token: UNBOX.tester.models.token,
                panel: panel2
            };
            panel2.set({
                content: new UNBOX.Views.Tester.EntryPointDetail.Panel(options)
            });

            //panel 3
            options = {};
            var panel3 = UNBOX.app.collections.panels.findWhere({
                number: 3
            });
            options = {
                collection: UNBOX.tester.collections.parameters,
                token: UNBOX.tester.models.token,
                panel: panel3,
                request: UNBOX.tester.models.request
            };
            panel3.set({
                content: new UNBOX.Views.Tester.Parameters.Panel(options)
            });

            //Output
            options = {};
            options = {
                model: UNBOX.tester.models.request
            };
            UNBOX.app.models.mainPanel.set({
                content: new UNBOX.Views.Tester.Output.Panel(options)
            });
        }else{
            UNBOX.app.router.navigate("login",{trigger: true});
        }
    },
    login: function(){
        if (UNBOX.app.models.currentLayout.get("name") !== "Home") {
            this.home();
        }
        if (UNBOX.Global.Utils.loggedIn()==false){
            var panel1 = UNBOX.app.collections.panels.findWhere({
                number: 1
            });
            var options = {
                model: UNBOX.app.user,
                panel: panel1
            };
            panel1.set({
                content: new UNBOX.Views.Home.Login(options)
            });
            panel1.trigger("open");
        }
    },
    home: function(){
        UNBOX.app.layouts.setCurrent("Home");
        UNBOX.app.view.reset(false,true,true);
        if (UNBOX.Global.Utils.loggedIn()==false){
            UNBOX.app.router.navigate("login");
            this.login();
        }else{
            this.profile();
        }
    },
    register: function(){
        UNBOX.app.layouts.setCurrent("Home");
        UNBOX.app.view.reset(false,true,true);
        if (UNBOX.Global.Utils.loggedIn()==false){
            var panel1 = UNBOX.app.collections.panels.findWhere({
                number: 1
            });
            var options = {
                model: UNBOX.app.user,
                panel: panel1
            };
            panel1.set({
                content: new UNBOX.Views.Home.Register(options)
            });
            panel1.trigger("open");
        }else{
            this.profile();
        }
    },
    profile: function(){

    },
    defaultRoute: function () {
        this.home();
    }
});
UNBOX.Views = {
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
            var selectView = new UNBOX.Views.DynamicOption({ model: model });
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
            this.collection.url = UNBOX.Global.ajaxURL + url;
            UNBOX.Collections.Utils.fetch({
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
            this.modules = this.options.modules || null;
            this.layouts = this.options.layouts || null;
            this.config = this.options.config || null;
            this.notices = this.options.notices || null;
            this.layout = this.options.layout || null;
            this.module = this.options.module || null;

            _.bindAll(this,"start","reset");

            //Dom elements
            this.$panel1 = $("#panel1");
            this.$panel2 = $("#panel2");
            this.$panel3 = $("#panel3");
            this.$main = $('#main');
            this.$navBar = $("#main-nav");
            this.$notices = $("#notices");

            //build nav
            this.nav = new UNBOX.Views.NavBar({
                el: this.$navBar,
                collection: this.layouts
            });
            //build notice
            this.notice = new UNBOX.Views.Notice({
                el: this.$notices,
                collection: this.notices
            });
            //build main
            this.main = new UNBOX.Views.Main({
                el: this.$main,
                model: this.model
            });

            //setup panels
            this.panel1 = null;
            this.panel2 = null;
            this.panel3 = null;

            this.render();
        },
        render: function() {
            return this;
        },
        start: function(){
            //build panels
            this.panel1 = new UNBOX.Views.Panel({
                el: this.$panel1,
                model: this.collection.findWhere({
                    number: 1
                })
            });
            this.panel2 = new UNBOX.Views.Panel({
                el: this.$panel2,
                model: this.collection.findWhere({
                    number: 2
                })
            });
            this.panel3 = new UNBOX.Views.Panel({
                el: this.$panel3,
                model: this.collection.findWhere({
                    number: 3
                })
            });
            //start
            //this.notice.;

            //stat History
            Backbone.history.start({
                root: window.location.pathname
            });
        },
        register: function(){

        },
        reset: function(panel1,panel2,panel3){
            this.panel1.reset(panel1);
            this.panel2.reset(panel2);
            this.panel3.reset(panel3);
        }
    }),
    NavBar: Backbone.View.extend({
        events: {
        },
        initialize: function(){
            _.bindAll(this,"render")
            this.collection.on("change",this.render);

            this.template = UNBOX.Global.Utils.getTemplate('navBtns',true);
        },
        render: function(){
            this.model = this.collection.findWhere({
                current: true,
                enabled: true
            });
            this.html = _.template(this.template,{
                current: this.model,
                modules: this.collection.where({
                    enabled: true
                }),
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

            this.$content = this.$el.children(".un-panel-content");

            this.render();
        },
        render: function(){
            this.template = this.$el.html();
            return this;
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
            this.$el.css("width", this.model.get("width")+"%");
        }
    }),
    Panel: Backbone.View.extend({
        events: {
            "click .un-close-panel": "closePanel",
            "click .un-open-panel": "openPanel"
        },
        initialize: function(options){
            this.options = options || {};

            _.bindAll(this,"openPanel","closePanel","showPanel","hidePanel","render","convert","setContent");
            this.model.on("change:number",this.convert);
            this.model.on("open",this.openPanel);
            this.model.on("close",this.closePanel);
            this.model.on("show",this.showPanel);
            this.model.on("hide",this.hidePanel);
            this.model.on("change:content",this.setContent);

            this.template = UNBOX.Global.Utils.getTemplate("panel",true);
            this.render();
        },
        render: function(){
            this.html = _.template(this.template,{
                num: this.model.get("number")
            });
            this.$el.html(this.html);

            this.$content = $("#panel"+this.model.get('number')+"_content");
            this.$panel = $("#panel"+this.model.get('number'));
            this.$panel_toggle = $("#panel"+this.model.get('number')+"_toggle");

            return this;
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

            _.bindAll(this,"render","notify","denotify");
            this.collection.on("show",this.notify);
            this.collection.on("unshow",this.denotify);

            this.template = UNBOX.Global.Utils.getTemplate("notice",true);

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
UNBOX.Views.Home = {
    Login: Backbone.View.extend({
        events: {
            "click #login": "login",
            "click #register": "register",
            "focusout input": "updateModel"
        },
        initialize: function(options) {
            this.options = options || {};
            this.panel = this.options.panel || {};

            this.model = this.model || new UNBOX.Models.User;

            this.username = null;
            this.password = null;

            _.bindAll(this,"login","updateModel");
        },
        render: function() {
            var template = UNBOX.Global.Utils.getTemplate("Login");
            this.template = _.template(template);
            this.$el.html(this.template);
            this.setup();

            return this;
        },
        setup: function(){
            //setup Google+ button
            UNBOX.Global.Login.Google.loginButton();
        },
        login: function(){
            UNBOX.app.user.login();
        },
        register: function(){
            UNBOX.app.router.navigate("register", {trigger: true});
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
            this.panel = this.options.panel || {};
            this.captcha = null;

            _.bindAll(this,"submit","updateModel");
        },
        render: function() {
            var template = UNBOX.Global.Utils.getTemplate("Register");
            this.template = _.template(template);
            this.$el.html(this.template);
            this.setup();

            return this;
        },
        setup: function(){
            this.captcha = UNBOX.Global.Login.Google.ReCaptcha.render('captcha');
        },
        submit: function(){
            this.model.url = "user/register";
            if (typeof grecaptcha !== 'undefined') {
                this.model.set('captcha', grecaptcha.getResponse(this.captcha));
            }
            UNBOX.Models.Utils.save({
                model: this.model,
                success: function(model,response,options){
                    UNBOX.Global.Utils.notice("User Created. Please login to access system.","success");
                    UNBOX.app.router.navigate("login",{trigger: true});
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
UNBOX.Views.Tester = {
    Setup: {
        Panel: Backbone.View.extend({
            events: {
            },
            initialize: function(options) {
                this.options = options || {};
                this.panel = this.options.panel || {};

                //prepare dom references
                this.$application = null;
                this.$api = null;
                this.$loginMethod = null;
                this.$httpMethod = null;
                this.$entryPoint = null;
                this.$api_login = null;

                //setup models
                this.application = new UNBOX.Models.Applications;
                this.api = new UNBOX.Models.APIs;
                this.loginMethod = new UNBOX.Models.Logins;
                this.httpMethod = new UNBOX.Models.HttpMethods;
                this.entryPoint = this.options.entryPoint || new UNBOX.Models.EntryPoints;
                this.token = this.options.token || new UNBOX.Models.Tokens;

                //setup collections
                this.applications = new UNBOX.Collections.Applications;
                this.apis = new UNBOX.Collections.APIs;
                this.loginMethods = new UNBOX.Collections.Logins;
                this.httpMethods = new UNBOX.Collections.HttpMethods;
                this.entryPoints = new UNBOX.Collections.EntryPoints;

            },
            render: function() {
                var template = UNBOX.Global.Utils.getTemplate("Setup");
                this.template = _.template(template);
                this.$el.html(this.template);

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
                this.applicationSelect = new UNBOX.Views.Tester.Setup.ApplicationSelect({
                    el: this.$application,
                    collection: this.applications,
                    model: this.application
                });
                this.apiSelect = new UNBOX.Views.Tester.Setup.APISelect({
                    el: this.$api,
                    collection: this.apis,
                    model: this.api
                });
                this.loginSelect = new UNBOX.Views.Tester.Setup.LoginSelect({
                    el: this.$loginMethod,
                    collection: this.loginMethods,
                    model: this.loginMethod
                });
                this.httpMethodSelect = new UNBOX.Views.Tester.Setup.HttpMethodSelect({
                    el: this.$httpMethod,
                    collection: this.httpMethods,
                    model: this.httpMethod
                });
                this.entryPointSelect = new UNBOX.Views.Tester.Setup.EntryPointSelect({
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
                this.loginSubPanel = new UNBOX.Views.Tester.Setup.LoginPanel({
                    el: this.$api_login,
                    model: this.loginMethod,
                    token: this.token
                });

                //load first dropdown
                this.applications.fetch({ reset: true });
            }
        }),
        ApplicationSelect: UNBOX.Views.DynamicSelect.extend({
            setSelectedId: function(applicationId) {
                this.apiSelect.selectedId = null;
                this.apiSelect.setApplicationId(applicationId);
                this.model = this.collection.get(applicationId);
                UNBOX.tester.models.application = this.model;
            }
        }),
        APISelect: UNBOX.Views.DynamicSelect.extend({
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
                UNBOX.tester.models.api = this.model;
            },
            setApplicationId: function(applicationID) {
                this.populateFrom("applications/" + applicationID + "/apis");
            }
        }),
        HttpMethodSelect: UNBOX.Views.DynamicSelect.extend({
            setSelectedId: function(httpMethodID) {
                this.entryPointSelect.selectedId = null;
                this.entryPointSelect.setMethodId(httpMethodID);
                UNBOX.tester.models.httpMethod = UNBOX.app.collections.httpMethods.findWhere({id:httpMethodID});
            },
            setApiID: function(apiID) {
                this.populateFrom("apis/" + apiID + "/methods");
            }
        }),
        EntryPointSelect: UNBOX.Views.DynamicSelect.extend({
            setSelectedId: function(entryPointID) {
                this.model.clear({
                    silent: true
                });
                this.model.set({
                    id: entryPointID
                },{
                    silent: true
                });
                UNBOX.Models.Utils.fetch({
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
        LoginSelect: UNBOX.Views.DynamicSelect.extend({
            setSelectedId: function(loginID) {
                var model = this.collection.get(loginID);
                this.model.set({
                    id: loginID,
                    login_entryPoint_id: model.get('login_entryPoint_id'),
                    logout_entryPoint_id: model.get('logout_entryPoint_id'),
                    value: model.get('value')
                });
                UNBOX.tester.models.loginMethod = this.model;
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
                this.token = this.options.token || new UNBOX.Models.Tokens;

                _.bindAll(this,"render","showForm","resetLogin","logout");
                this.model.bind("change",this.render);
                this.model.bind("logout",this.resetLogin);
                this.token.on("change:access_token",this.loginCheck);

                this.$web_address = $("#web_address");
                this.$login_method = $("#login_method");
                this.$api_login_form = $("#API_Login_form");
                this.loginForm = new UNBOX.Views.Tester.Setup.LoginForm({
                    el: this.$api_login_form,
                    model: this.model,
                    token: this.token
                });

                this.$api_login_info = $("#api_login_info");


                this.render();
            },
            render: function(){
                var template;
                if (this.token.get("access_token")==null||this.token.get("access_token")==""){
                    this.showForm();
                }else{
                    var template = UNBOX.Global.Utils.getTemplate("LoginInfo");
                    this.template = _.template(template,{
                        token: this.model
                    });
                    this.$api_login_info.html(this.template);
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
                UNBOX.tester.web_address = $(e.currentTarget).val();
            },
            resetLogin: function(){
                UNBOX.app.login_info = "";
                this.$loggedIn_btn.addClass("hidden");
                this.$api_login_form.trigger("reset");
                this.$web_address.value("");
                this.$login_method.value("");
                $(".logout").attr("disabled",true);
            },
            logout: function(){
                var data = {
                    'web_address': UNBOX.tester.web_address,
                    'token': UNBOX.tester.models.token.get("access_token")
                };
                $.ajax({
                    url: UNBOX.Global.ajaxURL+'apis/'+UNBOX.app.models.api.get('id')+'/test/'+UNBOX.app.models.login.get("logout_entryPoint_id"),
                    type: "POST",
                    data: data,
                    success: function(data){
                        UNBOX.tester.models.token.clear();
                        UNBOX.tester.models.token.trigger("logout");
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
                this.token = this.options.token || new UNBOX.Models.Tokens;

                _.bindAll(this,"render","login");

                this.$normal_div = $("#login_normal");
                this.$advanced_div = $("#login_advanced");
                this.$advanced_btn = $("#login_advanced_btn");
                this.$loggedIn_btn = $("#logged_in_info");

                this.$logoutBtn = $("#logoutBtn");
                this.$loginBtn = $("#loginBtn");

                this.collection = new UNBOX.Collections.Parameters({
                    loginMethod: this.model
                });
                this.collection.on("sync",this.render);


            },
            render: function(){
                var template = UNBOX.Global.Utils.getTemplate("LoginParam");
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
                this.template = _.template(template,{
                    parameters: normal_params
                });
                this.$normal_div.html(this.template);
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
                this.template = "";
                this.template = _.template(template,{
                    parameters: advanced_params
                });
                this.$advanced_div.removeClass('in');
                this.$advanced_div.html(this.template);
                this.$el.removeClass('hidden');
                $(".select2",this.$el).select2();
                return this;
            },
            login: function(){
                var loginForm = this.$el.serializeArray();
                loginForm.push({
                    name: 'web_address',
                    value: UNBOX.tester.web_address
                });
                $.ajax({
                    url: UNBOX.Global.ajaxURL+'apis/'+UNBOX.tester.models.api.get("id")+'/login/'+this.model.get("login_entryPoint_id"),
                    type: "POST",
                    data: loginForm,
                    context: this,
                    success: function(data){
                        this.$loggedIn_btn.removeClass("hidden");
                        UNBOX.tester.login_info = jQuery.parseJSON(data['response']);
                        if (this.token!==null) this.token.clear({silent: true});
                        this.token.set(UNBOX.tester.login_info);
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

                this.examples = new UNBOX.Collections.Examples({
                    entryPoint: this.model
                });
                this.exceptions = new UNBOX.Collections.Exceptions({
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
                var template = UNBOX.Global.Utils.getTemplate("EntryPointOverview");
                this.template = _.template(template);
                this.$el.html(this.template);

                this.setup();

                return this;
            },
            setup: function(){
                this.$ep_main = $("#ep_main");
                this.$ep_action = $("#ep_action1");
                this.$ep_parameters = $("#ep_parameters");
                this.$ep_examples = $("#ep_examples");
                this.$ep_exceptions = ("#ep_exceptions");

                this.entryPointDetail = new UNBOX.Views.Tester.EntryPointDetail.MainDetail({
                    el: this.$ep_main,
                    model: this.model
                });
                this.actionButtons= new UNBOX.Views.Tester.ActionButtons({
                    el: this.$ep_action,
                    model: this.token,
                    collection: this.collection,
                    panelNumber: this.panel.get("number")
                });
                this.parameterPanel = new UNBOX.Views.Tester.EntryPointDetail.Parameters({
                    el: this.$ep_parameters,
                    collection: this.collection
                });
                this.examplePanel = new UNBOX.Views.Tester.EntryPointDetail.Examples({
                    el: this.$ep_examples,
                    collection: this.examples
                });
                this.exceptionPanel = new UNBOX.Views.Tester.EntryPointDetail.Exceptions({
                    el: this.$ep_exceptions,
                    collection: this.exceptions
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
            initialize: function(){
                _.bindAll(this,"render");
                this.model.bind("sync",this.render);
            },
            render: function(){
                var template = UNBOX.Global.Utils.getTemplate("EntryPointMain");
                this.template = _.template(template,{
                    entryPoint: this.model
                });
                this.$el.html(this.template);
                return this;
            }
        }),
        Parameters: Backbone.View.extend({
            events: {
            },
            initialize: function() {
                _.bindAll(this,"render");
                this.collection.bind("sync",this.render);
            },
            render: function(){
                var template = UNBOX.Global.Utils.getTemplate("EntryPointParameters");
                this.template = _.template(template, {
                    parameters: this.collection.where({url_param: "0"})
                });
                this.$el.html(this.template);
                return this;
            }
        }),
        Examples: Backbone.View.extend({
            events: {
            },
            initialize: function() {
                _.bindAll(this,"render");
                this.collection.bind("sync",this.render);
            },
            render: function(){
                if (this.collection.length>0) {
                    var template = UNBOX.Global.Utils.getTemplate("EntryPointExamples");
                    this.template = _.template(template, {
                        examples: this.collection
                    });
                    this.$el.html(this.template);
                }
                return this;
            }
        }),
        Exceptions: Backbone.View.extend({
            events: {
            },
            initialize: function() {
                _.bindAll(this,"render");
                this.collection.bind("sync",this.render);
            },
            render: function(){
                if (this.collection.length>0) {
                    var template = UNBOX.Global.Utils.getTemplate("EntryPointExceptions");
                    this.template = _.template(template, {
                        exceptions: this.collection
                    });
                    this.$el.html(this.template);
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

                this.$url_parameters = null;
                this.$request_parameters = null;
                this.$actions = null;

                _.bindAll(this,"render","panelState");
                this.collection.on("testerSetup",this.panelState);
            },
            render: function(){
                var template = UNBOX.Global.Utils.getTemplate("ParameterForm");
                this.template = _.template(template);
                this.$el.html(this.template);

                this.setup();

                return this;
            },
            setup: function(){
                //setup dom references
                this.$url_parameters = $("#ep_url_params");
                this.$request_parameters = $("#ep_request_params");
                this.$actions = $("#ep_action2");

                this.urlParams = new UNBOX.Views.Tester.Parameters.UrlParams({
                    el: this.$url_parameters,
                    collection: this.collection
                });
                this.requestParams = new UNBOX.Views.Tester.Parameters.RequestParams({
                    el: this.$request_parameters,
                    collection: this.collection
                });
                this.actionButtons = new UNBOX.Views.Tester.ActionButtons({
                    el: this.$actions,
                    model: this.token,
                    collection: this.collection,
                    panelNumber: this.panel.get("number")
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
            initialize: function() {
                _.bindAll(this,"render");
                this.collection.on("testerSetup",this.render);
            },
            render: function(){
                var template = UNBOX.Global.Utils.getTemplate("Parameters");
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
                this.template = _.template(template,{
                    parameters: url_params
                });
                this.$el.html(this.template);
                return this;
            }
        }),
        RequestParams: Backbone.View.extend({
            initialize: function() {
                _.bindAll(this,"render");
                this.collection.on("testerSetup",this.render);
            },
            render: function(){
                var template = UNBOX.Global.Utils.getTemplate("Parameters");
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
                this.template = _.template(template,{
                    parameters: url_params
                });
                this.$el.html(this.template);
                return this;
            }
        })
    },
    Output: {
        Panel: Backbone.View.extend({
            events: {
                "click .format_type": "changeStyle"
            },
            initialize: function () {
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
                var template = UNBOX.Global.Utils.getTemplate("Output");
                this.template = _.template(template,{
                    request: this.model,
                    style: this.style
                });
                this.$el.html(this.template);

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

            _.bindAll(this,"render","testButtonState","testEP","generateScript","setupParams");
            this.collection.bind("sync",this.render);
            this.model.bind("change:access_token",this.testButtonState);
        },
        render: function(){
            var hasParams = false;
            if (this.collection.length>0){
                hasParams = true;
            }
            var template = UNBOX.Global.Utils.getTemplate("EntryPointActions");
            this.template = _.template(template,{
                hasParams: hasParams,
                panelNumber: this.panelNumber
            });
            this.$el.html(this.template);
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
                    value: UNBOX.tester.models.token.get('access_token')
                },
                {
                    name: 'web_address',
                    value: UNBOX.tester.web_address
                });
            $.ajax({
                url: UNBOX.Global.ajaxURL+'apis/'+UNBOX.tester.models.api.get('id')+'/test/'+UNBOX.tester.models.entryPoint.get("id"),
                type: "POST",
                data: paramForm,
                success: function(data){
                    UNBOX.tester.models.request.clear();
                    UNBOX.tester.models.request.set(data);
                },
                dataType: 'json'
            });
        },
        generateScript: function(e){

        }
    })
}
UNBOX.Views.Manager = {
    Actions: Backbone.View.extend({
        events: {
            "click .btn": "action"
        },
        initialize: function(options){
            this.options = options || {};
            this.module = this.options.module || false;
        },
        render: function(){
            var template = UNBOX.Global.Utils.getTemplate("Actions");
            this.template = _.template(template,{
                modules: this.collection.where({
                    enabled: true
                }),
                default_module: this.module
            });
            this.$el.html(this.template);
        },
        action: function(e){
            var module = $(e.currentTarget).data('module');
            var action = $(e.currentTarget).data('action');
            UNBOX.app.modules.setCurrent(module);
            UNBOX.app.router.navigate("manage/"+module+"/"+action,{trigger: true});
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
            initialize: function () {
                _.bindAll(this,"setNameFilter","previous","next");
                this.$output = $("#output");
                this.$nameFilter = $("#name");

                this.render();
            },
            render: function () {
                var template = UNBOX.Global.Utils.getTemplate("Output");
                this.template = _.template(template,{
                    request: this.model
                });
                this.$output.html(this.template);
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
        ApplicationSelect: UNBOX.Views.DynamicSelect.extend({
            setSelectedId: function(applicationId) {
                this.apiSelect.setApplicationID(applicationId);
                this.entryPointList.setApplicationID(applicationId);
            },
            setDisabled: function(){

            }
        }),
        APISelect: UNBOX.Views.DynamicSelect.extend({
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
        HttpMethodSelect: UNBOX.Views.DynamicSelect.extend({
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
            initialize: function(){
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
                var template = UNBOX.Global.Utils.getTemplate("List");
                this.template = _.template(template,{
                    entryPoints: this.collection.models
                });
                this.$el.html(this.template);
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
                    this.collection.url = UNBOX.Global.ajaxURL + "entryPoints/filter?" + filter + "&limit=" + this.limit + "&offset=" + this.offset;
                }else{
                    this.collection.url = UNBOX.Global.ajaxURL + "entryPoints?limit=" + this.limit + "&offset=" + this.offset;
                }
                UNBOX.Collections.Utils.fetch({
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
            _.bindAll(this,"render","getFields","panelState","record","clear","save","updateModel");
            this.model.on("sync",this.record,this);

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

            _.bindAll(this,"render","getFields","clear","save","updateModel");
            this.model.on("sync",this.render);

            this.quickRecord = $("#quickRecord");

            if (!(typeof this.model.get("id") =='undefined' || this.model.get("id")=="" || this.model.get("id")==null)){
                UNBOX.Models.Utils.fetch({
                    model: this.model
                });
            }
        },
        render: function(){
            var fields = this.getFields();
            var actionMenu = this.getActions();
            this.template = this.template==null ? UNBOX.Global.Utils.getTemplate("Record") : this.template;
            this.html = _.template(this.template, {
                currentModule: this.module,
                actions: actionMenu,
                fields: fields
            });
            this.$el.html(this.html);
        },
        getActions: function(){
            var template = UNBOX.Global.Utils.getTemplate("RecordActions");
            template = _.template(template, {
                currentModule: this.module
            });
            return template;
        },
        getFields: function(){
            var fields = [];
            var moduleFields = this.module.get("fields");
            var labelTemplate = UNBOX.Global.Utils.getTemplate("label",true);
            var helpTemplate = UNBOX.Global.Utils.getTemplate("help",true);
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
                                options: new UNBOX.Collections.Data,
                                help: field['help'],
                                disabled: attributes['disabled'],
                                value: this.model.get(fieldName)
                            };
                            var fieldTemplate = UNBOX.Global.Utils.getTemplate(attributes['type'], true);
                            if (attributes['type']=='select'){
                                if (!(typeof attributes['collection']=='undefined' || attributes['collection']==null || attributes['collection']=="")){
                                    for (var x=0; x<UNBOX.app.collections[attributes['collection']].length;x++){
                                        var model = UNBOX.app.collections[attributes['collection']].get(x);
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
                UNBOX.Models.Utils.save({
                    model: this.model,
                    options: {
                        module: this.module
                    },
                    success: function(model,response,options){
                        UNBOX.app.router.navigate("#manage/"+options.module.get('name')+"/view/"+model.get('id'));
                    }
                });
                /*
                this.model.save(null,{
                    module: this.module,
                    success: function(model,response,options){
                        UNBOX.app.router.navigate("#manage/"+options.module.get('name')+"/view/"+model.get('id'));
                    }
                });*/
            }else{
                UNBOX.Models.Utils.save({
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
UNBOX.Models = {
    Utils: {
        fetch: function(object){
            var model = object.model || null;
            var options = object.options || {};
            var success = object.success || function () {};
            var error = object.error || function () {};
            if (model!==null) {
                var notice = UNBOX.Global.Utils.Loading.start("Loading " + model.name + " at " + model.url);
                options.success = function (model, response, options) {
                    success(model,response,options);
                    UNBOX.Global.Utils.Loading.done(notice);
                }
                options.error = function (model, response, options) {
                    error(model,response,options);
                    UNBOX.Global.Utils.Loading.done(notice);
                    UNBOX.Global.Utils.log(response);
                }
                model.fetch(options);
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
            var notice = UNBOX.Global.Utils.Loading.start("Loading "+model.name+" at "+model.url);
            if (model!==null) {
                options.success = function (model, response, options) {
                    success(model,response,options);
                    UNBOX.Global.Utils.Loading.done(notice);
                }
                options.error = function (model, response, options) {
                    error(model,response,options);
                    UNBOX.Global.Utils.Loading.done(notice);
                    UNBOX.Global.Utils.log(response);
                }
                model.save(attributes, options);
            }else{
                console.log("No model passed to save");
            }
        }
    },
    Applications: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        name: "Application",
        urlRoot: UNBOX.Global.ajaxURL+'applications',
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
        urlRoot: UNBOX.Global.ajaxURL+'apis',
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
        urlRoot: UNBOX.Global.ajaxURL+'httpMethods',
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
        urlRoot: UNBOX.Global.ajaxURL+'entryPoints',
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
        urlRoot: UNBOX.Global.ajaxURL+'parameters',
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
        urlRoot: UNBOX.Global.ajaxURL+'parameterTypes',
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
        urlRoot: UNBOX.Global.ajaxURL+'examples',
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
        urlRoot: UNBOX.Global.ajaxURL+'exceptions',
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
        urlRoot: UNBOX.Global.ajaxURL+'logins',
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
            this.panels = this.options.panels || new UNBOX.Collections.Panels;

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
    Modules: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        urlRoot: UNBOX.Global.ajaxURL+"metadata/",
        default:{
            name: "",
            label: null,
            label_plural: null,
            enabled: true,
            current: false,
            fields: null,
            labels: {}
        },
        getValue: function(){
            return this.get('name');
        }
    }),
    Layouts: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
        },
        urlRoot: UNBOX.Global.ajaxURL+"metadata/",
        default:{
            name: "",
            label: null,
            label_plural: null,
            icon: "",
            enabled: true,
            current: false,
            link: null,
            links: null,
            labels: {}
        },
        getValue: function(){
            return this.get('name');
        }
    }),
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
    User: Backbone.Model.extend({
        initialize: function(){
            _.bindAll(this, 'getValue');
            this.resetToken();
        },
        urlRoot: "user/me",
        default: {
            token: null,
            name: null,
            username: null,
            first_name: null,
            last_name: null,
            email: null,
            password: null
        },
        getValue: function(){
            return this.get('name');
        },
        login: function(){
            var loading = UNBOX.Global.Utils.Loading.start("Logging In");
            this.resetToken();
            $.ajax({
                url: 'user/login',
                type: "POST",
                context: this,
                data: {
                    username: this.get('username'),
                    password: this.get('password')
                },
                success: function(data){
                    token = this.resetToken(data);
                    this.set({
                        token: token,
                        password: null
                    });
                    $.ajaxSetup({
                        headers: { 'Authorization' :'Bearer '+UNBOX.app.user.getToken() }
                    });
                    UNBOX.app.metadata.fetchAll();
                    this.fetch();
                    UNBOX.app.router.navigate("home",{trigger: true});
                },
                error: function(data){
                    UNBOX.Global.Utils.log(data);
                },
                dataType: 'json'
            }).done(function() {
                UNBOX.Global.Utils.Loading.done(loading);
            });
        },
        logout: function() {
            this.reset();
            $.ajaxSetup({
                headers: {}
            });
        },
        resetToken: function(data){
            var token = new UNBOX.Models.Tokens;
            if (typeof data !=='undefined'){
                token.set(data);
            }
            this.set({
                token: token
            });
            return this.get('token');
        },
        getToken: function(){
            var token = this.get('token');
            return token.get('access_token');
        }
    })
}
UNBOX.Collections = {
    Utils: {
        fetch: function (object) {
            var options = object.options || {};
            var success = object.success || function () {};
            var error = object.error || function () {};
            var collection = object.collection || null;
            if (collection !== null) {
                var notice = UNBOX.Global.Utils.Loading.start("Loading " + collection.name + " at " + collection.url);
                options.success = function (model, response, options) {
                    success(model,response,options);
                    UNBOX.Global.Utils.Loading.done(notice);
                }
                options.error = function (model, response, options) {
                    error(model,response,options);
                    UNBOX.Global.Utils.Loading.done(notice);
                    UNBOX.Global.Utils.log(response);
                }
                collection.fetch(options);
            }else{
                console.log("No Collection passed to fetch method");
            }
        }
    },
    Applications: Backbone.Collection.extend({
        model: UNBOX.Models.Applications,
        url: UNBOX.Global.ajaxURL+"applications/"
    }),
    APIs: Backbone.Collection.extend({
        model: UNBOX.Models.APIs,
        url: UNBOX.Global.ajaxURL+"apis/"
    }),
    Logins: Backbone.Collection.extend({
        model: UNBOX.Models.Logins,
        url: UNBOX.Global.ajaxURL+"logins/"
    }),
    HttpMethods: Backbone.Collection.extend({
        model: UNBOX.Models.HttpMethods,
        url: UNBOX.Global.ajaxURL+"httpMethods/"
    }),
    EntryPoints: Backbone.Collection.extend({
        model: UNBOX.Models.EntryPoints,
        url: UNBOX.Global.ajaxURL+"entryPoints/"
    }),
    Parameters: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.entryPoint = this.options.entryPoint || new UNBOX.Models.EntryPoints;
            this.loginMethod = this.options.loginMethod || new UNBOX.Models.Logins;

            _.bindAll(this,"getEntryPointParams","getLoginParams");
            this.entryPoint.on("change:id",this.getEntryPointParams);
            this.loginMethod.on("change:id",this.getLoginParams);
        },
        model: UNBOX.Models.Parameters,
        url: UNBOX.Global.ajaxURL+"parameters/",
        getEntryPointParams: function(){
            this.url = UNBOX.Global.ajaxURL+"entryPoints/"+this.entryPoint.get("id")+"/link/parameters";
            this.fetch();
        },
        getLoginParams: function(){
            console.log(this.loginMethod);
            this.url = UNBOX.Global.ajaxURL+"entryPoints/"+this.loginMethod.get("login_entryPoint_id")+"/link/parameters";
            this.fetch();
        }
    }),
    DataTypes: Backbone.Collection.extend({
        model: UNBOX.Models.Parameters,
        url: UNBOX.Global.ajaxURL+"parameterTypes/1"
    }),
    ApiTypes: Backbone.Collection.extend({
        model: UNBOX.Models.Parameters,
        url: UNBOX.Global.ajaxURL+"parameterTypes/2"
    }),
    Examples: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.entryPoint = this.options.entryPoint || new UNBOX.Models.EntryPoints;

            _.bindAll(this,"getExamples");
            this.entryPoint.on("change",this.getExamples);
        },
        model: UNBOX.Models.Examples,
        url: UNBOX.Global.ajaxURL+"examples/",
        getExamples: function(){
            this.url = UNBOX.Global.ajaxURL+"entryPoints/"+this.entryPoint.get("id")+"/link/examples";
            /*this.fetch();*/
        }
    }),
    Exceptions: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.entryPoint = this.options.entryPoint || new UNBOX.Models.EntryPoints;

            _.bindAll(this,"getExceptions");
            this.entryPoint.on("change",this.getExceptions);
        },
        model: UNBOX.Models.Exceptions,
        url: UNBOX.Global.ajaxURL+"exceptions/",
        getExceptions: function(){
            this.url = UNBOX.Global.ajaxURL+"entryPoints/"+this.entryPoint.get("id")+"/link/parameters";
            /*this.fetch();*/
        }
    }),
    MetaData: Backbone.Collection.extend({
        initialize: function(){
            _.bindAll(this,"fetchAll");
        },
        name: "Application Metadata",
        url: UNBOX.Global.ajaxURL + "metadata/",
        model: UNBOX.Models.Data,
        fetchAll: function(){
            UNBOX.Collections.Utils.fetch({
                collection: this,
                options: null,
                success: function() {
                    this.trigger("fetched");
                }
            });
        }
    }),
    Data: Backbone.Collection.extend({
        model: UNBOX.Models.Data
    }),
    Panels: Backbone.Collection.extend({
        model: UNBOX.Models.Panels
    }),
    Notices: Backbone.Collection.extend({
        initialize: function(){
            _.bindAll(this,"log","appLoading","done");
        },
        model: UNBOX.Models.Notices,
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
                        setTimeout(function(model){
                            UNBOX.app.collections.notices.done(model);
                        },10000,model);
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
    Layouts: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.metadata = typeof this.options.metadata !== 'undefined' ? this.options.metadata : new UNBOX.Collections.MetaData;
            _.bindAll(this,"update","setCurrent");
            this.metadata.bind("fetched",this.update)
        },
        model: UNBOX.Models.Layouts,
        update: function(layout,cached){
            layout = typeof layout !== "undefined" ? layout : "all";
            cached = typeof cached !== "undefined" ? cached : true;
            if (cached==true){
                var layouts = this.metadata.findWhere({key:"layouts"});
                if (layout=="all") {
                    this.reset(layouts.get('value'));
                }else{
                    var layoutArray = layouts.get('value');
                    for (var x = 0; x < layoutArray.length; x++) {
                        if (layout==layoutArray[x].name) {
                            var l = this.findWhere({
                                name: layout
                            });
                            l.set({
                                name: moduleArray[x].name,
                                label: moduleArray[x].label,
                                label_plural: moduleArray[x].label_plural,
                                link: moduleArray[x].link,
                                icon: moduleArray[x].icon,
                                enabled: moduleArray[x].enabled,
                                links: moduleArray[x].links,
                                templates: moduleArray[x].templates
                            });
                            break;
                        }
                    }
                }
            }else{
                if (layout=="all") {
                    this.fetch();
                }else{
                    var l = this.findWhere({
                        name: layout
                    });
                    l.url = l.urlRoot+"/"+layout;
                    l.fetch();
                }
            }
        },
        setCurrent: function(layout){
            UNBOX.app.models.currentLayout.set({
                    current: false
                },
                {
                    silent: true
                });
            var model = this.findWhere({ name: layout });
            model.set({
                current: true
            });
            UNBOX.app.models.currentLayout.set(
                model.toJSON()
            );
        }
    }),
    Modules: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.metadata = typeof this.options.metadata !== 'undefined' ? this.options.metadata : new UNBOX.Collections.MetaData;
            _.bindAll(this,"update","setCurrent");
            this.metadata.bind("fetched",this.update)
        },
        model: UNBOX.Models.Modules,
        update: function(module,cached){
            module = typeof module !== "undefined" ? module : "all";
            cached = typeof cached !== "undefined" ? cached : true;
            if (cached==true){
                var modules = this.metadata.findWhere({key:"modules"});
                if (module=="all") {
                    this.reset(modules.get('value'));
                }else{
                    var moduleArray = modules.get('value');
                    for (var x = 0; x < moduleArray.length; x++) {
                        if (module==moduleArray[x].name) {
                            var mod = this.findWhere({
                                name: module
                            });
                            mod.set({
                                name: moduleArray[x].name,
                                label: moduleArray[x].label,
                                label_plural: moduleArray[x].label_plural,
                                enabled: moduleArray[x].enabled,
                                fields: moduleArray[x].fields,
                                relationships: moduleArray[x].relationships,
                                options: moduleArray[x].options
                            });
                            break;
                        }
                    }
                }
            }else{
                if (module=="all") {
                    this.fetch();
                }else{
                    var mod = this.findWhere({
                        name: module
                    });
                    mod.url = mod.urlRoot+"/"+module;
                    mod.fetch();
                }
            }
        },
        setCurrent: function(module) {
            UNBOX.app.models.currentModule.set({
                    current: false
                },
                {
                    silent: true
                }
            );
            var model = this.findWhere({ name: module });
            model.set({
                current: true
            });
            UNBOX.app.models.currentModule.set(
                model.toJSON()
            );
        }
    }),
    Config: Backbone.Collection.extend({
        initialize: function(options){
            this.options = options || {};
            this.metadata = typeof this.options.metadata !== 'undefined' ? this.options.metadata : new UNBOX.Collections.MetaData;
            _.bindAll(this,"update");
            this.metadata.bind("fetched",this.update)
        },
        url: UNBOX.Global.ajaxURL + "metadata/config/",
        model: UNBOX.Models.Data,
        update: function(cached){
            cached = typeof cached !== "undefined" ? cached : true;
            if (cached==true){
                this.reset();
                var config = this.metadata.findWhere({key:"config"});
                var configArray = config.get('value');
                for(var key in configArray){
                    this.add({
                        key: key,
                        value: configArray[key]
                    });
                }
            }else{
                this.fetch();
            }
        }
    })
}

//Setup App
UNBOX.app = {
    metadata: new UNBOX.Collections.MetaData,
    config: null,
    modules: null,
    layouts: null,
    user: new UNBOX.Models.User,
    models: {
        mainPanel: null,
        currentLayout: new UNBOX.Models.Layouts,
        currentModule: new UNBOX.Models.Modules
    },
    collections: {
        httpMethods: new UNBOX.Collections.HttpMethods,
        dataTypes: new UNBOX.Collections.DataTypes,
        apiTypes: new UNBOX.Collections.ApiTypes,
        panels: new UNBOX.Collections.Panels,
        notices: new UNBOX.Collections.Notices
    },
    templates: {},
    view: null,
    nav: null,
    router: new UNBOX.Router
};
//Create objects for global storing of module specific data
UNBOX.tester = {
    test: null,
    web_address: null,
    login_info: null,
    models: {},
    collections: {}
};
UNBOX.manager = {
    models: {},
    collections: {}
};
UNBOX.automater = {};
UNBOX.documenter = {};
UNBOX.app.collections.panels.add([
    new UNBOX.Models.Panels({
        number: 1
    }),
    new UNBOX.Models.Panels({
        number: 2
    }),
    new UNBOX.Models.Panels({
        number: 3
    })
]);
UNBOX.app.models.mainPanel = new UNBOX.Models.MainPanel({
    panels: UNBOX.app.collections.panels
});
UNBOX.app.config = new UNBOX.Collections.Config({
    metadata: UNBOX.app.metadata
});
UNBOX.app.modules = new UNBOX.Collections.Modules({
    metadata: UNBOX.app.metadata
});
UNBOX.app.layouts = new UNBOX.Collections.Layouts({
    metadata: UNBOX.app.metadata
});
UNBOX.app.view = new UNBOX.Views.AppView({
    el: $("body"),
    model: UNBOX.app.models.mainPanel,
    collection: UNBOX.app.collections.panels,
    config: UNBOX.app.config,
    modules: UNBOX.app.modules,
    layouts: UNBOX.app.layouts,
    module: UNBOX.app.models.currentModule,
    layout: UNBOX.app.models.currentLayout,
    notices: UNBOX.app.collections.notices
});
UNBOX.Collections.Utils.fetch({
    collection: UNBOX.app.metadata,
    options: null,
    success: function() {
        UNBOX.app.metadata.trigger("fetched");
        UNBOX.Global.Bootstrap.start();
    }
});
function googleLoginWrapper(authResult){
    UNBOX.Global.Login.Google.loginButton(authResult);
}








