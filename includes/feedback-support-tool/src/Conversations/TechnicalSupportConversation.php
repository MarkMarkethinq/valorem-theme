<?php
namespace FeedbackSupportTool\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;

class TechnicalSupportConversation extends Conversation
{
    protected $problem;
    protected $steps;

    public function run()
    {
        $this->say('Ik help je graag met je technische probleem.');
        $this->askProblem();
    }

    private function askProblem()
    {
        $this->ask('Kun je het technische probleem beschrijven?', function($answer) {
            $this->problem = $answer->getText();
            $this->say('Ik heb je probleem genoteerd.');
            $this->askSteps();
        });
    }

    private function askSteps()
    {
        $this->ask('Welke stappen heb je al geprobeerd om het op te lossen?', function($answer) {
            $this->steps = $answer->getText();
            $this->say('Bedankt voor deze informatie.');
            $this->createSupportTicket();
        });
    }

    private function createSupportTicket()
    {
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