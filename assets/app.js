import './bootstrap.js';
import {Tooltip, Toast, Tab, Modal} from 'bootstrap'
import 'bootstrap';
import 'chartjs-adapter-date-fns';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';
import TimezoneService from './TimezoneService'


const timezoneService = new TimezoneService();
timezoneService.configureTimezone();

document.addEventListener('turbo:load', function (e) {
    let tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new Tooltip(tooltipTriggerEl)
    });
});


document.addEventListener('turbo:load', function () {
    const tabLinks = document.querySelectorAll('a[role="tab"]');
    for (const tabLink of tabLinks) {
        tabLink.addEventListener('click', function (event) {
            event.preventDefault();
            history.pushState({}, 'Page title', tabLink.href);
        });
    }

    const selectedTab = window.location.hash;
    console.log(`a[href='${selectedTab}}']`)
    let tabTrigger = new Tab(document.querySelector(`a[href='${selectedTab}']`));
    tabTrigger.show();
});


document.addEventListener('turbo:load', function () {

    const modals = document.querySelectorAll('a[data-bs-toggle="modal"]');


    modals.forEach((modal) => {
        let modalLinkBootStrapTarget = modal.getAttribute('data-bs-target').substring(1)
        let hash = window.location.hash.substring(1)

        console.log(modalLinkBootStrapTarget, hash)
        if (modalLinkBootStrapTarget === hash) {
            let modalEl = document.getElementById(modalLinkBootStrapTarget)
            new Modal(modalEl).show()
        }
    });

})
;


document.addEventListener('turbo:load', function (e) {
    const toastElList = document.querySelectorAll('.toast')
    const toastList = [...toastElList].map(toastEl => new Toast(toastEl, {}));
});