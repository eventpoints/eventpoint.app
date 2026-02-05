import {Controller} from '@hotwired/stimulus'

export default class extends Controller {

    static targets = ['list', 'map', 'listButton', 'mapButton']

    connect() {
        this._onSwitchToMap = () => this.showMap()
        window.addEventListener('dynamic-map:switch-to-map', this._onSwitchToMap)
    }

    disconnect() {
        window.removeEventListener('dynamic-map:switch-to-map', this._onSwitchToMap)
    }

    showList() {
        this.listTarget.classList.remove('d-none')
        this.mapTarget.classList.add('d-none')
        this.listButtonTarget.classList.add('active')
        this.mapButtonTarget.classList.remove('active')
    }

    showMap() {
        this.listTarget.classList.add('d-none')
        this.mapTarget.classList.remove('d-none')
        this.mapButtonTarget.classList.add('active')
        this.listButtonTarget.classList.remove('active')
        window.dispatchEvent(new Event('view-toggle:map-shown'))
    }
}
