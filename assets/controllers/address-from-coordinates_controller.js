import { Controller } from '@hotwired/stimulus';
import mapboxgl from 'mapbox-gl';
import MapboxGeocoder from '@mapbox/mapbox-gl-geocoder';

export default class extends Controller {

    static values = {
        token: String,
        latitude: String,
        longitude: String
    }

    static targets = [
        'address', 'map'
    ]

    connect() {

        const geocoder = new MapboxGeocoder({
            accessToken: this.tokenValue,
            mapboxgl: mapboxgl,
        });

        console.log(geocoder.geolocation)

    }
}
