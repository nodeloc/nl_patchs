import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend'
import Button from 'flarum/common/components/Button';
import CommentPost from 'flarum/forum/components/CommentPost';
import confirmModal from './components/confirmModal';

export function hookLikeButton() {
    extend(CommentPost.prototype, 'actionItems', function (items) {
        if(!items.has('like')) return;
        const likesComponent = items.get('like');
        items.remove('like');

        // const text = likesComponent && likesComponent.children && likesComponent.children[0];
        // let unlikeTest = app.translator.trans('flarum-likes.forum.post.unlike_link');
        // if(Array.isArray(unlikeTest)) unlikeTest = unlikeTest[0];

        // if (text === unlikeTest) {
        //     likesComponent.attrs.onclick = makeUnlikeAlert(likesComponent.attrs.onclick);
        // } else {
        //     likesComponent.attrs.onclick = makeLikeAlert(likesComponent.attrs.onclick);
        // }

        items.add('like', likesComponent, 100);

    });

}

function makeLikeAlert(original: Function) {
    return (e: any) => {
        confirmModal.create(
            app.translator.trans('nodeloc-nl-patchs.forum.like.on_title')+"",
            app.translator.trans('nodeloc-nl-patchs.forum.like.on_desc')+""
        ).then(()=>original(e)).catch(()=>{});
    }
}
function makeUnlikeAlert(original: Function) {
    return (e: any) => {
        confirmModal.create(
            app.translator.trans('nodeloc-nl-patchs.forum.like.off_title')+"",
            app.translator.trans('nodeloc-nl-patchs.forum.like.off_desc')+""
        ).then(()=>original(e)).catch(()=>{});
    }
}