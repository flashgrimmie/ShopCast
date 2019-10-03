<?php $this->load->view('templates/header');?>
<div class="container">
  <div class="row">
    <div class="col-md-12">

              <div class="widget">
                <div class="widget-head">
                  <div class="pull-left">Outlet Details</div>
                  <div class="clearfix"></div>
                </div>

                <div class="widget-content">
                  <div class="padd">
                    
                    <!-- Profile form -->
                                    <div class="form profile">
                                      <!-- Edit profile form (not working)-->
                                      <form class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/save_outlet/<?php echo $this->uri->segment(3)?>">
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="name">Name</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($outlet) ? $outlet->name : ''?>" name="name" class="form-control" required>
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="location">Address 1</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($outlet) ? $outlet->address1 : ''?>" name="address1" class="form-control" required>
                                            </div>
                                          </div>  
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="location">Address 2</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($outlet) ? $outlet->address2 : ''?>" name="address2" class="form-control">
                                            </div>
                                          </div>  
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="contact">Contact</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($outlet) ? $outlet->contact : ''?>" name="contact" class="form-control" required>
                                              <div class="error" id="username-error"></div>
                                            </div>
                                          </div>

                                          <div class="form-group">
                                              <label class="control-label col-lg-2" for="fax">Fax</label>
                                              <div class="col-lg-6">
                                                  <input type="text" value="<?php echo isset($outlet) ? $outlet->fax : ''?>" name="fax" class="form-control" required>
                                                  <div class="error" id="username-error"></div>
                                              </div>
                                          </div>

                                          <!-- Buttons -->
                                          <div class="form-group">
                                            <!-- Buttons -->
                                            <div class="col-lg-6 col-lg-offset-2">
                                              <button type="submit" class="btn btn-primary">Save</button>
                                              <a href="<?php echo base_url()?>system_setup/outlets" class="btn btn-danger">Cancel</a>
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
<?php $this->load->view('templates/footer');?>