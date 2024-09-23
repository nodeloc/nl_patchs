import app from "flarum/forum/app";
import Modal, { IInternalModalAttrs } from 'flarum/common/components/Modal';
import Button from 'flarum/common/components/Button';

export default class confirmModal extends Modal<{
  confirm: () => void,
  cancel: () => void,
  title: string,
  desc: string,
} & IInternalModalAttrs> {
  confirmed: boolean = false;
  static async create(title: string, desc: string) {
    return new Promise((resolve, reject) => {
      app.modal.show(confirmModal, {
        confirm: resolve,
        cancel: reject,
        title,
        desc
      });
    });
  }
  className() {
    return 'Modal Modal--small';
  }
  title() {
    return this.attrs.title;
  }
  onbeforeremove(vnode: any): Promise<void> | void {
    if (!this.confirmed) {
      this.confirmed = true;
      this.attrs.cancel();
    }
    return super.onbeforeremove(vnode);
  }

  content() {
    return (
      <div className="Modal-body">
        <p>{this.attrs.desc}</p>
        <div className="Form-group">
          <Button className="Button Button--primary Button--block" onclick={this.confirm.bind(this)}>
            {app.translator.trans('nodeloc-nl-patchs.forum.modal.confirm')}
          </Button>
        </div>
        <div className="Form-group">
          <Button className="Button Button--secondary Button--block" onclick={this.cancel.bind(this)}>
            {app.translator.trans('nodeloc-nl-patchs.forum.modal.cancel')}
          </Button>
        </div>
      </div>
    );
  }

  confirm() {
    if (!this.confirmed) {
      this.confirmed = true;
      this.attrs.confirm();
      app.modal.close();
    }
  }

  cancel() {
    if (!this.confirmed) {
      this.confirmed = true;
      this.attrs.cancel();
      app.modal.close();
    }
  }
}
