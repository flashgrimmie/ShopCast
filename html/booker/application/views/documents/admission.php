<?php $this->load->view('templates/doc_header');?>
<div class="container">
	
	<div class="row">
			<div class="span11">
				<div class="span4 offset1">
					<img src="<?php echo base_url() ?>img/logo.jpg" width="100px"/>
				</div>
				<div class="span5 text_right" style="margin-top:20px">
					<h1>Guilija 1986 DOOEL</h1>
					<p>Ул. Народен Фронт бр.5/2-11 Скопје Р.Македонија</p>
				</div>
			
			</div>

			<div class="span11">&nbsp;</div>

			<div class="span11 text_left">
				<div class="span4">
					<span><b><?php echo $do_details->dname?><b></span><br/>
					<span><b><?php echo $do_details->dlocation?><b></span><br/>
					<span><b><?php echo $do_details->dcontact?><b></span>
				</div>
			
			</div>

			<div class="span12">&nbsp;</div>
			<div class="span12 text_center"><h1>Испратница</h1></div>

			<div class="span12 text_left">
				<div class="span4">&nbsp;</div>
				<div class="span6 text_right">
					<span><small><b>Број:</b></small><?php echo $do_details->do_id ?></span><br/>
					<span><small><b>Дата:</b></small> <?php echo format_date($do_details->date)?></span><br/>
				</div>
			
			</div>

	</div>

<div class="row">
	<div class="span11">
		<table class="table bordered span11">
			<tr>
				<td><b>Бр.</b></td>
				<td><b>Опис</b></td>
				<td><b>Количина</b></td>
			</tr>
		<?php foreach($do_items as $item):?>

				<tr>
					<td><?php echo $item->stock_num;?></td>
					<td><?php echo $item->description;?></td>
					<td><?php echo $item->quantity;?></td>
				</tr>
		<?php endforeach;?>
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