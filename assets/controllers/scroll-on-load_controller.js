import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = [
        'comment'
    ]

    static values = {
        id: String
    }

    connect() {
        const commentFromUrl = `comment-${this.idValue}`;
        const targetElement = this.findTarget(commentFromUrl);

        console.log(targetElement)

        if (targetElement) {
            targetElement.scrollIntoView({behavior: 'smooth'});
            targetElement.classList.add('text-bg-info');
            setTimeout(() => {
                targetElement.classList.remove('text-bg-info');
            }, 1000);
        }
    }

    findTarget(commentId) {
        return this.commentTargets.find(target => target.getAttribute('id') === commentId);
    }
}
