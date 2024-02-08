import axios from "axios";

export default class TimezoneService {
    constructor() {
        this.timezone = null;
    }

    async configureTimezone() {
        this.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        // https://eventpoint.app/set/browser/timezone
        axios.post('https://localhost/set/browser/timezone', {
            timezone: this.timezone
        });
    }
}