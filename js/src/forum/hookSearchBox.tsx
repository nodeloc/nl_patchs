import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import HeaderSecondary from 'flarum/forum/components/HeaderSecondary';
import extractText from 'flarum/common/utils/extractText';
import IndexPage from 'flarum/components/IndexPage';
import Search from 'flarum/components/Search';
export function hookSearchBox() {
    const searchLabel = extractText(app.translator.trans('core.forum.header.search_placeholder'));
    extend(HeaderSecondary.prototype, 'items', function (items) {
        if (!(app.session?.user)) {
            items.add('search', <div
                role="search"
                aria-label={app.translator.trans('core.forum.header.search_role_label')}
                className='Search'
            >
                <form action="https://www.google.com/search" method="get" target="_blank">
                    <div className="Search-input">
                        <input name="sitesearch" type="hidden" value="nodeloc.com" />
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
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>, 1000);
        }
    })

  extend(IndexPage.prototype, 'viewItems', function (items) {
    if (!(app.session?.user)) {
      items.add('search', <div
        role="search"
        aria-label={app.translator.trans('core.forum.header.search_role_label')}
        className='Search'
      >
        <form action="https://www.google.com/search" method="get" target="_blank">
          <div className="Search-input">
            <input name="sitesearch" type="hidden" value="nodeloc.com" />
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
              <i class="fas fa-arrow-right"></i>
            </button>
          </div>
        </form>
      </div>, -100);
    }else{
      items.add('search', Search.component({
        state: app.search,
      }), -100);
    }
  });
}
