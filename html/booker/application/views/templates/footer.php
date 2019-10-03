</div>

   <!-- Mainbar ends -->
   <div class="clearfix"></div>

</div>
<!-- Content ends -->
<?php if(!isset($loginpage)) { ?>


    <div id="modalDelete" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h3 class="modal-title">Delete Dialog</h3>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete the entry?</p>
          </div>
          <div class="modal-footer">
            <a class="btn btn-danger deletemodalaction" id="deletemodalaction">Yes</a>
			<button class="btn" data-dismiss="modal" id="deletingmodalaction" aria-hidden="true" style="display:none;">Deleting...</button>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div id="modalSearchStock" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" style="width: 50%;">
        <div class="modal-content" style="overflow: auto;">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h3 class="modal-title">Select Item</h3>
          </div>
          <div class="modal-body">
            <table class="table table-hovered table-bordered">
              <thead>
                <th>Item No.</th>
                <th>Barcode</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Description</th>
                <th>Model</th>
                <th>Remark</th>
              </thead>
              <tbody id="searchdesc"></tbody>
            </table>
          </div>
        </div>
      </div>
    </div> 


    <!-- Footer starts -->
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-md-12">
                <!-- Copyright info -->
                <p class="copy">Copyright &copy; <?php echo date('Y') ?> | <a href="<?php echo base_url() ?>">ShopcastApp</a> </p>
          </div>
        </div>
      </div>
    </footer> 	

    <!-- Footer ends -->

    <!-- Scroll to top -->
    <span class="totop"><a href="#"><i class="fa fa-chevron-up"></i></a></span> 

    <script>
    $(document).ready(function(){
      $('body').on('click', '.delete-action', function() {
        var datahref = $(this).attr('data-href');
        $('.deletemodalaction').attr('href',datahref);
      });
	
      $(function() {
        $( ".datepicker" ).datepicker();
      });

      $('#refresh').click(function(){
        location.reload();
        return false;
      });
    });
	
	$('body').on('click','.deletemodalaction', function (){
        if ($(this).data('disabled')){
          alert('Please enter a reason for deleting this item.');
          return false;
        }
		else{
			 $('.error').html('');
			 document.getElementById('deletemodalaction').style['display']='none';	
			 document.getElementById('deletingmodalaction').style['display']='inline';
		}
      })

    function popup(url) {
      popupWindow = window.open(
        url,'popUpWindow','height=700,width=800,left=10,top=10,resizable=yes,scrollbars=yes,toolbar=yes,menubar=no,location=no,directories=no,status=yes')
    }

    function roundToTwo(num) {    
        return +(Math.round(num + "e+2")  + "e-2");
    }

    $('body').on('click','.itemsincart', function (e){
        e.stopPropagation();

    });

    $('body').on('click','.removefromcart', function (e){
          var closestli=$(this).closest('li');
          var removekey=closestli.data('rem');
          $.ajax({
            type: "POST",
            url: "<?php echo base_url() ?>inventory/delFromCart/",
            data: { removekey: removekey }
          }).done(function(response) {
              var itemsinchart=$('.numitems').html();
              $('.numitems').html(--itemsinchart);
              closestli.hide(500);
          });



          
    });

    
    
    </script>
    <script type="text/javascript">
    $(document).on('click', '.disable_it', function(){
        $(this).attr("disabled", "disabled");
        setTimeout(function(){
         $(this).removeAttribute("disabled")
        },4000);
    });
   </script>

    <!-- JS -->
    <script src="<?php echo base_url()?>assets/js/datatables/js/jquery.dataTables.js"></script>
	<script src="<?php echo base_url()?>assets/js/datatables/js/dataTables.tableTools.js"></script>



    <?php if(isset($flot) && $flot) { ?>
      <!-- jQuery Flot -->
      <script src="<?php echo base_url()?>assets/js/excanvas.min.js"></script>
      <script src="<?php echo base_url()?>assets/js/jquery.flot.js"></script>
      <script src="<?php echo base_url()?>assets/js/jquery.flot.resize.js"></script>
      <script src="<?php echo base_url()?>assets/js/jquery.flot.pie.js"></script>
      <script src="<?php echo base_url()?>assets/js/jquery.flot.stack.js"></script>
    <?php } ?>

    <?php if(isset($morris) && $morris){ ?>
    <!-- Morris JS -->
    <script src="<?php echo base_url()?>assets/js/raphael-min.js"></script>
    <script src="<?php echo base_url()?>assets/js/morris.min.js"></script>
    <?php } ?>

    <script src="<?php echo base_url()?>assets/js/jquery.slimscroll.min.js"></script> <!-- Slim Scroll -->
    <script src="<?php echo base_url()?>assets/js/sparklines.js"></script> <!-- Sparklines -->
    <script src="<?php echo base_url()?>assets/js/filter.js"></script> <!-- Filter for support page -->
    <script src="<?php echo base_url()?>assets/js/custom.js"></script> <!-- Custom codes -->
    <script src="<?php echo base_url()?>assets/js/charts.js"></script> <!-- Charts & Graphs -->
<?php } ?>

</body>

</html>