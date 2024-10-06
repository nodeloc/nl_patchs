import app from 'flarum/forum/app';
import { hookLikeButton } from './hookLikeButton';
import { overrideTagComposer } from './overrideTagComposer';
app.initializers.add('nodeloc/nl-patchs', () => {
  hookLikeButton();
  overrideTagComposer();
});
