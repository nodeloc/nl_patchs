import app from 'flarum/forum/app';
import { extend, override } from 'flarum/common/extend';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import getSelectableTags from 'flarum/tags/forum/utils/getSelectableTags';
import TagDiscussionModal from 'flarum/tags/forum/components/TagDiscussionModal';
import Tag from 'flarum/tags/common/models/Tag';
export function overrideTagComposer() {
    extend(DiscussionComposer.prototype, 'oninit', () => {
        const user = app.session.user;
        if (user)
            app.store.find('users', user.id() as string);
    }
    );
    DiscussionComposer.prototype.chooseTags = function (this: DiscussionComposer) {
        let selectableTags: Tag[] = getSelectableTags(null);

        if (!selectableTags.length) return;
        if (app.forum.attribute<number>('loungeCounter') <= 0) {
            selectableTags = selectableTags.filter(tag => tag.id() != app.forum.attribute<string>('loungeId'));
        }

        app.modal.show(TagDiscussionModal, {
            selectedTags: (this.composer.fields.tags || []).slice(0),
            onsubmit: (tags: Tag[]) => {
                this.composer.fields.tags = tags;
                this.$('textarea').focus();
            },
        });
    };
}