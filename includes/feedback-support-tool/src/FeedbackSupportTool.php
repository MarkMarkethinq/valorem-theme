<?php
namespace FeedbackSupportTool;

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Web\WebDriver;
use FeedbackSupportTool\Middleware\MessageMiddleware;
use FeedbackSupportTool\Conversations\FeedbackConversation;
use FeedbackSupportTool\Conversations\TechnicalSupportConversation;

class FeedbackSupportTool
{
    /**
     * Version number for assets
     */
    private $version = '1.0.0';

    /**
     * BotMan instance
     */
    private $botman;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize BotMan
        $this->init_botman();
    }

    /**
     * Initialize the tool
     */
    public function init()
    {
        // Only load for logged-in users
        if (!is_user_logged_in()) {
            return;
        }

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add AJAX handlers
        add_action('wp_ajax_submit_support_request', array($this, 'handle_support_request'));
        add_action('wp_ajax_get_bot_response', array($this, 'handle_bot_response'));

        // Add template to footer
        add_action('wp_footer', array($this, 'render_support_widget'));
    }

    /**
     * Initialize BotMan
     */
    private function init_botman()
    {
        // Require Composer's autoloader
        require_once get_template_directory() . '/vendor/autoload.php';

        // Load the web driver
        DriverManager::loadDriver(WebDriver::class);

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $config = [
            'web' => [
                'matchingData' => [
                    'driver' => 'web',
                ],
            ],
            'conversation_cache_time' => 40,
            'user_cache_time' => 30,
            'config' => [
                'conversation_cache_driver' => 'array',
                'user_cache_driver' => 'array'
            ]
        ];

        // Create BotMan instance
        $this->botman = BotManFactory::create($config);

        // Define conversation flows
        $this->define_conversations();
    }

    /**
     * Define bot conversations
     */
    private function define_conversations()
    {
        // Fallback
        $this->botman->fallback(function($bot) {
            $bot->reply('Sorry, ik begrijp niet helemaal wat je bedoelt. Probeer "feedback" voor het geven van feedback of "technisch" voor technische ondersteuning.');
        });
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts()
    {
        // Support Widget CSS
        wp_enqueue_style(
            'feedback-support-tool',
            plugin_dir_url(__FILE__) . '../assets/css/support-widget.css',
            array(),
            $this->version
        );

        // Support Widget JavaScript
        wp_enqueue_script(
            'feedback-support-tool',
            plugin_dir_url(__FILE__) . '../assets/js/support-widget.js',
            array('jquery'),
            $this->version,
            true
        );

        // Localize script
        wp_localize_script(
            'feedback-support-tool',
            'supportToolSettings',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('support_tool_nonce'),
                'strings' => array(
                    'sending' => __('Bericht versturen...', 'go-theme'),
                    'error' => __('Er is een fout opgetreden.', 'go-theme')
                )
            )
        );
    }

    /**
     * Handle support request via AJAX
     */
    public function handle_support_request()
    {
        // Verify nonce
        if (!check_ajax_referer('support_tool_nonce', 'nonce', false)) {
            wp_send_json_error('Beveiligingsverificatie mislukt. Vernieuw de pagina en probeer het opnieuw.');
            return;
        }

        $message = isset($_POST['message']) ? sanitize_text_field($_POST['message']) : '';
        
        if (empty($message)) {
            wp_send_json_error('Geen bericht ontvangen.');
            return;
        }

        try {
            // Create a closure to capture the response
            $botResponse = null;

            // Check if we're in a conversation
            $conversation = isset($_SESSION['conversation']) ? $_SESSION['conversation'] : null;
            $conversationType = isset($_SESSION['conversation_type']) ? $_SESSION['conversation_type'] : null;

            if ($conversation && $conversationType) {
                // Handle ongoing conversation
                switch ($conversationType) {
                    case 'feedback':
                        if (!isset($_SESSION['feedback_type'])) {
                            $_SESSION['feedback_type'] = $message;
                            $botResponse = 'Je hebt gekozen voor: ' . $message . '. Kun je je feedback beschrijven?';
                        } else if (!isset($_SESSION['feedback_description'])) {
                            $_SESSION['feedback_description'] = $message;
                            
                            // Create feedback post
                            $post_data = array(
                                'post_title' => ucfirst($_SESSION['feedback_type']) . ' feedback',
                                'post_content' => $message,
                                'post_status' => 'publish',
                                'post_type' => 'feedback'
                            );

                            $post_id = wp_insert_post($post_data);

                            if (!is_wp_error($post_id)) {
                                $botResponse = 'Je feedback is succesvol opgeslagen. Bedankt voor je bijdrage!';
                            } else {
                                $botResponse = 'Er is helaas iets misgegaan bij het opslaan van je feedback. Probeer het later opnieuw.';
                            }

                            // Clear session
                            unset($_SESSION['conversation']);
                            unset($_SESSION['conversation_type']);
                            unset($_SESSION['feedback_type']);
                            unset($_SESSION['feedback_description']);
                        }
                        break;

                    case 'technical':
                        if (!isset($_SESSION['problem'])) {
                            $_SESSION['problem'] = $message;
                            $botResponse = 'Ik heb je probleem genoteerd. Welke stappen heb je al geprobeerd om het op te lossen?';
                        } else if (!isset($_SESSION['steps'])) {
                            $_SESSION['steps'] = $message;
                            
                            // Create support ticket
                            $post_data = array(
                                'post_title' => 'Technisch probleem',
                                'post_content' => "Probleem: {$_SESSION['problem']}\n\nGeprobeerde stappen: {$message}",
                                'post_status' => 'publish',
                                'post_type' => 'support_ticket'
                            );

                            $post_id = wp_insert_post($post_data);

                            if (!is_wp_error($post_id)) {
                                $botResponse = 'Je support ticket is aangemaakt. We nemen zo snel mogelijk contact met je op.';
                            } else {
                                $botResponse = 'Er is helaas iets misgegaan bij het aanmaken van je support ticket. Probeer het later opnieuw.';
                            }

                            // Clear session
                            unset($_SESSION['conversation']);
                            unset($_SESSION['conversation_type']);
                            unset($_SESSION['problem']);
                            unset($_SESSION['steps']);
                        }
                        break;

                    case 'contact':
                        if (!isset($_SESSION['contact_method'])) {
                            $_SESSION['contact_method'] = strtolower($message);
                            
                            if ($_SESSION['contact_method'] === 'whatsapp') {
                                $whatsappNumber = '31612345678'; // Pas dit nummer aan
                                $botResponse = "Je kunt direct contact opnemen via WhatsApp: https://wa.me/$whatsappNumber";
                                
                                // Clear session
                                unset($_SESSION['conversation']);
                                unset($_SESSION['conversation_type']);
                                unset($_SESSION['contact_method']);
                            } else {
                                $botResponse = 'Wat is je naam?';
                            }
                        } else if (!isset($_SESSION['contact_name'])) {
                            $_SESSION['contact_name'] = $message;
                            $botResponse = 'Wat is je e-mailadres?';
                        } else if (!isset($_SESSION['contact_email'])) {
                            $_SESSION['contact_email'] = $message;
                            $botResponse = 'Wat is je vraag of bericht?';
                        } else if (!isset($_SESSION['contact_message'])) {
                            $_SESSION['contact_message'] = $message;
                            
                            // Create contact request
                            $post_data = array(
                                'post_title' => 'Contact aanvraag van ' . $_SESSION['contact_name'],
                                'post_content' => "Naam: {$_SESSION['contact_name']}\nEmail: {$_SESSION['contact_email']}\nBericht: {$message}",
                                'post_status' => 'publish',
                                'post_type' => 'contact_request'
                            );

                            $post_id = wp_insert_post($post_data);

                            if (!is_wp_error($post_id)) {
                                // Stuur e-mail notificatie
                                $to = get_option('admin_email');
                                $subject = 'Nieuwe contact aanvraag van ' . $_SESSION['contact_name'];
                                $mail_message = "Er is een nieuwe contact aanvraag ontvangen:\n\n";
                                $mail_message .= "Naam: {$_SESSION['contact_name']}\n";
                                $mail_message .= "Email: {$_SESSION['contact_email']}\n";
                                $mail_message .= "Bericht: {$message}";
                                
                                wp_mail($to, $subject, $mail_message);

                                $botResponse = 'Je aanvraag is succesvol verzonden. We nemen zo snel mogelijk contact met je op!';
                            } else {
                                $botResponse = 'Er is helaas iets misgegaan bij het verzenden van je aanvraag. Probeer het later opnieuw.';
                            }

                            // Clear session
                            unset($_SESSION['conversation']);
                            unset($_SESSION['conversation_type']);
                            unset($_SESSION['contact_method']);
                            unset($_SESSION['contact_name']);
                            unset($_SESSION['contact_email']);
                            unset($_SESSION['contact_message']);
                        }
                        break;
                }
            } else {
                // Start new conversation based on command
                if (preg_match('/feedback/i', $message)) {
                    $_SESSION['conversation'] = true;
                    $_SESSION['conversation_type'] = 'feedback';
                    $botResponse = 'Ik help je graag met het geven van feedback. Wat voor soort feedback wil je geven? (bug, suggestie, of vraag)';
                } elseif (preg_match('/technisch|probleem|error/i', $message)) {
                    $_SESSION['conversation'] = true;
                    $_SESSION['conversation_type'] = 'technical';
                    $botResponse = 'Ik help je graag met je technische probleem. Kun je beschrijven wat er aan de hand is?';
                } elseif (preg_match('/contact|medewerker|spreken/i', $message)) {
                    $_SESSION['conversation'] = true;
                    $_SESSION['conversation_type'] = 'contact';
                    $botResponse = 'Hoe wil je contact opnemen? (contactformulier/whatsapp)';
                } elseif (preg_match('/hi|hello|hallo/i', $message)) {
                    $botResponse = 'Hallo! Ik ben je support assistent. Hoe kan ik je helpen? Je kunt kiezen uit:\n- Feedback geven\n- Technische ondersteuning\n- Medewerker spreken';
                } else {
                    $botResponse = 'Sorry, ik begrijp niet helemaal wat je bedoelt. Je kunt kiezen uit:\n- Feedback geven\n- Technische ondersteuning\n- Medewerker spreken';
                }
            }

            wp_send_json_success(array(
                'response' => $botResponse
            ));

        } catch (Exception $e) {
            error_log('Support request error: ' . $e->getMessage());
            wp_send_json_error('Er is een onverwachte fout opgetreden. Probeer het later opnieuw.');
        }
    }

    /**
     * Handle bot response via AJAX
     */
    public function handle_bot_response()
    {
        // Verify nonce
        if (!check_ajax_referer('support_tool_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
            return;
        }

        try {
            // Run BotMan
            $this->botman->listen();
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }

    /**
     * Render support widget
     */
    public function render_support_widget()
    {
        include_once plugin_dir_path(__FILE__) . '../templates/support-widget.php';
    }
} 