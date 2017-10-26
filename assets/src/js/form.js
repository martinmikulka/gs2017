/// <reference path="./app.ts" />
var mmi;
(function (mmi) {
    var Form = (function () {
        function Form(id) {
            // TODO Fix
            this.clearMessages = function () {
                $(this.elements.form).find('.form-msg span').removeClass('active');
            };
            this.id = id;
            this.init();
        }
        /**
         *
         */
        Form.prototype.init = function () {
            var form = document.getElementById(this.id);
            $(form).on("submit", this.submit.bind(this));
            var filepicker = form.querySelector(".btn-filepicker");
            var filepickerInput = null;
            if (filepicker) {
                filepickerInput = filepicker.querySelector("input");
                $(filepickerInput).on("change", this.fileUploadCallback.bind(this));
            }
            var selectedFilesContainer = form.querySelector('.selected-files');
            this.elements = {
                form: form,
                filepickerInput: filepickerInput,
                selectedFilesContainer: selectedFilesContainer
            };
        };
        /**
         *
         */
        Form.prototype.fileUploadCallback = function (e) {
            if (this.elements.selectedFilesContainer) {
                this.clearFileInfo();
                if (e.target.files && e.target.files.length) {
                    var file = e.target.files[0];
                    var size = mmi.App.fileSize(file.size);
                    var infoEl = document.createElement("span");
                    var nameEl = document.createElement("span");
                    nameEl.classList.add("name");
                    nameEl.innerText = file.name;
                    infoEl.appendChild(nameEl);
                    var sizeEl = document.createElement("span");
                    sizeEl.classList.add("size");
                    sizeEl.innerText = " [" + size + "]";
                    infoEl.appendChild(sizeEl);
                    this.elements.selectedFilesContainer.appendChild(infoEl);
                }
            }
        };
        Form.prototype.clearFileInfo = function () {
            if (this.elements.selectedFilesContainer) {
                this.elements.selectedFilesContainer.innerHTML = "";
            }
        };
        // TODO Fix
        Form.prototype.processErrors = function (errors) {
            this.clearMessages();
            for (var _i = 0; _i < errors.length; _i++) {
                var id = errors[_i];
                $(this.elements.form).find('.form-msg #' + id).addClass('active');
            }
        };
        // TODO Fix
        Form.prototype.submit = function (e) {
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
        };
        // TODO Fix
        Form.prototype.beforeSendCallback = function () {
            $(this.elements.form).find('button[type="submit"]').attr('disabled', 'disabled');
        };
        // TODO Fix
        Form.prototype.successCallback = function (response) {
            if (response.status == 'success') {
                this.reset();
                $(this.elements.form).find('.form-msg .success').addClass('active');
            }
            else {
                this.processErrors(response.messages);
            }
        };
        // TODO Fix
        Form.prototype.completeCallback = function (response) {
            $(this.elements.form).find('button[type="submit"]').removeAttr('disabled');
            $.scrollTo(this.elements.form, {
                duration: 500,
                offset: -80
            });
        };
        // TODO Fix
        Form.prototype.errorCallback = function (response) {
            $(this.elements.form).find('.form-msg:first .error').addClass('active');
            console.error("Form.js: Error occured while sending request.");
            console.error(arguments);
        };
        // TODO Fix
        Form.prototype.reset = function () {
            this.elements.form.reset();
            this.clearMessages();
            this.clearFileInfo();
        };
        return Form;
    })();
    mmi.Form = Form;
})(mmi || (mmi = {}));
