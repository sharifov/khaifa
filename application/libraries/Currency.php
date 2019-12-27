<?php

class Currency {

	public $CI;

	public $currencies = [];

	public function __construct()
	{
		$this->CI = &get_instance();
		$this->CI->load->model('modules/Currency_model');
		
		$currencies = $this->CI->Currency_model->filter(['status' => 1])->all();

		foreach ($currencies as $currency) {
			$this->currencies[$currency->code] = [
				'id'   			=> $currency->id,
				'title'         => $currency->name,
				'symbol_left'   => $currency->symbol_left,
				'symbol_right'  => $currency->symbol_right,
				'decimal_place' => $currency->decimal_place,
				'value'         => $currency->value
			];
		}

		if(get_setting('currency_auto'))
		{
			$this->refresh();
		}
	}

	public function format($number, $currency, $value = '', $format = true) {

		$symbol_left = $this->currencies[$currency]['symbol_left'];
		$symbol_right = $this->currencies[$currency]['symbol_right'];
		$decimal_place = $this->currencies[$currency]['decimal_place'];


		if (!$value) {
			$value = $this->currencies[$currency]['value'];
		}

		$amount = $value ? (float)$number * $value : (float)$number;

		$amount = $this->round_up($amount, (int)$decimal_place);
		
		if (!$format) {
			return $amount;
		}

		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

		$string .= number_format($amount, (int)$decimal_place, '.',false);

		if ($symbol_right) {
			$string .= $symbol_right;
		}

		return $string;
	}

	public function format_new($number, $currency, $value = '', $format = true) {

		$symbol_left = $this->currencies[$currency]['symbol_left'];
		$symbol_right = $this->currencies[$currency]['symbol_right'];
		$decimal_place = $this->currencies[$currency]['decimal_place'];

		if (!$value) {
			$value = $this->currencies[$currency]['value'];
		}

		$amount = $value ? (float)$number / $value : (float)$number;

		$amount = $this->round_up($amount, (int)$decimal_place);

		if (!$format) {
			return $amount;
		}

		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

		$string .= number_format($amount, (int)$decimal_place, '.',false);

		if ($symbol_right) {
			$string .= $symbol_right;
		}

		return $string;
	}

    public function convertCurrencyString($currencyString)
    {
        $result         = 0;
        $code           = 'NaN';

        $currencyString = strtolower($currencyString);

        if(strpos($currencyString, '$') !== false || strpos($currencyString, 'usd') !== false) {

            $result = str_replace(['$', 'usd'], '', $currencyString);
            $code   = 'USD';

        }

        if(strpos($currencyString, '₼') !== false || strpos($currencyString, 'azn') !== false) {

            $result = str_replace(['₼', 'azn'], '', $currencyString);
            $code   = 'AZN';

        }

        if(strpos($currencyString, 'aed') !== false) {

            $result = str_replace('aed', '', $currencyString);
            $code   = 'AED';

        }

        return [
            'value' =>  trim($result),
            'code'  =>  $code
        ];
    }

	public function convert($value, $from, $to) {
		if (isset($this->currencies[$from])) {
			$from = $this->currencies[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->currencies[$to])) {
			$to = $this->currencies[$to]['value'];
		} else {
			$to = 1;
		}

        $to     = $to ?: 0;
        $from   = $from ?: 0;
        $value  = $value ?: 0;

        $value = (int)$value;

		return (int)($value * ($to / $from));
	}
	
	public function getId($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['id'];
		} else {
			return 0;
		}
	}

	public function getCode($currency_id)
	{
		$currency = $this->CI->Currency_model->filter(['id' => $currency_id])->one();
		if($currency)
		{
			return $currency->code;
		}
		return false;

	}

	public function getSymbolLeft($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_left'];
		} else {
			return '';
		}
	}

	public function getSymbolRight($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['symbol_right'];
		} else {
			return '';
		}
	}

	public function getDecimalPlace($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['decimal_place'];
		} else {
			return 0;
		}
	}

	public function getValue($currency) {
		if (isset($this->currencies[$currency])) {
			return $this->currencies[$currency]['value'];
		} else {
			return 0;
		}
	}

	public function has($currency) {
		return isset($this->currencies[$currency]);
	}

	public function formatter($value, $from, $to)
	{
		$symbol_left = $this->currencies[$to]['symbol_left'];
		$symbol_right = $this->currencies[$to]['symbol_right'];
		$decimal_place = $this->currencies[$to]['decimal_place'];

		$value = ceil($value);

		$amount = $this->convert($value, $to, $from);

		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

		$string .= ceil($amount);

		if ($symbol_right) {
			$string .= $symbol_right;
		}
		return $string;

	}

	public function formatter_without_symbol($value, $from, $to)
	{
		return ceil($this->convert(ceil($value), $to, $from));
	}

	public function frm($amount)
	{
		$symbol_left = $this->currencies[get_setting('currency')]['symbol_left'];
		$symbol_right = $this->currencies[get_setting('currency')]['symbol_right'];
		$decimal_place = $this->currencies[get_setting('currency')]['decimal_place'];


		$string = '';

		if ($symbol_left) {
			$string .= $symbol_left;
		}

		$string .= number_format($amount, (int)$decimal_place, '.',false);

		if ($symbol_right) {
			$string .= $symbol_right;
		}
		return $string;		

	}

	public function refresh($force = false)
	{
		
		$currency_data = [];

		if ($force)
		{
			$query = $this->CI->db->query("SELECT * FROM wc_currency WHERE code != 'AZN'");
		}
		else
		{
			$query = $this->CI->db->query("SELECT * FROM wc_currency WHERE code != 'AZN' AND updated_at < '" .  date('Y-m-d H:i:s', strtotime('-1 day')) . "'");
		}

            $results = $query->result_array();

		if(isset($_GET['test'])) {
            $url = "https://www.cbar.az/currencies/".date('d.m.Y').".xml";

            $xml = @simplexml_load_file($url);

            if($xml) {
                $data = $xml->xpath('ValType[@Type="Xarici valyutalar"]');
                foreach ($data[0]->Valute as $currency)
                {
                    foreach($results as $result)
                    {
                        if($result['code'] == $currency[0]['Code'])
                        {
                            $this->CI->db->query("UPDATE wc_currency SET value = '" . (float)$currency->Value . "', updated_at = '" . date('Y-m-d H:i:s') . "' WHERE code = '" . $result['code'] . "'");
                        }
                    }
                }
            }
        }


	}

	public function round_up($number, $precision = 2)
	{
		$fig = pow(10, $precision);
		return (ceil($number * $fig) / $fig);
	}
}