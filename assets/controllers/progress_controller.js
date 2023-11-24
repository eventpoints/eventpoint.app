import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["progressBar"];
    static values = {
        duration: Number,
        elapsed: Number
    }
    connect() {
        this.updateProgress(this.elapsedValue);
    }

    updateProgress(minutes) {
        const progress = (minutes / this.durationValue) * 100;

        this.progressBarTarget.style.width = progress + "%";
        this.progressBarTarget.setAttribute("aria-valuenow", progress);
    }
}
