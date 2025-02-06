<?php

if ( ! class_exists( 'GF_Field' ) ) {
    return; // Gravity Forms must be active
}

/**
 * Custom Gravity Forms field for OpenKVK Autocomplete.
 */
class GF_Field_OpenKvk_Autocomplete extends GF_Field_Text {

    public $type = 'openkvk_autocomplete';

    /**
     * @var bool
     */
    public $isRequired = false;

    /**
     * @var string
     */
    public $errorMessage;

    public function __construct($data = array()) {
        parent::__construct($data);
        $this->isRequired = rgar($data, 'isRequired');
        $this->errorMessage = rgar($data, 'errorMessage');
    }

    /**
     * Returns the field's form editor icon.
     */
    public function get_form_editor_field_icon() {
        return 'gform-icon--search';
    }

    /**
     * Defines the field title to be used in the form editor.
     */
    public function get_form_editor_field_title() {
        return esc_html__('OpenKVK Autocomplete', 'your-text-domain');
    }

    /**
     * Returns the field's form editor button.
     */
    public function get_form_editor_button() {
        return array(
            'group' => 'advanced_fields',
            'text'  => $this->get_form_editor_field_title(),
        );
    }

    /**
     * Returns the field's form editor settings.
     */
    public function get_form_editor_field_settings() {
        return array(
            'conditional_logic_field_setting',
            'error_message_setting',
            'label_setting',
            'label_placement_setting',
            'admin_label_setting',
            'size_setting',
            'rules_setting',
            'visibility_setting',
            'duplicate_setting',
            'required_setting',
            'placeholder_setting',
            'default_value_setting',
            'css_class_setting',
            'description_setting',
        );
    }

    /**
     * Render the actual input on the front-end form.
     */
    public function get_field_input( $form, $value = '', $entry = null ) {

        $form_id    = $form['id'];
        $field_id   = $this->id;
        $input_id   = 'input_' . $form_id . '_' . $field_id;
        $input_name = 'input_' . $field_id;

        // Placeholder text if specified in the field settings
        $placeholder = $this->placeholder ? sprintf( ' placeholder="%s"', esc_attr( $this->placeholder ) ) : '';

        // Add required attribute if the field is required
        $required = $this->isRequired ? 'required="required" aria-required="true"' : '';

        // Include the autocomplete functionality and styling
        $html = sprintf(
            '<div class="gf-openkvk-autocomplete-wrapper" style="position: relative;">
                <input 
                    type="text" 
                    name="%s" 
                    id="%s" 
                    value="%s" 
                    class="gf-openkvk-autocomplete" 
                    %s 
                    %s
                    autocomplete="off"
                />
                <ul class="gf-openkvk-suggestions" style="display: none;"></ul>
            </div>',
            esc_attr( $input_name ),
            esc_attr( $input_id ),
            esc_attr( $value ),
            $placeholder,
            $required
        );

        return $html;
    }

    /**
     * Validate the field value.
     */
    public function validate( $value, $form ) {
        if ( $this->isRequired && empty( $value ) ) {
            $this->failed_validation = true;
            $this->validation_message = empty( $this->errorMessage ) ? esc_html__( 'Dit veld is verplicht.', 'your-text-domain' ) : $this->errorMessage;
        }
    }
}

GF_Fields::register( new GF_Field_OpenKvk_Autocomplete() );
