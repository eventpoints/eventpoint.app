import { Controller } from '@hotwired/stimulus';
import '../styles/slider.css'
export default class extends Controller {
    static targets = ["slider", "slide"];

    initialize() {
        this.isDragging = false;
        this.startPosition = 0;
        this.currentTranslate = 0;
        this.prevTranslate = 0;
    }

    connect() {
        this.slider = this.sliderTarget;
        this.slides = this.slideTargets;
    }

    startTouch(event) {
        if (event.type === "touchstart") {
            this.startPosition = event.touches[0].clientX;
        } else {
            this.startPosition = event.clientX;
            event.preventDefault();
        }
        this.isDragging = true;
        requestAnimationFrame(() => this.animation());
    }

    endTouch() {
        this.isDragging = false;
        const movedBy = this.currentTranslate - this.prevTranslate;
        if (movedBy < -100 && this.currentTranslate !== 0) {
            this.navigate(1); // move to the next slide
        } else if (movedBy > 100 && this.currentTranslate !== -(this.slides.length - 1) * 100) {
            this.navigate(-1); // move to the previous slide
        } else {
            this.navigate(0); // stay in the current slide
        }
    }

    moveTouch(event) {
        if (this.isDragging) {
            const currentPosition = event.type === "touchmove" ? event.touches[0].clientX : event.clientX;
            this.currentTranslate = this.prevTranslate + currentPosition - this.startPosition;
        }
    }

    navigate(direction) {
        const slideWidth = this.slides[0].offsetWidth;
        this.prevTranslate = this.currentTranslate;
        if (direction === 1 && this.currentTranslate !== 0) {
            this.currentTranslate += slideWidth;
        } else if (direction === -1 && this.currentTranslate !== -(this.slides.length - 1) * slideWidth) {
            this.currentTranslate -= slideWidth;
        }
        this.slider.style.transform = `translateX(${this.currentTranslate}px)`;
    }

    animation() {
        if (this.isDragging) {
            requestAnimationFrame(() => this.animation());
        }
    }

    // Stimulus Actions
    start(event) {
        this.startTouch(event);
    }

    end() {
        this.endTouch();
    }

    move(event) {
        this.moveTouch(event);
    }

    // New Methods for Next and Previous
    next() {
        this.navigate(-1);
    }

    previous() {
        this.navigate(1);
    }
}
