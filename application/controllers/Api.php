<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {


    public function tes_fcm()
	{
		$driver="44";
		$server_key = get_setting('server_fcm_driver');
		$token = get_data('driver','id',$driver,'token');
		$title = "Tes Aja";
		$body = "Hai Ini Tes notifikasi";
		$screen ="list_trx";
		$hasil = $this->send_notif($server_key,$token,$title, $body, $screen);
		$result = json_decode($hasil);
		if ($result->success == 1) {
			echo "berhasil";
		} else {
			echo "gagal";
		}

	}

	public function tes_fcm_notif()
	{
		$server_key = get_setting('server_fcm_driver');
		$token = get_data('driver','id',"3",'token');
		$title = "Tes Aja";
		$body = "Hai Ini Tes";
		$screen ="list_notif";
		$hasil = $this->send_notif($server_key,$token,$title, $body, $screen);
		$result = json_decode($hasil);
		if ($result->success == 1) {
			echo "berhasil";
		} else {
			echo "gagal";
		}

	}

	public function getNotif(){
		if($_POST){
			$id= $this->input->post('id');
			$deleted = $this->input->post('deleted');
			$result=array();
			$data = $this->db->query("SELECT a.*,b.nama FROM notifikasi a join driver b on a.driver=b.id where a.driver = '$id' and a.deleted ='$deleted' order by a.id DESC");
			
			$response = array();
			if($data->num_rows() <= 0){
				$result=array(
					"status"=>"0",
					"pesan"=>"Tidak ada notifikasi"
				);
				echo json_encode($result);
			}else{
				foreach($data->result() as $rows){
					$sub_array=array();
					$sub_array[]="Hallo ".$rows->nama.", ".$rows->pesan;
					$sub_array[]=$rows->status;
					$sub_array[]=formatTanggal(substr($rows->created,0,10));
					$sub_array[]=substr($rows->created,11,19);
					$sub_array[]=$rows->id;
					$response[]=$sub_array;
				}
				
				$result=array(
					"status"=>"1",
					"pesan"=>"Success",
					"values"=>$response
				);
				echo json_encode($result);
			}
		}
	}

	public function getTotalNotif(){
		if($_POST){
			$result=array();
			$id = $this->input->post("id");
			$status = $this->input->post("status");
			$data = $this->db->get_where('notifikasi',array('driver'=>$id,'status'=>$status,'deleted'=>0));
			$result=array(
				"status"=>"1",
				"total"=>$data->num_rows()
			);
			echo json_encode($result);
		}
	}

    public function login()
	{
		if ($_POST) {
			$username = $this->input->post('username');
			$password = $this->input->post('password');
			$token_fcm = $this->input->post('token_fcm');
            $level = $this->input->post('level');
			$cek = $this->db->get_where('driver', array('username' => $username, 'password' => $password,'level'=> $level));

			if ($cek->num_rows() == 1) {
				$data = $cek->row();

				//cek driver aktif
				if ($level == 'driver') {
					$aktif_driver = $this->db->get_where('driver', array('id'=>$data->id))->row()->status;
					if ($aktif_driver == '0') {
						$result = array(
							'status' => "0",
							'pesan' => 'Akun kamu belum aktif, silahkan hubungi admin'
						);
						echo json_encode($result);
						exit();
					}
				}

				// update fcm token
				$this->db->where('id', $data->id);
				$this->db->update('driver', array('token'=>$token_fcm));
				$result = array(
                    'status'=>"1",
					'id' => $data->id,
					'nama' => $data->nama,
					'password' => $data->password,
					'email' => $data->username,
					'level' => $data->level,
					'nomor_wa' => $data->nomor_wa,
					'pesan' => "Selamat datang dan selamat beraktifitas $data->nama"
				);
				echo json_encode($result);
			} else {
				$result = array(
					'status' => "0",
					'pesan' => 'Gagal'
				);
				echo json_encode($result);
			}
		}
	}

    public function getSaldo(){
        if($_POST){
            $id = $this->input->post('id');
			$cek = $this->db->get_where('driver', array('id' => $id)); 
            if($cek->num_rows() > 0){
                $result = array(
                    "saldo"=>number_format($cek->row()->saldo,0,',','.'),
                    "status"=>"1",
                    "pesan"=>"Success"
                );
                echo json_encode($result);
            }
        }
    }

	public function getUser(){
        if($_POST){
            $id = $this->input->post('id');
			$cek = $this->db->get_where('driver', array('id' => $id)); 
            if($cek->num_rows() > 0){
                $result = array(
					"nama"=>$cek->row()->nama,
					"nomor_wa"=>$cek->row()->nomor_wa == null ? "0" :$cek->row()->nomor_wa,
					"alamat"=>$cek->row()->alamat ==null ? "-": $cek->row()->alamat,
					"kota"=>$cek->row()->kota == null ? "-" :$cek->row()->kota,
					"password"=>$cek->row()->password,
					"nomor_plat"=>$cek->row()->plat_kendaraan ==null ? "-" :$cek->row()->plat_kendaraan,
					"no_rekening"=>$cek->row()->no_rekening ==null ? "-" :$cek->row()->no_rekening,
					"nama_bank"=>$cek->row()->nama_bank ==null ? "-" :$cek->row()->nama_bank,
					"nama_rekening"=>$cek->row()->nama_rekening ==null ? "-" :$cek->row()->nama_rekening,
					"jenis_kendaraan"=>$cek->row()->jenis_mobil ==null ? "-" : $cek->row()->jenis_mobil,
					"tahun_kendaraan"=>$cek->row()->tahun_kendaraan ==null ? "-" : $cek->row()->tahun_kendaraan,
					"berlaku_sim"=>$cek->row()->masa_aktif_sim == null ? "0000-00-00" : $cek->row()->masa_aktif_sim,
					"berlaku_stnk"=>$cek->row()->masa_aktif_stnk == null ? "0000-00-00" : $cek->row()->masa_aktif_stnk,
					"berlaku_kir"=>$cek->row()->masa_aktif_kir == null ? "0000-00-00" : $cek->row()->masa_aktif_kir,
                    "foto"=>$cek->row()->foto_profil,
                    "foto_sim"=>$cek->row()->foto_sim,
                    "foto_stnk"=>$cek->row()->foto_stnk,
                    "foto_kir"=>$cek->row()->foto_kir,
					"username"=>$cek->row()->username,
                    "status"=>"1",
                    "pesan"=>"Success"
                );
                echo json_encode($result);
            }
        }
    }

	public function orderView(){
		$driver = $this->input->post("driver");
		$orders=$this->db->query("SELECT * FROM orders b where b.status ='new_order' and b.kode_order not in (SELECT a.kode_order from history_orders a where a.driver='$driver' and a.kode_order=b.kode_order)");
		$data=array();
		if($orders->num_rows() > 0){
			foreach($orders->result() as $rows){
				$sub_array=array();
				$sub_array[]=$rows->id;
				$sub_array[]=$rows->kode_order;
				$sub_array[]=$rows->nama_customer;
				$sub_array[]=$rows->kota.", ".$rows->alamat_jemput;
				$sub_array[]=$rows->kota_kirim.", ".$rows->alamat_antar;
				$sub_array[]=number_format($rows->ongkos_driver,0,',','.');
				$sub_array[]=$rows->barang_customer;
				$sub_array[]=$rows->nomor_wa;
				$sub_array[]=substr($rows->jam,0,5);
				$sub_array[]=format_hari_tanggal($rows->jadwal_kirim);
				$data[]=$sub_array;
			}
			echo json_encode(array(
				"status"=>"1",
				"pesan"=>"Success",
				"data"=>$data,
			));
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Orderan belum tersedia"
			));
		}
	}

	

	public function orderViewProcess(){
		$driver = $this->input->post("driver");
		$orders=$this->db->query("SELECT * FROM history_orders where driver='$driver' and status ='process'");
		$data=array();
		if($orders->num_rows() > 0){
			foreach($orders->result() as $rows){
				$sub_array=array();
				$sub_array[]=$rows->id_order;
				$sub_array[]=$rows->kode_order;
				$sub_array[]=$rows->nama_customer;
				$sub_array[]=$rows->kota.", ".$rows->alamat_jemput;
				$sub_array[]=$rows->kota_kirim.", ".$rows->alamat_antar;
				$sub_array[]=number_format($rows->ongkos_driver,0,',','.');
				$sub_array[]=$rows->barang_customer;
				$sub_array[]=$rows->nomor_wa;
				$sub_array[]=substr($rows->jam,0,5);
				$sub_array[]=format_hari_tanggal($rows->jadwal_kirim);
				$data[]=$sub_array;
			}
			echo json_encode(array(
				"status"=>"1",
				"pesan"=>"Success",
				"data"=>$data,
			));
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Orderan belum tersedia"
			));
		}
	}

	public function orderViewSuccess(){
		$driver = $this->input->post("driver");
		$orders=$this->db->query("SELECT * FROM history_orders where driver='$driver' and status ='completed'");
		$data=array();
		if($orders->num_rows() > 0){
			foreach($orders->result() as $rows){
				$sub_array=array();
				$sub_array[]=$rows->id_order;
				$sub_array[]=$rows->kode_order;
				$sub_array[]=$rows->nama_customer;
				$sub_array[]=$rows->kota.", ".$rows->alamat_jemput;
				$sub_array[]=$rows->kota_kirim.", ".$rows->alamat_antar;
				$sub_array[]=number_format($rows->ongkos_driver,0,',','.');
				$sub_array[]=$rows->barang_customer;
				$sub_array[]=$rows->nomor_wa;
				$sub_array[]=substr($rows->jam,0,5);
				$sub_array[]=format_hari_tanggal($rows->jadwal_kirim);
				$data[]=$sub_array;
			}
			echo json_encode(array(
				"status"=>"1",
				"pesan"=>"Success",
				"data"=>$data,
			));
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Orderan belum tersedia"
			));
		}
	}

	public function orderViewCancel(){
		$driver = $this->input->post("driver");
		$orders=$this->db->query("SELECT * FROM history_orders where driver='$driver' and status ='canceled'");
		$data=array();
		if($orders->num_rows() > 0){
			foreach($orders->result() as $rows){
				$sub_array=array();
				$sub_array[]=$rows->id_order;
				$sub_array[]=$rows->kode_order;
				$sub_array[]=$rows->nama_customer;
				$sub_array[]=$rows->kota.", ".$rows->alamat_jemput;
				$sub_array[]=$rows->kota_kirim.", ".$rows->alamat_antar;
				$sub_array[]=number_format($rows->ongkos_driver,0,',','.');
				$sub_array[]=$rows->barang_customer;
				$sub_array[]=$rows->nomor_wa;
				$sub_array[]=substr($rows->jam,0,5);
				$sub_array[]=format_hari_tanggal($rows->jadwal_kirim);
				$data[]=$sub_array;
			}
			echo json_encode(array(
				"status"=>"1",
				"pesan"=>"Success",
				"data"=>$data,
			));
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Orderan belum tersedia"
			));
		}
	}

	public function getNotification(){

		$notifPendaftaranDriver = $this->db->query("SELECT * FROM driver where confirm='0' order by id desc");
		
		$result=array();
		$data=array();
		
		if($notifPendaftaranDriver->num_rows() > 0){
			foreach($notifPendaftaranDriver->result() as $rows){
				$sub_array=array();
				$sub_array[]=$rows->id;
				$sub_array[]=$rows->nama;
				$sub_array[]=$rows->created;
				$sub_array[]="driver/update/".$rows->id;
				$sub_array[]="Hai, ada Driver yang mendaftar atas nama ".$rows->nama.", silahkan cek data nya";
				$data[]=$sub_array;
			}
			echo json_encode(array(
				"total_notif"=>$notifPendaftaranDriver->num_rows(),
				"pesan"=>"Kamu memiliki ".$notifPendaftaranDriver->num_rows()." pemberitahuan",
				"data"=>$data,
			));
		}else{
			echo json_encode(array(
				"total_notif"=>$notifPendaftaranDriver->num_rows(),
				"pesan"=>"Kamu memiliki ".$notifPendaftaranDriver->num_rows()." pemberitahuan",
				"data"=>$data,
			));
		}
	}


	public function updateDriver(){
		if($_POST){
			$id = $this->input->post('id');
			$result=array();
			$data=array(
				"nama"=>$this->input->post('nama'),
				"nomor_wa"=>$this->input->post('nomor_wa'),
				"password"=>$this->input->post('password'),
				"alamat"=>$this->input->post('alamat'),
				"kota"=>$this->input->post('kota'),
				"plat_kendaraan"=>$this->input->post('nomor_plat_kendaraan'),
				"jenis_mobil"=>$this->input->post('jenis_kendaraan'),
				"tahun_kendaraan"=>$this->input->post('tahun_kendaraan'),
				"masa_aktif_sim"=>$this->input->post('masa_berlaku_sim'),
				"masa_aktif_stnk"=>$this->input->post('masa_berlaku_stnk'),
				"masa_aktif_kir"=>$this->input->post('masa_berlaku_kir'),
				"no_rekening"=>$this->input->post('no_rekening'),
				"nama_bank"=>strtoupper($this->input->post('nama_bank')),
				"nama_rekening"=>strtoupper($this->input->post('nama_rekening')),
			);

			$this->db->where('id',$id);
			$this->db->update('driver',$data);
			$result=array(
				"status"=>"1",
				"pesan"=>"Update Data Berhasil"
			);
			echo json_encode($result);
		}else{
			echo json_encode(
				array(
				"status"=>"0",
				"pesan"=>"Something went wrong"
			));
		}
	}


    public function set_status_driver()
	{
		if ($_GET) {
			$id = $this->input->get('id');
			$update = $this->db->query("UPDATE driver SET status_online='0' WHERE id='$id'");
			if ($update) {
				echo "berhasil update $id";
			}
			exit();
		}
		$update = $this->db->query("UPDATE driver SET status_online='0'");
		if ($update) {
			echo "berhasil update";
		}
	}

	public function update_notifikasi()
	{
		if ($_GET) {
			$id = $this->input->get('id');
			$update = $this->db->query("UPDATE notifikasi SET status='2' WHERE id='$id'");
			if ($update) {
				echo "berhasil update $id";
			}
			exit();
		}
	}


	public function updateFotoProfile(){

      
		$id = $_POST['id'];
		$image = $_POST['image'];
    	$name = $_POST['name'];
		$folderPath="./image/".$name;
    	$realImage = base64_decode($image);
 
   		$files = file_put_contents("./image/profil_user/".$name, $realImage);
		
		
        
		$data = array(
            "foto_profil"=> $name,	
        );

		$this->db->where('id',$id);
        $this->db->update('driver',$data);
        
		echo json_encode(array(
			"status"=>"1",
			"pesan"=>"Foto Profil berhasil di perbarui",
           
        ));
    
    }

	public function updateFotoSim(){

      
		$id = $_POST['id'];
		$image = $_POST['image'];
    	$name = $_POST['name'];
    	$realImage = base64_decode($image);
 
   		$files = file_put_contents("./image/sim/".$name, $realImage);
		
		
        
		$data = array(
            "foto_sim"=> $name,	
        );

		$this->db->where('id',$id);
        $this->db->update('driver',$data);
        
		echo json_encode(array(
			"status"=>"1",
			"pesan"=>"Foto Sim berhasil di upload",
           
        ));
    
    }

	public function updateFotoSTNK(){

      
		$id = $_POST['id'];
		$image = $_POST['image'];
    	$name = $_POST['name'];
    	$realImage = base64_decode($image);
 
   		$files = file_put_contents("./image/stnk/".$name, $realImage);
		
		
        
		$data = array(
            "foto_stnk"=> $name,	
        );

		$this->db->where('id',$id);
        $this->db->update('driver',$data);
        
		echo json_encode(array(
			"status"=>"1",
			"pesan"=>"Foto STNK berhasil di upload",
           
        ));
    
    }

	public function updateFotoKir(){

      
		$id = $_POST['id'];
		$image = $_POST['image'];
    	$name = $_POST['name'];
    	$realImage = base64_decode($image);
 
   		$files = file_put_contents("./image/kir/".$name, $realImage);
		
		
        
		$data = array(
            "foto_kir"=> $name,	
        );

		$this->db->where('id',$id);
        $this->db->update('driver',$data);
        
		echo json_encode(array(
			"status"=>"1",
			"pesan"=>"Foto Kir berhasil di upload",
           
        ));
    
    }

	public function delete_notifikasi(){
		if ($_GET) {
			$id = $this->input->get('id');
			$delete = $this->db->query("UPDATE notifikasi SET deleted='1' WHERE id='$id'");
			if ($delete) {
				echo "berhasil delete $id";
			}
			exit();
		}
	}


	public function blastOrder(){
		date_default_timezone_set("Asia/Jakarta");
		// diatas untuk perhitungan waktu
		$dataOrders = $this->db->get_where('orders',array('driver'=>NULL,'status'=>NULL,'status_pengantaran'=>NULL));
		$data=array();
		$limit = $dataOrders->num_rows();
		foreach($dataOrders->result() as $rows){
			$driverOnSameArea = $this->db->query("select * from driver where kota like '%$rows->kota%'");
			$driverRand      = $this->db->query("SELECT * from driver order by rand() LIMIT 1");
			$tanggalSekarang = date($rows->created);
			$tanggalCreated  = date($rows->created, strtotime("+15 seconds"));
			
			$waktu2= strtotime($tanggalCreated);
			$waktu1= strtotime($tanggalSekarang);
			$totalDriver = $driverOnSameArea->num_rows();
			
			if($waktu1 < $waktu2){
				foreach($driverOnSameArea->result() as $row){
					$sub_array=array();
					$sub_array[]=$rows->id;
					$sub_array[]=$rows->kode_order;
					$sub_array[]=$rows->nama_customer;
					$sub_array[]=$row->nama;
					$sub_array[]=$row->token;
					$data[]=$sub_array;	
				}
				// echo json_encode(array("status"=>"1","data"=>$data)); 
				// sleep(15);
			}else{
				foreach($driverRand->result() as $row){
					$sub_array=array();
					$sub_array[]=$rows->id;
					$sub_array[]=$rows->kode_order;
					$sub_array[]=$rows->nama_customer;
					$sub_array[]=$row->nama;
					$sub_array[]=$row->token;
					$data[]=$sub_array;	
				}
			}
		}
		echo json_encode(array("status"=>"1","data"=>$data)); 

		  
	}

	public function blastById(){
		
		$id = $_GET['id'];
		
		date_default_timezone_set("Asia/Jakarta");
	
		$dataOrders = $this->db->get_where('orders',array('driver'=>NULL,'status'=>'new_order','status_pengantaran'=>NULL,'id'=>$id));

		$kota = $dataOrders->row()->kota;
		$tanggalSekarang = date($dataOrders->row()->created);
		$tanggalCreated15Detik  = date($dataOrders->row()->created, strtotime("+15 seconds"));

		$waktu15Detik = strtotime($tanggalCreated15Detik);
		$waktuAwal= strtotime($tanggalSekarang);
		$data=array();

		
			$driverOnSameArea = $this->db->query("select * from driver where kota like '%$kota%'");
				foreach($driverOnSameArea->result() as $rows ){
					$sub_array=array();
					$sub_array[]=$dataOrders->row()->id;
					$sub_array[]=$dataOrders->row()->kode_order;
					$sub_array[]=$dataOrders->row()->nama_customer;
					$sub_array[]=$rows->nama;
					$sub_array[]=$rows->token;
					$data[]=$sub_array;	
				}
			
			echo json_encode(array("result"=>$data));
	}

    public function register(){
        if($_POST){
            $username = $this->input->post("username");
            $password = $this->input->post("password");
            $nama     = $this->input->post("nama");
            $no_wa    = $this->input->post("nomor_wa");
            $status   = $this->input->post("status");
            $level    = $this->input->post("level");
            $token    = $this->input->post("token_fcm");
            $statusOn = $this->input->post("status_online");
            $saldo    = $this->input->post("saldo");
			$fotoProf = "default.png";
            $result   =array();
            $data=array(
                "username"=>$username,
                "password"=>$password,
                "nama"=>$nama,
                "nomor_wa"=>$no_wa,
                "token"=>$token,
                "status"=>$status,
                "level"=>$level,
                "status_online"=>$statusOn,
                "saldo"=>$saldo,
				"foto_profil"=>$fotoProf,
            );
			$cek= $this->db->get_where('driver',array('username'=>$username,'password'=>$password))->row();
			if($username == $cek->username){
				$result=array(
                    "status"=>"0",
                    "pesan" =>"Registrasi gagal username sudah digunakan silahkan gunakan username lain"
                );
				echo json_encode($result);
			}else{
			$insert = $this->db->insert('driver',$data);
			if($insert){
				$cek= $this->db->get_where('driver',array('username'=>$username,'password'=>$password))->row();
				$driver= $cek->id;
				
				// $cek= $this->db->get_where('driver',array('username'=>$username,'password'=>$password))->row();
				// $driver= $cek->id;
			
				$title = "Selamat bergabung di Move On";
				$body = "Selamat bergabung di Move On. Segera lengkapi data anda agar segera mendapatkan order dari konsumen. ";
				$screen ="list_notif";
				// $hasil = $this->send_notifikasi($server_key,$token,$title, $body, $screen);
				$insertNotif =array(
					"driver"=>$driver,
					"pesan"=>$body,
					"status"=>"1",
					"deleted"=>"0"
				);
				$this->db->insert('notifikasi',$insertNotif);
				$result=array(
					"status"=>"1",
					"pesan" =>"Registrasi berhasil silahkan login ke akun anda.",
					"idDriver"=>$cek->id,
				);
				echo json_encode($result);
				}else{
					$result=array(
						"status"=>"0",
						"pesan" =>"Registrasi gagal silahkan coba lagi nanti."
					); 
					echo json_encode($result);
				}
			}
        } else {
            $result = array(
                'status' => "0",
                'pesan' => 'Gagal'
            );
            echo json_encode($result);
        }
    }


	public function terima_order(){
		if($_POST){
			$driver = $this->input->post("driver");
			$id     = $this->input->post('id');
			$cekSaldo = $this->db->get_where('driver',array('id'=>$driver))->row();
			$dataOrder = $this->db->get_where('orders',array('id'=>$id))->row();
			$data=array(
				'id_order'=>$dataOrder->id,
				'kode_order' => $dataOrder->kode_order,
				'nama_customer' => $dataOrder->nama_customer,
				'nomor_wa' => $dataOrder->nomor_wa,
				'jadwal_kirim' => $dataOrder->jadwal_kirim,
				'jam' => $dataOrder->jam,
				'kota' => $dataOrder->kota,
				'lantai' => $dataOrder->lantai,
				'parkir_mobil' => $dataOrder->parkir_mobil,
				'kota_kirim' => $dataOrder->kota_kirim,
				'lantai_kirim' => $dataOrder->lantai_kirim,
				'parkir_mobil_kirim' => $dataOrder->parkir_mobil_kirim,
				'bantuan_customer' => $dataOrder->bantuan_customer,
				'barang_customer' => $dataOrder->barang_customer,
				'bantuan_driver' => $dataOrder->bantuan_driver,
				'bantuan_kenek' =>$dataOrder->bantuan_kenek,
				'biaya_tol' => $dataOrder->biaya_tol,
				'biaya_overload' => $dataOrder->biaya_overload,
				'waktu_tunggu' => $dataOrder->waktu_tunggu,
				'total_customer_bayar' => $dataOrder->total_customer_bayar,
				'biaya_layanan' => $dataOrder->biaya_layanan,
				'ongkos_driver' => $dataOrder->ongkos_driver,
				'cara_pembayaran' => $dataOrder->cara_pembayaran,
				'catatan' => $dataOrder->catatan,
				'status' => "process",
				'status_pengantaran' => "process",
				'driver' => $driver,
				'created_at' => $dataOrder->created_at,
				'alamat_jemput' => $dataOrder->alamat_jemput,
				'alamat_antar' => $dataOrder->alamat_antar,
			);
			if($cekSaldo->saldo >= 20000){
				if($dataOrder->driver==NULL || $dataOrder->driver==""){
					$insert = $this->db->insert('history_orders',$data);
					$update = $this->db->query("UPDATE orders set status='process', driver='$driver',status_pengantaran='process' where id='$id'");
	
					echo json_encode(array(
						"status"=>"1",
						"pesan"=>"Berhasil"
					));
	
				}else{
					echo json_encode(array(
						"status"=>"0",
						"pesan"=>"Order sudah diambil oleh driver lain"
					));
				}
			}else{
				echo json_encode(array(
					"status"=>"2",
					"pesan"=>"Saldo kamu tidak cukup"
				));
			}
			
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Gagal"
			));
		}
	}


	public function cancel_order(){
		if($_POST){
			$driver = $this->input->post("driver");
			$id     = $this->input->post('id');
			$cekSaldo = $this->db->get_where('driver',array('id'=>$driver))->row();
			$dataOrder = $this->db->get_where('orders',array('id'=>$id))->row();
			$data=array(
				'id_order'=>$dataOrder->id,
				'kode_order' => $dataOrder->kode_order,
				'nama_customer' => $dataOrder->nama_customer,
				'nomor_wa' => $dataOrder->nomor_wa,
				'jadwal_kirim' => $dataOrder->jadwal_kirim,
				'jam' => $dataOrder->jam,
				'kota' => $dataOrder->kota,
				'lantai' => $dataOrder->lantai,
				'parkir_mobil' => $dataOrder->parkir_mobil,
				'kota_kirim' => $dataOrder->kota_kirim,
				'lantai_kirim' => $dataOrder->lantai_kirim,
				'parkir_mobil_kirim' => $dataOrder->parkir_mobil_kirim,
				'bantuan_customer' => $dataOrder->bantuan_customer,
				'barang_customer' => $dataOrder->barang_customer,
				'bantuan_driver' => $dataOrder->bantuan_driver,
				'bantuan_kenek' =>$dataOrder->bantuan_kenek,
				'biaya_tol' => $dataOrder->biaya_tol,
				'biaya_overload' => $dataOrder->biaya_overload,
				'waktu_tunggu' => $dataOrder->waktu_tunggu,
				'total_customer_bayar' => $dataOrder->total_customer_bayar,
				'biaya_layanan' => $dataOrder->biaya_layanan,
				'ongkos_driver' => $dataOrder->ongkos_driver,
				'cara_pembayaran' => $dataOrder->cara_pembayaran,
				'catatan' => $dataOrder->catatan,
				'status' => "canceled",
				'status_pengantaran' => "canceled",
				'driver' => $driver,
				'created_at' => $dataOrder->created_at,
				'alamat_jemput' => $dataOrder->alamat_jemput,
				'alamat_antar' => $dataOrder->alamat_antar,
			);
			
				if($dataOrder->driver==NULL || $dataOrder->driver==""){
					$insert = $this->db->insert('history_orders',$data);
					// $update = $this->db->query("UPDATE orders set status='process', driver='$driver',status_pengantaran='process' where id='$id'");
	
					echo json_encode(array(
						"status"=>"1",
						"pesan"=>"Berhasil Cancel Order"
					));
	
				}else{
					echo json_encode(array(
						"status"=>"0",
						"pesan"=>"Gagal"
					));
				}
			
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Gagal"
			));
		}
	}

	public function cancelOrderByDriver(){
		$driver = $this->input->post('driver');
		$id     = $this->input->post('id');
		$update = $this->db->query("UPDATE history_orders set status='canceled', status_pengantaran='canceled by driver' where driver='$driver' and id_order='$id'");
		$updateOrder = $this->db->query("UPDATE orders set status='canceled', status_pengantaran='canceled by driver' where id='$id'");

		if($update && $updateOrder){
			echo json_encode(array(
				"status"=>"1",
				"pesan"=>"Berhasil melakukan Cancel",
			));
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Gagal melakukan Cancel",
			));
		}

	}
	public function finishOrder(){
		$driver = $this->input->post('driver');
		$id     = $this->input->post('id');
		$update = $this->db->query("UPDATE history_orders set status='completed', status_pengantaran='completed' where driver='$driver' and id_order='$id'");
		$updateOrder = $this->db->query("UPDATE orders set status='completed', status_pengantaran='completed' where id='$id'");
		$saldo = $this->db->query("SELECT ongkos_driver from orders where id='$id'")->row()->ongkos_driver;
		$tambahSaldo = $this->db->query("UPDATE driver set saldo = saldo + '$saldo' where id='$driver'");

		

		if($update && $updateOrder && $tambahSaldo){
			echo json_encode(array(
				"status"=>"1",
				"pesan"=>"Berhasil melakukan Cancel",
			));
			$data=array(
				"driver"=>$driver,
				"pesan"=>"Selamat, kamu telah menyelesaikan 1 orderan, saldo mu akan ditambahkan senilai Rp. ".number_format($saldo,0,',','.')."",
				"status"=>"1",
				"deleted"=>"0",
			);
			$insert = $this->db->insert('notifikasi',$data);
		}else{
			echo json_encode(array(
				"status"=>"0",
				"pesan"=>"Gagal melakukan Cancel",
			));
		}

	}

    public function set_status_driver_aktif()
	{
		if ($_GET) {
			$id = $this->input->get('id');
			$update = $this->db->query("UPDATE driver SET status_online='1' WHERE id='$id'");
			if ($update) {
				echo "berhasil update $id";
			}
			exit();
		}
		$update = $this->db->query("UPDATE driver SET status_online='1'");
		if ($update) {
			echo "berhasil update";
		}
	}

	private function send_notifikasi($server_key,$token,$title, $body, $screen)
	{
		# agar diparse sebagai JSON di browser
		header('Content-Type:application/json');

		# atur zona waktu sender server ke Jakarta (WIB / GMT+7)
		date_default_timezone_set("Asia/Jakarta");


		$headers = [
		'Content-Type:application/json',
		'Accept:application/json',
		'Authorization: key='.$server_key.''
		];


		// echo $post_raw_json;
		// exit();
		

		# Inisiasi CURL request
		$ch = curl_init();

		# atur CURL Options
		curl_setopt_array($ch, array(
		CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send', # URL endpoint
		CURLOPT_HTTPHEADER => $headers, # HTTP Headers
		CURLOPT_RETURNTRANSFER => 1, # return hasil curl_exec ke variabel, tidak langsung dicetak
		CURLOPT_FOLLOWLOCATION => 1, # atur flag followlocation untuk mengikuti bila ada url redirect di server penerima tetap difollow
		CURLOPT_CONNECTTIMEOUT => 60, # set connection timeout ke 60 detik, untuk mencegah request gantung saat server mati
		CURLOPT_TIMEOUT => 60, # set timeout ke 120 detik, untuk mencegah request gantung saat server hang
		CURLOPT_POST => 1, # set method request menjadi POST
		CURLOPT_POSTFIELDS => '{"notification": {"body": "'.$body.'","title": "'.$title.'","sound": "default","badge":"1"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "screen": "'.$screen.'", "status": "done"}, "to": "'.$token.'"}', # attached post data dalam bentuk JSON String,
		// CURLOPT_VERBOSE => 1, # mode debug
		// CURLOPT_HEADER => 1, # cetak header
		CURLOPT_SSL_VERIFYPEER => true  
		));

		# eksekusi CURL request dan tampung hasil responsenya ke variabel $resp
		$resp = curl_exec($ch);

		# validasi curl request tidak error
		if (curl_errno($ch) == false) {
		# jika curl berhasil
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == 200) {
		  # http code === 200 berarti request sukses (harap pastikan server penerima mengirimkan http_code 200 jika berhasil)
		//   return $resp;
			$send = '{"notification": {"body": "'.$body.'","title": "'.$title.'","sound": "default","badge":"1"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "screen": "'.$screen.'", "status": "done"}, "to": "'.$token.'"}';
			$this->db->insert('log_notif', array('log'=>$send,'resp'=>$resp));
			return $resp;
		} else {
		  # selain itu request gagal (contoh: error 404 page not found)
		  // echo 'Error HTTP Code : '.$http_code."\n";
		  
			$send = '{"notification": {"body": "'.$body.'","title": "'.$title.'","sound": "default","badge":"1"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "screen": "'.$screen.'", "status": "done"}, "to": "'.$token.'"}';
			$this->db->insert('log_notif', array('log'=>$send,'resp'=>$resp));
			return $resp;
		}
		} else {
		# jika curl error (contoh: request timeout)
		# Daftar kode error : https://curl.haxx.se/libcurl/c/libcurl-errors.html
		// echo "Error while sending request, reason:".curl_error($ch);
		}

		# tutup CURL
		curl_close($ch);
	}

    private function send_notif($server_key,$token,$title, $body, $screen)
	{
		# agar diparse sebagai JSON di browser
		header('Content-Type:application/json');

		# atur zona waktu sender server ke Jakarta (WIB / GMT+7)
		date_default_timezone_set("Asia/Jakarta");


		$headers = [
		'Content-Type:application/json',
		'Accept:application/json',
		'Authorization: key='.$server_key.''
		];


		// echo $post_raw_json;
		// exit();
		

		# Inisiasi CURL request
		$ch = curl_init();

		# atur CURL Options
		curl_setopt_array($ch, array(
		CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send', # URL endpoint
		CURLOPT_HTTPHEADER => $headers, # HTTP Headers
		CURLOPT_RETURNTRANSFER => 1, # return hasil curl_exec ke variabel, tidak langsung dicetak
		CURLOPT_FOLLOWLOCATION => 1, # atur flag followlocation untuk mengikuti bila ada url redirect di server penerima tetap difollow
		CURLOPT_CONNECTTIMEOUT => 60, # set connection timeout ke 60 detik, untuk mencegah request gantung saat server mati
		CURLOPT_TIMEOUT => 60, # set timeout ke 120 detik, untuk mencegah request gantung saat server hang
		CURLOPT_POST => 1, # set method request menjadi POST
		CURLOPT_POSTFIELDS => '{"notification": {"body": "'.$body.'","title": "'.$title.'","sound": "default","badge":"1"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "screen": "'.$screen.'", "status": "done"}, "to": "'.$token.'"}', # attached post data dalam bentuk JSON String,
		// CURLOPT_VERBOSE => 1, # mode debug
		// CURLOPT_HEADER => 1, # cetak header
		CURLOPT_SSL_VERIFYPEER => true  
		));

		# eksekusi CURL request dan tampung hasil responsenya ke variabel $resp
		$resp = curl_exec($ch);

		# validasi curl request tidak error
		if (curl_errno($ch) == false) {
		# jika curl berhasil
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		if ($http_code == 200) {
		  # http code === 200 berarti request sukses (harap pastikan server penerima mengirimkan http_code 200 jika berhasil)
		//   return $resp;
			$send = '{"notification": {"body": "'.$body.'","title": "'.$title.'","sound": "default","badge":"1"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "screen": "'.$screen.'", "status": "done"}, "to": "'.$token.'"}';
			$this->db->insert('log_notif', array('log'=>$send,'resp'=>$resp));
			return $resp;
		} else {
		  # selain itu request gagal (contoh: error 404 page not found)
		  // echo 'Error HTTP Code : '.$http_code."\n";
		  
			$send = '{"notification": {"body": "'.$body.'","title": "'.$title.'","sound": "default","badge":"1"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "screen": "'.$screen.'", "status": "done"}, "to": "'.$token.'"}';
			$this->db->insert('log_notif', array('log'=>$send,'resp'=>$resp));
			return $resp;
		}
		} else {
		# jika curl error (contoh: request timeout)
		# Daftar kode error : https://curl.haxx.se/libcurl/c/libcurl-errors.html
		// echo "Error while sending request, reason:".curl_error($ch);
		}

		# tutup CURL
		curl_close($ch);
	}

}

/* End of file Api.php */

?>