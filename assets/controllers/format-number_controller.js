import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ["input"]

    connect() {
        this.format(); // Initial formatting in case the field already has a value
    }

    format() {
        const input = this.inputTarget;
        let value = input.value.replace(/,/g, ''); // Remove existing commas
        console.log(value)
        if (!isNaN(value) && value !== '') {
            input.value = new Intl.NumberFormat('en-US').format(value);
        }
    }

    update() {
        this.format();
    }
}
