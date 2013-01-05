<?php
/**
 * Calendar Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die; ?>
<table class='calendar'>
    <caption><?php echo $this->row->month_name . ' ' . $this->row->year; ?></caption>
    <tr>
        <?php foreach ($this->row->days_of_week as $day_number) { ?>
        <th class='header'><?php echo $this->row->day_number; ?></th>
        <?php
    }
        $local_current_day = 1; ?>
    </tr>
<tr>
    <?php if ($this->row->day_of_week_number > 0) { ?>
    <td colspan='<?php echo $this->row->day_of_week_number; ?>'>&nbsp;</td>
    <?php
}
    $month = str_pad($this->row->month, 2, "0", STR_PAD_LEFT);
    while ($local_current_day <= $this->row->number_of_days) {
        if ($this->row->day_of_week_number == 7) {
            $this->row->day_of_week_number = 0;
            ?>
    </tr>
    <tr>
<?php
        }
        $local_current_day2 = str_pad($this->row->local_current_day, 2, '0', STR_PAD_LEFT);
        $date               = $this->row->year - $this->row->month - $local_current_day2;
        ?>
        <td class='day' rel='<?php echo $date; ?>'><?php echo $this->row->local_current_day; ?></td>
        <?php
        $local_current_day++;
        $day_number++;
    }

    if ($this->row->day_of_week_number != 7) {
        $remainingDays = 7 - $this->row->day_of_week_number; ?>
        <td colspan='<?php echo $remainingDays; ?>'>&nbsp;</td>
        <?php } ?>
</tr>
</table>
