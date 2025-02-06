<?php
/**
 * Feedback Support Tool
 * 
 * Een interactieve support tool met Botman integratie
 */

use BotMan\BotMan\BotManFactory;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Drivers\DriverManager;
use BotMan\Drivers\Web\WebDriver;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

/**
 * Message Middleware class to handle incoming messages
 */
class MessageMiddleware implements Received
{
    private $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function received(IncomingMessage $message, $next, $bot)
    {
        $message->setText($this->message);
        return $next($message);
    }
}

class Feedback_Support_Tool {
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
    public function __construct() {
        // Initialize BotMan
        $this->init_botman();
    }

    /**
     * Initialize the tool
     */
    public function init() {
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
    private function init_botman() {
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
    private function define_conversations() {
        // Fallback
        $this->botman->fallback(function($bot) {
            $bot->reply('Sorry, ik begrijp niet helemaal wat je bedoelt. Probeer "feedback" voor het geven van feedback of "technisch" voor technische ondersteuning.');
        });
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Support Widget CSS
        wp_enqueue_style(
            'feedback-support-tool',
            get_template_directory_uri() . '/includes/feedback-support-tool/assets/css/support-widget.css',
            array(),
            $this->version
        );

        // Support Widget JavaScript
        wp_enqueue_script(
            'feedback-support-tool',
            get_template_directory_uri() . '/includes/feedback-support-tool/assets/js/support-widget.js',
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
    public function handle_support_request() {
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
                } elseif (preg_match('/hi|hello|hallo/i', $message)) {
                    $botResponse = 'Hallo! Ik ben je support assistent. Hoe kan ik je helpen?';
                } else {
                    $botResponse = 'Sorry, ik begrijp niet helemaal wat je bedoelt. Probeer "feedback" voor het geven van feedback of "technisch" voor technische ondersteuning.';
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
    public function handle_bot_response() {
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
    public function render_support_widget() {
        include_once get_template_directory() . '/includes/feedback-support-tool/templates/support-widget.php';
    }
}

/**
 * Feedback conversation flow
 */
class Feedback_Conversation extends Conversation {
    protected $type;
    protected $description;

    public function run() {
        $this->askFeedbackType();
    }

    private function askFeedbackType() {
        $this->ask('Wat voor soort feedback wil je geven? (bug, suggestie, of vraag)', function($answer) {
            $this->type = $answer->getText();
            $this->say('Je hebt gekozen voor: ' . $this->type);
            $this->askDescription();
        });
    }

    private function askDescription() {
        $this->ask('Kun je je ' . $this->type . ' beschrijven?', function($answer) {
            $this->description = $answer->getText();
            $this->say('Bedankt voor je feedback! Ik ga dit verwerken.');
            $this->createFeedback();
        });
    }

    private function createFeedback() {
        // Create feedback post
        $post_data = array(
            'post_title' => ucfirst($this->type) . ' feedback',
            'post_content' => $this->description,
            'post_status' => 'publish',
            'post_type' => 'feedback'
        );

        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            $this->say('Je feedback is succesvol opgeslagen. Bedankt voor je bijdrage!');
        } else {
            $this->say('Er is helaas iets misgegaan bij het opslaan van je feedback. Probeer het later opnieuw.');
        }
    }
}

/**
 * Technical support conversation flow
 */
class Technical_Support_Conversation extends Conversation {
    protected $problem;
    protected $steps;

    public function run() {
        $this->say('Ik help je graag met je technische probleem.');
        $this->askProblem();
    }

    private function askProblem() {
        $this->ask('Kun je het technische probleem beschrijven?', function($answer) {
            $this->problem = $answer->getText();
            $this->say('Ik heb je probleem genoteerd.');
            $this->askSteps();
        });
    }

    private function askSteps() {
        $this->ask('Welke stappen heb je al geprobeerd om het op te lossen?', function($answer) {
            $this->steps = $answer->getText();
            $this->say('Bedankt voor deze informatie.');
            $this->createSupportTicket();
        });
    }

    private function createSupportTicket() {
        // Create support ticket post
        $post_data = array(
            'post_title' => 'Technisch probleem',
            'post_content' => "Probleem: {$this->problem}\n\nGeprobeerde stappen: {$this->steps}",
            'post_status' => 'publish',
            'post_type' => 'support_ticket'
        );

        $post_id = wp_insert_post($post_data);

        if (!is_wp_error($post_id)) {
            $this->say('Je support ticket is aangemaakt. We nemen zo snel mogelijk contact met je op.');
        } else {
            $this->say('Er is helaas iets misgegaan bij het aanmaken van je support ticket. Probeer het later opnieuw.');
        }
    }
}
