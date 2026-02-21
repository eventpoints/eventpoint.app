import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static values = {
        lat: Number,
        lng: Number,
    }

    connect() {
        this.element.addEventListener('ux:map:connect', (event) => {
            const { map, L } = event.detail

            L.circleMarker([this.latValue, this.lngValue], {
                radius: 10,
                fillColor: '#660033',
                color: '#ffffff',
                weight: 1,
                fillOpacity: 1,
            }).addTo(map)
        })
    }
}
