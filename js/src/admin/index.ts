import app from 'flarum/admin/app';

app.initializers.add('nodeloc/nl-patchs', () => {
  app.extensionData
    .for('nodeloc-nl-patchs')

    .registerSetting({
      label: app.translator.trans('nodeloc-nl-patchs.admin.settings.lounge_id'),
      setting: 'nodeloc-nl-patchs.lounge_id',
      type: 'number',
    })
    .registerSetting({
      label: app.translator.trans('nodeloc-nl-patchs.admin.settings.lounge_allow'),
      setting: 'nodeloc-nl-patchs.lounge_allow',
      type: 'number',
    })
    .registerPermission(
      {
        icon: 'fas fa-faucet',
        label: app.translator.trans('nodeloc-nl-patchs.admin.permissions.ignoreLoungeLimit'),
        permission: 'ignoreLoungeLimit',
      },
      "moderate"
    )
    .registerPermission(
      {
        icon: 'fas fa-star',
        label: app.translator.trans('nodeloc-nl-patchs.admin.permissions.followIgnoreBlocks'),
        permission: 'followIgnoreBlocks',
      },
      "moderate"
    )
    .registerPermission(
      {
        icon: 'fas fa-star',
        label: app.translator.trans('nodeloc-nl-patchs.admin.permissions.use_nodeloc_events'),
        permission: 'use_nodeloc_events',
      },
      "moderate"
    )
});
