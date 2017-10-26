var mmi;
(function (mmi) {
    var Carousel = (function () {
        function Carousel(elementId) {
            this.items = [];
            this.currentIndex = 0;
            this.elementId = elementId;
            this.initElements();
            this.initItems();
            this.attachEventHandlers();
            this.render();
        }
        /**
         * Initialization of html elements.
         */
        Carousel.prototype.initElements = function () {
            var container = document.getElementById(this.elementId);
            var controlsContainer = container.querySelector('[data-id="ControlsContainer"]');
            var nextBtn = controlsContainer.querySelector('[data-id="NextBtn"]');
            var prevBtn = controlsContainer.querySelector('[data-id="PrevBtn"]');
            this.elements = {
                container: container,
                controls: {
                    nextBtn: nextBtn,
                    prevBtn: prevBtn
                }
            };
        };
        /**
         * Initialization of items.
         */
        Carousel.prototype.initItems = function () {
            var itemsContainer = this.elements.container.querySelector('[data-id="ItemsContainer"]');
            var itemsList = itemsContainer.children;
            for (var i = 0; i < itemsList.length; i++) {
                this.items.push(itemsList[i]);
            }
            ;
        };
        /**
         * Attachment of event handlers.
         */
        Carousel.prototype.attachEventHandlers = function () {
            this.elements.controls.nextBtn.onclick = this.next.bind(this);
            this.elements.controls.prevBtn.onclick = this.previous.bind(this);
        };
        /**
         * Move to next item.
         */
        Carousel.prototype.next = function () {
            this.currentIndex = this.fixIndex(++this.currentIndex);
            this.render();
        };
        /**
         * Move to previous item.
         */
        Carousel.prototype.previous = function () {
            this.currentIndex = this.fixIndex(--this.currentIndex);
            this.render();
        };
        /**
         * Render items.
         */
        Carousel.prototype.render = function () {
            for (var _i = 0, _a = this.items; _i < _a.length; _i++) {
                var item = _a[_i];
                item.classList.remove('prev-2', 'prev-1', 'current', 'next-1', 'next-2', 'next-3');
            }
            this.items[this.fixIndex(this.currentIndex - 2)].classList.add('prev-2');
            this.items[this.fixIndex(this.currentIndex - 1)].classList.add('prev-1');
            this.items[this.currentIndex].classList.add('current');
            this.items[this.fixIndex(this.currentIndex + 1)].classList.add('next-1');
            this.items[this.fixIndex(this.currentIndex + 2)].classList.add('next-2');
            this.items[this.fixIndex(this.currentIndex + 3)].classList.add('next-3');
        };
        /**
         *
         * @param index
         */
        Carousel.prototype.fixIndex = function (index) {
            if (index >= this.items.length) {
                index = index - this.items.length;
            }
            else if (index < 0) {
                index = index + this.items.length;
            }
            return index;
        };
        return Carousel;
    })();
    mmi.Carousel = Carousel;
})(mmi || (mmi = {}));
