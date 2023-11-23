import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.element.classList.remove('light_theme')
        this.element.classList.remove('dark_theme')
        this.element.classList.add('dark_theme')
    }
    changeTheme(event) {

    }
}