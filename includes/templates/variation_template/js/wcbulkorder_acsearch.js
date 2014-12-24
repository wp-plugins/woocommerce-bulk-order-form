jQuery(document).ready(function ($){
	
	var product_search = 'bulk_order_product_search';
	var variation_search = 'bulk_order_variation_search';
	//WCBulkOrder.aftertypequantity
	//WCBulkOrder.nodatafound
	//WCBulkOrder.noproductselected
	//WCBulkOrder.beforetypelabel
	//WCBulkOrder.beforetypequanity
	WCBulkOrder.category = $('#BulkOrderForm').attr('category');
	WCBulkOrder.included = $('#BulkOrderForm').attr('included');
	WCBulkOrder.excluded = $('#BulkOrderForm').attr('excluded');
	function autocomplete() {
		$("input.wcbulkorderproduct").autocomplete({
			source: function(req, response){
				$.getJSON(WCBulkOrder.url+'?callback=?&action='+product_search+'&category='+WCBulkOrder.category+'&included='+WCBulkOrder.included+'&excluded='+WCBulkOrder.excluded+'&_wpnonce='+WCBulkOrder.search_products_nonce, req, response);
			},
			select: function(event, ui) {
				
				var $input = $(this);
				var $quantityInput = ($input).parent().siblings().find(".wcbulkorderquantity");
				var $displayPrice = $('.wcbulkorderprice', $input.parent().parent());
				var $ProdID = $('.wcbulkorderid', $input.parent().parent());
				var $variation = $('.wcbulkordervariation', $input.parent().parent());
				$ProdID.val(ui.item.id);
				if (ui.item.has_variation === 'no') {
					$variation.attr("placeholder", WCBulkOrder.variation_noproductsfound);
					$variation.attr("disabled", "disabled");
					$quantityInput.attr("placeholder", WCBulkOrder.enterquantity);
					$quantityInput.off('keyup').on('keyup', function() {
						window.symbol = ui.item.symbol;
						if ($quantityInput.val() > 0) {
							var total = parseInt($quantityInput.val()) * ui.item.price;
						}
						else {
							var total = 0;
						}
						$displayPrice.html(symbol + '<span>'+total.toFixed(2)+'</span>');
						$('.wcbulkorderpricetotal').html(symbol + "0.00");
						calculateTotal();
					});
				}
			},
			minLength: 0,
			delay: 500,
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
				var $ProdID = $('.wcbulkorderid', $input.parent().parent());
				$quantityInput.attr("placeholder", WCBulkOrder.enterquantity);
				$ProdID.val(ui.item.id);
				$quantityInput.off('keyup').on('keyup', function() {
					window.symbol = ui.item.symbol;
					if ($quantityInput.val() > 0) {
						var total = parseInt($quantityInput.val()) * ui.item.price;
					}
					else {
						var total = 0;
					}
					$displayPrice.html(symbol + '<span>'+total.toFixed(2)+'</span>');
					$('.wcbulkorderpricetotal').html(symbol + "0.00");
					calculateTotal();
					
				});
			},
			minLength: 0,
			delay: Delay,
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
		$(".wcbulkorderprice span").each(function() {
			var $item = +$(this).text();
			if( $item > 0) {
				sum += $item;
				$('.wcbulkorderpricetotal').html(window.symbol + sum.toFixed(2));
			}
		});	
	}

	$("input.wcbulkorderproduct").click(autocomplete());
	$("input.wcbulkordervariation").click(autocomplete_variation('0'));
	$("button.wcbulkordernewrow").live('click', function() {
		var $totalinput = $("tr:last").html();
		$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td style="width: 40%"><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" style="width: 100%"></td><td style="width: 20%"><input class="wcbulkordervariation ui-autocomplete-input" type="text" style="width: 100%" name="wcbulkordervariation[]" autocomplete="off"></td><td style="width: 20%"><input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" style="width: 100%"></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value=""></tr>');
		autocomplete();
		autocomplete_variation();
		return false;
	});

	$("button.wcbulkordernewrowprice").live('click', function() {
		var $totalinput = $("tr:last").html();
		$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td style="width: 40%"><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" style="width: 100%"></td><td style="width: 20%"><input class="wcbulkordervariation ui-autocomplete-input" type="text" style="width: 100%" name="wcbulkordervariation[]" autocomplete="off"></td><td style="width: 20%"><input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" style="width: 100%"></td><td style="width: 20%;text-align:center;color: green" class="wcbulkorderprice"></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value=""></tr>');
		autocomplete();
		autocomplete_variation();
		return false;
	});
	
});
