import app from 'flarum/forum/app';
import { hookLikeButton } from './hookLikeButton';
import { overrideTagComposer } from './overrideTagComposer';
app.initializers.add('nodeloc/nl-patchs', () => {
  hookLikeButton();
  overrideTagComposer();

  // 修复自动续期的装扮通知
  app.store.models.decorationStorePurchase.prototype.purchase_count = function () { return this.attribute("item_count") };
});
