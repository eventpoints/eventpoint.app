// fb-datepicker_controller.js
import { Controller } from '@hotwired/stimulus';
import { Datepicker } from 'flowbite-datepicker';

export default class extends Controller {
  static values = {
    format: { type: String, default: 'yyyy-mm-dd' },
    autohide: { type: Boolean, default: true },
    openOnFocus: { type: Boolean, default: true },
    minDate: String,
    maxDate: String,
  };

  connect() {
    if (this.element._fbDatepicker) return;

    const fmtAttr = this.element.getAttribute('datepicker-format');
    const fmt = this.hasFormatValue ? this.formatValue : (fmtAttr || 'yyyy-mm-dd');

    const opts = { format: fmt, autohide: this.autohideValue };

    const min = this.hasMinDateValue ? this.minDateValue : this.element.getAttribute('min');
    const max = this.hasMaxDateValue ? this.maxDateValue : this.element.getAttribute('max');
    if (min) opts.minDate = new Date(min);
    if (max) opts.maxDate = new Date(max);

    this.dp = new Datepicker(this.element, opts);
    this.element._fbDatepicker = this.dp;

    this._onChangeDate = () => {
      // These bubble up to filter-fragment (which listens on the wrapper)
      this.element.dispatchEvent(new Event('input',  { bubbles: true }));
      this.element.dispatchEvent(new Event('change', { bubbles: true }));
    };
    this.element.addEventListener('changeDate', this._onChangeDate);

    if (this.openOnFocusValue) {
      this._onFocus = () => this.dp.show();
      this.element.addEventListener('focus', this._onFocus);
    }
  }

  disconnect() {
    if (this._onFocus) {
      this.element.removeEventListener('focus', this._onFocus);
      this._onFocus = null;
    }
    if (this._onChangeDate) {
      this.element.removeEventListener('changeDate', this._onChangeDate);
      this._onChangeDate = null;
    }

    this.dp = null;
    delete this.element._fbDatepicker;
  }
}
