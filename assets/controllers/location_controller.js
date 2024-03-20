import {Controller} from '@hotwired/stimulus';
import mapboxgl from 'mapbox-gl';

export default class extends Controller {
    static targets = ['result', 'latitude', 'longitude', 'icon', 'address'];
    static values = {
        token: String,
        latitude: String,
        longitude: String,
    };

    connect() {

        this.lat = this.latitudeValue || '50.0755';
        this.lng = this.longitudeValue || '14.4378';

        mapboxgl.accessToken = this.tokenValue;
        this.map = new mapboxgl.Map({
            container: this.resultTarget,
            style: 'mapbox://styles/kez2/cl46unvmd000f15o0ayowykuo',
            zoom: 10,
            center: [parseFloat(this.lng), parseFloat(this.lat)],
        });


        this.map.on('load', () => {
            this.marker = new mapboxgl.Marker({
                color: '#000000',
                draggable: true,
            })
                .setLngLat([parseFloat(this.lng), parseFloat(this.lat)])
                .addTo(this.map);


            this.marker.on('dragend', async () => {
                const lngLat = this.marker.getLngLat();
                this.latitudeTarget.setAttribute('value', lngLat.lat);
                this.longitudeTarget.setAttribute('value', lngLat.lng);
                this.addressTarget.setAttribute('value', await this.reverseGeocode(lngLat.lng, lngLat.lat));
            });


            this.map.flyTo({
                center: [this.lng, this.lat],
                zoom: 15,
                speed: 1.2,
                curve: 1.4,
            });
        });
    }

    async getCurrentLocation(event) {
        event.preventDefault();
        this.iconTarget.classList.replace('bi', 'spinner-border');
        this.iconTarget.classList.replace('bi-crosshair', 'spinner-border-sm');

        try {
            const position = await new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject);
            });

            const {latitude, longitude} = position.coords;
            this.marker.setLngLat([longitude, latitude]);
            this.latitudeTarget.setAttribute('value', latitude);
            this.longitudeTarget.setAttribute('value', longitude);
            this.addressTarget.setAttribute('value', await this.reverseGeocode(longitude, latitude));


            this.map.flyTo({
                center: [longitude, latitude],
                zoom: 15,
                speed: 1.2,
                curve: 1.4,
            });
        } catch (error) {
            // Handle error
        } finally {
            this.iconTarget.classList.replace('spinner-border', 'bi');
            this.iconTarget.classList.replace('spinner-border-sm', 'bi-crosshair');
        }
    }

    async reverseGeocode(longitude, latitude) {
        const response = await fetch(
            `https://api.mapbox.com/geocoding/v5/mapbox.places/${longitude},${latitude}.json?access_token=${this.tokenValue}`
        )

        if (!response.ok) {
            throw new Error('Failed to reverse geocode coordinates.')
        }

        const data = await response.json()
        const firstFeature = data.features[0]
        console.log(firstFeature)

        if (firstFeature) {
            return firstFeature.place_name
        } else {
            return 'Address not found'
        }
    }
}
