import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import HeaderSecondary from 'flarum/forum/components/HeaderSecondary';
import extractText from 'flarum/common/utils/extractText';

export function hookSearchBox() {
    if (!(app.session.user)) {

        const searchLabel = extractText(app.translator.trans('core.forum.header.search_placeholder'));
        extend(HeaderSecondary.prototype, 'items', function (items) {
            items.add('search', <div
                role="search"
                aria-label={app.translator.trans('core.forum.header.search_role_label')}
                className='Search'
            >
                <form action="https://www.google.com/search" method="get">
                    <div className="Search-input">
                        <input
                            aria-label={searchLabel}
                            className="FormControl"
                            type="search"
                            name="q"
                            placeholder={searchLabel}
                        />
                        <button
                            className="Search-clear Button Button--icon Button--link"
                            aria-label={app.translator.trans('core.forum.header.search_clear_button_accessible_label')}
                            type="submit"
                        >
                            <i className='fas fa-times-circle'></i>
                        </button>
                    </div>
                </form>
            </div>)
        })
    }
}