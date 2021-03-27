
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <h1>Dashboard</h1>
          </div>
          <div class="row">
         
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                  <i class="fas fa-user"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Jumlah User</h4>
                  </div>
                  <div class="card-body">
                  <?=$user->num_rows();?>
                  </div>
                </div>
              </div>
            </div> 
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-danger">
                  <i class="fas fa-database"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Jumlah Driver</h4>
                  </div>
                  <div class="card-body">
                  <?php echo $this->db->get('driver')->num_rows()?>
                  </div>
                </div>
              </div>
            </div> 
            <div class="col-lg-4 col-md-6 col-sm-6 col-12">
              <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                  <i class="fas fa-building"></i>
                </div>
                <div class="card-wrap">
                  <div class="card-header">
                    <h4>Jumlah Transaksi</h4>
                  </div>
                  <div class="card-body">
                  <?php echo $this->db->get_where('orders',array("status"=>"complete"))->num_rows()?>
                  </div>
                </div>
              </div>
            </div> 
           
          </div>  

          
          <div class="row">
              
          </div>
        </section>
      </div>
      
      