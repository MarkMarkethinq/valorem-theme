<?php
/**
 * Support Widget Template
 */
?>
<div id="support-widget" class="support-widget">
    <button class="support-toggle">Support</button>
    <div class="support-container">
        <div class="support-header">
            <h3>Support Assistent</h3>
            <button class="support-close">&times;</button>
        </div>
        <div class="support-messages"></div>
        <div class="support-suggestions">
            <button class="suggestion-btn" data-message="feedback">Feedback geven</button>
            <button class="suggestion-btn" data-message="technisch">Technische hulp</button>
            <button class="suggestion-btn" data-message="medewerker spreken">Medewerker spreken</button>
            <button class="suggestion-btn" data-message="hallo">Start gesprek</button>
        </div>
        <form class="support-form">
            <input type="text" class="support-input" placeholder="Type je bericht...">
            <button type="submit">Verstuur</button>
        </form>
    </div>
</div> 