import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['search', 'item', 'empty'];
    static values = {
        minChars: { type: Number, default: 1 },
    };

    connect() {
        this.filter();
    }

    filter() {
        if (!this.hasSearchTarget) return;

        const q = this.normalize(this.searchTarget.value).trim();
        const active = q.length >= this.minCharsValue;

        let visible = 0;

        for (const el of this.itemTargets) {
            const txt = this.normalize(el.textContent);
            const show = !active || txt.includes(q);

            el.classList.toggle('hidden', !show);

            if (show) visible++;
        }

        if (this.hasEmptyTarget) {
            this.emptyTarget.classList.toggle('hidden', visible !== 0);
        }
    }

    normalize(value) {
        return (value ?? '')
                .toString()
                .toLowerCase()
                .normalize('NFKD');
    }
}