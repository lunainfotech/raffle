/**
 * Payment Authorization JavaScript
 * Handles payment authorization checks and user experience
 */

class PaymentAuthorization {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.checkAuthorizationStatus();
    }

    bindEvents() {
        // Check authorization on page load
        document.addEventListener('DOMContentLoaded', () => {
            this.checkAuthorizationStatus();
        });

        // Bind payment verification buttons
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-payment-verify]')) {
                this.verifyPayment(e.target.dataset.paymentId);
            }
            
            if (e.target.matches('[data-payment-cancel]')) {
                this.cancelPayment(e.target.dataset.paymentId);
            }
            
            if (e.target.matches('[data-payment-resend]')) {
                this.resendVerification(e.target.dataset.paymentId);
            }
        });
    }

    /**
     * Check if user has payment authorization
     */
    async checkAuthorizationStatus() {
        try {
            const response = await fetch('/payment/check', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                }
            });

            const data = await response.json();

            if (data.authorized) {
                this.showAuthorizedStatus();
            } else {
                this.showUnauthorizedStatus();
            }

            return data;
        } catch (error) {
            console.error('Error checking authorization:', error);
            return { authorized: false, message: 'Error checking authorization' };
        }
    }

    /**
     * Verify a payment
     */
    async verifyPayment(paymentId) {
        if (!confirm('Are you sure you want to verify this payment?')) {
            return;
        }

        try {
            const response = await fetch('/payment/verify', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    payment_intent_id: paymentId,
                    member_id: this.getMemberId()
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccessMessage('Payment verified successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showErrorMessage(data.message || 'Payment verification failed');
            }
        } catch (error) {
            console.error('Error verifying payment:', error);
            this.showErrorMessage('Error verifying payment');
        }
    }

    /**
     * Cancel a payment
     */
    async cancelPayment(paymentId) {
        if (!confirm('Are you sure you want to cancel this payment? This action cannot be undone.')) {
            return;
        }

        try {
            const response = await fetch('/payment/cancel', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    member_id: paymentId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccessMessage('Payment cancelled successfully!');
                setTimeout(() => location.reload(), 1500);
            } else {
                this.showErrorMessage(data.message || 'Failed to cancel payment');
            }
        } catch (error) {
            console.error('Error cancelling payment:', error);
            this.showErrorMessage('Error cancelling payment');
        }
    }

    /**
     * Resend payment verification
     */
    async resendVerification(paymentId) {
        if (!confirm('Are you sure you want to resend the payment verification?')) {
            return;
        }

        try {
            const response = await fetch('/payment/resend-verification', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCsrfToken()
                },
                body: JSON.stringify({
                    member_id: paymentId
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showSuccessMessage('Payment verification email sent successfully!');
            } else {
                this.showErrorMessage(data.message || 'Failed to send verification email');
            }
        } catch (error) {
            console.error('Error resending verification:', error);
            this.showErrorMessage('Error sending verification email');
        }
    }

    /**
     * Show authorized status
     */
    showAuthorizedStatus() {
        const statusElement = document.getElementById('payment-status');
        if (statusElement) {
            statusElement.innerHTML = `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-green-800 font-medium">Payment Authorized</span>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Show unauthorized status
     */
    showUnauthorizedStatus() {
        const statusElement = document.getElementById('payment-status');
        if (statusElement) {
            statusElement.innerHTML = `
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-yellow-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-yellow-800 font-medium">Payment Authorization Required</span>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Show success message
     */
    showSuccessMessage(message) {
        this.showNotification(message, 'success');
    }

    /**
     * Show error message
     */
    showErrorMessage(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    /**
     * Get CSRF token
     */
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    /**
     * Get member ID from data attribute or form
     */
    getMemberId() {
        const memberIdElement = document.querySelector('[data-member-id]');
        return memberIdElement ? memberIdElement.dataset.memberId : null;
    }
}

// Initialize payment authorization
document.addEventListener('DOMContentLoaded', () => {
    new PaymentAuthorization();
});

// Export for use in other scripts
window.PaymentAuthorization = PaymentAuthorization; 