<?php $this->load->view('templates/header');?>
<div class="container">
  <div class="row">
    <div class="col-md-12">

              <div class="widget">
                <div class="widget-head">
                  <div class="pull-left">Profile</div>
                  <div class="clearfix"></div>
                </div>

                <div class="widget-content">
                  <div class="padd">
                    
                    <!-- Profile form -->
                   
                                    <div class="form profile">
                                      <!-- Edit profile form (not working)-->
                                      <form class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/save_user/<?php echo $this->session->userdata('user_id')?>">
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="name">Name</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($user) ? $user->name : ''?>" name="name" class="form-control" required>
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="email">Email</label>
                                            <div class="col-lg-6">
                                              <input type="email" value="<?php echo isset($user) ? $user->email : ''?>" name="email" class="form-control">
                                            </div>
                                          </div>  
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="username">Username</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($user) ? $user->username : ''?>" id="username" name="username" class="form-control" required>
                                              <div class="error" id="username-error"></div>
                                            </div>
                                          </div> 
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="password">Password</label>
                                            <div class="col-lg-6">
                                              <input type="password" placeholder="Leave it blank if you don't want to change the password" class="form-control" name="password">
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="type_id">Type</label>
                                            <div class="col-lg-6">
                                              <select class="form-control" name="type_id">
                                                <?php foreach ($user_types as $key => $value) :?>
                                                  <option <?php echo isset($user->type_id) && $value->type_id==$user->type_id ? 'selected' : ''?> value="<?php echo $value->type_id?>"><?php echo $value->type?></option>
                                                <?php endforeach;?>
                                              </select>
                                            </div>
                                          </div>
                                          <!-- Buttons -->
                                          <div class="form-group">
                                             <!-- Buttons -->
                       <div class="col-lg-6 col-lg-offset-2">
                        <button type="submit" class="btn btn-primary">Save</button>
                        <a href="<?php echo base_url()?>system_setup/users" class="btn btn-danger">Cancel</a>
                      </div>
                                          </div>
                                      </form>
                                    </div>

                  </div>
                </div>
              </div>  
              
            </div>
  </div>
</div>
<script>
  $('#username').change(function(){
    $.ajax({
        type: "POST",
        url: "<?php echo base_url()?>system_setup/check_username/<?php echo $this->session->userdata('user_id')?>",
        data: {username:$('#username').val()}
      }).done(function( msg ) {
        $("#username-error").html(msg);
      });
  });
</script>
<?php $this->load->view('templates/footer');?>