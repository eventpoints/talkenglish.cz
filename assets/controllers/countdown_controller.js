import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["display"]
    static values = { seconds: Number }

    connect() {
        this.remainingTime = this.secondsValue
        this.updateDisplay()
        this.startCountdown()
    }

    disconnect() {
        clearInterval(this.timer)
    }

    startCountdown() {
        this.timer = setInterval(() => {
            if (this.remainingTime > 0) {
                this.remainingTime -= 1
                this.updateDisplay()
            } else {
                clearInterval(this.timer)
                this.refreshPageAfterTimeout()
            }
        }, 1000)
    }

    updateDisplay() {
        const minutes = Math.floor(this.remainingTime / 60)
        const seconds = this.remainingTime % 60
        this.displayTarget.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`
    }

    refreshPageAfterTimeout() {
        setTimeout(() => {
            window.location.reload()
        }, 1000)
    }
}
