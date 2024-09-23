import app from 'flarum/admin/app';

app.initializers.add('nodeloc/nl-patchs', () => {
  app.extensionData
    .for('nodeloc-nl-patchs')
    .registerPermission(
      {
        icon: 'fas fa-star',
        label: app.translator.trans('nodeloc-nl-patchs.admin.permissions.followIgnoreBlocks'),
        permission: 'followIgnoreBlocks',
      },
      "moderate"
    )
});
