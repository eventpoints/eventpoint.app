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
            this._setupMapLayers()
        })
    }

    disconnect() {
        window.removeEventListener('view-toggle:map-shown', this._onMapShown)
    }

    _setupMapLayers() {
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
            const props = e.features[0].properties
            const id = props.id

            this._clearAllHighlights()
            this._setActivePoint(id)
            const cardId = id.replace('event-', '')
            this._highlightEventCard(cardId)
        })

        this.mapbox.on('click', 'clusters', () => {
            document.querySelectorAll('[data-dynamic-map-event-id]').forEach(card => {
                card.classList.remove('ring-2', 'ring-indigo-500', 'shadow-lg', 'scale-[1.02]')
            })
            this._clearActivePoint()
        })

        this.mapbox.on('click', (e) => {
            if (!e.features) {
                document.querySelectorAll('[data-dynamic-map-event-id]').forEach(card => {
                    card.classList.remove('ring-2', 'ring-indigo-500', 'shadow-lg', 'scale-[1.02]')
                })
                this._clearActivePoint()
            }
        })

        this.mapbox.on('click', (e) => {
            document.querySelectorAll('[data-dynamic-map-event-id]').forEach(card => {
                card.classList.remove('ring-2', 'ring-indigo-500', 'shadow-lg', 'scale-[1.02]')
            })
            this._clearActivePoint()
        })

        this.mapbox.on('mouseenter', 'unclustered-point', () => {
            this.mapbox.getCanvas().style.cursor = 'pointer'
        })

        this.mapbox.on('mouseleave', 'unclustered-point', () => {
            this.mapbox.getCanvas().style.cursor = ''
        })
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
        this._clearAllHighlights()
        this._highlightEventCard(id)

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

    _clearAllHighlights() {
        document.querySelectorAll('[data-dynamic-map-event-id]').forEach(card => {
            card.style.boxShadow = ''
            card.style.borderColor = ''
            card.style.transform = ''
        })
    }

    _highlightEventCard(id) {
        const card = document.querySelector(`[data-dynamic-map-event-id="${id}"]`)
        if (card) {
            card.style.borderColor = '#660033';
            card.style.transform = 'scale(1.02)'
            card.scrollIntoView({ behavior: 'smooth', block: 'center' })
        }
    }

    _unhighlightEventCard(id) {
        const card = document.querySelector(`[data-dynamic-map-event-id="${id}"]`)
        if (card) {
            card.style.boxShadow = ''
            card.style.borderColor = ''
            card.style.transform = ''
        }
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
