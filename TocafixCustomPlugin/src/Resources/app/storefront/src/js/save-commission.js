import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import FormSerializeUtil from 'src/utility/form/form-serialize.util';

export default class TocafixCommissionPlugin extends Plugin {

    static options = {
        replaceSelectors: false,
        submitOnChange: false,
        saveButtonSelector: '.save-commisions',
    };

    init() {
        // In 6.5, HttpClient doesn't require parameters in constructor
        this._client = new HttpClient();
        this._form = this.el;
        this._saveButton = DomAccess.querySelector(this.el, this.options.saveButtonSelector);
        this._messageDisplay = document.querySelector('.commission-response');

        this._registerEvents();
    }

    _registerEvents() {
        this._saveButton.addEventListener('click', this._handleSubmit.bind(this));
    }

    _onKeypress(event) {
        if (event.key === 'Enter') {
            this._handleSubmit(event);
        }
    }

    _handleSubmit(event) {
        event.preventDefault();
        if (this._form.checkValidity() === false) {
            return;
        }

        // Changed from $emitter to this.$emitter
        this.$emitter.publish('beforeSubmit');
        this._fireRequest();
    }

    _fireRequest() {
        this._createLoadingIndicators();
        const action = DomAccess.getAttribute(this._form, 'data-action');
        this.$emitter.publish('beforeFireRequest');
        
        // Changed method - use fetch-based approach
        this._client.post(
            action, 
            this._getFormData(),
            (response) => this._onAfterAjaxSubmit(response)
        );
    }

    _getFormData() {
        return FormSerializeUtil.serialize(this._form);
    }

    _onAfterAjaxSubmit(response) {
        // Response might already be parsed in 6.5
        const data = typeof response === 'string' ? JSON.parse(response) : response;
        
        if (this._messageDisplay) {
            this._messageDisplay.innerHTML = data.alert;
            this._messageDisplay.classList.remove('d-none');
        }
        
        this._removeLoadingIndicators();
        this.$emitter.publish('onAfterAjaxSubmit', { response: data });
    }

    _createLoadingIndicators() {
        this.$emitter.publish('createLoadingIndicators');
    }

    _removeLoadingIndicators() {
        this.$emitter.publish('removeLoadingIndicators');
    }
}