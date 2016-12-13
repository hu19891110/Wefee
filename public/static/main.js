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
        "bootstrapswitch": "bootstrap-switch/bootstrap-switch.min"
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
        }
    }
});