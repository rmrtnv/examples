<?php
class Spot extends CI_Controller {
	
	    public function __construct()
        {
                parent::__construct();
                $this->load->model('spot_model');
        }

        public function searchspots()
        {		
				$data = $this->input->post('data');
				$data = json_decode($data);
                //print_r(array('surfer_level' => 2, 'wave_direction' => 1));
				$this->spot_model->search($data);
        }
        public function getspot($id)
        {
				$this->spot_model->get_spot($id);
                //echo 'getspot ';
        }
        public function createspot()
        {
				$data = $this->input->post('data');
				$data = json_decode($data, true);
                $this->spot_model->create_spot($data);
        }
        public function updatespot()
        {
				$data = $this->input->post('data');
				$data = json_decode($data, true);
                $this->spot_model->update_spot($data);
        }
        public function deletespot($id)
        {
				$this->spot_model->delete_spot($id);
			   //echo 'deletespot';
        }
        public function getlenght()
        {
				$this->spot_model->get_lenght();
        }
        public function searchspotsaround()
        {
                //echo 'searchspotsaround';
				//$this->input->raw_input_stream;
				//echo $this->input->input_stream('r');
				//$jsonData = json_decode(trim(file_get_contents('php://input')), true);
				//print_r($jsonData);
				$data = $this->input->post('data');
				$data = json_decode($data);
				$this->spot_model->spots_around($data);
				//echo $data->r;
        }
}
?>