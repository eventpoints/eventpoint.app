import '@symfony/autoimport';
import {startStimulusApp} from '@symfony/stimulus-bridge';
import PasswordVisibility from 'stimulus-password-visibility'
import Lightbox from 'stimulus-lightbox'
import Calendar from 'stimulus-calendar'

export const app = startStimulusApp(require.context(
    './controllers',
    true,
    /\.[jt]sx?$/
));
app.register('password-visibility', PasswordVisibility)
app.register('lightbox', Lightbox)
app.register('calendar', Calendar)