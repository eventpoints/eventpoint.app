import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {owner: String};

    connect() {
        this.previousOwnerId = null;
        this.toggleStickyTop();
    }
    updateStickyTop() {
        const currentOwnerId = this.ownerValue;

        if (currentOwnerId !== this.previousOwnerId) {
            // Update the sticky-top element or perform any other necessary actions
            console.log(`Message owner changed to: ${currentOwnerId}`);
            this.previousOwnerId = currentOwnerId;
            this.toggleStickyTop();
        }
    }

    toggleStickyTop() {
        const isVisible = this.ownerValue !== this.previousOwnerId;
        this.element.style.display = isVisible ? 'block' : 'none';
    }
}
