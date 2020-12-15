<?php
defined('_JEXEC') or die('Restricted access');
require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_djclassifieds'.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'djseo.php');
?>
<h2 class="subTitle">
<?php 
echo $plugin_params->get("plugintitle");
?>
</h2>
<table class="dj-items product-table">
<?php
$first_i = true;
foreach ($vars as $i)
{
?>
	<tr>
		<td>
			<?php //image
				//$image=explode(';',htmlspecialchars($i->image_url));
				echo '<a href="'.JURI::root().DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias).'">';
					if ($i->image_url){
						echo '<img style="width:56px;height:40px;" src="'.JURI::root().$i->image_url.'"';
					}
					else{
						echo '<img style="width:56px;height:40px;" src="'.JURI::root().'components/com_djclassifieds/assets/images/no-image.png" ';	
					}
				echo '</a>';
			?>
		</td>
		<td>
			<?php //title
				echo '<a href="'.JURI::root().DJClassifiedsSEO::getItemRoute($i->id.':'.$i->alias,$i->cat_id.':'.$i->c_alias).'">';
					echo $i->name;
				echo '</a>';
			?>
		</td>
		<td <?php if(!$first_i) echo "style='border-top:dotted 1px #dedede;'";?>>
			<?php //intro description
				echo $i->intro_desc;
			?>
		</td>
		<td>
			<?php //category
				echo $i->c_name;
			?>
		</td>
	</tr>	
<?php
$first_i = false;
}
?>
</table>
