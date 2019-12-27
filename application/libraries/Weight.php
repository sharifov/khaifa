<?php

class Weight {

	private $weights = [];
	public $CI;

	public function __construct()
	{
		$this->CI = &get_instance();

		$weight_class_query = $this->CI->db->query("SELECT * FROM wc_weight_class wc LEFT JOIN  wc_weight_class_translation wcd ON (wc.id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->CI->data['current_lang_id'] . "'");

		foreach ($weight_class_query->result() as $result)
		{
			$this->weights[$result->weight_class_id] = [
				'weight_class_id'	=> $result->weight_class_id,
				'title'				=> $result->name,
				'unit'				=> $result->unit,
				'value'				=> $result->value
			];
		}
	}

	public function convert($value, $from, $to)
	{
		if ($from == $to) {
			return $value;
		}

		if (isset($this->weights[$from])) {
			$from = $this->weights[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->weights[$to])) {
			$to = $this->weights[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',')
	{
		if (isset($this->weights[$weight_class_id]))
		{
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[$weight_class_id]['unit'];
		}
		else
		{
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($weight_class_id)
	{
		if (isset($this->weights[$weight_class_id]))
		{
			return $this->weights[$weight_class_id]['unit'];
		}
		else
		{
			return '';
		}
	}

	public function weight_to_gram_converter($weight, $class)
    {
        switch ($class) {
            case 1:
                return $weight * 1000;

            case 2:
                return $weight;

            case 3:
                return $weight * 453.592;

            case 4:
                return $weight * 28.3495;

            default:
                return $weight;
        }
    }
}