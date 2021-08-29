<?php $this->load->view('templates/header');?>
<style>
  th {
    font-size: 12px;
  }
</style>
            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
                <!-- ==================== TABLE ROW ==================== -->
              <form class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/update_permissions">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" id="tabs">
                            <div class="widget-head" style="padding-left: 0;padding-bottom: 1px;">
                              <ul style="background: inherit;border:none">
                                  <?php foreach($tabs as $tab){?>
                                    <li><a href="#tabs-<?php echo $tab['tab_name']?>"><?php echo $tab['type_name']?></a></li>
                                  <?php }?>
                              </ul>
                            </div>
                            <div class="widget-content">
                              <?php foreach ($tabs as $tab_key => $tab_value) {?>
                                <div class="clearfix" id="tabs-<?php echo $tab_value['tab_name']?>">
                                  <?php foreach($permissions as $key=>$permission){?>
                                    <div class="col-lg-6">
                                      <h3><?php echo humanize($key)?></h3>
                                      <?php foreach($permission as $value){?>
                                        <div class="form-group">
                                          <div class="col-lg-6">
                                            <label class="control-label">
                                              <input type="checkbox" name="<?php echo $value.'_'.$tab_key?>" <?php echo isset($user_types[$tab_key]->$value) && $user_types[$tab_key]->$value=='Y' ? 'checked' : ''?> value="Y"/>
                                              <?php echo humanize(str_replace($key.'_', '', $value))?>
                                            </label>
                                          </div>
                                        </div>
                                      <?php }?> 
                                    </div>
                                  <?php }?>
                                </div>
                              <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                  <div class="form-group">
                  <!-- Buttons -->
                    <div class="col-lg-12">
                      <button type="submit" class="btn btn-primary pull-right right10">Update</button>
                    </div>
                  </div>
                </div>
              </form>
                        <!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== --> 
        </div>
<script>
  $('#tabs').tabs();
</script>
<?php $this->load->view('templates/footer');?>