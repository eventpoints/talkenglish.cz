import {Controller} from "@hotwired/stimulus";
import confetti from "https://esm.run/canvas-confetti@1";

export default class extends Controller {

    static values = {
        score: Number
    }

    connect() {
        const score = this.scoreValue
        console.log(score)
        if (score > 70) {
            confetti({
                particleCount: 250,
                spread: 120,
            });
        }
    }
}
