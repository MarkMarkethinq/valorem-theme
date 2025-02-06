<?php
namespace FeedbackSupportTool\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class ContactConversation extends Conversation
{
    protected $contactMethod;
    protected $name;
    protected $email;
    protected $message;

    public function run()
    {
        $this->askContactMethod();
    }

    private function askContactMethod()
    {
        $this->ask('Hoe wil je contact opnemen? (contactformulier/whatsapp)', function($answer) {
            $this->contactMethod = strtolower($answer->getText());
            
            if ($this->contactMethod === 'whatsapp') {
                $this->handleWhatsApp();
            } else {
                $this->askName();
            }
        });
    }

    private function handleWhatsApp()
    {
        // Je kunt hier het WhatsApp nummer aanpassen
        $whatsappNumber = '31612345678';
        $this->say("Je kunt direct contact opnemen via WhatsApp: https://wa.me/$whatsappNumber");
        $this->say("Je wordt nu doorgestuurd naar WhatsApp...");
    }

    private function askName()
    {
        $this->ask('Wat is je naam?', function($answer) {
            $this->name = $answer->getText();
            $this->askEmail();
        });
    }

    private function askEmail()
    {
        $this->ask('Wat is je e-mailadres?', function($answer) {
            $this->email = $answer->getText();
            $this->askMessage();
        });
    }

    private function askMessage()
    {
        $this->ask('Wat is je vraag of bericht?', function($answer) {
            $this->message = $answer->getText();
            $this->createContactRequest();
        });
    }

    private function createContactRequest()
    {
        // Create contact form entry
        $post_data = array(
            'post_title' => 'Contact aanvraag van ' . $this->name,
            'post_content' => "Naam: {$this->name}\nEmail: {$this->email}\nBericht: {$this->message}",
            'post_status' => 'publish',
            'post_type' => 'contact_request'
        );

        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            // Optioneel: stuur een e-mail notificatie
            $to = get_option('admin_email');
            $subject = 'Nieuwe contact aanvraag van ' . $this->name;
            $message = "Er is een nieuwe contact aanvraag ontvangen:\n\n";
            $message .= "Naam: {$this->name}\n";
            $message .= "Email: {$this->email}\n";
            $message .= "Bericht: {$this->message}";
            
            wp_mail($to, $subject, $message);

            $this->say('Je aanvraag is succesvol verzonden. We nemen zo snel mogelijk contact met je op!');
        } else {
            $this->say('Er is helaas iets misgegaan bij het verzenden van je aanvraag. Probeer het later opnieuw.');
        }
    }
} 