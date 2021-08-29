<?php $this->load->view('templates/header');?>

            <!-- ==================== WIDGETS CONTAINER ==================== -->
            <div class="container">
              <div class="row top10"> 
                    <div class="col-md-12">
                        <a href="<?php echo base_url()?>system_setup/add_outlet" role="button" data-toggle="modal" class="btn btn-primary pull-right mb clearform" >
                      <i class="icon-plus-sign"></i> Add Outlet
                    </a>
                    </div>
                </div>
                <!-- ==================== TABLE ROW ==================== -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">Outlets</div>
                            <div class="widget-content">
                                <div id="datatable_wrapper" class="dataTables_wrapper" role="grid">
                                    <table class="table table-bordered dataTable" id="membersTable">
                                      <thead>
                                        <tr>
                                          <th>#</th>
                                          <th>Name</th>
                                          <th>Address 1</th>
                                          <th>Address 2</th>
                                          <th>Contact</th>
                                            <th>fax</th>
                                          <th></th>
                                        </tr>
                                      </thead>
                                      <tbody>
                                        <?php foreach($outlets as $key=>$value) { ?>
                                        <tr>
                                          <td><?php echo $key+1 ?></td>
                                          <td><?php echo $value->name ?></td>
                                          <td><?php echo $value->address1 ?></td>
                                          <td><?php echo $value->address2 ?></td>
                                          <td><?php echo $value->contact ?></td>
                                            <td><?php echo $value->fax ?></td>
                                          <td class="actions">
                                            <a class="btn btn-xs btn-default edit" href="<?php echo base_url()?>system_setup/add_outlet/<?php echo $value->outlet_id ?>">
                                              <i class="fa fa-pencil"></i>
                                            </a>
                                            <a class="btn btn-xs btn-danger delete-action" href="#modalDelete" data-href="<?php echo base_url() ?>system_setup/delete_outlet/<?php echo $value->outlet_id?>" role="button" data-toggle="modal">
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