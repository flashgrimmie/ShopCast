<div id="modalAddCustomer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form_customer" class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/saveCustomerAjax">
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
          <h3 class="modal-title">Add Customer</h3>
        </div>
        <div class="modal-body">
           <div class="form-group">
              <label class="control-label col-lg-2" for="name">Name</label>
              <div class="col-lg-6">
                <input type="text" value="" id="addcus_name" name="name" class="form-control" required>
              </div>
            </div>
            <div class="form-group">
              <label class="control-label col-lg-2" for="address">Address</label>
              <div class="col-lg-6">
                <input type="text" value="" name="address" class="form-control" required>
              </div>
            </div>  
            <div class="form-group">
              <label class="control-label col-lg-2" for="email">Email</label>
              <div class="col-lg-6">
                <input type="email" value="" name="email" class="form-control">
              </div>
            </div> 
            <div class="form-group">
              <label class="control-label col-lg-2" for="phone">Phone</label>
              <div class="col-lg-6">
                <input type="text" value="" id="addcus_phone" name="phone" class="form-control" required>
              </div>
            </div> 
            <div class="form-group">
              <label class="control-label col-lg-2" for="phone">Fax</label>
              <div class="col-lg-6">
                <input type="text" value="" id="addcus_fax" name="fax" class="form-control">
              </div>
            </div> 
            <div class="form-group">
              <label class="control-label col-lg-2" for="car_plate">Car Plate</label>
              <div class="col-lg-6">
                <input type="text" value="" id="addcus_carplate" name="car_plate" class="form-control" required>
                <div class="error" id="username-error"></div>
              </div>
            </div> 
            <!-- Buttons -->
            <div class="form-group">
            <label class="control-label col-lg-2" for="phone"></label>
              <div class="col-lg-6 error" id="customer_error"></div>
            </div> 
        </div>
        <div class="modal-footer">
          <button id="save_customer" class="btn btn-danger">Save</button>
          <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>