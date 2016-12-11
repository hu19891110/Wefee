/** Wefee */
require.config({
    baseUrl: "/static/js/",
    paths: {
        "jquery": "jquery.min",
        "bootstrap": "../bootstrap/js/bootstrap.min",
        "particleground": "particleground/jquery.particleground.min",
        "smvalidator": "SMValidator.min"
    },
    shim: {
        "particleground": {
            deps: ["jquery"],
            exports: "particleground"
        },
        "bootstrap": {
            deps: ["jquery"],
            exports: "bootstrap"
        }
    }
});