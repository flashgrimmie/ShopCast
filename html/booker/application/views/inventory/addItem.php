<?php $this->load->view('templates/header');?>

<div class="container">
	<div class="row">
		<div class="col-md-12">

			<div class="widget">
				<div class="widget-head">
					<div class="pull-left">Add Item Form</div>
					<div class="clearfix"></div>
				</div>

				<div class="widget-content">
					<div class="padd">

						<div class="form profile">
							<form class="form-horizontal" method='POST' action="<?php echo base_url()?>inventory/saveItem/<?php echo $this->uri->segment(3)?>"  enctype="multipart/form-data" >
								<div class="form-group">
									<label class="control-label col-lg-2" for="name">ISBN</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->stock_num) ? $item->stock_num : ''?>" name="stock_num" class="form-control" ></div>
								</div>
								<!---

								<div class="form-group">
									<label class="control-label col-lg-2" for="name">Part No.</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->part_no) ? $item->part_no : ''?>" name="part_no" class="form-control" ></div>
								</div>
								--->
								<div class="form-group">
									<label class="control-label col-lg-2" for="username">Description</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->description) ? $item->description : ''?>" name="description" class="form-control" ></div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-2" for="name">BarCode</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->barcode) ? $item->barcode : ''?>" name="barcode" class="form-control" ></div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-2" for="remark">Remark</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->remark) ? $item->remark : ''?>" name="remark" class="form-control"></div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-2" for="brand">Publisher</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->brand) ? $item->brand : ''?>" name="brand" class="form-control"></div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-2" for="brand">Category</label>
									<div class="col-lg-6">
										<select name="category" class="form-control">
											<?php foreach ($categories as $key => $value) {?>
												<option value="<?php echo $value->category?>" <?php echo isset($item->category) && $item->category==$value->category ? 'selected' : ''?>><?php echo $value->category_description?></option>
											<?php }?>
										</select>	
									</div>
								</div>
								<!---
								<div class="form-group">
									<label class="control-label col-lg-2" for="brand">Car Model</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->model_no) ? $item->model_no : ''?>" name="model_no" class="form-control"></div>
								</div>
								---->
								<div class="form-group">
									<label class="control-label col-lg-2" for="image">Image</label>
									<div class="col-lg-6">
										<input type="file" class="form-control" name="upload"></div>
								</div>

								<?php if(isset($item->image) && $item->image){ ?>
								<div class="form-group">
									<label class="control-label col-lg-2" for="image">&nbsp;</label>
									<div class="col-lg-6">
										<img style="max-width:470px" src="<?php echo base_url().'uploads/'.$item->image ?>">
									</div>
								</div>
								<?php } ?>

<?php
								if(!$item->item_id){
?>									
								<div class="form-group">
									<label class="control-label col-lg-2" for="qty">Quantity</label>
									<div class="col-lg-6">
										<input type="text" value="<?php echo isset($item->qty) ? $item->qty : ''?>" name="qty" class="form-control"  ></div>
								</div>
<?php
								}
?>								

								<div class="form-group">
									<label class="control-label col-lg-2" for="cost_price">Cost Price</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->cost_price) ? $item->cost_price : ''?>"name="cost_price"  ></div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-2" for="price1">Price 1</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->price1) ? $item->price1 : ''?>" name="price1"  ></div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-2" for="price2">Price 2</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->price2) ? $item->price2 : ''?>" name="price2" ></div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-2" for="price3">Price 3</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->price3) ? $item->price3 : ''?>" name="price3" ></div>
								</div>
								<div class="form-group">
									<label class="control-label col-lg-2" for="price4">Price 4</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->price4) ? $item->price4 : ''?>" name="price4" ></div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-2" for="price4">Pricing Info</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->pricing_info) ? $item->pricing_info : ''?>" name="pricing_info"></div>
								</div>

								<div class="form-group">
									<label class="control-label col-lg-2" for="low_stock">Low Stock Alert</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->low_stock) ? $item->low_stock : ''?>" name="low_stock"></div>
								</div>


								<div class="form-group">
									<label class="control-label col-lg-2" for="location">Location</label>
									<div class="col-lg-6">
										<input type="text" class="form-control" value="<?php echo isset($item->location) ? $item->location : ''?>" name="location"></div>
								</div>

								<!-- Buttons -->
								<div class="form-group">
									<!-- Buttons -->
									<div class="col-lg-6 col-lg-offset-2">
										<button type="submit" class="btn btn-primary">Save</button>
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