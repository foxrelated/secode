<div class="ynaffiliate_stistic_chart_box">
		<div class="ynaffiliate_stistic_chart_header">
			<span id="statistic_chart_type"><?php echo $this -> translate('Line chart');?></span><?php echo " ".$this -> translate('for')." ";?> <span id="title_statistic_group_by"><?php echo $this -> translate('commission rules')?></span>
		</div>

		<div class="ynaffiliate_statistics_nav">
		    <a id="ynaffiliate_stats_offset_previous" href="" onclick="processStatisticsPage(-1, event);"><i class="fa fa-angle-double-left fa-2x"></i></a>

		    <span id="ynaffilate_from_to"></span>

		    <a id="ynaffiliate_stats_offset_next" href="" onclick="processStatisticsPage(1, event);" style="display: none;"><i class="fa fa-angle-double-right fa-2x"></i></a>
		</div>

		<div class="ynaffiliate_stistic_chart_result">
			<div id="ynaffiliate_nodata" style="display: none" class="tip"><span><?php echo $this -> translate('No data')?></span></div>
		    <script type="text/javascript">
			 	var changeChartType = function(obj)
			 	{
			 		if(obj.value == 'pie')
			 		{
			 			$('chunk-wrapper').hide();
			 			$('statistic_chart_type').innerHTML = '<?php echo $this->string()->escapeJavascript($this -> translate('Pie chart'));?>';
			 		}
			 		else
			 		{
			 			$('chunk-wrapper').show();
			 			$('statistic_chart_type').innerHTML = '<?php echo $this->string()->escapeJavascript($this -> translate('Line chart'));?>';
			 		}
			 	} 	
			 	var changeGroupBy = function(obj)
			 	{
			 		if(obj.value == 'commission_rule')
			 		{
			 			$('statistic_group_by').innerHTML = '<?php echo $this->string()->escapeJavascript($this -> translate('commission rules'));?>';
			 			$('title_statistic_group_by').innerHTML = '<?php echo $this->string()->escapeJavascript($this -> translate('commission rules'));?>';
			 		}
			 		else
			 		{
			 			$('statistic_group_by').innerHTML = '<?php echo $this->string()->escapeJavascript($this -> translate('user network levels'));?>';
			 			$('title_statistic_group_by').innerHTML = '<?php echo $this->string()->escapeJavascript($this -> translate('user network levels'));?>';
			 		}
			 	} 	
			 	var changePeriod = function()
			 	{
			 		var periodEl = $('ynaffiliate_statistic_form').getElement('#period');
			      	var chunkEl = $('ynaffiliate_statistic_form').getElement('#chunk');
			      	switch( periodEl.get('value')) {
				        case 'ww':
				          var children = chunkEl.getChildren();
				          for( var i = 0, l = children.length; i < l; i++ ) {
				            if( ['dd'].indexOf(children[i].get('value')) == -1 ) {
				              children[i].setStyle('display', 'none');
				              if( children[i].get('selected') ) {
				                children[i].set('selected', false);
				              }
				            } else {
				              children[i].setStyle('display', '');
				              chunkEl.set('value', children[i].get('value'));
				            }
				          }
				          break;
				        case 'MM':
				          var children = chunkEl.getChildren();
				          for( var i = 0, l = children.length; i < l; i++ ) {
				            if( ['dd', 'ww'].indexOf(children[i].get('value')) == -1 ) {
				              children[i].setStyle('display', 'none');
				              if( children[i].get('selected') ) {
				                children[i].set('selected', false);
				              }
				            } else {
				              children[i].setStyle('display', '');
				              chunkEl.set('value', children[i].get('value'));
				            }
				          }
				          break;
				        case 'y':
				          var children = chunkEl.getChildren();
				          for( var i = 0, l = children.length; i < l; i++ ) {
				            if( ['ww', 'MM'].indexOf(children[i].get('value')) == -1 ) {
				              children[i].setStyle('display', 'none');
				              if( children[i].get('selected') ) {
				                children[i].set('selected', false);
				              }
				            } else {
				              children[i].setStyle('display', '');
				              chunkEl.set('value', children[i].get('value'));
				            }
				          }
				          break;
				        default:
				          break;
			      	}
			    }
			    
			    var currentArgs = {};
			    var processStatisticsFilter = function(formElement) {
			      var vals = formElement.toQueryString().parseQueryString();
			      vals.offset = 0;
			      vals.userID = <?php echo $this -> userId?>;
			      buildStatisticsChart(vals);
			      return false;
			    }
			    
			    var processStatisticsPage = function(count, event) {
			      event.preventDefault();
			      var args = $merge(currentArgs);
			      args.offset += count;
			      args.userID = <?php echo $this -> userId?>;
			      buildStatisticsChart(args);
			    }
			    var buildStatisticsChart = function(args) {
			      currentArgs = args;
			      $('ynaffiliate_stats_offset_next').setStyle('display', (args.offset < 0 ? '' : 'none'));
  			      var url = new URI('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . $this->url(array('controller' => 'statistic', 'action' => 'chart'), 'ynaffiliate_extended', true) ?>');
			      url.setData(args);
			      new Request.JSON({
			            method: 'post',
			            url: url,
			            data: {
			            },
			           	onSuccess: function(responseJSON) 
			            {
			                var tooltip = new Element('div', {
			                    id: "tooltip"
			                });
			                var json_data = responseJSON.json;
			                var json_dataLabels = responseJSON.dataLabels;
			                var d = [], count = 0, data = [];
				            if(args.chart_type == 'line')
			            	{    
				                for(var groupId in json_data)
				                {
				                	d = [];
				                	count = 0
				                	var ticks = [];
				                	for(var i in json_data[groupId])
					                {
					                    d.push([count, json_data[groupId][i]]);
					                    ticks.push([count, i]);
					                    count = count + 1;
					                }
					                data.push({data: d, label: json_dataLabels[groupId]});
				                }
				                flot.plot(document.id('placeholder'), data, {
				                    legend: {
				                        labelFormatter: function(label, series) {
				                            return label;
				                        }
				                    },
				                    series: {
				                        lines: {
				                            show: true
				                        },
				                        points: {
				                            show: true
				                        }
				                    },
				                    grid: {
				                        hoverable: true,
				                        clickable: true
				                    },
				                    xaxis: { 
				                        show: true,
				                        ticks: ticks
				                    }
				                });
				                if(args.chunk == "dd" && args.period =="y")
				                {
				                    $$('.xAxis .tickLabel').setStyle('display', 'none');
				                }
				                document.id('placeholder').addEvent('plothover', function (event, pos, items) 
				                {
				                    if (items) {
				                        var html = '';
				                        items.each(function (el) {
				                        	if(el)
							            	{
				                           		var y = el.datapoint[1].toFixed(2);
				                           		html += el.series.label + ': ' + y + "<br />";
				                           	}
				                        });
				            
				                        $("tooltip").set('html', html).setStyles({
				                            top: items[0].pageY,
				                            left: items[0].pageX
				                        });
				                        $("tooltip").fade('in');
				                    } else {
				                        $("tooltip").fade('out');
				                    }
				                });
				            }
				            else
				            {
				            	var count = 0;
				            	for(var groupId in json_data)
				                {
					                data.push({data: json_data[groupId], label: json_dataLabels[groupId]});
					                count ++;
				                }
				                if(count == 1 && json_data[1] == 0)
				            	{
				            		$('ynaffiliate_nodata').show();
				            	}
				            	else
				            	{
				            		$('ynaffiliate_nodata').hide();
				            	}
				                flot.plot(document.id('placeholder'), data, {
				                    series: {
				                        pie: {
				                            show: true
				                        }
				                    },
				                    grid: {
				                        clickable: true
				                    }
				                });
				                document.id('placeholder').addEvent('plotclick', function (event, pos, items) {
							        if (items) {
							            var html = '';
							            items.each(function (el) 
							            {
							            	if(el)
							            	{
								                var x = el.datapoint[0].toFixed(2)
								                html += el.series.label + "<br />";
								            }
							            });
							
							            $("tooltip").set('html', html)
							                .setStyles({
							                	top: pos.pageY,
							                	left: pos.pageX
							            });
							            $("tooltip").fade('in');
							        } else {
							            $("tooltip").fade('out');
							        }
							    });
				            }
			                var title_data = responseJSON.title;
			                $('ynaffilate_from_to').innerHTML = title_data;
			                tooltip.inject(document.body);
		               }
			        }).send();
			    }
			
			    window.addEvent('domready', function() 
			    {
			      	changePeriod();
			      	buildStatisticsChart({
				        'status' : 'all',
				        'chart_type':'line',
				        'group_by': 'commission_rule',
				        'chunk' : 'dd',
				        'period' : 'ww',
				        'start' : 0,
				        'offset' : 0,
				        'userID': <?php echo $this -> userId?>
			        });
			    });
			  </script>
			  <div class="fixed-scrolling">
			    <div id="placeholder" style="width:100%; height:350px;">
			      <div id="clickInfo"></div> 
			    </div>
			  </div>
		</div>
	</div>	