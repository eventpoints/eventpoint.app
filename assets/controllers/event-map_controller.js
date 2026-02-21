import {Controller} from '@hotwired/stimulus'
import L from 'leaflet'
import 'leaflet.markercluster'
import 'leaflet.markercluster/dist/MarkerCluster.css'
import 'leaflet.markercluster/dist/MarkerCluster.Default.css'

export default class extends Controller {

    static values = {
        boundaryUrl: String,
    }

    connect() {
        this._onMapShown = () => {
            if (this.leafletMap) this.leafletMap.invalidateSize()
        }
        window.addEventListener('view-toggle:map-shown', this._onMapShown)

        this.element.addEventListener('ux:map:connect', (event) => {
            this.leafletMap = event.detail.map
            this._activeMarkerId = null
            this._markerLookup = new Map()
            this._clusterGroup = null
            this._boundaryLayer = null

            const extra = event.detail.extra || {}
            const events = extra.events || []

            this._setupMarkers(events)
            this._loadCityBoundary()
        })
    }

    disconnect() {
        window.removeEventListener('view-toggle:map-shown', this._onMapShown)
    }

    _setupMarkers(events) {
        this._clusterGroup = L.markerClusterGroup()

        events.forEach((evt) => {
            const marker = L.circleMarker([evt.lat, evt.lng], {
                radius: 10,
                fillColor: '#777',
                color: '#ffffff',
                weight: 1,
                fillOpacity: 1,
            })

            marker._eventId = evt.id
            this._markerLookup.set(evt.id, marker)

            marker.on('click', () => {
                this._clearAllHighlights()
                this._setActivePoint(evt.id)
                this._highlightEventCard(evt.id)
            })

            this._clusterGroup.addLayer(marker)
        })

        this.leafletMap.addLayer(this._clusterGroup)

        this.leafletMap.on('click', () => {
            this._clearAllHighlights()
            this._clearActivePoint()
        })
    }

    flyToAssetOnMap(event) {
        const id = event.params.id
        const longitude = event.params.longitude
        const latitude = event.params.latitude

        window.dispatchEvent(new Event('dynamic-map:switch-to-map'))

        this._clearAllHighlights()
        this._setActivePoint(id)
        this._highlightEventCard(id)

        this.leafletMap.flyTo([latitude, longitude], 18, {
            duration: 1.2,
        })
    }

    _setActivePoint(id) {
        this._activeMarkerId = id
        const marker = this._markerLookup.get(id)
        if (marker) {
            marker.setStyle({fillColor: '#660033'})
        }
    }

    _clearActivePoint() {
        if (this._activeMarkerId) {
            const marker = this._markerLookup.get(this._activeMarkerId)
            if (marker) {
                marker.setStyle({fillColor: '#777'})
            }
            this._activeMarkerId = null
        }
    }

    _clearAllHighlights() {
        this._clearActivePoint()
        document.querySelectorAll('[data-event-map-event-id]').forEach(card => {
            card.style.boxShadow = ''
            card.style.borderColor = ''
            card.style.transform = ''
        })
    }

    _highlightEventCard(id) {
        const card = document.querySelector(`[data-event-map-event-id="${id}"]`)
        if (card) {
            card.style.borderColor = '#660033'
            card.style.transform = 'scale(1.02)'
            card.scrollIntoView({behavior: 'smooth', block: 'center'})
        }
    }

    async _loadCityBoundary() {
        if (!this.hasBoundaryUrlValue || !this.boundaryUrlValue) return

        try {
            const response = await fetch(this.boundaryUrlValue)
            if (!response.ok) return

            const data = await response.json()
            if (!data.geojson) return

            this._boundaryLayer = L.geoJSON(data.geojson, {
                style: {
                    color: '#660033',
                    weight: 2,
                    opacity: 0.6,
                    fillColor: '#660033',
                    fillOpacity: 0.05,
                },
            }).addTo(this.leafletMap)
        } catch (e) {
            // Silently fail if boundary cannot be loaded
        }
    }
}
