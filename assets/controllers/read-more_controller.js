import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['content', 'toggle'];
    static values = { moreText: String, lessText: String };

    connect() {
        this.expanded = false;
    }

    toggle() {
        this.expanded = !this.expanded;
        this.contentTarget.classList.toggle('line-clamp-3', !this.expanded);
        this.toggleTarget.textContent = this.expanded
            ? this.lessTextValue
            : this.moreTextValue;
    }
}
