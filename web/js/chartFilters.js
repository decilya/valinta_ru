var chartFilters = {

	chartDiv: $('[data-chart="true"]'),

	changeRangeButtons: $('[data-event="changeRange"]'),

	changeDetailElement: $('[name="detail"]'),

	dateRangePicker: $('#dateRangePicker'),

	dateRangePickerApplyButton: $('.applyBtn'),

	days: undefined,
	clicks: undefined,
	holidays: undefined,

	dateStart: undefined,
	dateEnd: undefined,

	activeFilter: undefined,
	detailLevel: undefined,

	chartBarWorkdayColor: 'rgba(195, 176, 250, 1)',
	chartBarHolidayColor: 'rgba(232, 176, 250, 1)',

	userId: null,

	init : function(){

		this.days = this.chartDiv.attr('data-days').split(',');
		this.clicks = this.chartDiv.attr('data-clicks').split(',');
		this.holidays = this.chartDiv.attr('data-holidays').split(',');
		this.activeFilter = this.chartDiv.attr('data-filter');
		this.detailLevel = this.chartDiv.attr('data-detail');

		this.dateStart = this.chartDiv.attr('data-start');
		this.dateEnd = this.chartDiv.attr('data-end');

		if(this.chartDiv.attr('data-user-id') !== undefined) this.userId = this.chartDiv.attr('data-user-id');

		this.detailLevelPossibility(chartFilters.activeFilter, this.days.length);

		this.drawChart();

		this.changeFilterEvent();
		this.changeDetailEvent();

	},

	drawChart: function(){

		$('<canvas id="myChart" width="400" height="200"></canvas>').insertAfter('#filterBlock');

		var days = this.days;
		var clicks = this.clicks;
		var holidays = this.holidays;

		var checkMaxClickValue = clicks.every(function(element, index, array){
			return element < 6;
		});

		var bgColor = [];

		holidays.forEach(function(element){
			bgColor.push((element > 5) ? chartFilters.chartBarHolidayColor : chartFilters.chartBarWorkdayColor );
		});

		//console.log(bgColor);

		var ctx = document.getElementById('myChart');

		var myChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: days,
				datasets: [{
					data: clicks,
					backgroundColor: bgColor,
					borderColor: bgColor,
					borderWidth: 1
				}]
			},
			options: {
				legend: {
					display: false
				},
				scales: {
					yAxes: [{
						ticks: {
							beginAtZero:true,
							stepSize: (checkMaxClickValue) ? 1 : false
						}
					}]
				},
				tooltips: {
					callbacks: {
						label: function(tooltipItem, data){
							return 'Просмотров: ' + tooltipItem.yLabel;
						},
						afterLabel: function(tooltipItem, data){
							return 'Ip-адресов: ' + helperObj.ips[tooltipItem.index];
						},
						title: function(tooltipItem, data){

							var index = chartFilters.holidays[tooltipItem[0].index];

							return (chartFilters.detailLevel == 'days') ? tooltipItem[0].xLabel + ', ' + helperObj.dayNames[index-1] : tooltipItem[0].xLabel;
						}
					}
				}
			}
		});
	},

	changeFilterEvent:function(){
		this.changeRangeButtons.on('click', this.changeFilter);
	},

	changeDetailEvent:function(){
		this.changeDetailElement.on('change', this.changeDetail);
	},



	changeRange: function(obj){

		var isDateRangePicker = !!(chartFilters.dateStart !== undefined && chartFilters.dateEnd !== undefined);
        
		$.ajax({
			method: 'POST',
			url: window.location.protocol + '//' + location.host + '/report/change-range',
			data:JSON.stringify({
				'range': chartFilters.activeFilter,
				'dateStart': (isDateRangePicker) ? chartFilters.dateStart : null,
				'dateEnd': (isDateRangePicker) ? chartFilters.dateEnd : null,
				'detailLevel': chartFilters.detailLevel,
				'userId': chartFilters.userId
			}),
			dataType:'json',

			success: function(data){

				//console.log(data);

				window.helperObj.ips = data.reports.ips.split(',');

				$('iframe.chartjs-hidden-iframe').add('#myChart').remove();

				chartFilters.days  = data.reports.days.split(',');
				chartFilters.chartDiv.attr('data-days', data.reports.days);

				chartFilters.clicks  = data.reports.clicks.split(',');
				chartFilters.chartDiv.attr('data-clicks', data.reports.clicks);


				chartFilters.holidays  = data.reports.holidays.split(',');
				chartFilters.chartDiv.attr('data-holidays', data.reports.holidays);


				chartFilters.dateStart = data.reports.dateStart;
				chartFilters.chartDiv.attr('data-start', data.reports.dateStart);


				chartFilters.dateEnd = data.reports.dateEnd;
				chartFilters.chartDiv.attr('data-end', data.reports.dateEnd);

				chartFilters.chartDiv.attr('data-filter', chartFilters.activeFilter);
				chartFilters.chartDiv.attr('data-detail', chartFilters.detailLevel);

				chartFilters.setDateTimePickerValues();

				if(chartFilters.activeFilter === 'custom'){
					var numberOfDays = data.reports.numberOfDays;
				}

				chartFilters.detailLevelPossibility(chartFilters.activeFilter, numberOfDays);

				chartFilters.drawChart();

			}
		});

	},

	setDateTimePickerValues: function(){
		chartFilters.dateRangePicker.val(chartFilters.dateStart + '  -  ' + chartFilters.dateEnd);
		$('[name="daterangepicker_start"]').val(chartFilters.dateStart);
		$('[name="daterangepicker_end"]').val(chartFilters.dateEnd);

	},

	changeFilter: function(){

		chartFilters.changeRangeButtons.add(chartFilters.dateRangePicker).removeClass('active').addClass('default');
		$(this).removeClass('default').addClass('active');

		chartFilters.activeFilter = $(this).attr('data-range');
		chartFilters.detailLevel = 'days';
		chartFilters.changeDetailElement.find('option').removeAttr('selected').filter('option[value="1"]').attr('selected', 'selected');

		chartFilters.changeRange();
	},

	changeDetail: function(){
		chartFilters.detailLevel = $(this).val();
		chartFilters.changeRange();
	},

	detailLevelPossibility: function(filter, numberOfDays){

		var optionWeek, optionMonth;
		optionWeek = $('[name="detail"]').find('option[value="weeks"]');
		optionMonth = $('[name="detail"]').find('option[value="months"]');

		switch(filter){
			case '7days':
				optionWeek.addClass('hidden');
				optionMonth.addClass('hidden');
				break;
			case '30days':
				optionWeek.removeClass('hidden');
				optionMonth.addClass('hidden');
				break;
			case '90days':
				optionWeek.removeClass('hidden');
				optionMonth.removeClass('hidden');
				break;
			case '365days':
				optionWeek.removeClass('hidden');
				optionMonth.removeClass('hidden');
				break;
			case 'custom':

				if(numberOfDays < 8){
					optionWeek.addClass('hidden');
					optionMonth.addClass('hidden');
				}else if(numberOfDays >= 8 && numberOfDays < 46){
					optionWeek.removeClass('hidden');
					optionMonth.addClass('hidden');

				}else if(numberOfDays >= 46){
					optionWeek.removeClass('hidden');
					optionMonth.removeClass('hidden');

				}
				break;
		}

	}

};

chartFilters.init();