import app from 'flarum/forum/app';
import { extend, override } from 'flarum/common/extend';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import getSelectableTags from 'flarum/tags/forum/utils/getSelectableTags';
import TagDiscussionModal from 'flarum/tags/forum/components/TagDiscussionModal';
import Tag from 'flarum/tags/common/models/Tag';
export function overrideTagComposer() {
    extend(DiscussionComposer.prototype, 'oninit', () => {
        app.request({ method: 'GET', url: app.forum.attribute('apiUrl') + '/nodeloc-lounge' }).then((result: any) => app.forum.pushAttributes(result));
    });

    DiscussionComposer.prototype.chooseTags = function (this: DiscussionComposer) {
        let selectableTags: Tag[] = getSelectableTags(null);

        if (!selectableTags.length) return;
        if (app.forum.attribute<number>('loungeCounter') <= 0) {
            selectableTags = selectableTags.filter(tag => tag.id() != app.forum.attribute<string>('loungeId'));
        }

        app.modal.show(TagDiscussionModal, {
            selectedTags: (this.composer.fields.tags || []).slice(0),
            selectableTags: () => selectableTags,
            onsubmit: (tags: Tag[]) => {
                this.composer.fields.tags = tags;
                this.$('textarea').focus();
            },
        });
    };
}