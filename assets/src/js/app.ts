/// <reference path="../../lib/jquery/index.d.ts" />
/// <reference path="../../lib/jquery-scrollto/index.d.ts" />
/// <reference path="../../lib/jquery-localscroll/index.d.ts" />
/// <reference path="../../lib/waypoints/index.d.ts" />
/// <reference path="./form.ts" />
/// <reference path="./carousel.ts" />

namespace mmi {

	export class App {

		private winnerForm: Form;

		constructor() {
			this.init();
			this.winnerForm = new Form("FormWinner");
			new Carousel("Carousel");
			setTimeout(this.initScrolling, 200);
		}

		/**
		 * Initialization.
		 */
		init(): void {
			if (typeof $ === "undefined") {
				return console.error("mmi.App(): jQuery library is missing.");
			}

			// Waypoints functionality for displaying/hiding floating menus
			if (Waypoint) {
				let waypoint = new Waypoint({
					element: document.getElementById("MenuWaypoint"),
					handler: function(direction) {
						let el = document.getElementById("Menu");
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
		}

		/**
		 * Initialization of scrolling functionality.
		 */
		initScrolling(): void {
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
		}

		static fileSize (size: number): string {
			let units: string[] = ["B", "kB", "MB", "GB"];
			for (let i = 0; i < units.length; i++) {
				if (size < 1024) {
					return Math.round(size) + " " + units[i];
				}
				size /= 1024;
			}
			return size + " TB";
		}

	}
}
