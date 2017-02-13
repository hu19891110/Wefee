/** Wefee */
require.config({
    baseUrl: "/static/js/",
    paths: {
        "jquery": "jquery.min",
        "bootstrap": "../bootstrap/js/bootstrap.min",
        "particleground": "particleground/jquery.particleground.min",
        "smvalidator": "SMValidator.min",
        "icheck": "icheck/icheck.min",
        "layer": "layer/layer",
        "bootstrapswitch": "bootstrap-switch/bootstrap-switch.min",
        "webuploader": "webuploader/webuploader.min",
        "flatpickr": "flatpickr/flatpickr.min",
        'flatpickrzh': "flatpickr/l10n/zh",
        'jscolor': "jscolor/jscolor.min",
        'ueditor': "ueditor/ueditor.all.min",
        'ueditor.config': "ueditor/ueditor.config",
    },
    map: {
        '*': {
            'css': '../css.min'
        }
    },
    shim: {
        "particleground": {
            deps: ["jquery"],
            exports: "particleground"
        },
        "bootstrap": {
            deps: ["jquery"],
            exports: "bootstrap"
        },
        "icheck": {
            deps: ["jquery"],
            exports: "icheck"
        },
        "layer": {
            deps: ["jquery"]
        },
        "bootstrapswitch": {
            deps: ["bootstrap"]
        },
        "flatpickr": {
            deps: ["css!/static/js/flatpickr/flatpickr.min.css"]
        },
        "ueditor": {
            deps: ["jquery", "ueditor.config", "css!/static/js/ueditor/themes/default/css/ueditor.min.css"]
        }
    }
});