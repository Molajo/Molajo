<?php
use Molajo\Service\Services;
/**
 *
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;  ?>

<div class="weather-wrapper">

	<div class="row">
		<div class="twelve columns">
		  <div class="weather current">
			<div class="weather-icon float-left">
			  <img src="<?php echo $this->row->now_icon; ?>" alt="<?php echo $this->row->now_condition; ?>" title="<?php echo $this->row->now_condition; ?>"  />
			</div>
			<div class="weather-info forecast-info float-left">
			  <b><?php echo Services::Language()->translate('Today'); ?></b><br/>
			  <div class="temp"><?php echo $this->row->now_temp; ?></div>
			  <div class="condition"><?php echo $this->row->now_condition; ?></div>
			  <div class="wind"><?php echo $this->row->now_wind_condition; ?></div>
			</div>
		  </div>
		</div>
	</div>

	<div class="row">
		<div class="twelve columns">
			<b><?php echo Services::Language()->translate('Forecast'); ?></b><br/>
	 	</div>
  	</div>

	<div class="row">
		<div class="twelve columns">
			<div class="weather forecast forecast-1">
				<div class="weather-icon float-left">
					<img src="<?php echo $this->row->day1_forecast_icon; ?>" alt="<?php echo $this->row->day1_forecast_day_of_week; ?>" title="<?php echo $this->row->day1_forecast_day_of_week; ?>"  />
				</div>
				<div class="weather-info forecast-info float-left">
					<b><?php echo $this->row->day1_forecast_day_of_week; ?></b><br/>
					<?php echo $this->row->day1_forecast_low_temperature; ?> | <?php echo $this->row->day1_forecast_high_temperature; ?><br/>
					<?php echo $this->row->day1_forecast_condition; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="twelve columns">
			<div class="weather forecast forecast-2">
				<div class="weather-icon float-left">
					<img src="<?php echo $this->row->day2_forecast_icon; ?>" alt="<?php echo $this->row->day2_forecast_day_of_week; ?>" title="<?php echo $this->row->day2_forecast_day_of_week; ?>"  />
				</div>
				<div class="weather-info forecast-info float-left">
					<b><?php echo $this->row->day2_forecast_day_of_week; ?></b><br/>
					<?php echo $this->row->day2_forecast_low_temperature; ?> | <?php echo $this->row->day2_forecast_high_temperature; ?><br/>
					<?php echo $this->row->day2_forecast_condition; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="twelve columns">
			<div class="weather forecast forecast-3">
				<div class="weather-icon float-left">
					<img src="<?php echo $this->row->day3_forecast_icon; ?>" alt="<?php echo $this->row->day3_forecast_day_of_week; ?>" title="<?php echo $this->row->day3_forecast_day_of_week; ?>"  />
				</div>
				<div class="weather-info forecast-info float-left">
					<b><?php echo $this->row->day3_forecast_day_of_week; ?></b><br/>
					<?php echo $this->row->day3_forecast_low_temperature; ?> | <?php echo $this->row->day3_forecast_high_temperature; ?><br/>
					<?php echo $this->row->day3_forecast_condition; ?>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="twelve columns">
			<div class="weather forecast forecast-4">
				<div class="weather-icon float-left">
					<img src="<?php echo $this->row->day4_forecast_icon; ?>" alt="<?php echo $this->row->day4_forecast_day_of_week; ?>" title="<?php echo $this->row->day4_forecast_day_of_week; ?>"  />
				</div>
				<div class="weather-info forecast-info float-left">
					<b><?php echo $this->row->day4_forecast_day_of_week; ?></b><br/>
					<?php echo $this->row->day4_forecast_low_temperature; ?> | <?php echo $this->row->day4_forecast_high_temperature; ?><br/>
					<?php echo $this->row->day4_forecast_condition; ?>
				</div>
			</div>
		</div>
	</div>

</div>
