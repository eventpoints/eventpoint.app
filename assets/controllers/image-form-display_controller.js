import {Controller} from '@hotwired/stimulus';
import {MasonryGrid, JustifiedGrid, FrameGrid, PackingGrid} from "@egjs/grid";

export default class extends Controller {

    static values = {
        image: String,
    };
    static targets = ['displayImage'];

    connect() {
        if (this.hasDisplayImageTarget && this.hasImageValue) {
            this.displayImageTarget.src = this.imageValue;
        }
    }

    load(event) {
        const file = event.target.files[0];

        if (file && this.hasDisplayImageTarget) {
            const reader = new FileReader();
            reader.onload = (e) => {
                this.displayImageTarget.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }


}
