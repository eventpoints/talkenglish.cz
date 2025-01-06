import {startStimulusApp} from '@symfony/stimulus-bundle';

import CharacterCounter from 'stimulus-character-counter'
import PasswordVisibility from 'stimulus-password-visibility'
import TextareaAutogrow from 'stimulus-textarea-autogrow'
import ReadMore from '@stimulus-components/read-more'
import Sound from '@stimulus-components/sound'


export const app = startStimulusApp();

app.register('character-counter', CharacterCounter)
app.register('textarea-autogrow', TextareaAutogrow)
app.register("password-visibility", PasswordVisibility)
app.register('read-more', ReadMore)
app.register('sound', Sound)