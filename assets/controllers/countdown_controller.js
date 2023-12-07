import { Controller } from '@hotwired/stimulus';
import CountDown from 'count-time-down';

export default class extends Controller {

    static values = {
        milliseconds: Number
    }
    static targets = ['result']

    connect() {
        let display = this.resultTarget
        let milliseconds = this.millisecondsValue

        const cd = new CountDown();
        cd.time = milliseconds;
        cd.cdType = 'h'
        cd.onTick = cd => {
            display.innerHTML = cd.hhmmss
        }
        cd.start();
    }
}
