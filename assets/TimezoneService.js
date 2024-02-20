import axios from "axios";

export default class TimezoneService {
    constructor() {
        this.timezone = null;
        this.uri = null;
    }


    async configureTimezone() {
        this.timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

        if (process.env.NODE_ENV === 'development') {
            this.uri = ' https://localhost/set/browser/timezone'
        } else {
            this.uri = ' https://eventpoint.app/set/browser/timezone'
        }

        axios.post(this.uri, {
            timezone: this.timezone
        });
    }
}