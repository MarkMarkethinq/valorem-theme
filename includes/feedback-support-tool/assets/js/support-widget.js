/**
 * Support Widget
 * Een interactieve support widget met chatbot functionaliteit
 */

(function($) {
    'use strict';

    class SupportWidget {
        constructor() {
            this.widget = $('#support-widget');
            this.container = this.widget.find('.support-container');
            this.messages = this.widget.find('.support-messages');
            this.form = this.widget.find('.support-form');
            this.input = this.widget.find('.support-input');
            this.suggestions = this.widget.find('.support-suggestions');
            
            this.init();
        }

        /**
         * Initialize the widget
         */
        init() {
            this.bindEvents();
            this.scrollToBottom();
        }

        /**
         * Bind event listeners
         */
        bindEvents() {
            // Toggle widget
            this.widget.find('.support-toggle').on('click', () => this.toggleWidget());
            this.widget.find('.support-close').on('click', () => this.closeWidget());

            // Handle form submission
            this.form.on('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });

            // Handle suggestion clicks
            this.suggestions.find('.suggestion-btn').on('click', (e) => {
                const message = $(e.target).data('message');
                this.input.val(message);
                this.handleSubmit();
            });

            // Close widget when clicking outside
            $(document).on('click', (e) => {
                if (!this.widget.is(e.target) && this.widget.has(e.target).length === 0) {
                    this.closeWidget();
                }
            });

            // Handle escape key
            $(document).on('keyup', (e) => {
                if (e.key === 'Escape') {
                    this.closeWidget();
                }
            });
        }

        /**
         * Toggle widget visibility
         */
        toggleWidget() {
            this.container.toggleClass('active');
            if (this.container.hasClass('active')) {
                this.input.focus();
                this.scrollToBottom();
            }
        }

        /**
         * Close widget
         */
        closeWidget() {
            this.container.removeClass('active');
        }

        /**
         * Handle form submission
         */
        async handleSubmit() {
            const message = this.input.val().trim();
            if (!message) return;

            // Clear input
            this.input.val('');

            // Add user message
            this.addMessage(message, 'user');

            // Disable input during request
            this.input.prop('disabled', true);

            try {
                const response = await this.sendMessage(message);
                if (response.success && response.data && response.data.response) {
                    this.addMessage(response.data.response, 'bot');
                } else {
                    const errorMessage = response.data || supportToolSettings.strings.error;
                    this.addMessage(errorMessage, 'bot error');
                    console.error('Server response error:', response);
                }
            } catch (error) {
                console.error('Request error:', error);
                this.addMessage(supportToolSettings.strings.error, 'bot error');
            }

            // Re-enable input
            this.input.prop('disabled', false);
            this.input.focus();
        }

        /**
         * Send message to server
         * @param {string} message 
         * @returns {Promise}
         */
        async sendMessage(message) {
            const formData = new FormData();
            formData.append('action', 'submit_support_request');
            formData.append('nonce', supportToolSettings.nonce);
            formData.append('message', message);

            const response = await $.ajax({
                url: supportToolSettings.ajaxurl,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false
            });

            return response;
        }

        /**
         * Add message to chat
         * @param {string} message 
         * @param {string} type 
         */
        addMessage(message, type = 'bot') {
            const $message = $('<div>', {
                class: `message ${type}-message`,
                text: message
            });

            this.messages.append($message);
            this.scrollToBottom();
        }

        /**
         * Scroll messages to bottom
         */
        scrollToBottom() {
            this.messages.scrollTop(this.messages[0].scrollHeight);
        }
    }

    // Initialize widget when document is ready
    $(document).ready(() => {
        window.supportWidget = new SupportWidget();
    });

})(jQuery); 