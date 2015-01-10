jQuery(document).ready(function ($){
	
	var product_search = 'bulk_order_product_search';
	var variation_search = 'bulk_order_variation_search';
	//WCBulkOrder.aftertypequantity
	//WCBulkOrder.nodatafound
	//WCBulkOrder.noproductselected
	//WCBulkOrder.beforetypelabel
	//WCBulkOrder.beforetypequanity
	WCBulkOrder.included = $('#BulkOrderForm').attr('included');
	WCBulkOrder.excluded = $('#BulkOrderForm').attr('excluded');
	WCBulkOrder.category = $('#BulkOrderForm').attr('category');
	WCBulkOrder.decmultiple = '1';
	while(WCBulkOrder.decmultiple.length <= WCBulkOrder.num_decimals){
		WCBulkOrder.decmultiple += '0';
	}
	//alert(WCBulkOrder.decmultiple);

	function autocomplete() {
		$("input.wcbulkorderproduct").autocomplete({
			source: function(req, response){
				$.getJSON(WCBulkOrder.url+'?callback=?&action='+product_search+'&category='+WCBulkOrder.category+'&included='+WCBulkOrder.included+'&excluded='+WCBulkOrder.excluded+'&_wpnonce='+WCBulkOrder.search_products_nonce, req, response);
			},
			select: function(event, ui) {
				
				var $input = $(this);
				var $quantityInput = ($input).parent().siblings().find(".wcbulkorderquantity");
				var $displayPrice = $('.wcbulkorderprice', $input.parent().parent());
				var $productSearch = $('.wcbulkorderproduct', $input.parent().parent());
				var $price = ui.item.price.replace(/[^\d,._']/g,"");
				var $calcprice = ui.item.price.replace(/[^\d]/g,"");
				//console.log($price);
				//console.log($calcprice);
				var initial = 0;
				$displayPrice.html('<span class="amount">'+ui.item.price+'</span>');
				$displayPrice.find('span').text(ui.item.price.replace($price,initial.toFixed(2)));
				var $ProdID = $('.wcbulkorderid', $input.parent().parent());
				var $variation = $('.wcbulkordervariation', $input.parent().parent());
				$variation.prop('disabled', false);
				$ProdID.val(ui.item.id);
				if (ui.item.has_variation === 'no') {
					$variation.attr("placeholder", WCBulkOrder.variation_noproductsfound);
					$variation.prop('disabled', true);
					$variation.val("");
					$quantityInput.attr("placeholder", WCBulkOrder.enterquantity);
					$quantityInput.off('keyup').on('keyup', function() {
						$this = $(this);
						if ($quantityInput.val() > 0) {
							var total = parseInt($quantityInput.val()) * $calcprice;
						}
						else {
							var total = 0;
						}
						var total = total/WCBulkOrder.decmultiple;
						var total = total.toFixed(WCBulkOrder.num_decimals).toString().replace(".", WCBulkOrder.decimal_sep);
						//console.log(total);
						$displayPrice.find('span').text(ui.item.price.replace($price,total));
						calculateTotal();
					});
					$input.on('autocompletechange change', function () {
				    	$this = $(this);
						if ($quantityInput.val() > 0) {
							var total = parseInt($quantityInput.val()) * $calcprice;
						}
						else {
							var total = 0;
						}
						var total = total/WCBulkOrder.decmultiple;
						var total = total.toFixed(WCBulkOrder.num_decimals).toString().replace(".", WCBulkOrder.decimal_sep);
						//console.log(total);
						$displayPrice.find('span').text(ui.item.price.replace($price,total));
						calculateTotal();
				    }).change();
				}
			},
			minLength: WCBulkOrder.minLength,
			delay: WCBulkOrder.Delay,
			search: function(event, ui) {
		       	var $input = $(this);
				var $spinner = $('.bulkorder_spinner', $input.parent());
		       	$spinner.show();
		   	},
			response: function(event, ui) {
				$('.bulkorder_spinner').hide();
				// ui.content is the array that's about to be sent to the response callback.
				if ((ui.content == null) || (ui.content === 0)){
					var $input = $(this);
					var $quantityInput = ($input).parent().siblings().find(".wcbulkorderquantity");
					var $displayPrice = $('.wcbulkorderprice', $input.parent().parent());
					$input.val("");
					$input.attr("placeholder", WCBulkOrder.noproductsfound);
					$displayPrice.html("");
					$quantityInput.val("");
					calculateTotal();
				}				
			},
			change: function (ev, ui) {
                if (!ui.item) {			
                    $(this).val("");
					$(this).attr("placeholder", WCBulkOrder.selectaproduct);
				}
				calculateTotal();
            }
		}).each(function(){
			if (WCBulkOrder.display_images === 'true') {
				$(this).data("ui-autocomplete")._renderItem = function (ul, item) {
					return $( "<li>" )
					.append( "<a>" + "<img class='wcbof_autocomplete_img' src='" + item.imgsrc + "' />" + item.label+ "</a>" )
					.appendTo( ul );
				} 
			} else {
				return;
			}
		}).click(function () {
		    $(this).autocomplete('search', '');
		});
	}
	
	function autocomplete_variation(Delay) {
		var ProdID = ProdID;
		$("input.wcbulkordervariation").autocomplete({
			source: function(req, response){
				var $input = $(this.element);
				var $ProdIDs = $('.wcbulkorderid', $input.parent().parent());
				var ProdID = $ProdIDs.val();
				$.getJSON(WCBulkOrder.url+'?callback=?&action='+variation_search+'&_wpnonce='+WCBulkOrder.search_products_nonce+'&term='+ProdID, response);
			},
			select: function(event, ui) {
				
				var $input = $(this);
				var $quantityInput = ($input).parent().siblings().find(".wcbulkorderquantity");
				var $displayPrice = $('.wcbulkorderprice', $input.parent().parent());
				var $price = ui.item.price.replace(/[^\d.,]/g,"");
				var $calcprice = ui.item.price.replace(/[^\d]/g,"");
				var $ProdID = $('.wcbulkorderid', $input.parent().parent());
				var $variation = $('.wcbulkordervariation', $input.parent().parent());
				var initial = 0;
				$displayPrice.html('<span class="amount">'+ui.item.price+'</span>');
				$displayPrice.find('span').text(ui.item.price.replace($price,initial.toFixed(2)));
				$variation.attr("placeholder", WCBulkOrder.variation_noproductsfound);
				$quantityInput.attr("placeholder", WCBulkOrder.enterquantity);
				$ProdID.val(ui.item.id);
				$quantityInput.off('keyup').on('keyup', function() {
					$this = $(this);
					if ($quantityInput.val() > 0) {
						var total = parseInt($quantityInput.val()) * $calcprice;
					}
					else {
						var total = 0;
					}
					var total = total/WCBulkOrder.decmultiple;
					var total = total.toFixed(WCBulkOrder.num_decimals).toString().replace(".", WCBulkOrder.decimal_sep);
					//console.log(total);
					$displayPrice.find('span').text(ui.item.price.replace($price,total));
					calculateTotal();
				});
				$input.on('autocompletechange change', function () {
			    	$this = $(this);
					if ($quantityInput.val() > 0) {
						var total = parseInt($quantityInput.val()) * $calcprice;
					}
					else {
						var total = 0;
					}
					var total = total/WCBulkOrder.decmultiple;
					var total = total.toFixed(WCBulkOrder.num_decimals).toString().replace(".", WCBulkOrder.decimal_sep);
					//console.log(total);
					$displayPrice.find('span').text(ui.item.price.replace($price,total));
					calculateTotal();
			    }).change();
			},
			minLength: WCBulkOrder.minLength,
			delay: WCBulkOrder.Delay,
			search: function(event, ui) {
				var $input = $(this);
				var $spinner = $('.bulkorder_spinner', $input.parent());
		       	$spinner.show();
		   	},
			response: function(event, ui) {
				$('.bulkorder_spinner').hide();
				// ui.content is the array that's about to be sent to the response callback.
				if ((ui.content == null) || (ui.content === 0)){
					var $input = $(this);
					var $quantityInput = ($input).parent().siblings().find(".wcbulkorderquantity");
					var $displayPrice = $('.wcbulkorderprice', $input.parent().parent());
					$input.val("");
					$input.attr("text", WCBulkOrder.variation_noproductsfound);
					$input.attr("disabled", "disabled");
					$displayPrice.html("");
					$quantityInput.val("");
					calculateTotal();
				}
			},
			change: function (ev, ui) {
                if (!ui.item) {			
                    $(this).val("");
					$(this).attr("placeholder", WCBulkOrder.selectaproduct);
				}	
            }
		}).each(function(){
			if (WCBulkOrder.display_images === 'true') {
				$(this).data("ui-autocomplete")._renderItem = function (ul, item) {
					return $( "<li>" )
					.append( "<a>" + "<img class='wcbof_autocomplete_img' src='" + item.imgsrc + "' />" + item.label+ "</a>" )
					.appendTo( ul );
				} 
			} else {
				return;
			}
		}).click(function () {
		    $(this).autocomplete('search')
		});
	}
	function calculateTotal() {
		var sum = 0;
		var $priceInput = $this.parent().parent().find(".wcbulkorderprice");
		$(".wcbulkorderprice span.amount").each(function() {
			var $val = $(this).text().replace(/[^\d]/g,"");
			if($val > 0) {
				sum = sum.toString().replace(/[^\d]/g,"");
				sum = parseFloat(sum) + parseFloat($val);
				sum = sum/WCBulkOrder.decmultiple;
				sum = sum.toFixed(WCBulkOrder.num_decimals).toString().replace(".", WCBulkOrder.decimal_sep);
				$price = $priceInput.find('span').text().replace(/[^\d.,_']/g,"");
				$prices = $priceInput.find('span').text();
				$this.parents().eq(4).find('.wcbulkorderpricetotal').text($prices.replace($price,sum));
			}
		});
	}
	$("input.wcbulkorderproduct").click(autocomplete());
	$("input.wcbulkordervariation").click(autocomplete_variation('0'));
	$("button.wcbulkordernewrow").live('click', function() {
		var $totalinput = $("tr:last").html();
		$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td class="wcbulkorder-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" /></td><td class="wcbulkorder-variation-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkordervariation[]" class="wcbulkordervariation" /></td><td class="wcbulkorder-quantity"><input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" /></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" /></tr>');
		autocomplete();
		autocomplete_variation();
		return false;
	});
	$("button.wcbulkordernewrowprice").live('click', function() {
		var $totalinput = $("tr:last").html();
		$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td class="wcbulkorder-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" /></td><td class="wcbulkorder-variation-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkordervariation[]" class="wcbulkordervariation" /></td><td class="wcbulkorder-quantity"><input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" /></td><td class="wcbulkorderprice"></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" /></tr>');
		autocomplete();
		autocomplete_variation();
		return false;
	});
	
});
