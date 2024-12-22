import app from 'flarum/forum/app';
import { hookLikeButton } from './hookLikeButton';
import { override } from 'flarum/common/extend';
import { hookSearchBox } from './hookSearchBox';
app.initializers.add('nodeloc/nl-patchs', () => {
  hookLikeButton();

  // 修复自动续期的装扮通知
  app.store.models.decorationStorePurchase.prototype.purchase_count = function () { return this.attribute("item_count") };

  // 修复
  override(app.notificationComponents.postLiked.prototype, 'excerpt', function (o) {
    if (this.attrs.notification.subject()) {
      if ((this.attrs.notification.subject() as any).contentPlain()) {
        return o();
      }
    }
    return '';
  });
  hookSearchBox();
});
