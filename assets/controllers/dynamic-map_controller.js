import {Controller} from '@hotwired/stimulus'
import mapboxgl from 'mapbox-gl'

export default class extends Controller {

    static values = {
        token: String,
        events: Array
    }

    static targets = ['map', 'event-card', 'mapInstance']


    connect() {

        mapboxgl.accessToken = this.tokenValue;
        this.mapbox = new mapboxgl.Map({
            container: this.mapTarget,
            style: 'mapbox://styles/kez2/cl46unvmd000f15o0ayowykuo',
            center: [14.4378, 50.0755],
            zoom: 8,
        })

        window.mapInstance = this.mapbox
        this.mapbox.addControl(new mapboxgl.FullscreenControl())
        this.mapbox.addControl(new mapboxgl.NavigationControl())

        this._onMapShown = () => this.mapbox.resize()
        window.addEventListener('view-toggle:map-shown', this._onMapShown)

        this.mapbox.on('load', () => {

            const featuresWithIds = this.eventsValue.map((feature, index) => ({
                ...feature,
                id: `event-${feature.id}`
            }))

            this.mapbox.addSource('points', {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: [...featuresWithIds]
                },
                cluster: true,
                clusterMaxZoom: 14,
                clusterRadius: 50
            })

            this.mapbox.addLayer({
                id: 'clusters',
                type: 'circle',
                source: 'points',
                filter: ['has', 'point_count'],
                paint: {
                    'circle-color': [
                        'step',
                        ['get', 'point_count'],
                        '#777',
                        10,
                        '#777',
                        30,
                        '#777',
                        50,
                        '#777'
                    ],
                    'circle-radius': [
                        'step',
                        ['get', 'point_count'],
                        20,
                        10,
                        30,
                        50,
                        40
                    ]
                }
            })

            this.mapbox.addLayer({
                id: 'cluster-count',
                type: 'symbol',
                source: 'points',
                filter: ['has', 'point_count'],
                layout: {
                    'text-field': '{point_count_abbreviated}',
                    'text-font': ['Arial Unicode MS Bold'],
                    'text-size': 12
                },
                paint: {
                    'text-color': '#ffffff'
                }
            })

            this.mapbox.addLayer({
                id: 'unclustered-point',
                type: 'circle',
                source: 'points',
                filter: ['!', ['has', 'point_count']],
                paint: {
                    'circle-color': '#777',
                    'circle-radius': 10,
                    'circle-stroke-width': 1,
                    'circle-stroke-color': '#ffffff'
                }
            })

            this.mapbox.on('click', 'unclustered-point', (e) => {
                const coordinates = e.features[0].geometry.coordinates.slice()
                const props = e.features[0].properties
                const title = props.title || ''
                const address = props.address || ''
                const id = props.id

                while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360
                }

                this._setActivePoint(id)

                const popup = new mapboxgl.Popup()
                    .setLngLat(coordinates)
                    .setHTML(`
                        <strong>${title}</strong>
                        <br><small>${address}</small>
                        <br><a href="/events/${id}">View event</a>
                    `)
                    .addTo(this.mapbox)

                popup.on('close', () => this._clearActivePoint())
            })

            this.mapbox.on('mouseenter', 'unclustered-point', () => {
                this.mapbox.getCanvas().style.cursor = 'pointer'
            })

            this.mapbox.on('mouseleave', 'unclustered-point', () => {
                this.mapbox.getCanvas().style.cursor = ''
            })
        })
    }

    disconnect() {
        window.removeEventListener('view-toggle:map-shown', this._onMapShown)
    }

    eventsValueChanged(current, old) {
        const featuresWithIds = current.map((feature, index) => ({
            ...feature,
            id: `event-${feature.id}`
        }));

        if (this.mapbox === undefined) {
            return;
        }

        this.mapbox.getSource('points').setData({
            type: 'FeatureCollection',
            features: featuresWithIds
        });
    }

    flyToAssetOnMap(event) {
        let id = event.params.id
        let longitude = event.params.longitude
        let latitude = event.params.latitude

        window.dispatchEvent(new Event('dynamic-map:switch-to-map'))

        this._setActivePoint(id)

        this.mapbox.flyTo({
            center: [longitude, latitude],
            zoom: 18,
            speed: 1.2,
            curve: 1.4
        });
    }

    _setActivePoint(id) {
        this.mapbox.setPaintProperty('unclustered-point', 'circle-color', [
            'case',
            ['==', ['get', 'id'], id],
            '#39775A',
            '#777',
        ]);
    }

    _clearActivePoint() {
        this.mapbox.setPaintProperty('unclustered-point', 'circle-color', '#777');
    }

    onMouseLeave(event) {
        let id = event.params.id
        this.mapbox.setPaintProperty('unclustered-point', 'circle-color', [
            'case',
            ['==', 'get', 'id', id],
            '#39775A',
            '#777',
        ]);
    }
}
