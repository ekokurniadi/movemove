
 <div class="main-content">
<section class="section">
  <div class="section-header">
    <h1> Import Barang </h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>dashboard"><i class="fa fa-dashboard"></i> Home</a></div>
      <div class="breadcrumb-item"><a href="#"> Import Barang </a></div>
    </div>
  </div>

  <div class="section-body">
  <div class="row">
      <div class="col-12 col-md-12 col-lg-12">
        <div class="card">
        <div class="card-header">
            <h4>Form Import Barang </h4>
        </div>
        <form action="<?php echo base_url('barang/import_barang_aksi')?>" method="post" class="form-horizontal" enctype="multipart/form-data">
	   
              <div class="form-group">
                <label class="col-sm-2 control-label" for="varchar">File Xls <?php echo form_error('nama') ?></label>
                <div class="col-sm-12">
                  <input type="file" class="form-control" name="filename" id="filename" placeholder="Nama" />
                </div>
              </div>
     
        <div class="card-footer text-left">
     
	    <button type="submit" class="btn btn-primary"><span class="fa fa-upload"></span>Proses</button> 
	    <a href="<?php echo site_url('barang') ?>" class="btn btn-icon icon-left btn-success">Cancel</a>
	
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
</div>
