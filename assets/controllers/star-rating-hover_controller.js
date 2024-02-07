import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['star']

    connect() {
        this.stars = this.starTargets;
        this.currentRating = this.getRating();
    }
    handleStarHover = (event) => {
        const starIndex = this.stars.indexOf(event.target);
        const currentRating = this.currentRating;

        this.dispatchEvent('hover', { starIndex, currentRating });
    };

    getRating() {
        // Get the current rating from the data-action attribute
        const dataAction = this.element.dataset.action.split('|');
        const actionType = dataAction[0];
        const actionData = dataAction[1];

        if (actionType === 'prevent') {
            // Extract the rating from the data-action-name attribute
            const rating = actionData.split('(')[1].split(')')[0];
            return Number(rating);
        }
    }

    hover(event) {
        const starIndex = event.detail.starIndex;
        const currentRating = event.detail.currentRating;

        if (starIndex < currentRating) {
            // Increase the current rating
            this.currentRating++;
            this.updateStarsColor();
        } else if (starIndex === currentRating) {
            // Highlight the current rating
            this.highlightStar(starIndex);
        } else {
            // Decrement the current rating
            this.currentRating--;
            this.updateStarsColor();
        }
    }

    updateStarsColor() {
        for (let i = 0; i < this.currentRating; i++) {
            this.stars[i].classList.add('bi-star-fill');
            this.stars[i].classList.remove('bi-star');
        }

        for (let i = this.currentRating; i < this.stars.length; i++) {
            this.stars[i].classList.add('bi-star');
            this.stars[i].classList.remove('bi-star-fill');
        }
    }

    highlightStar(index) {
        this.stars[index].classList.add('bi-star-fill');
        this.stars[index].classList.remove('bi-star');
    }
}
