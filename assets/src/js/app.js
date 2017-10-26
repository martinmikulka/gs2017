/// <reference path="../../lib/jquery/index.d.ts" />
/// <reference path="../../lib/jquery-scrollto/index.d.ts" />
/// <reference path="../../lib/jquery-localscroll/index.d.ts" />
/// <reference path="../../lib/waypoints/index.d.ts" />
/// <reference path="./form.ts" />
/// <reference path="./carousel.ts" />
var mmi;
(function (mmi) {
    var App = (function () {
        function App() {
            this.init();
            this.winnerForm = new mmi.Form("FormWinner");
            new mmi.Carousel("Carousel");
            setTimeout(this.initScrolling, 200);
        }
        /**
         * Initialization.
         */
        App.prototype.init = function () {
            if (typeof $ === "undefined") {
                return console.error("mmi.App(): jQuery library is missing.");
            }
            // Waypoints functionality for displaying/hiding floating menus
            if (Waypoint) {
                var waypoint = new Waypoint({
                    element: document.getElementById("MenuWaypoint"),
                    handler: function (direction) {
                        var el = document.getElementById("Menu");
                        if (direction === "down") {
                            el.classList.add("floating");
                            el.classList.remove("static");
                        }
                        else if (direction === "up") {
                            el.classList.add("static");
                            el.classList.remove("floating");
                        }
                    },
                    offset: 0
                });
            }
        };
        /**
         * Initialization of scrolling functionality.
         */
        App.prototype.initScrolling = function () {
            // If there is a fragment hash in URL, scroll to that point
            // Test for '#' is necessary for IE !
            if (window.location.hash && window.location.hash != '#' && $.scrollTo) {
                $.scrollTo(window.location.hash, {
                    duration: 500,
                    offset: -66
                });
            }
            // Animated scrolling
            if ($.localScroll) {
                $.localScroll({
                    lazy: true,
                    stop: true,
                    hash: true,
                    duration: 500,
                    offset: -66
                });
            }
        };
        App.fileSize = function (size) {
            var units = ["B", "kB", "MB", "GB"];
            for (var i = 0; i < units.length; i++) {
                if (size < 1024) {
                    return Math.round(size) + " " + units[i];
                }
                size /= 1024;
            }
            return size + " TB";
        };
        return App;
    })();
    mmi.App = App;
})(mmi || (mmi = {}));
