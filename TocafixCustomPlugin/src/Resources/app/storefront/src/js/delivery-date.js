import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';
import FormSerializeUtil from 'src/utility/form/form-serialize.util';
import flatpickr from 'flatpickr';
import { German } from 'flatpickr/dist/l10n/de.js';

export default class DeliveryDatePlugin extends Plugin {
    static options = {
        deliverydateCls: '#addDeliveryDateInput'
    };

    init() {
        
        this._picker = DomAccess.querySelector(this.el, this.options.deliverydateCls, false);
        this._client = new HttpClient();
        this._form = this.el;
        const me = this;
        let minDate = new Date();
        minDate.setDate(minDate.getDate() + 1);
        
        if (minDate.getDay() === 6) {
            minDate.setDate(minDate.getDate() + 2);
        }

        if (minDate.getDay() === 0) {
            minDate.setDate(minDate.getDate() + 1);
        }

        if (this._picker) {
            flatpickr(this._picker, {
                mode: "single",
                defaultDate: me._picker.value || minDate,
                inline: false,
                enableTime: false,
                dateFormat: "d.m.Y",
                onChange: function (selectedDates, dateStr, instance) {
                    me._submitDate(dateStr);
                },
                disable: [
                    function(date) {
                        // return true to disable
                        return (date.getDay() === 0 || date.getDay() === 6);
                    }
                ],
                locale: German,
                minDate: minDate
            });
        }
    }

    _getFormData() {
        return FormSerializeUtil.serialize(this._form);
    }

    _submitDate() {
        const action = DomAccess.getAttribute(this._form, 'data-action');
        this._client.post(action, this._getFormData());
    }
}