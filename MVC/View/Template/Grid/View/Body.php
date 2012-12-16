<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die; ?>
<tr<?php echo $this->row->grid_row_class; ?>><?php
    $columnCount = 1;
    $nowrap = ' nowrap';
    $first = 1;
    $columnArray = Services::Registry()->get('Grid', 'Tablecolumns');
    foreach ($columnArray as $column) {
        $class = '';
        $nowrap = '';
        if ($column == 'ordering') {
            $nowrap = ' nowrap';
        } elseif ($column == 'featured' || $column == 'stickied' || $column == 'status') {
            $class = ' class="center"';
        }
        ?>

        <td<?php echo $class ?><?php echo $nowrap; ?>><?php
            if ($column == 'title') {
               // echo '<a href="' . $this->row->catalog_id_url . '">';
                echo $this->row->$column;
                echo '</a>';

            } elseif ($column == 'username') {
                //    echo '<a href="' . $this->row->catalog_id_url . '">';
                    echo $this->row->$column;
                    echo '</a>';

            } elseif ($column == 'status') {
				if ((int) $this->row->status == 2) {
					$class = 'success radius label';
				} elseif ((int) $this->row->status == 1) {
					$class = 'success radius label';
				} elseif ((int) $this->row->status == 0) {
					$class = 'radius label';
				} elseif ((int) $this->row->status == -1) {
					$class = 'alert radius label';
				} elseif ((int) $this->row->status == -2) {
					$class = 'alert radius label';
				} elseif ((int) $this->row->status == -5) {
					$class = 'radius label';
				} elseif ((int) $this->row->status == -10) {
					$class = 'secondary radius label';
				}
				echo '<span class="' . $class . '">' . Services::Language()->translate($this->row->status_name) . '</span>';

			} elseif ($column == 'stickied') {
                echo '<span class="stickied">';
                if ((int) $this->row->$column == 1) {
                    echo '<i class="icon-star" alt="' . Services::Language()->translate('Stickied') .'"></i>';
                } else {
                    echo '<i class="icon-star-empty" alt="' . Services::Language()->translate('Not Stickied') .'"></i>';
                }
                echo '</span>';

            } elseif ($column == 'featured') {
                echo '<span class="featured">';
                if ((int) $this->row->$column == 1) {
                    echo '<i class="icon-star" alt="' . Services::Language()->translate('Featured') .'"></i>';
                } else {
                    echo '<i class="icon-star-empty" alt="' . Services::Language()->translate('Not Featured') .'"></i>';
                }
                echo '</span>';

            } elseif ($column == 'ordering') {
                echo '<span class="orderingicons">';
                if ((int) $this->row->last_row == 1) {
                    echo ' ';
                } else {
                    echo '<i class="icon-arrow-down"></i>';
                }
                if ((int) $this->row->$column == 1) {
                    echo ' ';
                } else {
                    echo '<i class="icon-arrow-up"></i>';
                }
                echo '</span>';
                echo '<span class="ordering">';
                echo $this->row->$column;
                echo '</span>';
            } else {
                echo $this->row->$column;
            }
        ?>
        </td><?php

        if ($first == 1) {
            $first = 0;
            $nowrap = '';
        }

        $columnCount++;
    }
    ?>
    <td class="center last">
        <input type=checkbox value="<?php echo $checked; ?>">
    </td>
</tr>
