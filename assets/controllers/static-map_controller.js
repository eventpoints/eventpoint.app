import {Controller} from '@hotwired/stimulus'
import mapboxgl from 'mapbox-gl'

export default class extends Controller {
    static targets = ["map"];

    static values = {
        token: String,
        longitude: String,
        latitude: String,
        interactive: Boolean
    }

    connect() {
        mapboxgl.accessToken = this.tokenValue;
        this.map = new mapboxgl.Map({
            container: this.mapTarget,
            style: 'mapbox://styles/kez2/cl46unvmd000f15o0ayowykuo',
            center: [this.longitudeValue, this.latitudeValue],
            zoom: 11,
            interactive: this.interactiveValue
        });

        this.addMarker();


        this.map.on('load', () => {
            this.map.flyTo({
                center: [this.longitudeValue, this.latitudeValue],
                zoom: 15,
                speed: 1.2,
                curve: 1.4,
            });
        });
    }

    addMarker() {
        new mapboxgl.Marker()
            .setLngLat([this.longitudeValue, this.latitudeValue])
            .addTo(this.map);
    }
}
