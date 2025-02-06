<?php
namespace FeedbackSupportTool\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class FeedbackConversation extends Conversation
{
    protected $type;
    protected $description;

    public function run()
    {
        $this->askFeedbackType();
    }

    private function askFeedbackType()
    {
        $this->ask('Wat voor soort feedback wil je geven? (bug, suggestie, of vraag)', function($answer) {
            $this->type = $answer->getText();
            $this->say('Je hebt gekozen voor: ' . $this->type);
            $this->askDescription();
        });
    }

    private function askDescription()
    {
        $this->ask('Kun je je ' . $this->type . ' beschrijven?', function($answer) {
            $this->description = $answer->getText();
            $this->say('Bedankt voor je feedback! Ik ga dit verwerken.');
            $this->createFeedback();
        });
    }

    private function createFeedback()
    {
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