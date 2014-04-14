jQuery(document).ready(function ($){
	
	var acs_action = 'myprefix_autocompletesearch';
	//WCBulkOrder.aftertypequantity
	//WCBulkOrder.nodatafound
	//WCBulkOrder.noproductselected
	//WCBulkOrder.beforetypelabel
	//WCBulkOrder.beforetypequanity
	function autocomplete() {
		$("input.wcbulkorderproduct").autocomplete({
			source: function(req, response){
				$.getJSON(WCBulkOrder.url+'?callback=?&action='+acs_action+'&_wpnonce='+WCBulkOrder.search_products_nonce, req, response);
			},
			select: function(event, ui) {
				
				var $input = $(this);
				var $quantityInput = ($input).parent().siblings().find(".wcbulkorderquantity");
				var $displayPrice = $('.wcbulkorderprice', $input.parent().parent());
				var $ProdID = $('.wcbulkorderid', $input.parent().parent());
				$quantityInput.attr("placeholder", "Enter Quantity");
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
			minLength: 2,
			delay: 500,
			response: function(event, ui) {
				// ui.content is the array that's about to be sent to the response callback.
				if ((ui.content == null) || (ui.content === 0)){
					//alert('nothing here');
					var $input = $(this);
					var $quantityInput = ($input).parent().siblings().find(".wcbulkorderquantity");
					var $displayPrice = $('.wcbulkorderprice', $input.parent().parent());
					$input.val("");
					$input.attr("placeholder", "No Products Found");
					$displayPrice.html("");
					$quantityInput.val("");
					calculateTotal();
				}
			},
			change: function (ev, ui) {
                if (!ui.item) {
					
                    $(this).val("");
					$(this).attr("placeholder", "Please Select a Product");
				}	
            }
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

	$("input.wcbulkorderproduct").click(autocomplete);
	$("button.wcbulkordernewrow").live('click', function() {
		var $totalinput = $("tr:last").html();
		$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td style="width: 60%"><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" style="width: 100%"></td><td style="width: 20%"><input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" style="width: 100%"></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value=""></tr>');
		autocomplete();
		return false;
	});
	$("button.wcbulkordernewrowprice").live('click', function() {
		var $totalinput = $("tr:last").html();
		$("tbody.wcbulkorderformtbody").append('<tr class="wcbulkorderformtr"><td style="width: 60%"><input type="text" name="wcbulkorderproduct[]" class="wcbulkorderproduct" style="width: 100%"></td><td style="width: 20%"><input type="text" name="wcbulkorderquantity[]" class="wcbulkorderquantity" style="width: 100%"></td><td style="width: 20%;text-align:center;color: green" class="wcbulkorderprice"></td><input type="hidden" name="wcbulkorderid[]" class="wcbulkorderid" value=""></tr>');
		autocomplete();
		return false;
	});
	
});
