<?php $this->load->view('templates/head'); ?>
<body>
<!-- Form area -->
<div class="admin-form">
  <div class="container">

    <div class="row">
      <div class="col-md-12">
        

        <!-- Widget starts -->
        <div class="widget">
          <!-- Widget head -->
          <div class="widget-head"> <i class="icon-lock"></i> Login </div>

          <div class="widget-content">
            <div class="padd">
              <!-- Login form -->
              <form class="form-horizontal" method="POST" action='<?php echo base_url() ?>login/loginaction'>
                <!-- Email -->
                <div class="form-group">
                  <label class="control-label col-lg-3" for="inputEmail">Username</label>
                  <div class="col-lg-9">
                    <input type="text" class="form-control" id="inputEmail" required name="username" placeholder="Username"></div>
                  </div>
                <!-- Password -->
                <div class="form-group">
                  <label class="control-label col-lg-3" for="inputPassword">Password</label>
                  <div class="col-lg-9">
                    <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Password"></div>
                </div>

                <div class="col-lg-9 col-lg-offset-3">
                  <button type="submit" class="btn btn-danger">Sign in</button>
                  <button type="reset" class="btn btn-default">Reset</button>
                </div>
                <br />
              </form>

            </div>
          </div>

          <div class="widget-foot">&nbsp;</div>
        </div>

        <?php if($this->session->flashdata('error')) {?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error')?></div>
        <?php }?>
        
        <?php if($this->session->flashdata('success')) {?>
            <div class="alert alert-success col-md-12"><?php echo $this->session->flashdata('success')?></div>
        <?php }?>


      </div>
    </div>
  </div>
</div>

<?php $this->load->view('templates/footer'); ?>