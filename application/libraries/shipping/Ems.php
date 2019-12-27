<?php

class Ems {

	protected $CI;
	protected $username = 'E66846';
	protected $password = 'E66846';
	protected $calculate_url = 'https://osb.epg.gov.ae/ebs/genericapi/ratecalculator';
	protected $tracking_url = 'https://osb.epg.gov.ae/ebs/genericapi/tracking';
	protected $booking_url = 'https://osb.epg.gov.ae/ebs/genericapi/booking';

	protected $sender = [
		'contact_name' 		=> 'Matlab',
		'company_name' 		=> 'Mimelon',
		'address'			=> 'Dubai',
		'city'				=> 1,
		'contact_mobile' 	=> '9713234534534',
		'contact_phone'		=> '9713234534534',
		'email' 			=> 'info@mimelon.com',
		'zip_code'			=> '1121',
		'state'				=> 'Dubai',
		'country'			=> 971
	];
	
	public function __construct()
	{
		$this->CI = &get_instance();
	}

	public $countries = [
		'1' => 247,
		'2' => 1,
		'2' => 2,
		'3' => 3,
		'4' => 303,
		'5' => 261,
		'6' => 4,
		'7' => 5,
		'9' => 6,
		'10' => 7,
		'11' => 8,
		'12' => 9,
		'197' => 10,
		'13' => 11,
		'14' => 12,
		'15' => 13,
		'16' => 15,
		'17' => 16,
		'18' => 18,
		'19' => 19,
		'20' => 20,
		'21' => 21,
		'22' => 22,
		'23' => 23,
		'24' => 24,
		'25' => 25,
		'26' => 26,
		'27' => 27,
		'28' => 28,
		'30' => 29,
		'32' => 30,
		'33' => 31,
		'34' => 32,
		'146' => 305,
		'35' => 33,
		'36' => 34,
		'37' => 35,
		'38' => 36,
		'195' => 37,
		'39' => 38,
		'40' => 40,
		'41' => 41,
		'42' => 42,
		'43' => 43,
		'44' => 44,
		'46' => 47,
		'47' => 48,
		'159' => 331,
		'48' => 49,
		'236' => 59,
		'49' => 50,
		'49' => 307,
		'50' => 51,
		'51' => 53,
		'53' => 54,
		'54' => 55,
		'151' => 56,
		'55' => 57,
		'56' => 58,
		'57' => 60,
		'58' => 61,
		'59' => 62,
		'60' => 63,
		'43' => 270,
		'62' => 64,
		'63' => 65,
		'64' => 66,
		'65' => 67,
		'66' => 68,
		'67' => 69,
		'68' => 70,
		'69' => 71,
		'70' => 73,
		'71' => 74,
		'72' => 75,
		'74' => 76,
		'75' => 77,
		'76' => 78,
		'78' => 79,
		'79' => 80,
		'80' => 81,
		'81' => 82,
		'82' => 83,
		'83' => 84,
		'222' => 85,
		'84' => 86,
		'85' => 87,
		'86' => 88,
		'87' => 89,
		'88' => 90,
		'54' => 272,
		'89' => 91,
		'90' => 92,
		'91' => 93,
		'92' => 94,
		'93' => 95,
		'95' => 97,
		'96' => 98,
		'97' => 99,
		'98' => 100,
		'99' => 101,
		'100' => 102,
		'101' => 103,
		'102' => 104,
		'103' => 105,
		'105' => 106,
		'52' => 107,
		'106' => 108,
		'107' => 109,
		'222' => 266,
		'108' => 110,
		'109' => 111,
		'110' => 112,
		'111' => 113,
		'112' => 114,
		'113' => 115,
		'114' => 116,
		'115' => 117,
		'116' => 118,
		'116' => 312,
		'117' => 119,
		'118' => 120,
		'119' => 121,
		'120' => 122,
		'121' => 123,
		'122' => 125,
		'123' => 126,
		'124' => 128,
		'125' => 129,
		'125' => 313,
		'126' => 130,
		'127' => 131,
		'128' => 133,
		'129' => 134,
		'130' => 135,
		'131' => 136,
		'132' => 137,
		'159' => 139,
		'133' => 140,
		'134' => 141,
		'135' => 142,
		'136' => 143,
		'137' => 250,
		'138' => 144,
		'139' => 262,
		'140' => 145,
		'141' => 146,
		'142' => 147,
		'239' => 275,
		'143' => 148,
		'144' => 149,
		'145' => 150,
		'146' => 151,
		'147' => 152,
		'148' => 314,
		'148' => 153,
		'149' => 154,
		'150' => 155,
		'243' => 156,
		'151' => 315,
		'152' => 157,
		'153' => 158,
		'154' => 159,
		'155' => 160,
		'156' => 161,
		'157' => 259,
		'158' => 162,
		'160' => 163,
		'161' => 164,
		'162' => 166,
		'163' => 263,
		'244' => 258,
		'164' => 168,
		'165' => 169,
		'166' => 170,
		'167' => 171,
		'168' => 172,
		'169' => 174,
		'170' => 175,
		'171' => 176,
		'172' => 177,
		'173' => 178,
		'174' => 179,
		'175' => 180,
		'176' => 181,
		'177' => 182,
		'151' => 253,
		'87' => 254,
		'197' => 184,
		'178' => 185,
		'179' => 186,
		'198' => 187,
		'180' => 188,
		'159' => 328,
		'181' => 318,
		'4' => 189,
		'181' => 190,
		'182' => 191,
		'183' => 192,
		'184' => 194,
		'185' => 195,
		'240' => 244,
		'186' => 196,
		'187' => 197,
		'188' => 198,
		'189' => 199,
		'190' => 200,
		'191' => 201,
		'192' => 202,
		'245' => 329,
		'193' => 203,
		'194' => 204,
		'194' => 205,
		'245' => 278,
		'195' => 206,
		'196' => 208,
		'178' => 321,
		'179' => 322,
		'180' => 324,
		'199' => 209,
		'200' => 210,
		'202' => 211,
		'203' => 212,
		'204' => 213,
		'205' => 214,
		'76' => 215,
		'206' => 216,
		'207' => 217,
		'208' => 218,
		'13' => 257,
		'209' => 219,
		'210' => 220,
		'212' => 221,
		'213' => 222,
		'197' => 223,
		'214' => 224,
		'215' => 225,
		'216' => 226,
		'217' => 227,
		'218' => 228,
		'219' => 229,
		'220' => 230,
		'221' => 971,
		'223' => 231,
		'225' => 232,
		'226' => 233,
		'227' => 234,
		'228' => 235,
		'229' => 236,
		'230' => 237,
		'231' => 239,
		'232' => 238,
		'231' => 325,
		'233' => 240,
		'223' => 242,
		'181' => 310,
		'235' => 243,
		'240' => 297,
		'236' => 311,
		'237' => 245,
		'238' => 246,
	];

	public function calculate($width = 10, $height = 10, $length = 10, $weight=1000)
	{
		$width = (int)$width;
		$height = (int)$height;
		$length = (int)$length;
		$weight = (int)$weight;


		$country_id = get_country_id();
		
		$this->CI->db->where('country_id', $country_id);
		$query = $this->CI->db->get('ems_country');
		
		if($query->num_rows())
		{
			
			$country_row = $query->row();
			$express_zone = $country_row->express_zone;
			$express_price = 0;

			$premium_zone = $country_row->premium_zone;
			$premium_price = 0;

			$standard_zone = $country_row->standard_zone;
			$standard_price = 0;
		}
		else {
		    return [];
        }


		
		//Express Price
		if($express_zone == 1)
		{	
			if($weight <= 500)
			{
				$express_price = 33;
			}
			if($weight > 500 && $weight <= 5000)
			{
				$express_price = 33;
				$express_price += ceil((($weight-500)/500))*5;
			}
			elseif($weight > 5000 && $weight <= 10000)
			{
				$express_price = 78;
				$express_price += ceil((($weight-5000)/500))*7;
			}
			elseif($weight > 10000 && $weight <= 20000)
			{
				$express_price = 148;
				$express_price += ceil((($weight-10000)/500))*6;
			}
			elseif($weight > 20000 && $weight <= 30000)
			{
				$express_price = 246;
				$express_price += ceil((($weight-20000)/500))*6;
			}
			elseif($weight > 30000)
			{
				$express_price = 306;
				$express_price += ceil((($weight-30000)/500))*4;
			}
		}
		elseif($express_zone == 2)
		{	
			if($weight <= 500)
			{
				$express_price = 55;
			}
			if($weight > 500 && $weight <= 2500)
			{
				$express_price = 55;
				$express_price += ceil((($weight-500)/500))*11;
			}
			elseif($weight > 2500 && $weight <= 5000)
			{
				$express_price = 91;
				$express_price += ceil((($weight-2500)/500))*9;
			}
			elseif($weight > 5000 && $weight <= 9500)
			{
				$express_price = 136;
				$express_price += ceil((($weight-5000)/500))*7;
			}
			elseif($weight > 9500)
			{
				$express_price = 199;
				$express_price += ceil((($weight-9500)/500))*6;
			}
		}
		elseif($express_zone == 3)
		{	
			if($weight <= 500)
			{
				
				$express_price = 70;
			}
			if($weight > 500 && $weight <= 9500)
			{
				$express_price = 70;
				$express_price += ceil((($weight-500)/500))*10;
			}
			elseif($weight > 9500 && $weight <= 19500)
			{
				$express_price = 250;
				$express_price += ceil((($weight-9500)/500))*8;
			}
			elseif($weight > 19500)
			{
				$express_price = 410;
				$express_price += ceil((($weight-19500)/500))*9;
			}
		}
		elseif($express_zone == 4)
		{	
			if($weight <= 500)
			{
				$express_price = 75;
			}
			if($weight > 500 && $weight <= 5000)
			{
				$express_price = 75;
				$express_price += ceil((($weight-500)/500))*12;
			}
			elseif($weight > 5000)
			{
				$express_price = 183;
				$express_price += ceil((($weight-5000)/500))*10;
			}
		}
		elseif($express_zone == 5)
		{	
			if($weight <= 500)
			{
				$express_price = 80;
			}
			if($weight > 500)
			{
				$express_price = 80;
				$express_price += ceil((($weight-500)/500))*15;
			}
		}

		if($weight <= 2000)
		{
			//Standard Price
			if($standard_zone == 1)
			{	
				if($weight <= 20)
				{
					$standard_price = 3;
				}
				elseif($weight > 20 && $weight <= 50)
				{
					$standard_price = 4;
				}
				elseif($weight > 50 && $weight <=100)
				{
					$standard_price = 5;
				}
				elseif($weight > 100 && $weight <=250)
				{
					$standard_price = 10;
				}
				elseif($weight > 250 && $weight <=500)
				{
					$standard_price = 19;
				}
				elseif($weight > 500 && $weight <=1000)
				{
					$standard_price = 33;
				}
				elseif($weight > 1000 && $weight <= 2000)
				{
					$standard_price = 55;
				}
			}
			elseif($standard_zone == 2)
			{	
				if($weight <= 20)
				{
					$standard_price = 4;
				}
				elseif($weight > 20 && $weight <= 50)
				{
					$standard_price = 5;
				}
				elseif($weight > 50 && $weight <=100)
				{
					$standard_price = 7;
				}
				elseif($weight > 100 && $weight <=250)
				{
					$standard_price = 16;
				}
				elseif($weight > 250 && $weight <=500)
				{
					$standard_price = 28;
				}
				elseif($weight > 500 && $weight <=1000)
				{
					$standard_price = 42;
				}
				elseif($weight > 1000 && $weight <= 2000)
				{
					$standard_price = 80;
				}
			}
			elseif($standard_zone == 3)
			{	
				if($weight <= 20)
				{
					$standard_price = 5;
				}
				elseif($weight > 20 && $weight <= 50)
				{
					$standard_price = 6;
				}
				elseif($weight > 50 && $weight <=100)
				{
					$standard_price = 9;
				}
				elseif($weight > 100 && $weight <=250)
				{
					$standard_price = 19;
				}
				elseif($weight > 250 && $weight <=500)
				{
					$standard_price = 32;
				}
				elseif($weight > 500 && $weight <=1000)
				{
					$standard_price = 50;
				}
				elseif($weight > 1000 && $weight <= 2000)
				{
					$standard_price = 90;
				}
			}
			elseif($standard_zone == 4)
			{	
				if($weight <= 20)
				{
					$standard_price = 6;
				}
				elseif($weight > 20 && $weight <= 50)
				{
					$standard_price = 7;
				}
				elseif($weight > 50 && $weight <=100)
				{
					$standard_price = 11;
				}
				elseif($weight > 100 && $weight <=250)
				{
					$standard_price = 23;
				}
				elseif($weight > 250 && $weight <=500)
				{
					$standard_price = 40;
				}
				elseif($weight > 500 && $weight <=1000)
				{
					$standard_price = 60;
				}
				elseif($weight > 1000 && $weight <= 2000)
				{
					$standard_price = 110;
				}
			}
			elseif($standard_zone == 5)
			{	
				if($weight <= 20)
				{
					$standard_price = 7;
				}
				elseif($weight > 20 && $weight <= 50)
				{
					$standard_price = 8;
				}
				elseif($weight > 50 && $weight <=100)
				{
					$standard_price = 13;
				}
				elseif($weight > 100 && $weight <=250)
				{
					$standard_price = 29;
				}
				elseif($weight > 250 && $weight <=500)
				{
					$standard_price = 56;
				}
				elseif($weight > 500 && $weight <=1000)
				{
					$standard_price = 80;
				}
				elseif($weight > 1000 && $weight <= 2000)
				{
					$standard_price = 140;
				}
			}
		}
		

		if($weight <= 30000)
		{
			if($height <= 110 && $width <= 70 && $length <= 70)
			{
				//Premium Price
				if($premium_zone == 1)
				{	
					if($weight <= 500)
					{
						$premium_price = 45;
					}
					if($weight > 500 && $weight <= 5000)
					{
						$premium_price = 45;
						$premium_price += ceil((($weight-500)/500))*25;
					}
					elseif($weight > 5000 && $weight <= 14500)
					{
						$premium_price = 270;
						$premium_price += ceil((($weight-5000)/500))*20;
					}
					elseif($weight > 14500 && $weight <= 30000)
					{
						$premium_price = 650;
						$premium_price += ceil((($weight-14500)/500))*12;
					}
				}
				elseif($premium_zone == 2)
				{	
					if($weight <= 500)
					{
						$premium_price = 70;
					}
					if($weight > 500 && $weight <= 3000)
					{
						$premium_price = 70;
						$premium_price += ceil((($weight-500)/500))*25;
					}
					elseif($weight > 3000 && $weight <= 10000)
					{
						$premium_price = 195;
						$premium_price += ceil((($weight-3000)/500))*30;
					}
					elseif($weight > 10000 && $weight <= 30000)
					{
						$premium_price = 615;
						$premium_price += ceil((($weight-10000)/500))*25;
					}
				}
				elseif($premium_zone == 3)
				{	
					if($weight <= 500)
					{
						$premium_price = 85;
					}
					if($weight > 500 && $weight <= 5000)
					{
						$premium_price = 85;
						$premium_price += ceil((($weight-500)/500))*30;
					}
					elseif($weight > 5000 && $weight <= 5500)
					{
						$premium_price = 355;
						$premium_price += ceil((($weight-5000)/500))*25;
					}
					elseif($weight > 5500 && $weight <= 30000)
					{
						$premium_price = 380;
						$premium_price += ceil((($weight-5500)/500))*20;
					}
				}
				elseif($premium_zone == 4)
				{	
					if($weight <= 500)
					{
						$premium_price = 100;
					}
					if($weight > 500 && $weight <= 5000)
					{
						$premium_price = 100;
						$premium_price += ceil((($weight-500)/500))*35;
					}
					elseif($weight > 5000 && $weight <= 15000)
					{
						$premium_price = 415;
						$premium_price += ceil((($weight-5000)/500))*30;
					}
					elseif($weight > 15000 && $weight <= 30000)
					{
						$premium_price = 1015;
						$premium_price += ceil((($weight-15000)/500))*25;
					}
				}
				elseif($premium_zone == 5)
				{	
					if($weight <= 500)
					{
						$premium_price = 120;
					}
					if($weight > 500 && $weight <= 5000)
					{
						$premium_price = 120;
						$premium_price += ceil((($weight-500)/500))*35;
					}
					if($weight > 5000 && $weight <= 15000)
					{
						$premium_price = 435;
						$premium_price += ceil((($weight-5000)/500))*30;
					}
					if($weight > 15000 && $weight <= 30000)
					{
						$premium_price = 1035;
						$premium_price += ceil((($weight-15000)/500))*25;
					}
				}
			}
		}
		

		if($express_price > 0) {
			$arr[] = [
				'name' 		=> 'Express',
				'code'		=> 'ems_express',
				'price'		=> $this->CI->currency->formatter_without_symbol($express_price, 'AED', 'USD'),
				'show_price' => $this->CI->currency->formatter($express_price, 'AED', $this->CI->data['current_currency']),
				'currency' => 'USD'
			];
		}

		if($premium_price > 0) {
			$arr[] = [
				'name' 		=> 'Premium',
				'code'		=> 'ems_premium',
				'price'		=> $this->CI->currency->formatter_without_symbol($premium_price, 'AED', 'USD'),
				'show_price' => $this->CI->currency->formatter($premium_price, 'AED', $this->CI->data['current_currency']),
				'currency' => 'USD'
			];
		}

		if($standard_price > 0) {
			
			$standard_price += 10;
			$arr[] = [
				'name' 		=> 'Standard',
				'code'		=> 'ems_standard',
				'price'		=> $this->CI->currency->formatter_without_symbol($standard_price, 'AED', 'USD'),
				'show_price' => $this->CI->currency->formatter($standard_price, 'AED', $this->CI->data['current_currency']),
				'currency' => 'AED'
			];
		}
		

		// $dimension_unit = 'Centimetre';
		// $weight_unit = 'Grams';

		// $to_country = $this->countries[get_country_id()];


		// $xml = '
		// 	<soapenv:Envelope 	xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:epg="http://epg.generic.calculaterate/">
		// 		<soapenv:Header>
		// 			<epg:AuthHeader>
		// 				<epg:AccountNo>'.$this->username.'</epg:AccountNo>
		// 				<epg:Password>'.$this->password.'</epg:Password>
		// 			</epg:AuthHeader>
		// 		</soapenv:Header>
		// 		<soapenv:Body>
		// 			<epg:CalculateRateRequest>
		// 			<epg:RateCalculationRequest>
		// 				<epg:ShipmentType>ALL</epg:ShipmentType>
		// 				<epg:ServiceType>International</epg:ServiceType>
		// 				<epg:ContentTypeCode>None</epg:ContentTypeCode>
		// 				<epg:OriginState></epg:OriginState>
		// 				<epg:OriginCity>1</epg:OriginCity>
		// 				<epg:DestinationCountry>'.$to_country.'</epg:DestinationCountry>
		// 				<epg:DestinationState></epg:DestinationState>
		// 				<epg:DestinationCity></epg:DestinationCity>
		// 				<epg:Height>'.$height.'</epg:Height>
		// 				<epg:Width>'.$width.'</epg:Width>
		// 				<epg:Length>'.$length.'</epg:Length>
		// 				<epg:DimensionUnit>'.$dimension_unit.'</epg:DimensionUnit>
		// 				<epg:Weight>'.$weight.'</epg:Weight>
		// 				<epg:WeightUnit>'.$weight_unit.'</epg:WeightUnit>
		// 				<epg:CalculationCurrencyCode>USD</epg:CalculationCurrencyCode>
		// 				<!--Optional:-->
		// 				<epg:IsRegistered>Yes</epg:IsRegistered>
		// 				<epg:ProductCode>PRO-26</epg:ProductCode>
		// 			</epg:RateCalculationRequest>
		// 			</epg:CalculateRateRequest>
		// 		</soapenv:Body>
		// 	</soapenv:Envelope>
		// ';

		// $headers = 	[
		// 	'SOAPAction: http://epg.generic.calculaterate/CalculatePriceRate',
		// 	"Content-type: text/xml;charset=\"utf-8\"",
		// 	"Accept: text/xml",
		// 	"Cache-Control: no-cache",
		// 	"Pragma: no-cache",
		// 	"Content-length: ".strlen($xml),
		// ];

		// $response = $this->request($xml, $headers, $this->calculate_url);


		// $arr = [];
		// if($response)
		// {
		// 	$clean_xml = str_ireplace(['soapenv:', 'soap:', 'ns2:'], '', $response);
		// 	$xml = simplexml_load_string($clean_xml);

		// 	if(isset($xml) && !empty($xml))
		// 	{
		// 		foreach($xml->Body->CalculateRateResponse->RateCalculation->RateList as $Rate)
		// 		{
		// 			if($Rate->ContentType != 'Document')
		// 			{
		// 				if($Rate->TotalPrice != 0)
		// 				{
		// 					$arr[] = [
		// 						'name' 		=> (string)$Rate->ShipmentType,
		// 						'code'		=> 'ems_'.strtolower((string)$Rate->ShipmentType),
		// 						'price'		=> (string)$Rate->TotalPrice,
		// 						'show_price' => $this->CI->currency->formatter($Rate->TotalPrice, 'USD', $this->CI->data['current_currency']),
		// 						'currency' => 'USD'
		// 					];
		// 				}
						

		// 			}

					
		// 		}

		// 		function sortByOrder($a, $b) {
		// 			return $a['price'] - $b['price'];
		// 		}
				
		// 		usort($arr, 'sortByOrder');
		// 	}
		// }
		if(isset($arr) && !empty($arr))
		{
			return $arr;
		}
		else
		{
			return [];
		}

	}

	public function tracking($reference)
	{
		$xml = '<soapenv:Envelope 	xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:epg="http://epg.generic.tracking/">
			<soapenv:Header>
				<epg:AuthHeader>
					<epg:AccountNo>'.$this->username.'</epg:AccountNo>
					<epg:Password>'.$this->password.'</epg:Password>
				</epg:AuthHeader>
			</soapenv:Header>
			<soapenv:Body>
				<epg:TrackShipmentByAwbNo>
					<epg:AwbNo>'.$reference.'</epg:AwbNo>
				</epg:TrackShipmentByAwbNo>
			</soapenv:Body>
		</soapenv:Envelope>';
		

		$headers = 	[
			'SOAPAction: http://epg.generic.tracking/TrackShipmentByAwbNo',
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"Content-length: ".strlen($xml),
		];

		$result = $this->request($xml, $headers, $this->tracking_url);
		$data = [];
		//var_dump($result); die;
		if($result)
		{
			$clean_xml = str_ireplace(['soapenv:', 'soap:', 'ns2:'], '', $result);
			
			$xml = simplexml_load_string($clean_xml);

			$rows = $xml->Body->TrackShipmentResponse->TrackShipmentResult->trackingDetails;

			foreach($rows->TrackHistory as $row)
			{
				$data[] = [
					'TransactionType'				=> (string)$row->TransactionType,
					'TransactionDescription'		=> (string)$row->TransactionDescription,
					'TranscationDate'				=> (string)$row->TranscationDate,
					'Origin'						=> (string)$row->Origin,
					'Destination'					=> (string)$row->Destination,
					'Status'						=> (string)$row->Status,
					'remarks'						=> (string)$row->remarks
				];
			}
			return $data;
		}
	}

	public function booking($receiver = [])
	{
		$to_country = $this->countries[$receiver['country']];
		$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:epg="http://epg.generic.booking/">
		<soapenv:Header>
			<epg:AuthHeader>
				<!--Optional:-->
				<epg:AccountNo>'.$this->username.'</epg:AccountNo>
				<!--Optional:-->
				<epg:Password>'.$this->password.'</epg:Password>
			</epg:AuthHeader>
		</soapenv:Header>
		<soapenv:Body>
		<epg:CreateBookingRequest>
		<epg:BookingRequest>
		<epg:SenderContactName>'.$this->sender['contact_name'].'</epg:SenderContactName>
		<epg:SenderCompanyName>'.$this->sender['company_name'].'</epg:SenderCompanyName>
		<epg:SenderAddress>'.$this->sender['address'].'</epg:SenderAddress>
		<epg:SenderCity>'.$this->sender['city'].'</epg:SenderCity>
		<epg:SenderContactMobile>'.$this->sender['contact_mobile'].'</epg:SenderContactMobile>
		<epg:SenderContactPhone>'.$this->sender['contact_phone'].'</epg:SenderContactPhone>
		<epg:SenderEmail>'.$this->sender['email'].'</epg:SenderEmail>
		<epg:SenderZipCode>'.$this->sender['zip_code'].'</epg:SenderZipCode>
		<epg:SenderState>'.$this->sender['state'].'</epg:SenderState>
		<epg:SenderCountry>'.$this->sender['country'].'</epg:SenderCountry>
		<epg:ReceiverContactName>'.$receiver['contact_name'].'</epg:ReceiverContactName>
		<epg:ReceiverCompanyName>'.$receiver['company_name'].'</epg:ReceiverCompanyName>
		<epg:ReceiverAddress>'.$receiver['address'].'</epg:ReceiverAddress>
		<epg:ReceiverCity>'.$receiver['city'].'</epg:ReceiverCity>
		<epg:ReceiverContactMobile>'.$receiver['contact_mobile'].'</epg:ReceiverContactMobile>
		<epg:ReceiverContactPhone>'.$receiver['contact_phone'].'</epg:ReceiverContactPhone>
		<epg:ReceiverEmail>'.$receiver['email'].'</epg:ReceiverEmail>
		<epg:ReceiverZipCode>'.$receiver['zip_code'].'</epg:ReceiverZipCode>
		<epg:ReceiverState>'.$receiver['state'].'</epg:ReceiverState>
		<epg:ReceiverCountry>'.$to_country.'</epg:ReceiverCountry>
		<epg:ReferenceNo>1241</epg:ReferenceNo>
		<epg:ReferenceNo1>242423</epg:ReferenceNo1>
		<epg:ReferenceNo2>3345</epg:ReferenceNo2>
		<epg:ReferenceNo3>4646</epg:ReferenceNo3>
		<epg:ContentTypeCode>NonDocument</epg:ContentTypeCode>
		<epg:NatureType>11</epg:NatureType>
		<epg:Service>International</epg:Service>
		<epg:ShipmentType>'.$receiver['shipment_type'].'</epg:ShipmentType>
		<epg:DeleiveryType></epg:DeleiveryType>
		<epg:Registered>No</epg:Registered>
		<epg:PaymentType>Credit</epg:PaymentType>
		<epg:CODAmount></epg:CODAmount>
		<epg:CODCurrency></epg:CODCurrency>
		<epg:CommodityDescription>sample</epg:CommodityDescription>
		<epg:Pieces>1</epg:Pieces>
		<epg:Weight>'.$receiver['weight'].'</epg:Weight>
		<epg:WeightUnit>Grams</epg:WeightUnit>
		<epg:Length>'.$receiver['length'].'</epg:Length>
		<epg:Width>'.$receiver['weight'].'</epg:Width>
		<epg:Height>'.$receiver['length'].'</epg:Height>
		<epg:DimensionUnit>Centimetre</epg:DimensionUnit>
		<epg:ItemValue>124</epg:ItemValue>
		<epg:ValueCurrency>USD</epg:ValueCurrency>
		<epg:ProductCode></epg:ProductCode>
		<epg:SpecialInstructionsID></epg:SpecialInstructionsID>
		<epg:DeliveryInstructionsID></epg:DeliveryInstructionsID>
		<epg:LabelType>RPT</epg:LabelType>
		<epg:RequestSource></epg:RequestSource>
		<epg:isReturnItem>No</epg:isReturnItem>
		<epg:SendMailToSender>No</epg:SendMailToSender>
		<epg:SendMailToReceiver>Yes</epg:SendMailToReceiver>
		<epg:CustomDeclarations>
		<!--1 or more repetitions:-->
		<epg:CustomDeclarationRequest>
		<epg:HSCode></epg:HSCode>
		<epg:TotalUnits>1</epg:TotalUnits>
		<epg:Weight>1</epg:Weight>
		<epg:Value>1</epg:Value>
		<epg:DeclaredCurrency></epg:DeclaredCurrency>
		<epg:FileName></epg:FileName>
		<epg:FileType></epg:FileType>
		<epg:FileContent></epg:FileContent>
		<!--Optional:-->
		<epg:CreatedBy>siraj</epg:CreatedBy>
		</epg:CustomDeclarationRequest>
		</epg:CustomDeclarations>
		</epg:BookingRequest>
		</epg:CreateBookingRequest>
		</soapenv:Body>
		</soapenv:Envelope>';

		//return $xml;

		$headers = 	[
			'SOAPAction: http://epg.generic.booking/CreateBooking',
			"Content-type: text/xml;charset=\"utf-8\"",
			"Accept: text/xml",
			"Cache-Control: no-cache",
			"Pragma: no-cache",
			"Content-length: ".strlen($xml),
		];
		
		$result = $this->request($xml, $headers, $this->booking_url);

		if($result)
		{
			$clean_xml = str_ireplace(['soapenv:', 'soap:', 'ns2:'], '', $result);
			
			$xml = simplexml_load_string($clean_xml);
			$number = $xml->Body->CreateBookingResponse->BookingResponse->AWBNumber;
		}

		return $number;
	}

	public function request($xml, $headers = [], $url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERPWD, $this->username.":".$this->password);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_TIMEOUT, 100);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec ($ch);
		curl_close ($ch);

		return $response;
	}
}