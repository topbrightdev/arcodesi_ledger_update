/*
Template Name: Adminto - Responsive Bootstrap 5 Admin Dashboard
Author: CoderThemes
Website: https://coderthemes.com/
Contact: support@coderthemes.com
File: Layout
*/

class LeftSidebar {

    constructor() {
        this.body = $('body');
        this.window = $(window)
    }

    initMenu() {
        var self = this;

        // var defaultSidebarSize = sidebar.size ? sidebar.size : 'default';

        // resets everything

        // Left menu collapse
        // $('.button-menu-mobile').on('click', function (event) {
        //     event.preventDefault();
        //     var sidebarSize = self.body.attr('data-sidebar-size');
        //     if (self.window.width() >= 993) {
        //         if (sidebarSize === 'condensed') {
        //             self.changeSize(defaultSidebarSize);
        //             window.dispatchEvent(new Event('adminto.setFluid'));
        //         } else {
        //             self.changeSize('condensed');
        //             window.dispatchEvent(new Event('adminto.setBoxed'));
        //         }
        //     } else {
        //         self.changeSize(defaultSidebarSize);
        //         self.body.toggleClass('sidebar-enable');
        //         window.dispatchEvent(new Event('adminto.setFluid'));
        //     }
        // });

        // sidebar - main menu
        if ($("#side-menu").length) {
            var navCollapse = $('#side-menu li .collapse');

            // open one menu at a time only
            navCollapse.on({
                'show.bs.collapse': function (event) {
                    var parent = $(event.target).parents('.collapse.show');
                    $('#side-menu .collapse.show').not(parent).collapse('hide');
                }
            });

            // activate the menu in left side bar (Vertical Menu) based on url
            $("#side-menu a").each(function () {
                var pageUrl = window.location.href.split(/[?#]/)[0];
                if (this.href == pageUrl) {
                    $(this).addClass("active");
                    $(this).parent().addClass("menuitem-active");
                    $(this).parent().parent().parent().addClass("show");
                    $(this).parent().parent().parent().parent().addClass("menuitem-active"); // add active to li of the current link

                    var firstLevelParent = $(this).parent().parent().parent().parent().parent().parent();
                    if (firstLevelParent.attr('id') !== 'sidebar-menu') firstLevelParent.addClass("show");

                    $(this).parent().parent().parent().parent().parent().parent().parent().addClass("menuitem-active");

                    var secondLevelParent = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    if (secondLevelParent.attr('id') !== 'wrapper') secondLevelParent.addClass("show");

                    var upperLevelParent = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    if (!upperLevelParent.is('body')) upperLevelParent.addClass("menuitem-active");
                }
            });
        }


        // handling two columns menu if present
        var twoColSideNav = $("#two-col-sidenav-main");
        if (twoColSideNav.length) {
            var twoColSideNavItems = $("#two-col-sidenav-main .nav-link");
            var sideSubMenus = $(".twocolumn-menu-item");

            // showing/displaying tooltip based on screen size
            // if (this.window.width() >= 585) {
            //     twoColSideNavItems.tooltip('enable');
            // } else {
            //     twoColSideNavItems.tooltip('disable');
            // }

            var nav = $('.twocolumn-menu-item .nav-second-level');
            var navCollapse = $('#two-col-menu li .collapse');

            // open one menu at a time only
            navCollapse.on({
                'show.bs.collapse': function () {
                    var nearestNav = $(this).closest(nav).closest(nav).find(navCollapse);
                    if (nearestNav.length) nearestNav.not($(this)).collapse('hide'); else navCollapse.not($(this)).collapse('hide');
                }
            });

            twoColSideNavItems.on('click', function (e) {
                var target = $($(this).attr('href'));

                if (target.length) {
                    e.preventDefault();

                    twoColSideNavItems.removeClass('active');
                    $(this).addClass('active');

                    sideSubMenus.removeClass("d-block");
                    target.addClass("d-block");

                    // showing full sidebar if menu item is clicked
                    $.LayoutThemeApp.leftSidebar.changeSize('default');
                    return false;
                }
                return true;
            });

            // activate menu with no child
            var pageUrl = window.location.href; //.split(/[?#]/)[0];
            twoColSideNavItems.each(function () {
                if (this.href === pageUrl) {
                    $(this).addClass('active');
                }
            });


            // activate the menu in left side bar (Two column) based on url
            $("#two-col-menu a").each(function () {
                if (this.href == pageUrl) {
                    $(this).addClass("active");
                    $(this).parent().addClass("menuitem-active");
                    $(this).parent().parent().parent().addClass("show");
                    $(this).parent().parent().parent().parent().addClass("menuitem-active"); // add active to li of the current link

                    var firstLevelParent = $(this).parent().parent().parent().parent().parent().parent();
                    if (firstLevelParent.attr('id') !== 'sidebar-menu') firstLevelParent.addClass("show");

                    $(this).parent().parent().parent().parent().parent().parent().parent().addClass("menuitem-active");

                    var secondLevelParent = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    if (secondLevelParent.attr('id') !== 'wrapper') secondLevelParent.addClass("show");

                    var upperLevelParent = $(this).parent().parent().parent().parent().parent().parent().parent().parent().parent().parent();
                    if (!upperLevelParent.is('body')) upperLevelParent.addClass("menuitem-active");

                    // opening menu
                    var matchingItem = null;
                    var targetEl = '#' + $(this).parents('.twocolumn-menu-item').attr("id");
                    $("#two-col-sidenav-main .nav-link").each(function () {
                        if ($(this).attr('href') === targetEl) {
                            matchingItem = $(this);
                        }
                    });
                    if (matchingItem) matchingItem.trigger('click');
                }
            });
        }
    }

    init() {
        this.initMenu();
    }


}

class Topbar {

    constructor() {
        this.body = $('body');
        this.window = $(window);
    }

    toggleRightSideBar() {

        var self = this;
        if(document.body.classList.contains('right-bar-enabled'))
            document.body.classList.remove('right-bar-enabled')
        else
            document.body.classList.add('right-bar-enabled')
    }

    initMenu() {
        const  self = this;
        document.querySelector('.right-bar-toggle')?.addEventListener('click', function () {
            self.toggleRightSideBar();
        });

        // Serach Toggle
        $('#top-search').on('click', function (e) {
            $('#search-dropdown').addClass('d-block');
        });

        // hide search on opening other dropdown
        $('.topbar-dropdown').on('show.bs.dropdown', function () {
            $('#search-dropdown').removeClass('d-block');
        });

        //activate the menu in topbar(horizontal menu) based on url
        $(".navbar-nav a").each(function () {
            var pageUrl = window.location.href.split(/[?#]/)[0];
            if (this.href == pageUrl) {
                $(this).addClass("active");
                $(this).parent().addClass("active");
                $(this).parent().parent().addClass("active");

                $(this).parent().parent().parent().addClass("active");
                $(this).parent().parent().parent().parent().addClass("active");
                if ($(this).parent().parent().parent().parent().hasClass('mega-dropdown-menu')) {
                    $(this).parent().parent().parent().parent().parent().addClass("active");
                    $(this).parent().parent().parent().parent().parent().parent().addClass("active");

                } else {
                    var child = $(this).parent().parent()[0].querySelector('.dropdown-item');
                    if (child) {
                        var pageUrl = window.location.href.split(/[?#]/)[0];
                        if (child.href == pageUrl || child.classList.contains('dropdown-toggle')) child.classList.add("active");
                    }
                }
                var el = $(this).parent().parent().parent().parent().addClass("active").prev();
                if (el.hasClass("nav-link")) el.addClass('active');
            }
        });

        // Topbar - main menu
        $('.navbar-toggle').on('click', function (event) {
            $(this).toggleClass('open');
            $('#navigation').slideToggle(400);
        });


        //Horizontal Menu (For SM Screen)
        var AllNavs = document.querySelectorAll('ul.navbar-nav .dropdown .dropdown-toggle');

        var isInner = false;

        AllNavs.forEach(function (element) {
            element.addEventListener('click', function (event) {
                if (!element.parentElement.classList.contains('nav-item')) {
                    isInner = true;
                    element.parentElement.parentElement.classList.add('show');
                    var parent = element.parentElement.parentElement.parentElement.querySelector('.nav-link');
                    parent.ariaExpanded = true;
                    parent.classList.add("show");
                    bootstrap.Dropdown.getInstance(element).show();
                }
            });

            element.addEventListener('hide.bs.dropdown', function (event) {
                if (isInner) {
                    event.preventDefault();
                    event.stopPropagation();
                    isInner = false;
                }
            });
        });

    }

    init() {
        this.initMenu();
    }

}

class RightSidebar {

    constructor() {
        this.body = $('body');
        this.window = $(window);
    }

    init() {
        var self = this;

        $(document).on('click', 'body', function (e) {
            // hiding search bar
            if ($(e.target).closest('#top-search').length !== 1) {
                $('#search-dropdown').removeClass('d-block');
            }
            if ($(e.target).closest('.right-bar-toggle, .right-bar').length > 0) {
                return;
            }

            if ($(e.target).closest('.left-side-menu, .side-nav').length > 0 || $(e.target).hasClass('button-menu-mobile') || $(e.target).closest('.button-menu-mobile').length > 0) {
                return;
            }

            $('body').removeClass('right-bar-enabled');
            $('body').removeClass('sidebar-enable');

        });
    }

}

class ThemeCustomizer {


    constructor() {
        this.body = document.body;
        this.defaultConfig = {
            leftbar: {
                color: 'light', size: 'default', position: 'fixed',
            }, layout: {
                color: 'light', size: 'fluid', mode: 'default',
            }, topbar: {
                color: 'light'
            }, sidebar: {
                user: true
            }
        }

    }

    initConfig() {
        let config = JSON.parse(JSON.stringify(this.defaultConfig));
        config['leftbar']['color'] = this.body.getAttribute('data-leftbar-color') ?? this.defaultConfig.leftbar.color;
        config['leftbar']['size'] = this.body.getAttribute('data-leftbar-size') ?? this.defaultConfig.leftbar.size;
        config['leftbar']['position'] = this.body.getAttribute('data-leftbar-position') ?? this.defaultConfig.leftbar.position;
        config['layout']['color'] = this.body.getAttribute('data-layout-color') ?? this.defaultConfig.layout.color;
        config['layout']['size'] = this.body.getAttribute('data-layout-size') ?? this.defaultConfig.layout.size;
        config['layout']['mode'] = this.body.getAttribute('data-layout-mode') ?? this.defaultConfig.layout.mode;
        config['topbar']['color'] = this.body.getAttribute('data-topbar-color') ?? this.defaultConfig.topbar.color;
        config['sidebar']['user'] = this.body.getAttribute('data-sidebar-user') ?? this.defaultConfig.sidebar.user;
        this.defaultConfig = JSON.parse(JSON.stringify(config));
        this.config = config;
        this.setSwitchFromConfig();
    }

    changeLeftbarColor(color) {
        this.config.leftbar.color = color;
        this.body.setAttribute('data-leftbar-color', color);
        this.setSwitchFromConfig();
    }

    changeLeftbarPosition(position) {
        this.config.leftbar.position = position;
        this.body.setAttribute('data-leftbar-position', position);
        this.setSwitchFromConfig();
    }

    changeLeftbarSize(size) {
        this.config.leftbar.size = size;
        this.body.setAttribute('data-leftbar-size', size);
        this.setSwitchFromConfig();
    }

    changeLayoutMode(mode) {
        this.config.layout.mode = mode;
        this.body.setAttribute('data-layout-mode', mode);
        this.setSwitchFromConfig();
    }

    changeLayoutColor(color) {
        this.config.layout.color = color;
        this.body.setAttribute('data-layout-color', color);
        this.setSwitchFromConfig();
    }

    changeLayoutSize(size) {
        this.config.layout.size = size;
        this.body.setAttribute('data-layout-size', size);
        this.setSwitchFromConfig();
    }

    changeTopbarColor(color) {
        this.config.topbar.color = color;
        this.body.setAttribute('data-topbar-color', color);
        this.setSwitchFromConfig();
    }

    changeSidebarUser(showUser) {
        this.config.sidebar.user = showUser;
        if (showUser) {
            this.body.setAttribute('data-sidebar-user', showUser);
        } else {
            this.body.removeAttribute('data-sidebar-user');
        }
        this.setSwitchFromConfig();
    }

    resetTheme() {
        this.config = JSON.parse(JSON.stringify(this.defaultConfig));
        this.changeLeftbarColor(this.config.leftbar.color);
        this.changeLeftbarPosition(this.config.leftbar.position);
        this.changeLeftbarSize(this.config.leftbar.size);
        this.changeLayoutColor(this.config.layout.color);
        this.changeLayoutSize(this.config.layout.size);
        this.changeLayoutMode(this.config.layout.mode);
        this.changeTopbarColor(this.config.topbar.color);
        this.changeSidebarUser(this.config.sidebar.user);
    }

    initSwitchListener() {
        const self = this;
        document.querySelectorAll('input[name=leftbar-color]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeLeftbarColor(element.value);
            })
        });
        document.querySelectorAll('input[name=leftbar-size]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeLeftbarSize(element.value);
            })
        });
        document.querySelectorAll('input[name=leftbar-position]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeLeftbarPosition(element.value);

            })
        });
        document.querySelectorAll('input[name=layout-color]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeLayoutColor(element.value);
            })
        });
        document.querySelectorAll('input[name=layout-size]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeLayoutSize(element.value);
            })
        });

        document.querySelectorAll('input[name=layout-mode]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeLayoutMode(element.value);
            })
        });
        document.querySelectorAll('input[name=topbar-color]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeTopbarColor(element.value);
            })
        });
        document.querySelectorAll('input[name=sidebar-user]').forEach(function (element) {
            element.addEventListener('change', function (e) {
                self.changeSidebarUser(element.checked);
            })
        });
        document.querySelector('#resetBtn')?.addEventListener('click', function (e) {
            self.resetTheme();
        });

        document.querySelector('.button-menu-mobile')?.addEventListener('click', function () {
            if (self.config.leftbar.size === 'default') {
                self.changeLeftbarSize('condensed');
            } else {
                self.changeLeftbarSize('default');
            }
        })
    }


    setSwitchFromConfig() {
        document.querySelectorAll('.right-bar input[type=checkbox]').forEach(function (checkbox) {
            checkbox.checked = false;
        })
        let config = this.config;
        if (config) {
            let leftbarColorSwitch = document.querySelector('input[type=checkbox][name=leftbar-color][value=' + config.leftbar.color + ']');
            let leftbarSizeSwitch = document.querySelector('input[type=checkbox][name=leftbar-size][value=' + config.leftbar.size + ']');
            let leftbarPositionSwitch = document.querySelector('input[type=checkbox][name=leftbar-position][value=' + config.leftbar.position + ']');

            let layoutColorSwitch = document.querySelector('input[type=checkbox][name=layout-color][value=' + config.layout.color + ']');
            let layoutSizeSwitch = document.querySelector('input[type=checkbox][name=layout-size][value=' + config.layout.size + ']');
            let layoutModeSwitch = document.querySelector('input[type=checkbox][name=layout-mode][value=' + config.layout.type + ']');

            let topbarColorSwitch = document.querySelector('input[type=checkbox][name=topbar-color][value=' + config.topbar.color + ']');
            let sidebarUserSwitch = document.querySelector('input[type=checkbox][name=sidebar-user]');


            if (leftbarColorSwitch) leftbarColorSwitch.checked = true;
            if (leftbarSizeSwitch) leftbarSizeSwitch.checked = true;
            if (leftbarPositionSwitch) leftbarPositionSwitch.checked = true;

            if (layoutColorSwitch) layoutColorSwitch.checked = true;
            if (layoutSizeSwitch) layoutSizeSwitch.checked = true;
            if (layoutModeSwitch) layoutModeSwitch.checked = true;

            if (topbarColorSwitch) topbarColorSwitch.checked = true;
            if (sidebarUserSwitch && config.sidebar.user.toString() === "true") sidebarUserSwitch.checked = true;
        }
    }


    
    init() {
        this.initConfig();
        this.initSwitchListener();
        // this.setSwitchFromConfig();

    }
}


class Layout {

    init() {
        this.themeCustomizer = new ThemeCustomizer();
        this.themeCustomizer.init();
        this.leftSidebar = new LeftSidebar();
        this.topbar = new Topbar();
        this.rightSidebar = new RightSidebar(this);
        this.rightSidebar.init();
        this.topbar.init();
        this.leftSidebar.init();


    }
}

window.addEventListener('DOMContentLoaded', function (e) {
    new Layout().init();
})


/*
Template Name: Adminto - Responsive Bootstrap 5 Admin Dashboard
Author: CoderThemes
Website: https://coderthemes.com/
Contact: support@coderthemes.com
File: Main Js File
*/


!function ($) {
    "use strict";

    var Components = function () {
    };

    //initializing tooltip
    Components.prototype.initTooltipPlugin = function () {
        $.fn.tooltip && $('[data-bs-toggle="tooltip"]').tooltip()
    },

        //initializing popover
        Components.prototype.initPopoverPlugin = function () {
            $.fn.popover && $('[data-bs-toggle="popover"]').popover()
        },

        //initializing toast
        Components.prototype.initToastPlugin = function () {
            $.fn.toast && $('[data-bs-toggle="toast"]').toast()
        },

        //initializing form validation
        Components.prototype.initFormValidation = function () {
            $(".needs-validation").on('submit', function (event) {
                $(this).addClass('was-validated');
                if ($(this)[0].checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                    return false;
                }
                return true;
            });
        },

        // Counterup
        Components.prototype.initCounterUp = function () {
            var delay = $(this).attr('data-delay') ? $(this).attr('data-delay') : 100; //default is 100
            var time = $(this).attr('data-time') ? $(this).attr('data-time') : 1200; //default is 1200
            $('[data-plugin="counterup"]').each(function (idx, obj) {
                $(this).counterUp({
                    delay: delay,
                    time: time
                });
            });
        },

        //peity charts
        Components.prototype.initPeityCharts = function () {
            $('[data-plugin="peity-pie"]').each(function (idx, obj) {
                var colors = $(this).attr('data-colors') ? $(this).attr('data-colors').split(",") : [];
                var width = $(this).attr('data-width') ? $(this).attr('data-width') : 20; //default is 20
                var height = $(this).attr('data-height') ? $(this).attr('data-height') : 20; //default is 20
                $(this).peity("pie", {
                    fill: colors,
                    width: width,
                    height: height
                });
            });
            //donut
            $('[data-plugin="peity-donut"]').each(function (idx, obj) {
                var colors = $(this).attr('data-colors') ? $(this).attr('data-colors').split(",") : [];
                var width = $(this).attr('data-width') ? $(this).attr('data-width') : 20; //default is 20
                var height = $(this).attr('data-height') ? $(this).attr('data-height') : 20; //default is 20
                $(this).peity("donut", {
                    fill: colors,
                    width: width,
                    height: height
                });
            });

            $('[data-plugin="peity-donut-alt"]').each(function (idx, obj) {
                $(this).peity("donut");
            });

            // line
            $('[data-plugin="peity-line"]').each(function (idx, obj) {
                $(this).peity("line", $(this).data());
            });

            // bar
            $('[data-plugin="peity-bar"]').each(function (idx, obj) {
                var colors = $(this).attr('data-colors') ? $(this).attr('data-colors').split(",") : [];
                var width = $(this).attr('data-width') ? $(this).attr('data-width') : 20; //default is 20
                var height = $(this).attr('data-height') ? $(this).attr('data-height') : 20; //default is 20
                $(this).peity("bar", {
                    fill: colors,
                    width: width,
                    height: height
                });
            });
        },

        Components.prototype.initKnob = function () {
            $('[data-plugin="knob"]').each(function (idx, obj) {
                $(this).knob();
            });
        },

        Components.prototype.initTippyTooltips = function () {
            if ($('[data-plugin="tippy"]').length > 0) {
                tippy('[data-plugin="tippy"]');
            }
        },

        Components.prototype.initShowPassword = function () {
            $("[data-password]").on('click', function () {
                if ($(this).attr('data-password') == "false") {
                    $(this).siblings("input").attr("type", "text");
                    $(this).attr('data-password', 'true');
                    $(this).addClass("show-password");
                } else {
                    $(this).siblings("input").attr("type", "password");
                    $(this).attr('data-password', 'false');
                    $(this).removeClass("show-password");
                }
            });
        },

        Components.prototype.initMultiDropdown = function () {
            $('.dropdown-menu a.dropdown-toggle').on('click', function (e) {
                if (!$(this).next().hasClass('show')) {
                    $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                }
                var $subMenu = $(this).next(".dropdown-menu");
                $subMenu.toggleClass('show');

                return false;
            });
        },

        //initilizing
        Components.prototype.init = function () {
            this.initTooltipPlugin(),
                this.initPopoverPlugin(),
                this.initToastPlugin(),
                this.initFormValidation(),
                this.initCounterUp(),
                this.initPeityCharts(),
                this.initKnob();
            this.initTippyTooltips();
            this.initShowPassword();
            this.initMultiDropdown();
        },

        $.Components = new Components, $.Components.Constructor = Components

}(window.jQuery),

    function ($) {
        "use strict";

        /**
         Portlet Widget
         */
        var Portlet = function () {
            this.$body = $("body"),
                this.$portletIdentifier = ".card",
                this.$portletCloser = '.card a[data-toggle="remove"]',
                this.$portletRefresher = '.card a[data-toggle="reload"]'
        };

        //on init
        Portlet.prototype.init = function () {
            // Panel closest
            var $this = this;
            $(document).on("click", this.$portletCloser, function (ev) {
                ev.preventDefault();
                var $portlet = $(this).closest($this.$portletIdentifier);
                var $portlet_parent = $portlet.parent();
                $portlet.remove();
                if ($portlet_parent.children().length == 0) {
                    $portlet_parent.remove();
                }
            });

            // Panel Reload
            $(document).on("click", this.$portletRefresher, function (ev) {
                ev.preventDefault();
                var $portlet = $(this).closest($this.$portletIdentifier);
                // This is just a simulation, nothing is going to be reloaded
                $portlet.append('<div class="card-disabled"><div class="card-portlets-loader"></div></div>');
                var $pd = $portlet.find('.card-disabled');
                setTimeout(function () {
                    $pd.fadeOut('fast', function () {
                        $pd.remove();
                    });
                }, 500 + 300 * (Math.random() * 5));
            });
        },
            //
            $.Portlet = new Portlet, $.Portlet.Constructor = Portlet

    }(window.jQuery),

    function ($) {
        'use strict';

        var App = function () {
            this.$body = $('body'),
                this.$window = $(window)
        };

        /**
         * Initlizes the controls
         */
        App.prototype.initControls = function () {
            // remove loading
            setTimeout(function () {
                document.body.classList.remove('loading');
            }, 350);

            // Preloader
            $(window).on('load', function () {
                $('#status').fadeOut();
                $('#preloader').delay(350).fadeOut('slow');
            });

            $('[data-toggle="fullscreen"]').on("click", function (e) {
                e.preventDefault();
                $('body').toggleClass('fullscreen-enable');
                if (!document.fullscreenElement && /* alternative standard method */ !document.mozFullScreenElement && !document.webkitFullscreenElement) {  // current working methods
                    if (document.documentElement.requestFullscreen) {
                        document.documentElement.requestFullscreen();
                    } else if (document.documentElement.mozRequestFullScreen) {
                        document.documentElement.mozRequestFullScreen();
                    } else if (document.documentElement.webkitRequestFullscreen) {
                        document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                    }
                } else {
                    if (document.cancelFullScreen) {
                        document.cancelFullScreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.webkitCancelFullScreen) {
                        document.webkitCancelFullScreen();
                    }
                }
            });
            document.addEventListener('fullscreenchange', exitHandler);
            document.addEventListener("webkitfullscreenchange", exitHandler);
            document.addEventListener("mozfullscreenchange", exitHandler);

            function exitHandler() {
                if (!document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
                    $('body').removeClass('fullscreen-enable');
                }
            }
        },

            //initilizing
            App.prototype.init = function () {
                $.Portlet.init();
                $.Components.init();

                this.initControls();


                // showing the sidebar on load if user is visiting the page first time only
                // var bodyConfig = this.$body.data('layout');
                // if (window.sessionStorage && bodyConfig && bodyConfig.hasOwnProperty('showRightSidebarOnPageLoad') && bodyConfig['showRightSidebarOnPageLoad']) {
                //     var alreadyVisited = sessionStorage.getItem("_ADMINTO_VISITED_");
                //     if (!alreadyVisited) {
                //         $.RightBar.toggleRightSideBar();
                //         sessionStorage.setItem("_ADMINTO_VISITED_", true);
                //     }
                // }

                //Popovers
                var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
                var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                    return new bootstrap.Popover(popoverTriggerEl)
                })

                //Tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                })

                //Toasts
                var toastElList = [].slice.call(document.querySelectorAll('.toast'))
                var toastList = toastElList.map(function (toastEl) {
                    return new bootstrap.Toast(toastEl)
                })

                // Toasts Placement
                var toastPlacement = document.getElementById("toastPlacement");
                if (toastPlacement) {
                    document.getElementById("selectToastPlacement").addEventListener("change", function () {
                        if (!toastPlacement.dataset.originalClass) {
                            toastPlacement.dataset.originalClass = toastPlacement.className;
                        }
                        toastPlacement.className = toastPlacement.dataset.originalClass + " " + this.value;
                    });
                }

                // liveAlert
                var alertPlaceholder = document.getElementById('liveAlertPlaceholder')
                var alertTrigger = document.getElementById('liveAlertBtn')

                function alert(message, type) {
                    var wrapper = document.createElement('div')
                    wrapper.innerHTML = '<div class="alert alert-' + type + ' alert-dismissible" role="alert">' + message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'

                    alertPlaceholder.append(wrapper)
                }

                if (alertTrigger) {
                    alertTrigger.addEventListener('click', function () {
                        alert('Nice, you triggered this alert message!', 'primary')
                    })
                }


                if(document.getElementById('app-style').href.includes('rtl.min.css')){
                    document.getElementsByTagName('html')[0].dir="rtl";
                }
            },

            $.App = new App, $.App.Constructor = App


    }(window.jQuery),
//initializing main application module
    function ($) {
        "use strict";
        $.App.init();
    }(window.jQuery);

// Waves Effect
Waves.init();

// Feather Icons
feather.replace()