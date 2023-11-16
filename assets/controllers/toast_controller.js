import { Controller } from '@hotwired/stimulus';
import {Toast} from "bootstrap";

export default class extends Controller {
    connect() {
        const container = document.getElementById('toast-container');
        if(this.element.parentNode !== container){
            container.appendChild(this.element);
            return;
        }

       const toast = new Toast(this.element);
       toast.show();

    }
}
