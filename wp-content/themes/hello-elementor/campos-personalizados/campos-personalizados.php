<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;
class CamposPersonalizados
{
    public function __construct() {
        add_action('after_setup_theme', array($this, 'carrega_carbon_fields'));
        add_action('carbon_fields_loaded', array($this, 'carrega_outros'));
    }


    public function carrega_carbon_fields() {
        require_once get_template_directory() . '/vendor/autoload.php';
        \Carbon_Fields\Carbon_Fields::boot();
    }

    public function carrega_outros() {
        include 'outros/packages.php';

        packages();
    }
}

new CamposPersonalizados();