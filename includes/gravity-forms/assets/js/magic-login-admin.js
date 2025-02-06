jQuery(document).ready(function($) {
    // Handle generate link button click
    $('#generate_link').on('click', function() {
        const email = $('#user_email').val();
        if (!email) {
            alert('Voer een e-mailadres in');
            return;
        }

        const button = $(this);
        button.prop('disabled', true).text('Genereren...');

        $.ajax({
            url: magicLoginAdmin.ajaxurl,
            type: 'POST',
            data: {
                action: 'generate_magic_link',
                email: email,
                _ajax_nonce: magicLoginAdmin.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#magic_link').val(response.data.login_url);
                    $('#magic_link_result').show();
                } else {
                    alert(response.data || 'Er is een fout opgetreden');
                }
            },
            error: function() {
                alert('Er is een fout opgetreden bij het genereren van de link');
            },
            complete: function() {
                button.prop('disabled', false).text('Genereer Link');
            }
        });
    });

    // Handle copy link button click
    $('#copy_link').on('click', function() {
        const linkInput = $('#magic_link')[0];
        linkInput.select();
        document.execCommand('copy');
        
        const button = $(this);
        button.text('Gekopieerd!');
        setTimeout(function() {
            button.text('Kopieer Link');
        }, 2000);
    });
}); 