<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News_category_model extends Core_Model
{

    public $table = 'news_category';
    public $primary_key = 'id';
    public $protected = [];
    public $table_translation = 'news_category_translation';
    public $table_translation_key = 'news_category_id';
    public $table_language_key = 'language_id';

    public $timestamps = true;

    public function __construct()
    {
        parent::__construct();
    }
}