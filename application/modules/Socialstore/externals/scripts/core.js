en4.store = {
	shippingCategories: function(element) {
		if (element.value == 1) {
			document.getElementById('category-wrapper').setStyle('display','none');
		}
		else {
			document.getElementById('category-wrapper').setStyle('display','block');
		}
	},
	
	shippingCountries: function(element) {
		if (element.value == 1) {
			document.getElementById('country-wrapper').setStyle('display','none');
		}
		else {
			document.getElementById('country-wrapper').setStyle('display','block');
		}
	},
	
	changeCategory : function(element, name, model, route) {
		/*if (element.value == '') {
			
		}
		else {*/
		element.form[name].value = element.value;
		//}
		var e = element.name;
		var prefix = 'id_wrapper_' + name + '_';
		var level = element.name.replace(name + '_', '');
		level = parseInt(level);
		var ne = document.getElementById(prefix + (level + 1));
		if (name == 'location_id') {
			var max = 3;
		}
		else {
			var max = 9;
		}

		for (i = level; i < max; i++) {
			if ((document.getElementById(prefix + (i + 1)))) {
				document.getElementById(prefix + (i + 1)).setStyle('display', 'none');
			}
		}
		;

		var request = new Request({
			'url' : en4.core.baseUrl + route + '/store/change-multi-level',
			'data' : {
				'format' : 'html',
				'id' : element.value,
				'name' : name,
				'level' : level,
				'model' : model
			},
			'onComplete' : function(a) {
				if (a != '') {
					ne.setStyle('margin-top', '8px');
					ne.setStyle('display', 'block');
					ne.innerHTML = a;
				}
			}
		});
		request.send();
		
	},


	follow : function(store_id, user_id, text_url) {
		var request = new Request.JSON(
				{
					'format' : 'json',
					'url' : en4.core.baseUrl
							+ 'socialstore/my-follow-store/follow',
					'data' : {
						'user_id' : user_id,
						'store_id' : store_id
					},
					'onComplete' : function(response) {
						if (response.signin == 0) {
							window.location = en4.core.baseUrl + 'login/return_url/64-' + text_url;
							return;
						}
						var ele_array = $$('.store_follow_' + store_id);
						var length = ele_array.length;
						for (i = 0; i < length; i++) {
							ele_array[i].innerHTML = response.text;
							if(response.follow){
								$(ele_array[i]).removeClass("store_follow_follow").addClass("store_follow_unfollow");	
							}else{
								$(ele_array[i]).removeClass("store_follow_unfollow").addClass("store_follow_follow");
							}
						}
					}
				});
		request.send();
	},
	fav : function(product_id, user_id, text_url) {
		var request = new Request.JSON(
				{
					'format' : 'json',
					'url' : en4.core.baseUrl
							+ 'socialstore/my-favourite-product/favourite',
					'data' : {
						'user_id' : user_id,
						'product_id' : product_id
					},
					'onSuccess' : function(response) {
						if (response.signin == 0) {
							window.location = en4.core.baseUrl + 'login/return_url/64-' + text_url;
							return;
						}
						var ele_array = $$('.store_fav_' + product_id);
						var length = ele_array.length;
						for (i = 0; i < length; i++) {
							ele_array[i].innerHTML = response.text;
							if(response.favourite){
								$(ele_array[i]).removeClass("store_fav_favourite").addClass("store_fav_unfavourite");	
							}else{
								$(ele_array[i]).removeClass("store_fav_unfavourite").addClass("store_fav_favourite");
							}
						}
					}
				});
		request.send();
	},
	wishlist : function(product_id, user_id, text_url) {
		var request = new Request.JSON(
				{
					'format' : 'json',
					'url' : en4.core.baseUrl
							+ 'socialstore/wishlist/add-to-wishlist',
					'data' : {
						'user_id' : user_id,
						'product_id' : product_id
					},
					'onSuccess' : function(response) {
						if (response.signin == 0) {
							window.location = en4.core.baseUrl + 'login/return_url/64-' + text_url;
							return;
						}
						var ele_array = $$('.store_wishlist_' + product_id);
						var length = ele_array.length;
						for (i = 0; i < length; i++) {
							ele_array[i].innerHTML = response.text;
							if(response.wishlist){
								$(ele_array[i]).removeClass("store_wishlist_add").addClass("store_wishlist_remove");
							}else{
								$(ele_array[i]).removeClass("store_wishlist_remove").addClass("store_wishlist_add");
							}
						}
					}
				});
		request.send();
	},
	changeCountry : function() {
		if ($('country').value == 'US') {
			$('region-wrapper').setStyle('display','none');
			$('state-wrapper').setStyle('display','block');
		}
		else {
			$('region-wrapper').setStyle('display','block');
			$('region').value = '';
			$('state-wrapper').setStyle('display','none');
		
		}
	},
	
	updateWidgetCookie : function(key, viewtype) {
		myCookie = Cookie.write(key, viewtype, {domain: window.location.hostname, path: en4.core.baseUrl});
		if (viewtype == 'ynstore_ul_grid') {
			$('ynstore_listing' + key).removeClass('ynstore_list_active').addClass('ynstore_list');
			$('ynstore_grid' + key).removeClass('ynstore_grid').addClass('ynstore_grid_active');
			$('ynul-' + key).removeClass('ynstore_ul_listing').addClass('ynstore_ul_grid');
			$$('.pricecart_wrapper').addClass('yndivgrid');
			$$('.pricecart_wrapper.yndivgrid').setStyle('display', 'block');
			$$('.pricecart_wrapper.yndivlist').setStyle('display', 'none');
			$$('.product_description').setStyle('word-wrap', 'break-word');
			$$('.store_addtocart').setStyle('marginTop', '2px');
			$$('.store_outofstock').setStyle('marginTop', '2px');
		}
		if (viewtype == 'ynstore_ul_listing') {
			$('ynstore_listing' + key).removeClass('ynstore_list').addClass('ynstore_list_active');
			$('ynstore_grid' + key).removeClass('ynstore_grid_active').addClass('ynstore_grid');
			$('ynul-' + key).removeClass('ynstore_ul_grid').addClass('ynstore_ul_listing');
			$$('.pricecart_wrapper.yndivgrid').setStyle('display', 'none');
			$$('.pricecart_wrapper.yndivlist').setStyle('display', 'block');
			$$('.pricecart_wrapper').removeClass('yndivgrid');
			$$('.store_addtocart').setStyle('marginTop', '10px');
			$$('.store_outofstock').setStyle('marginTop', '10px');
		}
	}
	

}
en4.socialstore = {
		addClass : function(className, key, viewtype) {
			if (viewtype == 'ynstore_ul_listing') {
				var listing = new Element('a', {
					id: 'ynstore_listing' + key,
					href: 'javascript:void(0)',
					'class' : 'ynstore_list_active',
					html: 'test',
					events: {
						click: function() { 
							en4.store.updateWidgetCookie(key, viewtype);
						}
					}
				});
				var grid = new Element('a', {
					id: 'ynstore_grid' + key,
					href: 'javascript:void(0)',
					'class': 'ynstore_grid',
					html: 'test',
					events: {
						click: function() { 
							en4.store.updateWidgetCookie(key, 'ynstore_ul_grid');
						}
					}
				});
				$$('.pricecart_wrapper.yndivgrid').setStyle('display', 'none');
				$$('.pricecart_wrapper.yndivlist').setStyle('display', 'block');
			}
			else if (viewtype == 'ynstore_ul_grid') {
				var listing = new Element('a', {
					id: 'ynstore_listing' + key,
					href: 'javascript:void(0)',
					'class' : 'ynstore_list',
					html: 'test',
					events: {
						click: function() { 
							en4.store.updateWidgetCookie(key, 'ynstore_ul_listing');
						}
					}
				});
				var grid = new Element('a', {
					id: 'ynstore_grid' + key,
					href: 'javascript:void(0)',
					'class': 'ynstore_grid_active',
					html: 'test',
					events: {
						click: function() { 
							en4.store.updateWidgetCookie(key, viewtype);
						}
					}
				});
				$$('.pricecart_wrapper.yndivgrid').setStyle('display', 'block');
				$$('.pricecart_wrapper.yndivlist').setStyle('display', 'none');
				$$('.product_description').setStyle('word-wrap', 'break-word');
				$$('.store_addtocart').setStyle('marginTop', '2px');
			}
			header = $$('.' + className)[0].firstChild;
			listing.inject(header, 'top');
			grid.inject(header, 'top');
		},
		attrUpdatePrice : function (type_id, option_id) {
			var request = new Request.JSON({
				'url' : en4.core.baseUrl + 'socialstore/product/get-adjust-price',
				'data' : {
					'format' : 'json',
					'type_id': type_id,
					'option_id': option_id
				},
				'onSuccess': function(response) {
					var adjust = response.adjust;
					if (adjust == 1) {
						var price = response.price;
						$('ynstore_attr_price_' + type_id).innerHTML = response.price;
					}
					else {
						$('ynstore_attr_price_' + type_id).innerHTML = '';
					}
				}
			});
			request.send();
		}
}

en4.socialstore.shipping = {
		updateShippingList: function(order_id) {
			var request = new Request.JSON({
				'url' : en4.core.baseUrl + 'socialstore/payment/update-shipping-list',
				'data' : {
					'format' : 'json',
					'order_id': order_id
				},
				'onSuccess': function(response) {
					var address = response.add;
					var id = response.address_id;
					var ele_array = $$('.ynstore_addresses');
					el = ele_array[0];
					var children = ele_array.getChildren();
					temp = "ynstore_addresses_list_" + id;
					str = temp.toString();
					flag = false;
					children[0].each(function(ele, i) {
						if (ele.get('id') === str) {
							html = '<div class="ynstore_left_addbook"><span class="ynstore_addbook_value">' + address + '</span></div><div class="ynstore_right_addbook"><a class="smoothbox" href="' + en4.core.baseUrl + 'socialstore/payment/edit-shipping-address/id/' + id + '">Edit</a> | <a class="smoothbox" href="' + en4.core.baseUrl + 'socialstore/payment/delete-shipping-address/id/' + id + '">Delete</a></div>';
							ele.innerHTML = html;
							var smoothBoxLinks = ele.getElements('a.smoothbox');
		                    // add events for showing the smoothbox when clicking on tag a with class smoothbox
		                    smoothBoxLinks.addEvent('click', function(event) {
		                        event.stop();
		                        Smoothbox.open(this);
		                    });
							flag = true;
						}
					});
					if (flag == false) {
						html = '<div class="ynstore_addresses_list" id="ynstore_addresses_list_' + id + '"><div class="ynstore_left_addbook"><span class="ynstore_addbook_value">' + address + '</span></div><div class="ynstore_right_addbook"><a class="smoothbox" href="' + en4.core.baseUrl + 'socialstore/payment/edit-shipping-address/id/' + id + '">Edit</a> | <a class="smoothbox" href="' + en4.core.baseUrl + 'socialstore/payment/delete-shipping-address/id/' + id + '">Delete</a></div></div>';
						if (el.insertAdjacentHTML)
						    el.insertAdjacentHTML ("afterBegin", html);
						else {
							var range = document.createRange();
						    var frag = range.createContextualFragment(html);
						    el.getChildren()[0].parentNode.insertBefore(frag, el.getChildren()[0]);
						}
						var smoothBoxLinks = el.getChildren()[0].getElements('a.smoothbox');
	                    // add events for showing the smoothbox when clicking on tag a with class smoothbox
	                    smoothBoxLinks.addEvent('click', function(event) {
	                        event.stop();
	                        Smoothbox.open(this);
	                    });
					}

				}
			});
			request.send();
		},
		updateShippingBook: function(order_id,count) {
			var request = new Request.JSON({
				'url' : en4.core.baseUrl + 'socialstore/payment/update-shipping-list',
				'data' : {
					'format' : 'json',
					'order_id': order_id,
					'count': count
				},
				'onSuccess': function(response) {
					var addresses = response.adds;
					var ele_array = $$('.ynstore_addresses');
					el = ele_array[0];
					addresses.each(function(item){
						html = '<div class="ynstore_addresses_list" id="ynstore_addresses_list_' + item.id + '"><div class="ynstore_left_addbook"><span class="ynstore_addbook_value">' + item.add + '</span></div><div class="ynstore_right_addbook"><a class="smoothbox" href="' + en4.core.baseUrl + 'socialstore/payment/edit-shipping-address/id/' + item.id + '">Edit</a> | <a class="smoothbox" href="' + en4.core.baseUrl + 'socialstore/payment/delete-shipping-address/id/' + item.id + '">Delete</a></div></div>';
						if (el.insertAdjacentHTML)
						    el.insertAdjacentHTML ("afterBegin", html);
						else {
						    var range = document.createRange();
						    var frag = range.createContextualFragment(html);
						    el.getChildren()[0].parentNode.insertBefore(frag, el.getChildren()[0]);
						}
						var smoothBoxLinks = el.getChildren()[0].getElements('a.smoothbox');
	                    // add events for showing the smoothbox when clicking on tag a with class smoothbox
	                    smoothBoxLinks.addEvent('click', function(event) {
	                        event.stop();
	                        Smoothbox.open(this);
	                    });
					});
				}
			});
			request.send();
		},
		deleteShippingList: function(id) {
			var ele_array = $$('.ynstore_addresses');
			var children = ele_array.getChildren();
			var temp = "ynstore_addresses_list_" + id;
			var str = temp.toString();
			children[0].each(function(el, i) {
				if (el.get('id') === str) {
					el.destroy();
				}
			})
		},
		addAnotherBox: function(order_id) {
			var sb_url = en4.core.baseUrl + 'socialstore/payment/add-shipping-address/order_id/' + order_id;
		    Smoothbox.open(sb_url);
		},
		addBookBox: function(order_id) {
			var sb_url = en4.core.baseUrl + 'socialstore/payment/add-from-book/order_id/' + order_id;
		    Smoothbox.open(sb_url);
		}
}

en4.socialstore.packages = {
		changeAddress: function (store_id, address_id, category, order_id, orderitem_id, string) {
			var string_array = string.id.split('_');
			count = string_array[string_array.length -1];
			if (address_id == 0){
				$('ynstore_shipping_method_select_' + orderitem_id + '_' + count).options.length = 0;
				$('ynstore_shipping_method_select_' + orderitem_id + '_' + count).options[0] = new Option(ynstorePackageTrans.none, 0);
			}
			else {
				var request = new Request.JSON({
					'url' : en4.core.baseUrl + 'socialstore/payment/get-shipping-method',
					'data' : {
						'format' : 'json',
						'store_id' : store_id,
						'address_id' : address_id,
						'category' : category,
						'order_id' :order_id
					},
					'onSuccess': function(response) {
						if (response.error == 1) {
							alert(response.text);
						}
						else {
							rules = response.rules;
							$('ynstore_shipping_method_select_' + orderitem_id + '_' + count).options.length = 0;
							rules.each(function(item, i){
								var newOpt = new Option(item.name, item.id);
								$('ynstore_shipping_method_select_' + orderitem_id + '_' + count).options[i] = newOpt;
							});
						}
					}
				});
				request.send();
			}
		},
		addPackage: function(orderitem_id, row_id) {
			var rows = $('ynstore_package_table').rows.length - 1;
			var new_row = rows + 1;
			var original = $('ynstore_orderitem_' + orderitem_id + '_' + row_id);
			var product = original.getElementsByTagName('input')[0].name;
			var product_id = product.substring(13,product.indexOf("]"));
			var quantity = original.getElementsByTagName('input')[0].value.toInt();
			q = (quantity - '1').toInt();
			original.getElementsByTagName('input')[0].value = quantity - 1;
			if (original.getElementsByTagName('input')[0].value == 1) {
				temp_id = original.getElementsByTagName('input')[0].id;
				var string_array = temp_id.split('_');
				previous = string_array[string_array.length -1];
				current = string_array[string_array.length -2];
				original.getElementsByTagName('td')[5].innerHTML = ynstorePackageTrans.na;
			}
			else {
			var origin_html = '<a onclick="javascript:en4.socialstore.packages.addPackage(' + orderitem_id + ',' + row_id + ');" href="javascript:void(0);">' + ynstorePackageTrans.multi + '</a>';
			original.getElementsByTagName('td')[5].innerHTML = origin_html;
			}
			var clone = original.cloneNode(true);
			clone.id = 'ynstore_orderitem_' + orderitem_id + '_' + new_row;
			clone.getElementsByTagName('input')[0].id = 'ynstore_package_quantity_' + new_row + '_' + row_id;
			clone.getElementsByTagName('input')[0].value = 1;
			clone.getElementsByTagName('input')[0].name = 'cartitem_qty[' + product_id + '][' + new_row + '][quantity]';
			clone.getElementsByTagName('input')[1].name = 'cartitem_qty[' + product_id + '][' + new_row + '][options]';
			clone.getElementsByTagName('select')[0].id = 'ynstore_shipping_address_select_' + new_row;
			clone.getElementsByTagName('select')[0].value = 0;
			clone.getElementsByTagName('select')[0].name = 'cartitem_qty[' + product_id + '][' + new_row + '][address]';
			clone.getElementsByTagName('select')[1].id = 'ynstore_shipping_method_select_' + orderitem_id + '_' + new_row;
			clone.getElementsByTagName('select')[1].name = 'cartitem_qty[' + product_id + '][' + new_row + '][rule]';
			var html = '<a onclick="javascript:en4.socialstore.packages.removePackage(\'' + clone.id + '\',' + row_id + ',' + new_row + ',' + orderitem_id + ');" href="javascript:void(0);">' + ynstorePackageTrans.remove + '</a>';
			clone.getElementsByTagName('td')[5].innerHTML = html;
			clone.getElementsByTagName('td')[2].id = "ynstore_package_quantity_" + new_row;
			clone.getElementsByTagName('select')[1].options.length = 0;
			clone.getElementsByTagName('select')[1].options[0] = new Option(ynstorePackageTrans.none, 0);
			$('ynstore_package_table_body').appendChild(clone);
		},
		removePackage: function(row_id, old_row, new_row, orderitem_id) {
			new_quantity = $('ynstore_package_quantity_' + new_row + '_' + old_row).value.toInt();
			old_quantity = $('ynstore_orderitem_' + orderitem_id + '_' + old_row).getElementsByTagName('input')[0].value.toInt();
			id = $('ynstore_orderitem_' + orderitem_id + '_' + old_row).getElementsByTagName('input')[0].id;
			var string_array = id.split('_');
			old = string_array[string_array.length -1];
			current = string_array[string_array.length -2];
			quantity = new_quantity + old_quantity; 
			$('ynstore_orderitem_' + orderitem_id + '_' + old_row).getElementsByTagName('input')[0].value = quantity;
			if (quantity > 1) {
				var html = '<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage(' + orderitem_id + ',' + old_row + ');">' + ynstorePackageTrans.multi + '</a>';
				if (old != 0) {
					var flag = 0;
					var trs = $('ynstore_package_table_body').getElementsByTagName('input');
						for (var i = 0; i < trs.length; i++) {  
							item = trs[i];
							check_id = item.id;
							check_array = check_id.split('_');
							check = check_array[check_array.length -1];
							if (old_row == check) {
								flag++;
							}
					};
					if (flag <= 1) {
						html += ' | ';
						html += '<a onclick="javascript:en4.socialstore.packages.removePackage(\'' + $('ynstore_orderitem_' + orderitem_id + '_' + old_row).id + '\',' + old + ',' + current + ',' + orderitem_id + ');" href="javascript:void(0);">' + ynstorePackageTrans.remove + '</a>';
					}
				}
				$('ynstore_orderitem_' + orderitem_id + '_' + old_row).getElementsByTagName('td')[5].innerHTML = html;
			}
			document.getElementById(row_id).destroy();
		},
		updatePackage: function(object_id,orderitem_id,row,total,new_value,row_number) {
			/**
			 * current/row/tdrow: row current update value
			 * old/old_row/old_tdrow: the row that gives birth to current row. value will be update between old and current
			 * previous: row that gives birth to old row. only for render html script
			 */
			row_id = row.id;
			var quantity = 0;
			$$('.ynstore_package_quantity_' + row_number).each(function(item) {
				console.log(item.id);
				if (row_id != item.id) {
					item_quantity = item.getElementsByTagName('input')[0].value.toInt();
					quantity = quantity + item_quantity;
				}
			});
			old_quantity = total - quantity;
			console.log(old_quantity);
			input = row.getElementsByTagName('input')[0];
			if((parseFloat(new_value) == parseInt(new_value)) && !isNaN(new_value)){
				if (new_value <= 0) {
					input.value = old_quantity;
					return;
				}
				string = input.id;
				var string_array = string.split('_');
				old = string_array[string_array.length -1];
				current = string_array[string_array.length -2];
				if (old == 0) {
					input.value = old_quantity;
					return;
				}
				var old_row = $('ynstore_package_quantity_' + old).getElementsByTagName('input')[0];
				temp_string = old_row.id;
				var temp_array = temp_string.split('_');
				previous = temp_array[temp_array.length -1];
				old_value = old_row.value.toInt();
				temp_value = old_value + old_quantity;
				if (new_value >= temp_value) {
					input.value = old_quantity;
					return;
				}
				old_row.value = temp_value - new_value;
				var old_tdrow = old_row.parentNode.parentNode;
				var tdrow = row.parentNode;
				html = '';
				if (old_row.value == 1) {
					old_tdrow.getElementsByTagName('td')[5].innerHTML = ynstorePackageTrans.na;
				} 
				else {
					flag = 0;
					if (old_row.value > 1) {
						html += '<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage(' + orderitem_id + ',' + old + ');">' + ynstorePackageTrans.multi + '</a>';
						flag = 1;
					}
					/*if (previous != 0) {
						if (flag == 1) {
							html += ' | ';
						}
						console.log(1);
						html += '<a onclick="javascript:en4.socialstore.packages.removePackage(\'' + old_tdrow.id + '\',' + previous + ',' + old + ',' + orderitem_id + ');" href="javascript:void(0);">' + ynstorePackageTrans.remove + '</a>';
					}*/
					old_tdrow.getElementsByTagName('td')[5].innerHTML = html;
				}
				another_html = '';
				if (new_value > 1) {
					another_html += '<a href="javascript:void(0);" onclick="javascript:en4.socialstore.packages.addPackage(' + orderitem_id + ',' + current + ');">' + ynstorePackageTrans.multi + '</a> | ';
				}
				if (new_value == 1 && tdrow.getElementsByTagName('td')[5].innerHTML == ynstorePackageTrans.na) {
					tdrow.getElementsByTagName('td')[5].innerHTML = ynstorePackageTrans.na;
				} 
				else {
					console.log(2);
					another_html += '<a onclick="javascript:en4.socialstore.packages.removePackage(\'' + tdrow.id + '\',' + old + ',' + current + ',' + orderitem_id + ');" href="javascript:void(0);">' + ynstorePackageTrans.remove + '</a>';
					tdrow.getElementsByTagName('td')[5].innerHTML = another_html;
				}
			} 
			else {
				input.value = old_quantity;
			}
		}
}

en4.store.cart = {
	addProduct : function(id, qty) {
		var request = new Request.JSON({
			'url' : en4.core.baseUrl + 'socialstore/my-cart/ajax-add-product',
			'data' : {
				'format' : 'json',
				'product_id' : id,
				'product_qty' : qty
			},
			'onSuccess': function(response) {
				if (response.error == 1) {
					alert(response.text);
					return;
				}
				var ele_array = $$('.socialstore_main_mycart');
				ele_array[0].innerHTML = '<span>' + response.text + '</span>';
			}
		});
		request.send();
	},
	addProductBox: function(pro_id) {
		eles = $$('.ynstore_attr_option');
		str = '';
		for (i = 0; i < eles.length; i++) {
			ele = $$('.ynstore_attr_option')[i].getElement('select');
			id = ele.get('id');
			id_split = id.split("_");
		    type = id_split[id_split.length - 1];
		    option = ele.value;
		    if (i == 0) {
		    	str+= option;
		    }
		    else {
		    	str+= '-' + option; 
		    }
		}
		var sb_url = en4.core.baseUrl + 'socialstore/my-cart/add-product/id/' + pro_id + '/option/' + str;
	    Smoothbox.open(sb_url);
	}
}

var initMap = function() {
	var position = {
		lat : 40.675658,
		lng : -73.995287
	};
	if ($('longitude') && $('longitude').value) {
		position.lat = parseFloat($('latitude').value);
		position.lng = parseFloat($('longitude').value);
	}
	var myLatlng = new google.maps.LatLng(position.lat, position.lng);
	var myOptions = {
		zoom : 15,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var mapEle = document.createElement('DIV');
	mapEle.id = 'map_canvas_edit';
	$('latitude-element').appendChild(mapEle);
	function deleteMarker() {
		if (marker) {
			marker.setMap(null);
			marker = null;
		}
	}

	function resetMarker(pos) {
		deleteMarker();
		marker = new google.maps.Marker({
			position : pos,
			animation : google.maps.Animation.DROP,
			draggable : true,
			map : map,
			title : "Drag this marker to set position of your Store!"
		});
		updatePosition(pos);
		return marker;
	}

	var map = new google.maps.Map(document.getElementById('map_canvas_edit'),
			myOptions);

	var input = document.getElementById('address');
	var autocomplete = new google.maps.places.Autocomplete(input);

	var marker = resetMarker(myLatlng);

	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		// infowindow.close();
		var place = autocomplete.getPlace();
		if (place.geometry.viewport) {
			deleteMarker();
			map.fitBounds(place.geometry.viewport);
		} else {
			var pos = place.geometry.location;
			marker = resetMarker(pos);
			map.setCenter(pos);
			map.setZoom(17);
			// Why 17? Because it looks good.
		}
	});
	function showInfo(msg) {

	}

	// Add dragging event listeners.
	google.maps.event.addListener(marker, 'dragstart', function() {
		showInfo('Dragging...');
	});
	google.maps.event.addListener(map, "rightclick", function(event) {
		resetMarker(event.latLng);
	});
	function updatePosition(pos) {
		if (pos && pos.lat && pos.lng) {
			$('latitude').value = pos.lat();
			$('longitude').value = pos.lng();
		}
	}
	;

	google.maps.event.addListener(marker, 'dragend', function() {
		updatePosition(marker.getPosition());
	});
}
function viewGoogleMap(canvasId) {
	var ele = $(canvasId);
	if (ele == null || ele == undefined) {
		return;
	}
	var lat = ele.getAttribute('latitude');
	var lng = ele.getAttribute('longitude');
	if (lat) {
		lat = parseFloat(lat)
	}
	if (lng) {
		lng = parseFloat(lng)
	}
	if (!lat || !lng) {
		ele.innerHTML = "no position associate with this store!";
		return;
	}
	var myLatlng = new google.maps.LatLng(lat, lng);
	var myOptions = {
		zoom : 13,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var map = new google.maps.Map(document.getElementById(canvasId), myOptions);
	var marker = new google.maps.Marker({
		position : myLatlng,
		map : map,
		title : "Store Position"
	});
}

function viewGoogleMapFromAddress(canvasId) {
	var position = {
		lat : 40.675658,
		lng : -73.995287
	};
	var ele = $(canvasId);
	if (ele == null || ele == undefined) {
		return;
	}
	var request = {
		address : ele.getAttribute('title') + ' - '
				+ ele.getAttribute('location')
	};
	var myLatlng = new google.maps.LatLng(position.lat, position.lng);
	var myOptions = {
		zoom : 15,
		center : myLatlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	var map = null;
	geocoder = new google.maps.Geocoder();

	function matchGeoCoder(request) {
		geocoder.geocode(request, showResults);
	}
	function showResults(results, status) {
		if (status == google.maps.GeocoderStatus.OK && results
				&& results.length) {
			var result = results[0];
			var latlng = result.geometry.location;
			map = new google.maps.Map(document.getElementById(canvasId), {
				zoom : 15,
				center : latlng,
				mapTypeId : google.maps.MapTypeId.ROADMAP
			});
			var marker = new google.maps.Marker({
				position : latlng,
				map : map,
				title : result.formatted_address
			});
			var infowindow = new google.maps.InfoWindow({
				content : $('company_component_inforbox').innerHTML
			});
			google.maps.event.addListener(marker, 'click', function() {
				infowindow.open(map, marker);
			});
			$('store_loading_google_map').style.display = 'none';
		} else {
			$('store_loading_google_map').innerHTML = 'Invalid Address!';
		}

	}
	matchGeoCoder(request);
}
