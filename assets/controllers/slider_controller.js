import { Controller } from '@hotwired/stimulus';
import '../styles/slider.css'
export default class extends Controller {
    static targets = ["slider", "slide"];

    connect() {
        this.slideWidth = this.slideTargets[0].offsetWidth;
    }

    next() {
        this.sliderTarget.scrollLeft += this.slideWidth;
    }

    previous() {
        this.sliderTarget.scrollLeft -= this.slideWidth;
    }
}
