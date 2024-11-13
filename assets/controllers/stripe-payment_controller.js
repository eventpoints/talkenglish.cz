import { Controller } from "@hotwired/stimulus";
import { loadStripe } from "@stripe/stripe-js";

export default class extends Controller {
    static targets = ["paymentForm", "paymentElement", "paymentMessage", "submit", "spinner", "buttonText", "dpmCheckerLink"];
    static values = {
        publicKey: String,
        path: String,
        csrfToken: String,
        returnPath: String
    }

    async connect() {
        this.isLoading = false;
        this.stripe = await loadStripe(this.publicKeyValue);
        this.elements = null;
        await this.initializePayment();
    }

    async initializePayment() {
        const response = await fetch(this.pathValue, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": this.csrfTokenValue
            },
        });

        if (!response.ok) {
            this.showMessage("Failed to initialize payment. Please try again.");
            return;
        }

        const { clientSecret, dpmCheckerLink } = await response.json();
        this.elements = this.stripe.elements({ clientSecret: clientSecret });

        const paymentElementOptions = { layout: "tabs" };
        const paymentElement = this.elements.create("payment", paymentElementOptions);
        paymentElement.mount(this.paymentElementTarget);
        this.setLoading(false);

        if (dpmCheckerLink) {
            this.dpmCheckerLinkTarget.href = dpmCheckerLink;
        }
    }

    async handleSubmit(event) {
        event.preventDefault();
        this.setLoading(true);

        const { error } = await this.stripe.confirmPayment({
            elements: this.elements,
            confirmParams: {
                return_url: this.returnPathValue,
            },
        });

        if (error) {
            this.showMessage(error.message || "An unexpected error occurred.");
        }

        this.setLoading(false);
    }

    showMessage(messageText) {
        this.paymentMessageTarget.classList.remove("hidden");
        this.paymentMessageTarget.textContent = messageText;

        setTimeout(() => {
            this.paymentMessageTarget.classList.add("hidden");
            this.paymentMessageTarget.textContent = "";
        }, 4000);
    }

    setLoading(isLoading) {
        this.isLoading = isLoading;
        this.updateLoadingState();
    }

    updateLoadingState() {
        this.submitTarget.disabled = this.isLoading;
        this.spinnerTarget.classList.toggle("hidden", !this.isLoading);
        this.buttonTextTarget.classList.toggle("hidden", this.isLoading);
    }
}