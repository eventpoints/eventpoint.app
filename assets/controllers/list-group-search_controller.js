import {Controller} from '@hotwired/stimulus';

export default class extends Controller {

    static targets = ['input', 'item', 'checkbox']

    connect() {
    }

    type(event) {
        this.itemTargets.map((item, index) => {

            if (item.innerText.toLowerCase().includes(this.inputTarget.value.toLowerCase())) {
                this.itemTargets[index].classList.remove('visually-hidden')
            } else {
                this.itemTargets[index].classList.add('visually-hidden')
            }
        })
    }

}

