namespace mmi {

    export class Carousel {

        constructor(elementId: string) {

            this.elementId = elementId;

            this.initElements();
            this.initItems();
            this.attachEventHandlers();
            this.render();
        }

        /**
         * Initialization of html elements.
         */
        private initElements(): void {

            let container = <HTMLDivElement>document.getElementById(this.elementId);

            let controlsContainer = <HTMLDivElement>container.querySelector('[data-id="ControlsContainer"]');
            let nextBtn = <HTMLButtonElement>controlsContainer.querySelector('[data-id="NextBtn"]');
            let prevBtn = <HTMLButtonElement>controlsContainer.querySelector('[data-id="PrevBtn"]');

            this.elements = {
                container: container,
                controls: {
                    nextBtn: nextBtn,
                    prevBtn: prevBtn
                }
            };
        }

        /**
         * Initialization of items.
         */
        private initItems(): void {

            let itemsContainer = <HTMLDivElement>this.elements.container.querySelector('[data-id="ItemsContainer"]');
            let itemsList = itemsContainer.children;
            for (let i = 0; i < itemsList.length; i++) {
                this.items.push(<HTMLDivElement>itemsList[i]);
            };

        }

        /**
         * Attachment of event handlers.
         */
        private attachEventHandlers(): void {
            this.elements.controls.nextBtn.onclick = this.next.bind(this);
            this.elements.controls.prevBtn.onclick = this.previous.bind(this);
        }

        /**
         * Move to next item.
         */
        private next(): void {

            this.currentIndex = this.fixIndex(++this.currentIndex);
            this.render();
        }

        /**
         * Move to previous item.
         */
        private previous(): void {

            this.currentIndex = this.fixIndex(--this.currentIndex);
            this.render();
        }

        /**
         * Render items.
         */
        render(): void {

            for (let item of this.items) {
                item.classList.remove('prev-2', 'prev-1', 'current', 'next-1', 'next-2', 'next-3');
            }

            this.items[this.fixIndex(this.currentIndex - 2)].classList.add('prev-2');
            this.items[this.fixIndex(this.currentIndex - 1)].classList.add('prev-1');
            this.items[this.currentIndex].classList.add('current');
            this.items[this.fixIndex(this.currentIndex + 1)].classList.add('next-1');
            this.items[this.fixIndex(this.currentIndex + 2)].classList.add('next-2');
            this.items[this.fixIndex(this.currentIndex + 3)].classList.add('next-3');
        }

        /**
         * 
         * @param index
         */
        fixIndex(index: number): number {

            if (index >= this.items.length) {
                index = index - this.items.length;
            } else if (index < 0) {
                index = index + this.items.length;
            }

            return index;
        }

        private elementId: string;
        private elements: IElements;
        private items: HTMLDivElement[] = [];
        private currentIndex: number = 0;
    }

    interface IElements {
        container: HTMLDivElement,
        controls: {
            nextBtn: HTMLButtonElement,
            prevBtn: HTMLButtonElement
        }
    }

}