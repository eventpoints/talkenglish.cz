import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    connect() {
        setTimeout(() => {
            this.element.classList.add("slide-up");
            setTimeout(() => this.element.remove(), 500); 
        }, 2000);
    }
}
