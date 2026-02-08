import {Controller} from '@hotwired/stimulus';

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
        item.classList.add('opacity-50', 'pointer-events-none')
    }

    enableItem(emailAddress) {
        let item = this.itemTargets.filter((item) => {
            return item.getAttribute('data-email-value').toLowerCase() === emailAddress
        })[0]
        if (item) {
            item.classList.remove('opacity-50', 'pointer-events-none')
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
        badge.innerHTML = `<div class="flex items-center justify-between rounded-full bg-gray-100 text-gray-800 px-3 py-1 m-1"><span class="text-base me-3">${emailAddress}</span><span class="bi bi-x-circle text-lg text-red-600 cursor-pointer" data-action="click->autocompleter#remove"></span></div>`
        return badge
    }
}
