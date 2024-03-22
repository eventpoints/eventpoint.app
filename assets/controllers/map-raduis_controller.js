import { Controller } from '@hotwired/stimulus';
import mapboxgl from 'mapbox-gl';

export default class extends Controller {
    static targets = ["map", 'radius'];
    static values = {
        token: String,
        longitude: String,
        latitude: String,
        city: String
    };

    connect() {
        this.drawCircle();
    }

    cityValueChanged() {
        this.drawCircle();
    }

    async drawCircle() {
        const token = this.tokenValue;
        const longitude = parseFloat(this.longitudeValue);
        const latitude = parseFloat(this.latitudeValue);
        const radiusKilometers = parseFloat(this.radiusTarget.value);

        // Load the map
        mapboxgl.accessToken = token;
        this.map = new mapboxgl.Map({
            container: this.mapTarget,
            style: 'mapbox://styles/mapbox/streets-v11',
            zoom: 10,
            center: [longitude, latitude],
        });

        // Wait for the map to load
        await new Promise(resolve => {
            this.map.on('load', resolve);
        });

        // Attach zoom event listener
        this.map.on('zoom', () => {
            this.updateCircleRadius();
        });

        // Convert kilometers to meters
        const radiusMeters = radiusKilometers * 1000;

        // Convert meters to pixels based on zoom level
        const zoom = this.map.getZoom();
        const metersPerPixel = 40075016.686 / Math.pow(2, zoom + 8);
        const radiusPixels = radiusMeters / metersPerPixel;

        // Draw circle
        this.map.addSource("circle-source", {
            type: "geojson",
            data: {
                type: "Feature",
                properties: {},
                geometry: {
                    type: "Point",
                    coordinates: [longitude, latitude]
                }
            }
        });

        this.map.addLayer({
            id: "radius-layer",
            type: "circle",
            source: "circle-source",
            paint: {
                "circle-radius": radiusPixels,
                "circle-color": "#007cbf",
                "circle-opacity": 0.3
            }
        });

        // this.fitCircleBounds(radiusMeters, longitude, latitude);
    }

    updateCircleRadius() {
        const radiusKilometers = parseFloat(this.radiusTarget.value);
        const zoom = this.map.getZoom();

        // Convert kilometers to meters
        const radiusMeters = radiusKilometers * 1000;

        // Convert meters to pixels based on zoom level
        const metersPerPixel = 40075016.686 / Math.pow(2, zoom + 8);
        const radiusPixels = radiusMeters / metersPerPixel;

        // Update circle radius
        this.map.setPaintProperty('radius-layer', 'circle-radius', radiusPixels);

        // Zoom to fit the circle with padding
        // this.fitCircleBounds(radiusMeters);
    }
}
