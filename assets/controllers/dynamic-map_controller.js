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
        this.map = new mapboxgl.Map({
            container: this.mapTarget,
            style: 'mapbox://styles/kez2/cl46unvmd000f15o0ayowykuo',
            center: [14.4378, 50.0755],
            zoom: 8
        })
        window.mapInstance = this.map
        this.map.addControl(new mapboxgl.FullscreenControl())
        this.map.addControl(new mapboxgl.NavigationControl())

        this.map.on('render', (e) => {
            let center = this.map.getCenter()
        })

        this.map.on('load', () => {
            const featuresWithIds = this.eventsValue.map((feature, index) => ({
                ...feature,
                id: `event-${feature.id}`
            }))

            this.map.addSource('points', {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: [...featuresWithIds]
                },
                cluster: true,
                clusterMaxZoom: 14,
                clusterRadius: 50
            })

            this.map.addLayer({
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

            this.map.addLayer({
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

            this.map.addLayer({
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

            this.map.on('click', 'unclustered-point', (event) => {
                let id = event.features[0].properties.id
                let card = document.querySelector(`#asset-${id}`)
                if (card != null) {
                    card.classList.toggle('text-bg-primary')
                }
            })

        })
    }

    flyToAssetOnMap(event) {
        let id = event.params.id
        let longitude = event.params.longitude
        let latitude = event.params.latitude

        this.map.setPaintProperty('unclustered-point', 'circle-color', [
            'case',
            ['==', ['get', 'id'], id],
            '#39775A',
            '#777777',
        ]);

        this.map.flyTo({
            center: [longitude, latitude],
            zoom: 18,
            speed: 1.2,
            curve: 1.4
        });
    }

    onMouseLeave(event) {
        let id = event.params.id
        this.map.setPaintProperty('unclustered-point', 'circle-color', [
            'case',
            ['==', 'get', 'id', id],
            '#39775A',
            '#777',
        ]);
    }
}
