<?php
class Spot_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }
		
		public function get_spot($id)
		{
				//echo $id;
				$query = $this->db->select('*')->from('main')->where('id', $id);
				$data = $query->get()->result_array();
				$data = $this->pre_send($data);
				//$data = array($data, "best_season"=>array('from' => 1, 'to' => 2));
				$data = json_encode($data);
				//$data = str_replace('\r\n', "<br>", $data);
				$this->output->set_content_type('application/json')->set_output($data);
				//echo $data;
				//print_r($data);
		}
		
		public function create_spot($data)
		{
				$data = $this->pre_store($data);
				$this->db->insert('main', $data);
		}	

		public function update_spot($data)
		{
				$data = $this->pre_store($data);
				$this->db->replace('main', $data);
		}	
		
		public function search($data)
		{
				//$data = array('surfer_level' => 2, 'wave_direction' => 1);
				//print_r($data);
				$this->db->select('*')->from('main');
				if (isset($data->wave_power))
				{
					foreach(explode(',', $data->wave_power) as $key => $value) $this->db->like('wave_power', $value);
					unset($data->wave_power);
				}
				//if (isset($data->dangers)) echo '!';
				$query = $this->db->where((array)$data)->get();
				//echo $query;
				$data = $query->result_array();
				$data = $this->pre_send((array)$data);
				$data = json_encode($data);
				$this->output->set_content_type('application/json')->set_output($data);
		}
		
		public function delete_spot($id)
		{
				//echo $id;
				$this->db->delete('main', array('id' => $id));
				//$data = $query->get();
		}
		
		public function get_lenght()
		{
				echo $this->db->count_all('main');
		}
		
		public function spots_around($data)
		{
				$dist = $data->r;
				$lat = $data->lat;
				$lon = $data->lon;
				$count = 100;
				$tname = 'main';
				$query = $this->db->query("SELECT name, lat, lon, 3956 * 2 * 
          ASIN(SQRT( POWER(SIN(($lat - abs(lat))*pi()/180/2),2)
          +COS($lat*pi()/180 )*COS(abs(lat)*pi()/180)
          *POWER(SIN(($lon-lon)*pi()/180/2),2))) 
          as distance FROM $tname WHERE 
          lon between ($lon-$dist/abs(cos(radians($lat))*69)) 
          and ($lon+$dist/abs(cos(radians($lat))*69)) 
          and lat between ($lat-($dist/69)) 
          and ($lat+($dist/69)) 
          having distance < $dist ORDER BY distance limit $count;");
				$data = $query->result_array();
				$data = json_encode($data);
				$this->output->set_content_type('application/json')->set_output($data);
				//print_r($data);
		}

		private function pre_send($data)
		{
				//print_r($data);
				//SURFER LEVEL ?
				//SHARK RATE OR FACTOR ?
				foreach(array_keys($data) as $key)
				{	
					$data[$key]['wave_power'] = explode(',', $data[$key]['wave_power']);
					$data[$key]['dangers'] = explode(',', $data[$key]['dangers']);
					$data[$key]['coords'] = array($data[$key]['lat'], $data[$key]['lon']);
					//$swell_size = array("suitable_swell_size" => array('from' => $data[$key]['suitable_swell_size_from'], 'to' => $data[$key]['suitable_swell_size_to']));
					$data[$key]['suitable_swell_size'] = array('from' => $data[$key]['suitable_swell_size_from'], 'to' => $data[$key]['suitable_swell_size_to']);
					$data[$key]['best_season'] = array('from' => $data[$key]['best_season_from'], 'to' => $data[$key]['best_season_to']);
					unset($data[$key]['suitable_swell_size_from']);
					unset($data[$key]['suitable_swell_size_to']);
					unset($data[$key]['best_season_from']);
					unset($data[$key]['best_season_to']);
					unset($data[$key]['lat']);
					unset($data[$key]['lon']);
					
					//$data[$key] = array($data[$key], $swell_size);
				}
				//print_r($data);
				$data = array('spots' => $data);
				return $data;
		}
		
		private function pre_store($data)
		{
				$data['wave_power'] = implode(',', $data['wave_power']);
				$data['dangers'] = implode(',', $data['dangers']);
				$data['lat'] = $data['coords'][0];
				$data['lon'] = $data['coords'][1];
				$data['suitable_swell_size_from'] = $data['suitable_swell_size']['from'];
				$data['suitable_swell_size_to'] = $data['suitable_swell_size']['to'];
				$data['best_season_from'] = $data['best_season']['from'];
				$data['best_season_to'] = $data['best_season']['to'];
				unset($data['coords']);
				unset($data['suitable_swell_size']);
				unset($data['best_season']);
				//print_r($data);
				return $data;
		}
}
?>