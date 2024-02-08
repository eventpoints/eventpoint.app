import axios from "axios";

export default class TimezoneService {
    constructor() {
        this.timezone = null;
    }

    async configureTimezone() {
        this.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        axios.post('https://eventpoint.app/set/browser/timezone', {
            timezone: this.timezone
        });
    }
}