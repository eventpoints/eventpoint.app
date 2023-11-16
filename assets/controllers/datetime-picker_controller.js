import {Controller} from '@hotwired/stimulus';
import MaterialDateTimePicker from 'material-datetime-picker'

export default class extends Controller {

    static targets = [
        'input'
    ]

    connect() {
        this.picker = new MaterialDateTimePicker({
            container: document.body,
        })
            .on('submit', (val) => {
                this.inputTarget.value = val.toISOString()
            })
        this.inputTarget.parent.on('click', () => this.picker.open())
    }

    open(event) {
        this.picker.open()
    }

    close(event) {
        this.picker.close()
    }

}
