import {startStimulusApp} from '@symfony/stimulus-bridge';
import PasswordVisibility from 'stimulus-password-visibility'
import TextareaAutogrow from 'stimulus-textarea-autogrow'
import Clipboard from 'stimulus-clipboard'
import Lightbox from 'stimulus-lightbox'
import Calendar from 'stimulus-calendar'
import SsrAutocompleteController from '../vendor/kerrialnewham/autocomplete/assets/controllers/ssr_autocomplete_controller.js';

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));
app.register('password-visibility', PasswordVisibility)
app.register('textarea-autogrow', TextareaAutogrow)
app.register('lightbox', Lightbox)
app.register('calendar', Calendar)
app.register('clipboard', Clipboard)
app.register('kerrialnewham--autocomplete--ssr-autocomplete', SsrAutocompleteController);