<?php $this->load->view('templates/header');?>

            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
              <div class="row top10"> 
                    <div class="col-md-12">
                        <a href="<?php echo base_url()?>system_setup/add_supplier/<?php echo $this->uri->segment(3)?>" class="btn btn-primary pull-right mb clearform" >
                      <i class="icon-plus-sign"></i> Add Supplier
                    </a>
                    </div>
                </div>
                <!-- ==================== TABLE ROW ==================== -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">Suppliers</div>
                            <div class="widget-content">
                                <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                                    <table class="table table-bordered dataTable" id="membersTable">
                                      <thead>
                                        <tr>
                                          <th>#</th>
                                          <th>Name</th>
                                          <th>Address</th>
                                          <th>Contact Person</th>
                                          <th>Email</th>
                                          <th>Phone</th>
                                          <th></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php foreach($suppliers as $key=>$value) { ?>
                                        <tr>
                                          <td><?php echo $key+1 ?></td>
                                          <td><?php echo $value->name ?></td>
                                          <td><?php echo $value->address ?></td>
                                          <td><?php echo $value->contact_person ?></td>
                                          <td><?php echo $value->email ?></td>
                                          <td><?php echo $value->phone ?></td>
                                          <td class="actions">
                                            <a class="btn btn-xs btn-default edit" href="<?php echo base_url()?>system_setup/add_supplier/<?php echo $value->supplier_id?>">
                                              <i class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-xs btn-danger delete-action" href="#modalDelete" data-href="<?php echo base_url() ?>system_setup/delete_supplier/<?php echo $value->supplier_id?>" role="button" data-toggle="modal" >
                                              <i class="fa fa-times"></i>
                                            </a>
                                          </td>
                                        </tr>
                                        <?php } ?>
                                      </tbody>
                                    </table> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ==================== END OF BORDERED TABLE FLOATING BOX ==================== --> 
              </div>
  <script>
   $(document).ready(function() {
     $('.dataTable').dataTable({"sPaginationType": "full_numbers"});
   });
  </script>

<?php $this->load->view('templates/footer');?>