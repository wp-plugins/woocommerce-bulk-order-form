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
				var initial = 0;
				$displayPrice.html('<span class="amount">'+ui.item.price+'</span>');
				$displayPrice.find('span').text(ui.item.price.replace($price,initial.toFixed(2)));
				var $ProdID = $('.wcbulkorderid', $input.parent().parent());
				var $variation = $('.wcbulkordervariation', $input.parent().parent());
				$variation.prop('disabled', false);
				$ProdID.val(ui.item.id);
				if(ui.item.variation_template == '2'){
					$variation.parent().html(ui.item.attribute_html);
				}
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
						$displayPrice.find('span').text(ui.item.price.replace($price,total));
						calculateTotal(ui.item.price);
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
						$displayPrice.find('span').text(ui.item.price.replace($price,total));
						calculateTotal(ui.item.price);
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
					calculateTotal(ui.item.price);
				}				
			},
			change: function (ev, ui) {
                if (!ui.item) {			
                    $(this).val("");
					$(this).attr("placeholder", WCBulkOrder.selectaproduct);
				}
				calculateTotal(ui.item.price);
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
					$displayPrice.find('span').text(ui.item.price.replace($price,total));
					calculateTotal(ui.item.price);
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
					$displayPrice.find('span').text(ui.item.price.replace($price,total));
					calculateTotal(ui.item.price);
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
					calculateTotal(ui.item.price);
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
	function calculateTotal(priceinputs) {
		var sum = 0;
		$(".wcbulkorderprice span.amount").each(function() {
			var $val = $(this).text().replace(/[^\d]/g,"");
			if($val > 0) {
				sum = sum.toString().replace(/[^\d]/g,"");
				sum = parseFloat(sum) + parseFloat($val);
				sum = sum/WCBulkOrder.decmultiple;
				sum = sum.toFixed(WCBulkOrder.num_decimals).toString().replace(".", WCBulkOrder.decimal_sep);
				$price = priceinputs.replace(/[^\d.,_']/g,"");
				$prices = priceinputs;
				$(this).parents().eq(4).find('.wcbulkorderpricetotal').text($prices.replace($price,sum));
			}
		});
	}
	$("input.wcbulkorderproduct").click(autocomplete());
	$("input.wcbulkordervariation").click(autocomplete_variation('0'));
	$("button.wcbulkordernewrow").live('click', function() {
		var $totalinput = $("tr:last").html();
		if(WCBulkOrder.single_buttons == '2'){
			$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td class="wcbulkorder-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" /></td><td class="wcbulkorder-variation-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkordervariation[]" class="wcbulkordervariation" /></td><td class="wcbulkorder-quantity"><input type="number" name="wcbulkorderquantity[]" class="wcbulkorderquantity" /></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" /><td class="wcbulkorderaddtocart"><input type="button" value="Add to Cart" data-link=""><span class="add_to_cart_message"></span></td></tr>');
		} else {
			$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td class="wcbulkorder-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" /></td><td class="wcbulkorder-variation-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkordervariation[]" class="wcbulkordervariation" /></td><td class="wcbulkorder-quantity"><input type="number" name="wcbulkorderquantity[]" class="wcbulkorderquantity" /></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" /></tr>');
		}
		autocomplete();
		autocomplete_variation();
		$('.wcbulkorderquantity, input.wcbulkorderproduct').off('keyup').on('keyup', function() {
			var item = $(this);
			get_ajax_price(item);
		});
		$('.wcbulkorderquantity').bind('keyup mouseup', function(){
			var item = $(this);
			get_ajax_price(item);
		});
		return false;
	});
	$("button.wcbulkordernewrowprice").live('click', function() {
		var $totalinput = $("tr:last").html();
		if(WCBulkOrder.single_buttons == '2'){
			$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td class="wcbulkorder-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" /></td><td class="wcbulkorder-variation-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkordervariation[]" class="wcbulkordervariation" /></td><td class="wcbulkorder-quantity"><input type="number" name="wcbulkorderquantity[]" class="wcbulkorderquantity" /></td><td class="wcbulkorderprice"></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" /><td class="wcbulkorderaddtocart"><input type="button" value="Add to Cart" data-link=""><span class="add_to_cart_message"></span></td></tr>');
		} else {
			$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td class="wcbulkorder-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" /></td><td class="wcbulkorder-variation-title"><i class="bulkorder_spinner"></i><input type="text" name="wcbulkordervariation[]" class="wcbulkordervariation" /></td><td class="wcbulkorder-quantity"><input type="number" name="wcbulkorderquantity[]" class="wcbulkorderquantity" /></td><td class="wcbulkorderprice"></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value="" /></tr>');
		}
		autocomplete();
		autocomplete_variation();
		$('.wcbulkorderquantity, input.wcbulkorderproduct').off('keyup').on('keyup', function() {
			var item = $(this);
			get_ajax_price(item);
		});
		$('.wcbulkorderquantity').bind('keyup mouseup', function(){
			var item = $(this);
			get_ajax_price(item);
		});
		return false;
	});

	$("td.wcbulkorderaddtocart input").click(function(){
		var $id = $(this).parent().parent().find(".wcbulkorderid").val();
		var $quantity = $(this).parent().parent().find(".wcbulkorderquantity").val();
		var $attribute_array = {};
		var $cart_message_container = $(this).parent().parent().find(".add_to_cart_message");
		var $att_input = $(this).parent().parent().find("select.attribute-selection");

		$att_input.each(function( index ) {
			var $att = $(this).data('attribute');
			$attribute_array[$att] = $(this).val();
		});

		var $url = WCBulkOrder.url+'?callback=?&action=ajax_product_add_to_cart';
		var request = $.ajax({
		  url: $url,
		  type: "POST",
		  data: { id : $id, quantity: $quantity, atts: $attribute_array },
		  dataType: "html"
		});
		 
		request.done(function( msg ) {
			$cart_message_container.html( msg );
			$cart_message_container.fadeIn('slow', function () {
			    $(this).delay(3000).fadeOut('slow');
			});
		});
		 
		request.fail(function( jqXHR, textStatus ) {
			$cart_message_container.html('Looks like there was an error. Please try again or contact us for help');
			$cart_message_container.fadeIn('slow', function () {
			    $(this).delay(3000).fadeOut('slow');
			});
		});
	});

	$(".bulkorderform_submit").click(function(event){
		event.preventDefault();
		var $data_array = {};
		var $cart_message_container = $(this).parents().find(".multi_add_to_cart_message");
		$("input.wcbulkorderproduct").each(function( index ) {
			var $id = $(this).parent().parent().find(".wcbulkorderid").val();
			var $quantity = $(this).parent().parent().find(".wcbulkorderquantity").val();
			var $att_input = $(this).parent().parent().find("select.attribute-selection");
			var $attribute_array = {};
			$att_input.each(function( index ) {
				var $att = $(this).data('attribute');
				$attribute_array[$att] = $(this).val();
			});
			$data_array[$id] = { id : $id, quantity: $quantity, atts: $attribute_array };
		});
		var $url = WCBulkOrder.url+'?callback=?&action=ajax_multiproduct_add_to_cart';
		var request = $.ajax({
		  url: $url,
		  type: "POST",
		  data: $data_array,
		  dataType: "html"
		});
		 
		request.done(function( msg ) {
			$cart_message_container.html( msg );
			$cart_message_container.fadeIn('slow', function () {
			    $(this).delay(20000).fadeOut('slow');
			});
			if($('a.checkouturl').length === 0){
				$('<a href="'+WCBulkOrder.checkouturl+'" class="add_to_cart_button single_add_to_cart_button checkouturl">'+WCBulkOrder.checkouttext+'</a>').insertAfter('.bulkorderform_submit');
			}
		});
		 
		request.fail(function( jqXHR, textStatus ) {
			$cart_message_container.html('Looks like there was an error. Please try again or contact us for help');
			$cart_message_container.fadeIn('slow', function () {
			    $(this).delay(20000).fadeOut('slow');
			});
		});
	});

	$('.wcbulkorderquantity, input.wcbulkorderproduct').off('keyup').on('keyup', function() {
		var item = $(this);
		get_ajax_price(item);
	});
	$('.wcbulkorder-variation-title select').live('change', function() {
		var item = $(this);
		get_ajax_price(item);
	});
	$('.wcbulkorderquantity').bind('keyup mouseup', function(){
		var item = $(this);
		get_ajax_price(item);
	});

	function get_ajax_price(item){
		$this = item;
		var $id = $this.parent().parent().find(".wcbulkorderid").val();
		var $quantity = $this.parent().parent().find(".wcbulkorderquantity").val();
		var $attribute_array = {};
		var $priceinput = $this.parent().parent().find("td.wcbulkorderprice");
		var $cart_message_container = $this.parent().parent().find(".add_to_cart_message");
		$("select.attribute-selection").each(function( index ) {
			var $att = $this.data('attribute');
			$attribute_array[$att] = $this.val();
		});
		
		var $url = WCBulkOrder.url+'?callback=?&action=ajax_item_price';
		var request = $.ajax({
		  url: $url,
		  type: "POST",
		  data: { id : $id, quantity: $quantity, atts: $attribute_array },
		  dataType: "html"
		});
		 
		request.done(function( msg ) {
			var $price = msg.replace(/[^\d.,]/g,"");
			var $calcprice = msg.replace(/[^\d]/g,"");
			if ($quantity > 0) {
				var total = parseInt($quantity) * $calcprice;
			}
			else {
				var total = 0;
			}
			var total = total/WCBulkOrder.decmultiple;
			var total = total.toFixed(WCBulkOrder.num_decimals).toString().replace(".", WCBulkOrder.decimal_sep);
			$priceinput.html(msg);
			$priceinput.find('span').text($priceinput.find('span').text().replace($price,total));
			calculateTotal($priceinput.find('span').text());
		});
		 
		request.fail(function( jqXHR, textStatus ) {
			$cart_message_container.html('Looks like there was an error. Please try again or contact us for help');
			$cart_message_container.fadeIn('slow', function () {
			    $(this).delay(3000).fadeOut('slow');
			});
		});

	}
	
});