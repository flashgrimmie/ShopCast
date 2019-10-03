<?php $this->load->view('templates/header');?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">Invoice Items</div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <table class="table table-bordered">
            <thead>
              <th>Stock Code</th>
              <th>Description</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Discount(%)</th>
              <th>Discount($)</th>
              <th>Total</th>
              <th></th>
            </thead>
            <tbody id="iitems">
              <?php foreach($i_items as $item_details){?>
                <tr class="text-center" id="item_<?php echo $item_details->iitem_id?>">
                  <td><?php echo $item_details->stock_num?></td>
                  <td><?php echo $item_details->description?></td>
                  <td><?php echo format_number($item_details->price)?></td>
                  <td><?php echo isset($item_details) ? $item_details->quantity : ''?></td>
                  <td><?php echo isset($item_details) ? $item_details->discount : ''?></td>
                  <td><?php echo isset($item_details) ? $item_details->discount_value : ''?></td>
                  <td id="linetotal_<?php echo $item_details->iitem_id?>"><?php echo format_number((($item_details->price*(1-$item_details->discount/100))-$item_details->discount_value)*$item_details->quantity)?></td>
                  <td><a class="btn btn-xs btn-default" id="returnitem_<?php echo $item_details->iitem_id?>" data-toggle="modal" href="#modalReturn" role="button">
                        <i class="fa fa-share-square-o"></i>
                      </a>
                  </td>
                </tr>
              <?php }?>
            </tbody>
          </table>
        </div>
      </div>                
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="widget">
        <div class="widget-head">
          <div class="pull-left">Returned Items</div>
          <div class="clearfix"></div>
        </div>
        <div class="widget-content">
          <table class="table table-bordered">
            <thead>
              <th>Stock Code</th>
              <th>Description</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Discount(%)</th>
              <th>Discount($)</th>
              <th>Total</th>
              <th>Date Returned</th>
              <th></th>
            </thead>
            <tbody id="returneditems">
              <?php foreach($returned_items as $item_details){?>
                <tr class="text-center" id="item_<?php echo $item_details->iitem_id?>">
                  <td><?php echo $item_details->stock_num?></td>
                  <td><?php echo $item_details->description?></td>
                  <td><?php echo format_number($item_details->price)?></td>
                  <td><?php echo isset($item_details) ? $item_details->quantity : ''?></td>
                  <td><?php echo isset($item_details) ? $item_details->discount : ''?></td>
                  <td><?php echo isset($item_details) ? $item_details->discount_value : ''?></td>
                  <td id="linetotal_<?php echo $item_details->iitem_id?>"><?php echo format_number((($item_details->price*(1-$item_details->discount/100))-$item_details->discount_value)*$item_details->quantity)?></td>
                  <td><?php echo format_date($item_details->date_returned)?></td>
                  <td><a class="btn btn-xs btn-success" id="undoreturn_<?php echo $item_details->iitem_id?>" role="button">
                        <i class="fa fa-undo"></i>
                      </a>
                  </td>
                </tr>
              <?php }?>
            </tbody>
          </table>
        </div>
      </div>                
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="padd top10">
        <div class="control-group width60 pull-right">
          <label class="control-label" for="total">Discount:&nbsp;<?php echo isset($invoice_details) ? $invoice_details->discount : '0'?>%</label>
        </div>
        <div class="clearfix"></div>
        <div class="control-group width60 pull-right">
          <label class="control-label" for="total">Total:&nbsp;<span id="total"><?php echo format_price(isset($invoice_details) ? $invoice_details->total : '0')?></span></label>
        </div>
        <div class="clearfix"></div>
        <div class="submit form-group clearfix top10">
         <!-- <a class="btn btn-default pull-right" href="javascript:popup('<?php echo base_url() ?>documents/invoice/<?php echo $this->uri->segment(3)?>')"><i class="fa fa-print"></i>&nbsp;Print</a>-->
		  <a class="btn btn-default pull-right" href="<?php echo base_url() ?>retail/create_cn/<?php echo $this->uri->segment(3)?>"><i class="fa fa-print"></i>&nbsp;Credit Note</a>
        </div>
      </div>
    </div>
  </div>
</div>
<div id="modalReturn" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <h3 class="modal-title">Return Items</h3>
          </div>
          <div class="modal-body">
            <div class="control-group">
              <label class="control-label" for="stock_num">Stock Code:&nbsp;</label><span id="stock_num"></span>
              <input type="hidden" id="iitem_id"/>
            </div>
            <div class="control-group">
              <label class="control-label" for="description">Description:&nbsp;</label><span id="description"></span>
            </div>
            <div class="control-group">
              <label class="control-label" for="quantity">QTY to return:&nbsp;<input id="quantity" style="display:inline-block" class="form-control inputsmall"/></label>
            </div>
            <div class="control-group">
              <p class="error"></p>
            </div>
          </div>
          <div class="modal-footer">
            <a class="btn btn-success" id="returnitems">Confirm</a>
            <button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
          </div>
        </div>
      </div>
    </div>
<script>
$(document).ready(function(){
  $('body').on('click','[id*="returnitem_"]',function(){
      var tr=$(this).parent().parent();
      var arr=tr.attr('id').split('_');
      var id=arr[1];
      $('#modalReturn #stock_num').html(tr.find('td:first-child').html());
      $('#modalReturn #description').html(tr.find('td:nth-child(2)').html());
      $('#modalReturn #quantity').val(tr.find('td:nth-child(4)').html());
      $('#modalReturn #iitem_id').val(arr[1]);
  });

  $('#quantity').keyup(function(){
    $('.error').html('');
  });

  $('#returnitems').click(function(){
    $.ajax({
      type: "POST",
      url: "<?php echo base_url() ?>retail/return_iitem",
      data: {id:$('#iitem_id').val(), quantity:$('#quantity').val()}
    }).done(function( msg ) {
      var obj=JSON.parse(msg);
      if(obj.type=='error') {
        $('.error').html(obj.msg);
      } else { 
        $('#total').html(obj.msg);
        $('#returneditems').append(obj.returned);
        $('#modalReturn').modal('hide');
      }
    });
  });

  $('body').on('click','[id*="undoreturn_"]',function(){
    var btn=$(this);
    var arr=btn.attr('id').split('_');
    var id=arr[1];
    $.ajax({
      type: "POST",
      url: "<?php echo base_url() ?>retail/undoreturn_iitem",
      data: {id:id}
    }).done(function( msg ) {
      btn.parent().parent().remove();
      $('#total').html(msg);
    });
  });

  $('#modalReturn').on('hidden.bs.modal', function () {
    $('.error').html('');
  });

})
</script>
<?php $this->load->view('templates/footer')?>