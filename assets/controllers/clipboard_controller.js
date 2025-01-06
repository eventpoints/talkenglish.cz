import Clipboard from "@stimulus-components/clipboard"
import {Toast} from "bootstrap"

export default class extends Clipboard {
    connect() {
        super.connect()
        console.log("Do what you want here.")
    }

    copied() {
        super.copied();

        const toastElement = document.createElement('div');
        toastElement.className = 'toast position-fixed bottom-0 start-0 end-0 mx-auto mb-3  text-bg-success border-0';
        toastElement.setAttribute('role', 'alert');
        toastElement.setAttribute('aria-live', 'assertive');
        toastElement.setAttribute('aria-atomic', 'true');
        toastElement.innerHTML = `<div class="toast-body text-center">Copied to clipboard</div>`;
        document.body.appendChild(toastElement);

        const toast = new Toast(toastElement);
        toast.show();
    }
}