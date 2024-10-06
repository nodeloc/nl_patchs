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
    override(TagDiscussionModal.prototype, "oninit", function (this: TagDiscussionModal, org, ...args) {
        org.apply(this, args);
        if (this instanceof TagDiscussionModal) {
            override(this.attrs as any, "selectableTags", (function (org: (s: Tag[]) => Tag[], selectableTags: Tag[]) {
                selectableTags = org(selectableTags);
                if (app.forum.attribute<number>('loungeCounter') <= 0) {
                    selectableTags = selectableTags.filter(tag => tag.id() != app.forum.attribute<string>('loungeId'));
                }
                return selectableTags;
            }) as any);
        }
    })
}