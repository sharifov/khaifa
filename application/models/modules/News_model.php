<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News_model extends Core_Model
{

    public $table = 'news';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'news_translation';
    public $table_translation_key = 'news_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}