<?php

class Length {
	
	private $lengths = [];

	public $CI;

	public function __construct()
	{
		$this->CI = &get_instance();

		$length_class_query = $this->CI->db->query("SELECT * FROM wc_length_class mc LEFT JOIN wc_length_class_translation mcd ON (mc.id = mcd.length_class_id) WHERE mcd.language_id = '" . (int)$this->CI->data['current_lang_id'] . "'");

		foreach ($length_class_query->result() as $result)
		{
			$this->lengths[$result->length_class_id] = [
				'length_class_id'	=> $result->length_class_id,
				'title'				=> $result->name,
				'unit'				=> $result->unit,
				'value'				=> $result->value
			];
		}
	}

	public function convert($value, $from, $to)
	{
		if ($from == $to)
		{
			return $value;
		}

		if (isset($this->lengths[$from]))
		{
			$from = $this->lengths[$from]['value'];
		}
		else
		{
			$from = 1;
		}

		if (isset($this->lengths[$to]))
		{
			$to = $this->lengths[$to]['value'];
		}
		else
		{
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $length_class_id, $decimal_point = '.', $thousand_point = ',')
	{
		if (isset($this->lengths[$length_class_id]))
		{
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->lengths[$length_class_id]['unit'];
		}
		else
		{
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($length_class_id)
	{
		if (isset($this->lengths[$length_class_id]))
		{
			return $this->lengths[$length_class_id]['unit'];
		}
		else
		{
			return '';
		}
	}
}
