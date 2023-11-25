import {startStimulusApp} from '@symfony/stimulus-bridge';
import PasswordVisibility from 'stimulus-password-visibility'
// import TextareaAutogrow from 'stimulus-textarea-autogrow'
// import ReadMore from 'stimulus-read-more'
// import Clipboard from 'stimulus-clipboard'
import Lightbox from 'stimulus-lightbox'
// import Calendar from 'stimulus-calendar'

export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\\.[jt]sx?$/
));
app.register('password-visibility', PasswordVisibility)
// app.register('textarea-autogrow', TextareaAutogrow)
app.register('lightbox', Lightbox)
// app.register('calendar', Calendar)
// app.register('read-more', ReadMore)
// app.register('clipboard', Clipboard)