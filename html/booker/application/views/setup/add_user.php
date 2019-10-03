<?php $this->load->view('templates/header');?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">User Details</div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <div class="padd">
            <!-- Profile form -->
            <div class="form profile">
              <!-- Edit profile form (not working)-->
              <form class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/save_user/<?php echo isset($profile) ? $this->session->userdata('user_id') : $this->uri->segment(3)?>/<?php echo isset($profile) ? $profile : ''?>">
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
                      <input type="password" placeholder="Leave it blank if you don't want to change the password" class="form-control" name="password" <?php echo $this->uri->segment(3) || isset($profile) ? '' : 'required'?>>
                    </div>
                  </div>
                  <?php if(!isset($profile)){?>
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
                  <?php }?>
				  <?php if(!isset($profile) && $this->user_model->isAdmin()){?>
                      <div class="form-group">
                        <label class="control-label col-lg-2" for="type_id">Outlet</label>
                        <div class="col-lg-6">
                          <select name="outlet_id" class="form-control">
                            <?php foreach ($outlets as $outlet) {?>
                              <option value="<?php echo $outlet->outlet_id?>" <?php echo isset($user->outlet_id) && $user->outlet_id==$outlet->outlet_id ? 'selected' : ''?>><?php echo $outlet->name?></option>
                            <?php }?>
                          </select>
                        </div>
                      </div>
                  <?php }?>
                  <div id="ip_list">
                    <div class="form-group">
                      <label class="control-label col-lg-2">Allowed IP Addresses</label>
                      <div class="col-lg-5">
                        <input type="text" value="<?php echo isset($ip_addresses[0]) ? $ip_addresses[0]->ip_address : ''?>" name="ip_addresses[]" class="form-control">
                      </div>
                      <div class="col-lg-1"><a id="add_ip" class="btn btn-default"><i class="fa fa-plus"></i></a></div>
                    </div> 
                    <?php if(isset($ip_addresses)){
                      for($i=1;$i<count($ip_addresses);$i++){?>
                      <div class="form-group">
                        <label class="control-label col-lg-2"></label>
                        <div class="col-lg-5">
                          <input type="text" value="<?php echo $ip_addresses[$i]->ip_address?>" name="ip_addresses[]" class="form-control">
                        </div>
                        <div class="col-lg-1"><a id="remove_ip" class="btn btn-danger"><i class="fa fa-times"></i></a></div>
                      </div> 
                    <?php }}?>
                  </div>
                  <!-- Buttons -->
                  <div class="form-group">
                     <!-- Buttons -->
										 <div class="col-lg-6 col-lg-offset-2">
											<button type="submit" class="btn btn-primary">Save</button>
											<a href="<?php echo base_url().(isset($profile) ? '' : 'system_setup/users')?>" class="btn btn-danger">Cancel</a>
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
  $(document).ready(function(){
  	$('#username').change(function(){
  		$.ajax({
  			  type: "POST",
  			  url: "<?php echo base_url()?>system_setup/check_username/<?php echo $this->uri->segment(3)?>",
  			  data: {username:$('#username').val()}
  			}).done(function( msg ) {
  				$("#username-error").html(msg);
  			});
  	});
    $('#add_ip').click(function(){
      var content='<div class="form-group"><label class="control-label col-lg-2"></label>';
      content+='<div class="col-lg-5"><input type="text" name="ip_addresses[]" class="form-control"></div>';
      content+='<div class="col-lg-1"><a id="remove_ip" class="btn btn-danger"><i class="fa fa-times"></i></a></div></div>';
      $('#ip_list').append(content);
    });

    $('body').on('click','#remove_ip',function(){
      $(this).parent().parent().remove();
    });
  });
</script>
<?php $this->load->view('templates/footer');?>