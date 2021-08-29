<?php $this->load->view('templates/header');?>
<div class="container">
  <div class="row">
    <div class="col-md-12">

      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">Customer Details</div>
          <div class="clearfix"></div>
        </div>

        <div class="widget-content">
          <div class="padd">

      <!-- Profile form -->
                    <div class="form profile">
                      <!-- Edit profile form (not working)-->
                      <form class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/save_customer/<?php echo $this->uri->segment(3)?>">
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="name">Name</label>
                            <div class="col-lg-6">
                              <input type="text" value="<?php echo isset($customer) ? $customer->name : ''?>" name="name" class="form-control" required>
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="address">Address 1</label>
                            <div class="col-lg-6">
                              <input type="text" value="<?php echo isset($customer) ? $customer->address : ''?>" name="address" class="form-control" required>
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="control-label col-lg-2" for="address">Address 2</label>

                              <div class="col-lg-6">
                                  <input type="text" value="<?php echo isset($customer) ? $customer->address_1 : '' ?>"
                                         name="address_1" class="form-control" required>
                              </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="email">Email</label>
                            <div class="col-lg-6">
                              <input type="email" value="<?php echo isset($customer) ? $customer->email : ''?>" name="email" class="form-control">
                            </div>
                          </div> 
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="phone">Phone</label>
                            <div class="col-lg-6">
                              <input type="text" value="<?php echo isset($customer) ? $customer->phone : ''?>" name="phone" class="form-control" required>
                            </div>
                          </div> 
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="phone">Fax</label>
                            <div class="col-lg-6">
                              <input type="text" value="<?php echo isset($customer->fax) ? $customer->fax : ''?>" name="fax" class="form-control">
                            </div>
                          </div>
                          <div class="form-group">
                            <label class="control-label col-lg-2" for="phone">Car Plate</label>
                            <div class="col-lg-6">
                              <input type="text" value="<?php echo isset($customer->car_plate) ? $customer->car_plate : ''?>" name="car_plate" class="form-control">
                              <div class="error" id="car_plate-error"></div>
                            </div>
                          </div>
                          <div class="form-group">
                              <label class="control-label col-lg-2" for="address">Attention</label>

                              <div class="col-lg-6">
                                  <input type="text" value="<?php echo isset($customer) ? $customer->attention : '' ?>"
                                         name="attention" class="form-control" >
                              </div>
                          </div>
                          <!-- Buttons -->
                          <div class="form-group">
                            <!-- Buttons -->
                            <div class="col-lg-6 col-lg-offset-2">
                              <button type="submit" class="btn btn-primary">Save</button>
                              <a href="<?php echo base_url()?>system_setup/customers" class="btn btn-danger">Cancel</a>
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