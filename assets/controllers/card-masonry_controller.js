import { Controller } from '@hotwired/stimulus';
import { MasonryGrid } from "@egjs/grid";

export default class extends Controller {
    connect() {
        const grid = new MasonryGrid(this.element, {
            defaultDirection: "start",
            gap: 10,
            align: "start",
            column: 2,
            columnSize: 200,
            columnSizeRatio: 0,
        });

        grid.renderItems();
    }
}
