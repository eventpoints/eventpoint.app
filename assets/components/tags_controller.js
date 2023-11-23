import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input", "tags"]; // Change "tagList" to "tags"

    static VALID_EMAIL_REGEX = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i;

    connect() {
        // Initialize your controller here
    }

    addTag(event) {
        if (event.key === "Enter" || event.key === ",") {
            event.preventDefault();

            const inputValue = this.inputTarget.value.trim();

            if (inputValue) {
                if (this.isValidEmail(inputValue)) {
                    this.tagsTarget.insertAdjacentHTML( // Change "tagList" to "tags"
                        "beforeend",
                        `<div class="list-group-item">${inputValue}</div>`
                    );

                    this.inputTarget.classList.replace('is-invalid','is-valid')
                    this.inputTarget.classList.remove('is-valid')
                    this.inputTarget.value = "";
                } else {
                    this.inputTarget.classList.add('is-invalid')
                }
            }
        }
    }

    isValidEmail(email) {
        return email.match(this.constructor.VALID_EMAIL_REGEX);
    }
}