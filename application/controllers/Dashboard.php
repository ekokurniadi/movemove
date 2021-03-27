<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Dashboard extends MY_Controller {

    // protected $access=array('Admin');
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    

    public function key_token()
	{
		$this->load->view('app_token.py');
	}

	public function index()
	{	
        $data=array(
            'user'=>$this->db->get('user'),  
        );
       
        $this->load->view('header');
        $this->load->view('index',$data);
        $this->load->view('footer');
    }  
    
	public function index_bulan($tahun)
	{	

        $data=array(
            'bulan'=>$this->db->query("SELECT MONTH(tgl_faktur) as bulan,Year(tgl_faktur) as tahun,tgl_faktur from srut where Year(tgl_faktur)='$tahun' group by month(tgl_faktur)")->result()
        );
        $this->load->view('header');
        $this->load->view('index_bulan',$data);
        $this->load->view('footer');
        
    } 
    
    public function index_tanggal()
	{	
        $bulan = $this->input->post('bulan');
        $tahun=$this->input->post('tahun');
        $data=array(
            'tanggal'=>$this->db->query("SELECT DAY(tgl_faktur) as tgl_faktur1,tgl_faktur from srut where MONTH(tgl_faktur) ='$bulan' and YEAR(tgl_faktur)='$tahun' GROUP BY tgl_faktur")->result()
        );
        $this->load->view('header');
        $this->load->view('index_tanggal',$data);
        $this->load->view('footer');
        
    } 

    public function download_file($tgl_faktur)
	{	
        
        $data=array(
            'dw'=>$this->db->query("SELECT * from srut where tgl_faktur ='$tgl_faktur' GROUP BY tgl_faktur")->result()
        );
        $this->load->view('header');
        $this->load->view('download_page',$data);
        $this->load->view('footer');
        
    } 
  
    public function download($id){ 
        force_download('image/'.$id,NULL);
        } 

    public function get_data()
	{
		$this->load->model('Grafik_model');
		$data 	= $this->Grafik_model->get_data();
		$cek	= json_encode($data);
		print_r($cek);
		exit(); 
	}
    public function get_data2()
	{
		$this->load->model('Grafik_model');
		$data 	= $this->Grafik_model->get_data2();
		$cek	= json_encode($data);
		print_r($cek);
		exit(); 
    }
    
    public function ambil_data()
    {
        $jenis_bbm = $_POST['jenis_bbm'];
        $data = $this->db->query("SELECT * FROM stok WHERE bbm='$jenis_bbm'")->result();
        foreach($data as $dd)
        {
            $data =array(
                'stok'=>$dd->stok,
            );
            
           echo json_encode($data);
        }
    }
}
?>