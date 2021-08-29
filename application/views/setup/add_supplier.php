<?php $this->load->view('templates/header');?>
<div class="container">
	<div class="row">
		<div class="col-md-12">

              <div class="widget">
                <div class="widget-head">
                  <div class="pull-left">Supplier Details</div>
                  <div class="clearfix"></div>
                </div>

                <div class="widget-content">
                  <div class="padd">
                    
                    <!-- Profile form -->
                                    <div class="form profile">
                                      <!-- Edit profile form (not working)-->
                                      <form class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/save_supplier/<?php echo $this->uri->segment(3)?>">
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="name">Name</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($supplier) ? $supplier->name : ''?>" name="name" class="form-control" required>
                                            </div>
                                          </div>
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="address">Address</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($supplier) ? $supplier->address : ''?>" name="address" class="form-control" required>
                                            </div>
                                          </div>  
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="contact_person">Contact Person</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($supplier) ? $supplier->contact_person : ''?>" name="contact_person" class="form-control" required>
                                           	  <div class="error" id="username-error"></div>
                                            </div>
                                          </div> 
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="email">Email</label>
                                            <div class="col-lg-6">
                                              <input type="email" value="<?php echo isset($supplier) ? $supplier->email : ''?>" name="email" class="form-control" required>
                                              <div class="error" id="username-error"></div>
                                            </div>
                                          </div> 
                                          <div class="form-group">
                                            <label class="control-label col-lg-2" for="phone">Phone</label>
                                            <div class="col-lg-6">
                                              <input type="text" value="<?php echo isset($supplier) ? $supplier->phone : ''?>" name="phone" class="form-control" required>
                                              <div class="error" id="username-error"></div>
                                            </div>
                                          </div> 
                                          <!-- Buttons -->
                                          <div class="form-group">
                                            <!-- Buttons -->
                    				            		<div class="col-lg-6 col-lg-offset-2">
                    			   									<button type="submit" class="btn btn-primary">Save</button>
                    		  										<a href="<?php echo base_url()?>system_setup/suppliers" class="btn btn-danger">Cancel</a>
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