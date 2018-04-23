<?php defined('_JEXEC') or die('JS: No Direct Access');


$visitor_type_name = JTEXT::_('Not identified'); //_JS_DB_IPADD__TYPE_NOT_IDENTIFIED_VISITOR
if ($this->VisitorObj->client_type == _JS_DB_IPADD__TYPE_REGULAR_VISITOR)
    $visitor_type_name = JTEXT::_('Regular');
else if ($this->VisitorObj->client_type == _JS_DB_IPADD__TYPE_BOT_VISITOR)
    $visitor_type_name = JTEXT::_('Bot');
$date = js_getDate($this->VisitObj->changed_at);
?>

<div style="text-align: center; font-weight: bold; font-size: larger;"><?php echo JTEXT::_( 'Visitor details' );?></div>
<div style="text-align: center;">
<table class="adminlist" style="width: 90%;"  align="center">
    <thead>
    <tr>
        <th>#</th>
        <th style="text-align: left;"><?php echo JTEXT::_( 'Visitor' );?></th>
        <th style="text-align: left;"><?php echo JTEXT::_( 'Value' );?></th>
    </tr>
    </thead>
    <tbody>
		<tr class="row0">
			<td style="text-align: right;"><em>1.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'Visit date' );?>:</td>
			<td nowrap="nowrap"><?php echo $date->toFormat();?></td>
		</tr>
		<tr class="row1">
			<td style="text-align: right;"><em>2.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'Username' );?>:</td>
			<td nowrap="nowrap"><?php echo ( ($this->VisitObj->joomla_userid > 0) ? $this->VisitObj->joomla_username : JTEXT::_( 'Not logged in' ) );?></td>
		</tr>
		<tr class="row0">
			<td style="text-align: right;"><em>2.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'Visitor-ID' );?>:</td>
			<td nowrap="nowrap"><?php echo $this->VisitorObj->visitor_id;?></td>
		</tr>
		<tr class="row1">
			<td style="text-align: right;"><em>3.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'IP' );?>:</td>
			<td nowrap="nowrap"><?php echo long2ip($this->VisitorObj->visitor_ip);?></td>
		</tr>
		<tr class="row0">
			<td style="text-align: right;"><em>4.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'NS-Lookup' );?>:</td>
			<td nowrap="nowrap"><?php echo $this->VisitorObj->visitor_nslookup;?></td>
		</tr>
		<tr class="row1">
			<td style="text-align: right;"><em>5.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'System' );?>:</td>
			<td nowrap="nowrap"><?php echo $this->VisitorObj->system;?></td>
		</tr>
		<tr class="row0">
			<td style="text-align: right;"><em>6.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'Browser' );?>:</td>
			<td nowrap="nowrap"><?php echo $this->VisitorObj->browser;?></td>
		</tr>
		<tr class="row1">
			<td style="text-align: right;"><em>7.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'Visitor type' );?>:</td>
			<td nowrap="nowrap"><?php echo $visitor_type_name;?></td>
		</tr>
		<tr class="row0">
			<td style="text-align: right;"><em>8.</em></td>
			<td style="padding-right: 20px;" nowrap="nowrap"><?php echo JTEXT::_( 'Country' );?>:</td>
			<td nowrap="nowrap"><?php echo $this->VisitorObj->country;?></td>
		</tr>
	</tbody>	
</table>
</div>
<div style="text-align: center; font-weight: bold; font-size: larger;"><br/><?php echo JTEXT::_( 'Visitor Comment' );?></div>
<div style="text-align: center;">
  <?php if($this->VisitorObj->visitor_id > 0){;?>
    <input type="text" name="note" size="50"/>
  <?php };?>
</div>
