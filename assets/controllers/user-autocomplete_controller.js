import {Controller} from '@hotwired/stimulus';
import axios from 'axios'

export default class extends Controller {

    static values = {
        path: String
    }

    static targets = [
        'list'
    ]

    connect() {

    }

    async typing(event) {

        await axios.get(this.pathValue, {
            params: {
                query: event.target.value
            }
        }).then((response) => {

            response.data.forEach((user) => {
                let el = document.createElement('div')
                el.classList.add('list-group-item')
                el.innerText = user.email
                this.listTarget.appendChild(el)
            })

        }).finally(() => {

        })
    }
}
