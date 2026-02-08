import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const container = document.getElementById('toast-container');
        if(this.element.parentNode !== container){
            container.appendChild(this.element);
            return;
        }

        this.element.classList.remove('hidden');
        this.element.classList.add('flex');

        setTimeout(() => {
            this.element.classList.add('hidden');
            this.element.classList.remove('flex');
        }, 5000);
    }

    show(event) {
        if (event.detail && event.detail.message) {
            this.element.querySelector('span:last-child').textContent = event.detail.message;
        }
        this.connect();
    }
}
