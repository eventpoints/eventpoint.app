import {Controller} from '@hotwired/stimulus'
import mapboxgl from 'mapbox-gl'

export default class extends Controller {
    static targets = ["map"];

    static values = {
        token: String,
        longitude: String,
        latitude: String,
    }

    connect() {
        mapboxgl.accessToken = this.tokenValue;
        this.map = new mapboxgl.Map({
            container: this.mapTarget,
            style: 'mapbox://styles/kez2/cl46unvmd000f15o0ayowykuo',
            center: [this.longitudeValue, this.latitudeValue],
            zoom: 11,
            interactive: false
        });

        this.addMarker();
    }

    addMarker() {
        new mapboxgl.Marker()
            .setLngLat([this.longitudeValue, this.latitudeValue])
            .addTo(this.map);
    }
}
