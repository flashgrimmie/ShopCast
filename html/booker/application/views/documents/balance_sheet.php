<?php $this->load->view('templates/head_pdf')?>
<style>
  .in{
    padding-left: 30px;
  }
  .inner{
    padding-left:100px;
  }
  table{
    margin-left: 100px;
  }
  h3,h5 {
    font-weight:bold;
  }
</style>
<h3 class="text-center">Summary Collection and Payment <?php echo date('M, Y',strtotime($this->input->get('date').'-01'))?></h3>
<br/><br/>
<table>
  <tr>
    <td class="in">Cash Sale Receipt:</td>
    <td><?php echo format_price($cash_sales)?></td>
  </tr>
  <tr>
    <td class="in">Invoice Receipt:</td>
    <td><?php echo format_price($invoices)?></td>
  </tr>
  <tr>
    <td class="in">Inter Outlet D.O Total:</td>
    <td><?php echo format_price($delivery_orders)?></td>
  </tr>
  <tr>
    <td><h5>Total Receipt&nbsp;(Cash In)</h5></td>
    <td class="inner"><?php echo format_price($total_receipt)?></td>
  </tr>
  <tr>
    <td><h5>Less:</h5></td>
    <td class="inner"></td>
  </tr>
  <?php if(is_array($recurring_expences)) foreach($recurring_fields as $value) :?>
    <tr>
      <td class="in"><?php echo humanize($value)?>:&nbsp;</td>
      <td><?php echo format_price($recurring_expences->$value)?></td>
    </tr>
  <?php endforeach;?>
  <?php foreach($one_time_expences as $value) :?>
    <tr>
      <td class="in"><?php echo $value->name?>:&nbsp;</td>
      <td><?php echo format_price($value->value)?></td>
    </tr>
  <?php endforeach;?>
  <?php foreach($outlet_expenses as $value) :?>
    <tr>
      <td class="in"><?php echo $value->name?>:&nbsp;</td>
      <td><?php echo format_price($value->value)?></td>
    </tr>
  <?php endforeach;?>
  <?php foreach($purchase_invoices as $value) :?>
    <tr>
      <td class="in"><?php echo $value->supplier?>:&nbsp;</td>
      <td><?php echo format_price($value->price)?></td>
    </tr>
  <?php endforeach;?>
  <tr>
    <td><h5>BALANCE</h5></td>
    <td class="inner"><?php echo format_price($balance)?></td>
  </tr>
  </table>
</body>
</html>