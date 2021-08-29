<?php $this->load->view('templates/header');?>
<div class="container">
	<div class="row">
		<div class="col-md-12">
      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">Category Details</div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <div class="padd">
            <!-- Profile form -->
            <div class="form profile">
              <!-- Edit profile form (not working)-->
              <form class="form-horizontal" method="post" action="<?php echo base_url()?>system_setup/save_category/<?php echo $this->uri->segment(3) ?>">
                  <div class="form-group">
                    <label class="control-label col-lg-2" for="category">Category</label>
                    <div class="col-lg-6">
                      <input type="text" value="<?php echo isset($category->category) ? $category->category : ''?>" name="category" class="form-control" required>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="control-label col-lg-2" for="category">Category Description</label>
                    <div class="col-lg-6">
                      <input type="text" value="<?php echo isset($category->category_description) ? $category->category_description : ''?>" name="category_description" class="form-control" required>
                    </div>
                  </div>
                  <!-- Buttons -->
                  <div class="form-group">
                     <!-- Buttons -->
										 <div class="col-lg-6 col-lg-offset-2">
											<button type="submit" class="btn btn-primary">Save</button>
											<a href="<?php echo base_url().(isset($profile) ? '' : 'system_setup/categories')?>" class="btn btn-danger">Cancel</a>
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