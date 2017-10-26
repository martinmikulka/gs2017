/// <reference path="./app.ts" />

namespace mmi {

	export class Form {

		private id: string;
		private elements: IElements;

		constructor(id: string) {
			this.id = id;
			this.init();
		}

		/**
		 *
		 */
		init(): void {
			let form: HTMLFormElement = <HTMLFormElement>document.getElementById(this.id);
			$(form).on("submit", this.submit.bind(this));

			let filepicker: HTMLLabelElement = <HTMLLabelElement>form.querySelector(".btn-filepicker");
			let filepickerInput: HTMLInputElement = null;
			if (filepicker) {
				filepickerInput = <HTMLInputElement>filepicker.querySelector("input");
				$(filepickerInput).on("change", this.fileUploadCallback.bind(this));
			}

			let selectedFilesContainer: HTMLDivElement = <HTMLDivElement>form.querySelector('.selected-files');

			this.elements = {
				form: form,
				filepickerInput: filepickerInput,
				selectedFilesContainer: selectedFilesContainer
			};
		}

		/**
		 *
		 */
		fileUploadCallback(e): void {
			if (this.elements.selectedFilesContainer) {
				this.clearFileInfo();
				if (e.target.files && e.target.files.length) {
					let file = e.target.files[0];
					let size = App.fileSize(file.size);

					let infoEl: HTMLSpanElement = <HTMLSpanElement>document.createElement("span");

					let nameEl: HTMLSpanElement = <HTMLSpanElement>document.createElement("span");
					nameEl.classList.add("name");
					nameEl.innerText = file.name;
					infoEl.appendChild(nameEl);

					let sizeEl: HTMLSpanElement = <HTMLSpanElement>document.createElement("span");
					sizeEl.classList.add("size");
					sizeEl.innerText = " [" + size + "]";
					infoEl.appendChild(sizeEl);

					this.elements.selectedFilesContainer.appendChild(infoEl);
				}
			}
		}

		clearFileInfo(): void {
			if (this.elements.selectedFilesContainer) {
				this.elements.selectedFilesContainer.innerHTML = "";
			}
		}

		// TODO Fix
		clearMessages = function () {
			$(this.elements.form).find('.form-msg span').removeClass('active');
		}

		// TODO Fix
		processErrors(errors): void {
			this.clearMessages();
			for (let id of errors) {
				$(this.elements.form).find('.form-msg #' + id).addClass('active');
			}
		}

		// TODO Fix
		submit(e): boolean {
			if (FormData) {
				var data = new FormData(this.elements.form);
				$.ajax({
					url: this.elements.form.getAttribute('action') || '/',
					type: this.elements.form.getAttribute('method') || 'post',
					dataType: 'json',
					contentType: false,
					processData: false,
					data: data,
					beforeSend: this.beforeSendCallback.bind(this),
					success: this.successCallback.bind(this),
					complete: this.completeCallback.bind(this),
					error: this.errorCallback.bind(this)
				});
				return false;
			}
		}

		// TODO Fix
		beforeSendCallback(): void {
			$(this.elements.form).find('button[type="submit"]').attr('disabled', 'disabled');
		}

		// TODO Fix
		successCallback(response): void {
			if (response.status == 'success') {
				this.reset();
				$(this.elements.form).find('.form-msg .success').addClass('active');
			} else {
				this.processErrors(response.messages);
			}
		}

		// TODO Fix
		completeCallback(response): void {
			$(this.elements.form).find('button[type="submit"]').removeAttr('disabled');
			$.scrollTo(this.elements.form, {
				duration: 500,
				offset: -80
			});
		}

		// TODO Fix
		errorCallback(response): void {
			$(this.elements.form).find('.form-msg:first .error').addClass('active');
			console.error("Form.js: Error occured while sending request.");
			console.error(arguments);
		}

		// TODO Fix
		reset(): void {
			this.elements.form.reset();
			this.clearMessages();
			this.clearFileInfo();
		}
	}

	interface IElements {
		form: HTMLFormElement,
		filepickerInput: HTMLInputElement,
		selectedFilesContainer: HTMLDivElement
	}
}
