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
<h3 class="text-center">General Ledger</h3>
<br/><br/>
<table>
  <tr>
    <td colspan="2">
      <h5>SALES:</h5>
    </td>
  </tr>
  <tr>
    <td class="in">Cash Sales Total:</td>
    <td><?php echo format_price($cash_sales)?></td>
  </tr>
  <tr>
    <td class="in">Invoices Total:</td>
    <td><?php echo format_price($invoices)?></td>
  </tr>
  <tr>
    <td class="in">Partial Payments:</td>
    <td><?php echo format_price($partial_payments)?></td>
  </tr>
  <tr>
    <td class="in">Inter Outlet D.O Total:</td>
    <td><?php echo format_price($delivery_orders)?></td>
  </tr>
  <tr>
    <td class="in">Less:&nbsp;Sales Return:</td>
    <td><?php echo format_price($returned_sales)?></td>
  </tr>
  <tr>
    <td>Total:&nbsp;Net Sales</td>
    <td class="inner"><?php echo format_price($net_sales)?></td>
  </tr>
  <tr>
    <td>Less:&nbsp;Cost of Sales</td>
    <td class="inner"><?php echo format_price($cost_sales)?></td>
  </tr>
  <tr>
    <td>GROSS PROFIT</td>
    <td class="inner"><?php echo format_price($gross_profit)?></td>
  </tr>
  <tr>
    <td>Less:&nbsp;Expenses</td>
    <td class="inner"><?php echo format_price($purchase_total+$one_time_total+$recurring_total)?></td>
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
  <tr>
    <td>NETT PROFIT:</td>
    <td class="inner"><?php echo format_price($net_profit)?></td>
  </tr>
  </table>
</body>
</html>