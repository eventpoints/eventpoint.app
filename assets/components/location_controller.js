import {Controller} from '@hotwired/stimulus'
import mapboxgl from 'mapbox-gl'

export default class extends Controller {

    static targets = ['result', 'latitude', 'longitude', 'icon']
    static values = {
        token: String
    }

    connect() {
        this.lat = 50.0755
        this.lng = 14.4378
        mapboxgl.accessToken = this.tokenValue
        this.map = new mapboxgl.Map({
            container: this.resultTarget,
            style: 'mapbox://styles/mapbox/streets-v11',
            zoom: 10,
            center: [14.4378, 50.0755]
        });


        this.map.on('load', () => {
            this.marker = new mapboxgl.Marker({
                color: '#000000',
                draggable: true
            })
                .setLngLat([this.lng, this.lat])
                .addTo(this.map)

            this.marker.on('dragend', () => {
                const lngLat = this.marker.getLngLat()
                this.latitudeTarget.setAttribute('value', lngLat.lat)
                this.longitudeTarget.setAttribute('value', lngLat.lng)
            })

        })

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
            this.latitudeTarget.setAttribute('value', latitude)
            this.longitudeTarget.setAttribute('value', longitude)
            this.map.flyTo({
                center: [longitude, latitude],
                zoom: 15,
                speed: 1.2,
                curve: 1.4
            });
        } catch (error) {
            // Handle error
        } finally {
            this.iconTarget.classList.replace('spinner-border', 'bi');
            this.iconTarget.classList.replace('spinner-border-sm', 'bi-crosshair');
        }
    }

}
