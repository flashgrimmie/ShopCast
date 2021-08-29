<?php $this->load->view('templates/doc_header');?>
<div class="container">
	
	<div class="row">
			<div class="span11">
				
				<img src="<?php echo base_url() ?>img/borbone.jpg"/>
			
			</div>

			<div class="span11">&nbsp;</div>

			<div class="span11 text_left"  style="margin-top:40px">
				<div class="span4" style="border:1px solid grey; padding:20px; text-align:center">
					<span><b><?php echo $pi_info['supplier']?><b></span><br/>
					<span><b><?php echo $pi_info['address']?><b></span>
				</div>
			</div>

			<div class="span12">&nbsp;</div>
			<div class="span12 text_center"><h1>Приемница</h1></div>

			<div class="span12 text_left">
				<div class="span4">&nbsp;</div>
				<div class="span6 text_right">
					<span><small><b>Број:</b></small><?php echo $pi_info['invoice_num'] ?></span><br/>
					<span><small><b>Дата на прием:</b></small> <?php echo format_date($pi_info['date'])?></span><br/>
					<span><small><b>Дата на издавање:</b></small> <?php echo format_date($pi_info['issue_date'])?></span><br/>
				</div>
			
			</div>

	</div>
	<br clear="all"/>
	<?php //var_dump($pi_items); exit; ?>
<div class="row">
	<div class="span11">
		<table class="table bordered span11">
			<tr>
				<td><b>Бр.</b></td>
				<td class="span4"><b>Опис</b></td>
				<td><b>Количина</b></td>
				<td><b>Цена</b></td>
				<td><b>Вкупна цена</b></td>
			</tr>
		<?php foreach($pi_items as $key=>$item):?>
				<tr>
					<td><?php echo $key+1?></td>
					<td><?php echo $item->description;?></td>
					<td><?php echo $item->quantity;?></td>
					<td><?php echo $item->cost;?> ден.</td>
					<td><?php echo number_format($item->cost*$item->quantity,0,'',',');?> ден.</td>
				</tr>
		<?php endforeach;?>
			<tr>
						<td></td>
						<td></td>
						<td></td>
						<td><b>Вкупно:</b></td>
						<td><?php echo number_format($pi_info['subtotal'],0,'',',') ?> ден.</td>
			</tr>
			<tr>
						<td></td>
						<td></td>
						<td></td>
						<td><b>ДДВ 18%:</b></td>
						<td><?php echo  number_format($pi_info['total']-$pi_info['subtotal'],0,'',',') ?> ден.</td>
			</tr>
			<tr>
						<td></td>
						<td></td>
						<td></td>
						<td><b>Вкупно со ДДВ:</b></td>
						<td><?php echo  number_format($pi_info['total'],0,'',',') ?> ден.</td>
			</tr>
		</table>
	</div>
</div>
	<div class="row span12">
		<div class="span12">&nbsp;</div>
		<div class="span4 pull-right">
			<div class="underlined">&nbsp;</div>
			<label for="signature" class="text_right">Примил</label><br/>
		</div>
	</div>
</div>
</body>
</html>