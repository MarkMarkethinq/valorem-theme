document.addEventListener('DOMContentLoaded', function() {
    let isProcessing = false;

    // Luister naar beide form events
    jQuery(document).on('gform_page_loaded gform_post_render', function(event, formId, currentPage) {
        // Voorkom dubbele uitvoering
        if (isProcessing) {
            return;
        }
        
        // Check of het het juiste formulier is en pagina 2 (vergelijk als string)
        if (formId === parseInt(domainSuggestions.intakeFormId) && currentPage.toString() === '2') {
            isProcessing = true;
            const companyField = jQuery('input[name="input_1"]');
            
            if (companyField.length) {
                const companyName = companyField.val();
                if (companyName && companyName.trim() !== '') {
                    getDomainSuggestion(companyName);
                }
            }

            // Reset de flag na een korte vertraging
            setTimeout(() => {
                isProcessing = false;
            }, 1000);
        }
    });

    // Helper functie om de huidige pagina te bepalen
    function gformCalculateCurrentPage() {
        const currentPageField = jQuery('input[name="gform_source_page_number"]').val();
        return currentPageField ? parseInt(currentPageField) : 1;
    }

    // Functie voor het ophalen van domeinsuggesties
    function getDomainSuggestion(companyName) {
        if (!companyName || companyName.trim() === '') {
            return;
        }
        
        const ajaxURL = domainSuggestions.ajaxurl;
        
        jQuery.ajax({
            url: ajaxURL,
            type: 'POST',
            data: {
                action: 'get_domain_suggestion',
                company_name: companyName,
                page: '2'
            },
            success: function(response) {
                if (response.success && response.data && response.data.domain) {
                    const domainField = jQuery('input[name="input_6"]') || // Via input name
                                      jQuery('#input_7_6') ||             // Via specifiek ID
                                      jQuery('label:contains("Domeinnaam")').siblings('input'); // Via label

                    if (domainField.length) {
                        domainField.val(response.data.domain);
                    }
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = 'Er is een fout opgetreden bij het ophalen van de domeinnaamsuggestie';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.data && response.data.message) {
                        errorMessage = response.data.message;
                    }
                } catch (e) {
                    // Error parsing kan stilletjes falen
                }
            }
        });
    }
}); 