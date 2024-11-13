import app from 'flarum/forum/app';
import { extend, override } from 'flarum/common/extend';
import DiscussionComposer from 'flarum/forum/components/DiscussionComposer';
import getSelectableTags from 'flarum/tags/forum/utils/getSelectableTags';
import TagDiscussionModal from 'flarum/tags/forum/components/TagDiscussionModal';
import Tag from 'flarum/tags/common/models/Tag';
import extractText from 'flarum/common/utils/extractText';
export function overrideTagComposer() {
    extend(DiscussionComposer.prototype, 'oncreate', function () {
        // @ts-ignore
        this.shownShareTip = false;
        setTimeout((t: any) => {
            let selectableTags: Tag[] = getSelectableTags(null);
            const shareTag = selectableTags.find((tag) => tag.id() == app.forum.attribute("nodeloc-nl-patchs.share_id"));
            if (shareTag && t.composer.fields.tags && t.composer.fields.tags.find((t: Tag) => t.id() == shareTag?.id())) {
                // @ts-ignore
                if (!this.shownShareTip) {
                    // @ts-ignore
                    this.shownShareTip = true;
                    alert(shareTag.attribute("template"));

                };
            }
        }, 1, this);
    });
    DiscussionComposer.prototype.chooseTags = function (this: DiscussionComposer) {
        let selectableTags: Tag[] = getSelectableTags(null);
        const shareTag = selectableTags.find((tag) => tag.id() == app.forum.attribute("nodeloc-nl-patchs.share_id"));

        app.modal.show(TagDiscussionModal, {
            selectedTags: (this.composer.fields.tags || []).slice(0),
            onsubmit: (tags: Tag[]) => {
                this.composer.fields.tags = tags;
                if (shareTag && tags.find(t => t.id() == shareTag?.id())) {
                    // @ts-ignore
                    if (!this.shownShareTip) {
                        // @ts-ignore
                        this.shownShareTip = true;
                        alert(shareTag.attribute("template"));
                    };
                }
                this.$('textarea').focus();
            },
        });
    };
}
