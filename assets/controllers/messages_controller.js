import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        console.log('CONodjwoajdpowaj')
        this.scrollToBottom()
    }

    scrollToBottom() {
        const element = this.element;
        element.scrollTop = element.scrollHeight;
    }
}
