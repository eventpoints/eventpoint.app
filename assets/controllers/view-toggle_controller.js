import {Controller} from '@hotwired/stimulus'

export default class extends Controller {

    static targets = ['list', 'map', 'listButton', 'mapButton']

    connect() {
        this.showList()
        this._onSwitchToMap = () => this.showMap()
        window.addEventListener('dynamic-map:switch-to-map', this._onSwitchToMap)
    }

    disconnect() {
        window.removeEventListener('dynamic-map:switch-to-map', this._onSwitchToMap)
    }

    showList() {
        this.listTarget.classList.remove('hidden')
        this.mapTarget.classList.add('hidden')
        this.listButtonTarget.classList.add('active')
        this.mapButtonTarget.classList.remove('active')
    }

    showMap() {
        this.listTarget.classList.add('hidden')
        this.mapTarget.classList.remove('hidden')
        this.mapButtonTarget.classList.add('active')
        this.listButtonTarget.classList.remove('active')
        window.dispatchEvent(new Event('view-toggle:map-shown'))
    }
}
