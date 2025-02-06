<?php
/**
 * Template voor de feedback tool
 */
?>
<div id="feedback-tool">
    <button class="feedback-button">Feedback Geven</button>
    
    <div class="feedback-overview">
        <h3>Mijn feedback</h3>
        <div class="feedback-list"></div>
    </div>

    <form id="feedback-form" title="Feedback Geven">
        <div class="feedback-message"></div>
        
        <div class="form-group">
            <label>Element: <span class="element-type"></span></label>
        </div>

        <div class="form-group">
            <label>Type feedback:</label>
            <div id="feedback-type"></div>
        </div>

        <div class="form-group">
            <label for="feedback-comment">Opmerking:</label>
            <textarea id="feedback-comment" name="comment" required></textarea>
        </div>

        <div class="form-group">
            <label for="feedback-attachments">Bijlagen:</label>
            <input type="file" id="feedback-attachments" name="attachments[]" multiple accept="image/*,application/pdf,.doc,.docx,.txt">
            <div class="selected-files"></div>
            <p class="description">Toegestane bestanden: afbeeldingen, PDF, Word, tekst</p>
        </div>

        <div class="form-actions">
            <button type="submit" class="submit-button">Verstuur Feedback</button>
            <button type="button" class="cancel-button">Annuleren</button>
        </div>
    </form>
</div>