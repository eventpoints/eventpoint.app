import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['timezone', 'region'];

    connect() {
        // Store the initial timezone when the controller connects
        this.initialTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        // Attach the change event listener to the region target
        this.regionTarget.addEventListener('change', this.handleRegionChange.bind(this));
    }

}
