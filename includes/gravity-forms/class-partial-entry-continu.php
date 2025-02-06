<?php
add_filter('gform_pre_render', 'load_partial_entry_debug');
function load_partial_entry_debug($form) {

    // Controleer of de entry_id aanwezig is
    if (isset($_GET['entry_id'])) {
        $entry_id = intval($_GET['entry_id']);

        // Laad gegevens uit de database
        $entry = GFAPI::get_entry($entry_id);

        if (is_wp_error($entry)) {
            return $form;
        }

        // Vul formulier in met entry-gegevens
        foreach ($form['fields'] as &$field) {
            $field_id = $field->id;
            if (isset($entry[$field_id])) {
                $field['defaultValue'] = $entry[$field_id];
                error_log("Veld {$field_id} ingesteld op: {$entry[$field_id]}");
            }
        }
    } 

    return $form;
}
