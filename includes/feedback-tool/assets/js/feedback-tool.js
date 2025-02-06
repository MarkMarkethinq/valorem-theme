/**
 * Feedback Tool
 * Een interactieve tool voor het verzamelen van feedback op website-elementen
 */

(function($) {
    'use strict';

    // Configuratie object voor de feedback tool
    const config = {
        selectors: {
            tool: '#feedback-tool',
            toggle: '.feedback-button',
            form: '#feedback-form',
            feedbackType: '#feedback-type',
            comment: '#feedback-comment',
            elementType: '.element-type',
            overview: '.feedback-list',
            attachments: '#feedback-attachments',
            selectedFiles: '.selected-files'
        },
        classes: {
            feedbackMode: 'feedback-mode',
            active: 'active',
            highlight: 'feedback-highlight'
        }
    };

    // Object met feedback opties per element type
    const feedbackOptions = {
        img: [
            { value: 'replace-logo', label: 'Logo vervangen door tekst' },
            { value: 'update-image', label: 'Afbeelding vervangen' },
            { value: 'remove-image', label: 'Afbeelding verwijderen' }
        ],
        div: [
            { value: 'edit-content', label: 'Inhoud wijzigen' },
            { value: 'remove-section', label: 'Sectie verwijderen' }
        ],
        'service-item': [
            { value: 'edit-service', label: 'Dienst wijzigen' },
            { value: 'add-service', label: 'Nieuwe dienst toevoegen' },
            { value: 'remove-service', label: 'Dienst verwijderen' }
        ],
        default: [
            { value: 'edit-text', label: 'Tekst wijzigen' },
            { value: 'remove-element', label: 'Element verwijderen' }
        ]
    };

    class FeedbackTool {
        constructor() {
            this.isActive = false;
            this.selectedElement = null;
            this.init();
        }

        /**
         * Initialiseer de feedback tool
         */
        init() {
            this.initializeDialog();
            this.bindEvents();
            this.renderFeedbackList();
        }

        /**
         * Initialiseer de jQuery UI dialog
         */
        initializeDialog() {
            $(config.selectors.form).dialog({
                autoOpen: false,
                modal: true,
                width: 500,
                close: () => this.resetForm()
            });
        }

        /**
         * Render de feedback lijst
         */
        renderFeedbackList() {
            const $list = $(config.selectors.overview);
            const $overview = $('.feedback-overview');
            $list.empty();

            if (feedbackToolSettings.feedback.length === 0) {
                $overview.hide();
                return;
            } else {
                $overview.show();
            }

            feedbackToolSettings.feedback.forEach(feedback => {
                const isCompleted = feedback.status === 'completed';
                const deleteButton = isCompleted ? '' : `
                    <button class="delete-feedback" data-id="${feedback.id}" title="Verwijder feedback">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                `;

                $list.append(`
                    <div class="feedback-item">
                        <div class="feedback-header">
                            <div class="feedback-status ${feedback.status}">
                                ${this.getStatusText(feedback.status)}
                            </div>
                            ${deleteButton}
                        </div>
                        <div class="feedback-type">${feedback.feedbackType}</div>
                        <div class="feedback-comment">${feedback.comment}</div>
                        ${feedback.response ? `<div class="feedback-response">${feedback.response}</div>` : ''}
                    </div>
                `);
            });

            // Bind delete events
            $('.delete-feedback').on('click', (e) => {
                const id = $(e.currentTarget).data('id');
                this.deleteFeedback(id);
            });
        }

        /**
         * Vertaal status naar leesbare tekst
         * @param {string} status 
         * @returns {string}
         */
        getStatusText(status) {
            const statusTexts = {
                'pending': 'In afwachting',
                'in-progress': 'In behandeling',
                'completed': 'Afgerond',
                'rejected': 'Afgewezen'
            };
            return statusTexts[status] || status;
        }

        /**
         * Controleer of een element deel uitmaakt van de feedback tool
         * @param {HTMLElement} element 
         * @returns {boolean}
         */
        isToolElement(element) {
            const $element = $(element);
            return (
                $element.closest(config.selectors.tool).length > 0 ||
                $element.closest('.ui-dialog').length > 0 ||
                $element.closest('.ui-widget-overlay').length > 0 ||
                $element.hasClass('ui-widget-overlay') ||
                $element.hasClass('ui-dialog') ||
                $element.parents('.ui-dialog').length > 0
            );
        }

        /**
         * Bind event listeners
         */
        bindEvents() {
            // Toggle feedback mode
            $(config.selectors.toggle).on('click', () => this.toggleFeedbackMode());

            // Handle element clicks in feedback mode
            $('body').on('click', '*', (e) => {
                if (!this.isActive) return;
                if (this.isToolElement(e.target)) {
                    e.stopPropagation();
                    return;
                }

                e.preventDefault();
                e.stopPropagation();
                this.handleElementClick(e.target);
            });

            // Handle form submission
            $(config.selectors.form).on('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });

            // Handle cancel button
            $(config.selectors.form).find('.cancel-button').on('click', () => {
                $(config.selectors.form).dialog('close');
            });

            // Handle mouseover for element highlighting
            $('body').on('mouseover', '*', (e) => {
                if (!this.isActive) return;
                if (this.isToolElement(e.target)) return;

                this.handleElementHover(e.target);
            });

            // Handle mouseout
            $('body').on('mouseout', '*', (e) => {
                if (!this.isActive) return;
                $(e.target).removeClass(config.classes.highlight);
                $(e.target).removeAttr('data-element-info');
            });

            // Handle escape key to exit feedback mode
            $(document).on('keyup', (e) => {
                if (e.key === 'Escape' && this.isActive) {
                    this.toggleFeedbackMode();
                }
            });

            // Handle file selection
            $(config.selectors.attachments).on('change', (e) => {
                this.handleFileSelection(e.target.files);
            });

            // Handle file removal
            $(config.selectors.selectedFiles).on('click', '.remove-file', (e) => {
                const index = $(e.target).closest('.selected-file').data('index');
                this.removeFile(index);
            });
        }

        /**
         * Toggle feedback mode aan/uit
         */
        toggleFeedbackMode() {
            this.isActive = !this.isActive;
            $('body').toggleClass(config.classes.feedbackMode, this.isActive);
            $(config.selectors.toggle)
                .toggleClass(config.classes.active, this.isActive)
                .text(this.isActive ? 'Feedback Modus Afsluiten' : 'Feedback Geven');

            if (!this.isActive) {
                $(config.selectors.form).dialog('close');
            }

            // Ververs de feedback lijst bij activeren van de modus
            if (this.isActive) {
                this.renderFeedbackList();
            }
        }

        /**
         * Handle element click in feedback mode
         * @param {HTMLElement} element 
         */
        handleElementClick(element) {
            this.selectedElement = element;
            const $element = $(element);
            
            // Bepaal element type en eigenschappen
            const elementInfo = {
                type: element.tagName.toLowerCase(),
                id: $element.attr('id') || '',
                classes: $element.attr('class') || '',
                isService: $element.hasClass('service-item'),
                selector: this.getElementSelector(element)
            };

            // Update form met element informatie
            this.updateForm(elementInfo);
            $(config.selectors.form).dialog('open');
        }

        /**
         * Genereer een unieke selector voor het element
         * @param {HTMLElement} element 
         * @returns {string}
         */
        getElementSelector(element) {
            const $element = $(element);
            let selector = '';

            // Probeer eerst ID
            if (element.id) {
                return `#${element.id}`;
            }

            // Probeer dan classes (exclusief feedback tool classes)
            const className = element.className;
            if (className && typeof className === 'string') {
                const classes = className.split(/\s+/)
                    .filter(cls => !cls.startsWith('feedback-') && !cls.startsWith('ui-'))
                    .join('.');

                if (classes) {
                    selector = '.' + classes;
                }
            } else if ($element.attr('class')) {
                // Fallback naar jQuery's class attribuut
                const classes = $element.attr('class').split(/\s+/)
                    .filter(cls => !cls.startsWith('feedback-') && !cls.startsWith('ui-'))
                    .join('.');

                if (classes) {
                    selector = '.' + classes;
                }
            }

            // Als er geen classes zijn, gebruik tag met index
            if (!selector) {
                const tagName = element.tagName.toLowerCase();
                const index = Array.from(element.parentNode.children)
                    .filter(child => child.tagName.toLowerCase() === tagName)
                    .indexOf(element);
                selector = `${tagName}:nth-child(${index + 1})`;
            }

            return selector;
        }

        /**
         * Update formulier op basis van element type
         * @param {Object} elementInfo 
         */
        updateForm(elementInfo) {
            // Toon element informatie
            let elementDescription = elementInfo.type.toUpperCase();
            if (elementInfo.id) elementDescription += ` #${elementInfo.id}`;
            $(config.selectors.elementType).text(elementDescription);

            // Bepaal en vul feedback opties
            const options = this.getFeedbackOptions(elementInfo);
            const $container = $(config.selectors.feedbackType);
            $container.empty();

            // Add radio buttons
            options.forEach((option, index) => {
                const $label = $('<label>', {
                    class: 'radio-label',
                    for: `feedback-type-${index}`
                });

                const $input = $('<input>', {
                    type: 'radio',
                    name: 'feedback-type',
                    value: option.value,
                    required: true,
                    id: `feedback-type-${index}`
                });

                const $span = $('<span>', {
                    text: option.label
                });

                $label.append($input, $span);
                $container.append($label);
            });
        }

        /**
         * Bepaal feedback opties voor element type
         * @param {Object} elementInfo 
         * @returns {Array}
         */
        getFeedbackOptions(elementInfo) {
            if (elementInfo.isService) return feedbackOptions['service-item'];
            return feedbackOptions[elementInfo.type] || feedbackOptions.default;
        }

        /**
         * Handle form submission
         */
        async handleFormSubmit() {
            const formData = new FormData();
            formData.append('action', 'submit_feedback');
            formData.append('nonce', feedbackToolSettings.nonce);

            const feedbackData = {
                elementType: this.selectedElement.tagName.toLowerCase(),
                elementId: $(this.selectedElement).attr('id') || '',
                elementClasses: $(this.selectedElement).attr('class') || '',
                elementSelector: this.getElementSelector(this.selectedElement),
                feedbackType: $('input[name="feedback-type"]:checked').val(),
                comment: $(config.selectors.comment).val(),
                timestamp: new Date().toISOString()
            };

            formData.append('feedback', JSON.stringify(feedbackData));

            // Add files to FormData
            const files = $(config.selectors.attachments)[0].files;
            Array.from(files).forEach(file => {
                formData.append('attachments[]', file);
            });

            try {
                // Disable form controls
                const $form = $(config.selectors.form);
                const $submitButton = $form.find('.submit-button');
                const $inputs = $form.find('input, select, textarea, button');
                
                $inputs.prop('disabled', true);
                $submitButton.text('Bezig met verzenden...');

                const response = await $.ajax({
                    url: feedbackToolSettings.ajaxurl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });

                if (response.success) {
                    // Voeg nieuwe feedback toe aan de lijst
                    const newFeedback = {
                        id: response.data.id,
                        feedbackType: feedbackData.feedbackType,
                        comment: feedbackData.comment,
                        status: 'in-progress',
                        timestamp: feedbackData.timestamp
                    };
                    
                    // Update de lokale feedback lijst
                    feedbackToolSettings.feedback.unshift(newFeedback);
                    
                    // Ververs de weergave
                    this.renderFeedbackList();

                    // Toon succes melding
                    const $message = $('<div>', {
                        class: 'feedback-message success',
                        html: `
                            <div class="feedback-message-content">
                                <div class="feedback-message-icon">✓</div>
                                <div class="feedback-message-text">${feedbackToolSettings.strings.success}</div>
                            </div>
                        `
                    });

                    // Reset form controls
                    $inputs.prop('disabled', false);
                    $submitButton.text('Verstuur Feedback');

                    $form.find('.form-group, .form-actions').hide();
                    $form.prepend($message);

                    // Sluit dialog na 2 seconden
                    setTimeout(() => {
                        this.resetForm();
                        $form.dialog('close');
                    }, 2000);
                } else {
                    throw new Error(response.data || 'Er is een fout opgetreden');
                }
            } catch (error) {
                console.error('Error bij versturen feedback:', error);
                
                // Toon error melding
                const $message = $('<div>', {
                    class: 'feedback-message error',
                    html: `
                        <div class="feedback-message-content">
                            <div class="feedback-message-icon">✕</div>
                            <div class="feedback-message-text">${feedbackToolSettings.strings.error}</div>
                        </div>
                    `
                });

                const $form = $(config.selectors.form);
                $form.find('.feedback-message').remove();
                $form.prepend($message);

                // Enable form controls
                const $inputs = $form.find('input, select, textarea, button');
                $inputs.prop('disabled', false);
                $form.find('.submit-button').text('Verstuur Feedback');
            }
        }

        /**
         * Reset form fields
         */
        resetForm() {
            const $form = $(config.selectors.form);
            
            // Remove any existing messages
            $form.find('.feedback-message').remove();
            
            // Reset form fields
            $('input[name="feedback-type"]').prop('checked', false);
            $(config.selectors.comment).val('');
            $(config.selectors.attachments).val('');
            $(config.selectors.selectedFiles).empty();
            
            // Show form elements again
            $form.find('.form-group, .form-actions').show();
            
            this.selectedElement = null;
        }

        /**
         * Handle element hover voor highlighting
         * @param {HTMLElement} element 
         */
        handleElementHover(element) {
            const $element = $(element);
            const elementInfo = `${element.tagName.toLowerCase()}${$element.attr('id') ? ` #${$element.attr('id')}` : ''}`;
            
            $element
                .addClass(config.classes.highlight)
                .attr('data-element-info', elementInfo);
        }

        /**
         * Verwijder feedback
         * @param {number} id 
         */
        async deleteFeedback(id) {
            if (!confirm('Weet je zeker dat je deze feedback wilt verwijderen?')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'delete_feedback');
                formData.append('nonce', feedbackToolSettings.nonce);
                formData.append('feedback_id', id);

                const response = await $.ajax({
                    url: feedbackToolSettings.ajaxurl,
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false
                });

                if (response.success) {
                    // Ververs de lijst
                    feedbackToolSettings.feedback = feedbackToolSettings.feedback.filter(item => item.id !== id);
                    this.renderFeedbackList();
                } else {
                    throw new Error(response.data || 'Er is een fout opgetreden');
                }
            } catch (error) {
                console.error('Error bij verwijderen feedback:', error);
                alert('Er is een fout opgetreden bij het verwijderen van de feedback.');
            }
        }

        /**
         * Handle file selection
         * @param {FileList} files 
         */
        handleFileSelection(files) {
            const $container = $(config.selectors.selectedFiles);
            $container.empty();

            Array.from(files).forEach((file, index) => {
                const $file = $('<div>', {
                    class: 'selected-file',
                    'data-index': index
                });

                $('<div>', {
                    class: 'selected-file-name',
                    text: file.name
                }).appendTo($file);

                $('<button>', {
                    type: 'button',
                    class: 'remove-file',
                    html: '×',
                    title: 'Verwijder bestand'
                }).appendTo($file);

                $file.appendTo($container);
            });
        }

        /**
         * Remove file from selection
         * @param {number} index 
         */
        removeFile(index) {
            const $input = $(config.selectors.attachments);
            const files = Array.from($input[0].files);
            
            // Create new FileList without the removed file
            const dt = new DataTransfer();
            files.forEach((file, i) => {
                if (i !== index) dt.items.add(file);
            });
            
            $input[0].files = dt.files;
            this.handleFileSelection(dt.files);
        }
    }

    // Initialiseer de feedback tool wanneer het document geladen is
    $(document).ready(() => {
        window.feedbackTool = new FeedbackTool();
    });

})(jQuery); 