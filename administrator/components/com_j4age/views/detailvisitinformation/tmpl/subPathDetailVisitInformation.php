<?php defined('_JEXEC') or die('JS: No Direct Access');     ?>

<div style="text-align: center; font-weight: bold; font-size: larger;"><?php echo JTEXT::_( 'Path info' );?></div>

		<div style="text-align: center;">
		<table class="adminlist" style="width: 90%;" align="center">

			<thead>
                <tr>
                    <th style="width: 1%;" title="><?php echo JTEXT::_( 'Pages are ordered in visit order' );?>"><?php echo JTEXT::_( 'Order' );?></th>
                    <th style="width: 80%; text-align: left;" title="<?php echo JTEXT::_( 'Pages are ordered in visit order' );?>"><?php echo JTEXT::_( 'Page' );?></th>
                    <th align="left" width="20%"><?php echo JTEXT::_( 'Time' );?></th>
                    <th align="left" width="20%"><?php echo JTEXT::_( 'Duration' );?></th>
                </tr>
			</thead>
<?php
		// Body
		if( count($this->path_result_arr) > 0 )
        {
			$k			= 0;
			$order_nbr	= 0;
			foreach( $this->path_result_arr as $rowkey=>$row ) {
				$order_nbr++;
                $durationStr = "-";
                $timeStr = "-";
                if($row->timestamp > 0)
                {
                    $time = js_getDate($row->timestamp);

                    $timeDiff = 0;
                    if(isset($this->path_result_arr[$rowkey+1]))
                    {
                        $timeDiff = ( $this->path_result_arr[$rowkey+1]->timestamp -  $row->timestamp );
                    }
                    $minutes = (int)($timeDiff / 60);
                    $seconds = $timeDiff % 60;

                    $timeStr = $time->toFormat();
                    $durationStr = ($rowkey == (count($this->path_result_arr)-1) ? "-" : ($minutes."m ".$seconds."s") );
                }

        ?>
				<tr class="row<?php echo $k;?>">
                    <td style="text-align: right;"><em><?php echo $order_nbr;?></em></td>
                    <td>
                        <a href="<?php echo htmlentities($row->page);?>" target="_blank" title="<?php echo JTEXT::_( 'Click opens new window' );?>">
                            <?php echo ( $row->page_title == '' ? $row->page : $row->page_title ) ?>
                        </a>
                    </td>
                    <td><?php echo $timeStr?></td>
                    <td><?php echo $durationStr;?></td>
				</tr>
 <?php
				$k = 1 - $k;
			}
		}
?>

        <thead>
        <tr>
        <th><?php echo count($this->path_result_arr);?></th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
        </tr>
        </thead>

    </table>
</div>
