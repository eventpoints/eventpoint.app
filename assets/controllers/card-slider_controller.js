import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["card"];

    connect() {
        this.currentIndex = 0;
        this.showCurrentCard();
    }

    showCurrentCard() {
        this.cardTargets.forEach((card, index) => {
            const isInRange = index >= this.currentIndex && index < this.currentIndex + 2;
            card.style.display = isInRange ? "block" : "none";
        });
    }

    next() {
        this.currentIndex = (this.currentIndex + 1) % (this.cardTargets.length - 1);
        this.showCurrentCard();
    }

    prev() {
        this.currentIndex = (this.currentIndex - 1 + (this.cardTargets.length - 1)) % (this.cardTargets.length - 1);
        this.showCurrentCard();
    }
}