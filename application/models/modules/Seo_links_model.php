<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seo_links_model extends Core_Model
{

    public $table = 'seo_links';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'seo_links_translation';
    public $table_translation_key = 'seo_links_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}