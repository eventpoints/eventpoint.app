import {Controller} from '@hotwired/stimulus';
import {Toast} from "bootstrap";

export default class extends Controller {

    static targets = ['input', 'list', 'item', 'selected']
    static values = {
        path: String,
        isVisible: false
    }

    connect() {
        this.outputArray = []
    }

    filter(event) {
        if (event.key === 'Enter') {
            this.addUnknownEmail()
        }

        const inputValue = this.inputTarget.value.toLowerCase()
        this.itemTargets.forEach((item) => {
            const itemValue = item.getAttribute('data-email-value').toLowerCase()
            if (itemValue.includes(inputValue)) {
                item.style.display = "block"
            } else {
                item.style.display = "none"
            }
        });
    }

    add(event) {
        let emailAddress = event.target.getAttribute('data-email-value').toLowerCase()
        if (this.canAddEmailToOutputArray(emailAddress)) {
            this.outputArray.push(emailAddress)
            let badge = this.createEmailBadge(emailAddress)
            this.selectedTarget.appendChild(badge)
            this.disableItem(event.target)
        }
    }

    remove(event) {
        let badge = event.target.parentElement.parentElement
        let emailAddress = badge.getAttribute('data-email-value').toLowerCase()
        let emailToRemove = this.outputArray.indexOf(emailAddress);
        this.outputArray.splice(emailToRemove, 1);
        this.enableItem(emailAddress)
        badge.remove();
    }

    disableItem(item) {
        item.classList.add('disabled')
    }

    enableItem(emailAddress) {
        let item = this.itemTargets.filter((item) => {
            return item.getAttribute('data-email-value').toLowerCase() === emailAddress
        })[0]
        if (item) {
            item.classList.remove('disabled')
        }
    }

    canAddEmailToOutputArray(emailAddress) {
        return !this.outputArray.includes(emailAddress);
    }

    addUnknownEmail() {
        let emailAddress = this.inputTarget.value.toLowerCase()
        if (this.canAddEmailToOutputArray(emailAddress)) {
            this.outputArray.push(emailAddress)
            let badge = this.createEmailBadge(emailAddress)
            this.selectedTarget.appendChild(badge)
            this.inputTarget.value = ''
        }

    }

    createEmailBadge(emailAddress) {
        let badge = document.createElement('div')
        badge.setAttribute('data-email-value', emailAddress)
        badge.innerHTML = `<div class="d-flex justify-content-between align-items-center badge rounded-pill text-bg-dark-grey m-1"><div class="lead me-3">${emailAddress}</div><div class="bi bi-x-circle fs-5 link-danger" data-action="click->autocompleter#remove"></div></div>`
        return badge
    }
}
