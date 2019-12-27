<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH.'/third_party/algolia/autoload.php';

use Algolia\AlgoliaSearch\SearchClient;

class Algolia {

    protected $client;

    const APPLICATION_ID    =   'EZAG2DUL38';

    const SEARCH_ONLY_KEY   =   '7ba62c7c497dbeda794c9338af82f60c';

    const ADMIN_KEY         =   '0ae814cedf72a56bc6bdc571ef385626';

    public function __construct()
    {
        $this->client = SearchClient::create(
            self::APPLICATION_ID,
            self::ADMIN_KEY
        );
    }

    public function save($objectIndex, $data)
    {
        $index = $this->client->initIndex($objectIndex);

        return $index->saveObjects($data, ['autoGenerateObjectIDIfNotExist' => true]);
    }
	
	public function clear($objectIndex)
    {
       $index = $this->client->initIndex($objectIndex);

        return $index->clearObjects();
    }

    public function search($objectIndex, $query, $page=0)
    {
        $index = $this->client->initIndex($objectIndex);
		
        return $index->search($query, ['page'=>$page]);
    }
	
	public function maximum($objectIndex, $query, $filter=[])
    {
        $index = $this->client->initIndex($objectIndex);
		
		$_settings = [
			'hitsPerPage'=>'9999999999999999'
		];
		
		if($filter){
			$_k =  key($filter);
			$_settings['filters'] = $_k.':'.$filter[$_k];
		}
		
        return $index->search($query, $_settings);
    }

    public function delete($objectIndex, $id)
    {
        $index = $this->client->initIndex($objectIndex);

        return $index->deleteObject($id);
    }




}