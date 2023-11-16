import { Controller } from '@hotwired/stimulus';
import { MasonryGrid, JustifiedGrid, FrameGrid, PackingGrid } from "@egjs/grid";

export default class extends Controller {
    connect() {
        const grid = new FrameGrid(this.element, {
            gap: 10,
            frame: [[1,1,2,2],[3,3,2,2],[4,4,4,5]],
            rectSize: 0,
            useFrameFill: true,
            useResizeObserver: true,
            observeChildren: true,
        });

        grid.renderItems();
    }
}
