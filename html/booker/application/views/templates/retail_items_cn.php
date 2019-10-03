    <style>
      #itable {
        font-size:11.5px;
        background: #fff;
        border: #ccc 1px solid;
        color:#555;
        box-shadow: 1px 1px 5px #888888;
      }
      #itable td {
        vertical-align: middle;
        cursor: pointer;
      }
      #itable_info {
        display:none;
      }
      #tabs .widget-content {
        padding-bottom: 40px;
      }
      .widget-content {
        overflow-x: visible;
      }
    </style>
    <div class="col-md-6" style="z-index:9;position:relative;">
      <div class="retail_items" style="display:none"></div>
      <div class="widget" id="tabs">
        <div class="widget-head" style="padding: 1px;">
          <div class="pull-left">
            <ul style="background:inherit;border:none">
              <li><a href="#thumbnail-view">Thumbnail View</a></li>
              <li><a href="#table-view">Table View</a></li>
            </ul>
          </div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <div class="padd" id="thumbnail-view">
            <div>
              <input type="text" class="form-control filter" id="filter_items" placeholder="Filter Items"/>
            </div>
            <div class="gallery clearfix">
              <?php foreach($retail_items as $key=>$value){?>
              <div class="col-md-4">
                <a id="retailitem_<?php echo $value->item_id?>" class="prettyPhoto[pp_gal]">
                  <?php if($value->image){?>
                  <div class="retail_imgwrap">
                    <img src="<?php echo base_url()?>uploads/<?php echo $value->image?>" alt="">
                  </div>
                  <?php } else {?>
                    <div class="no-image"><?php echo $value->description?></div>
                  <?php }?>
                  <span class="label label-default"><?php echo $value->stock_num?></span>
                </a>
              </div>
              <?php }?>
            </div>
          </div>
          <div class="padd" id="table-view">
            <table id="itable" class="table table-striped table-bordered table-hover">
              <thead>
                <th>Item No.</th>
                <th>Part No.</th>
                <th>Barcode</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Description</th>
                <th>Model</th>
                <th>Remark</th>
                <th>QTY</th>
                <th>Sell Price</th>
                <th>Last Sell Price</th>
              </thead>
            </table>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>  
    </div>

<div id="modalCalculateChange" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
        <h3 class="modal-title">Calculate Change</h3>
      </div>
      <div class="modal-body">
        <div class="control-group width60 top10">
          <label class="control-label" for="date">Total for payment</label><br/>
          <input type="text" id="total_payment" value="<?php echo isset($invoice_details->to_pay) ? format_number($invoice_details->to_pay) : (isset($invoice_details->total) ? format_number($invoice_details->total) : '')?>" class="form-control dtpicker"/>
          <span class="add-on btn btn-default" id="currency_total">$</span>
        </div>
        <div class="control-group width60 top10">
          <label class="control-label" for="date">Cash</label><br/>
          <input type="text" id="cash" class="form-control dtpicker"/>
          <span class="add-on btn btn-default" id="currency_cash">$</span>
        </div>
        <div class="control-group width60 top10">
          <h4 class="control-label" for="date">Change:&nbsp;<span id="change"></span></h4>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
      </div>
      <input type="hidden" id="item_ids" value=""/>
    </div>
  </div>
</div>
<script>
  $(document).ready(function(){
    $('#cash, #total_payment').keyup(function(){
      $('#change').html(roundToTwo($('#cash').val()-$('#total_payment').val()));
    });
    $('#tabs').tabs();

    $('body').on('keyup',' [id^="quantity_"], [id^="discountvalue_"]',function(){
      if($(this).val()!='' && ($(this).val()!=parseFloat($(this).val()) || $(this).val()<0)) {
        $(this).val('');
        alert('Incorrect value!');
      }
    });

    var oTable = $('#itable').dataTable({
      "bServerSide": true,
      "sAjaxSource": "<?php echo base_url() ?>retail/getRetailItemsTable",
      "sServerMethod": "POST",
      "aaSorting": [[0, 'asc']],
      "aoColumns": [
        { "aaData": "0", "sSortDataType": "dom-text", "sType": "numeric", 'sClass': "center" },
        { "aaData": "1"},
        { "aaData": "2", 'sClass': "center" },
        { "aaData": "3", 'sClass': "center" },
        { "aaData": "4", 'sClass': "center" },
        { "aaData": "5", 'sClass': "center" },
        { "aaData": "6", 'sClass': "center" },
        { "aaData": "7", 'sClass': "center" },
        { "aaData": "8", 'sClass': "center" },
        { "aaData": "9", 'sClass': "center" },
        { "aaData": "10", 'sClass': "center" },
      ],
      "fnRowCallback": function( nRow, aData, iDisplayIndex ) {
          $('#table-view').css('overflow','visible');
      }
    });

    $("body").click(function(e) {
      var target=e.target;
      if(target.id!='itable' && target.id!='ui-id-2' && target.parentNode.parentNode.parentNode.id!='itable') {
        $('#table-view').css('overflow','hidden');
      }
    });

    $("#itable").on("click", "tr", function(e) {
        var iPos = oTable.fnGetPosition( this );
        var aData = oTable.fnGetData( iPos );
        var iId = aData[11];
        var qty = aData[8];        
        var existis=0;
        ids=$('#item_ids').val();
        $('[id^="itemid_"]').each(function(){
          if($(this).val()==iId) {
            alert('Item is already added!');
            existis=1;
          }
        });
        if(existis) {
          return false;
        }
        var add_item_url='selectInvoiceItemCN';
        <?php if($this->uri->segment(2)=='create_do'){?>
          add_item_url='selectDOItem';
        <?php }?>
        $.ajax({
          type: "POST",
          url: "<?php echo base_url() ?>retail/"+add_item_url,
          data: {item_id:iId,item_ids:$('#item_ids').val()}
        }).done(function( msg ) {
            if($('#iitems tr').length==1) {
              $('#no-items').hide();
            }
            $('#iitems').append(msg);
            //$('#table-view').css('overflow','hidden');
            update_total();
        }); //clicks a button on the first cell
        $('#item_ids').val(ids+','+iId);
    });
  });
</script>