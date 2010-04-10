<?php if (!$isvalidPage) : ?>
<p>Sorry, but you cannot access this page directly</p>
<?php else : ?>
<div class="wrap">
	<form method="post" action="">
	<?php wp_nonce_field('update-options'); ?>
	<table width="80%" border="0">
      <tr>
        <td width="68%"><h2>Remind Me Introduction</h2></td>
        <td width="32%" align="right"><label>
          <input name="items[support]" type="checkbox" id="support" value="1" <?php if ($items["support"]):?>checked="checked"<?php endif; ?> />
        Support </label></td>
      </tr>
    </table>
	<table width="80%">
	<tr>
	<td> <p>The Remind Me Wordpress plugin is the ultimate deep linking plugin available to Wordpress users. A deep link is a link from one of your posts to another post on your website. Instead of always linking to your home page, deep linking allows you to build authority on various posts you've already written. This authority increase results in more traffic coming to these posts from search engines. In other words, instead of always battling to get your home page to rank well Google, you'll now be able to rank all your articles on Google. </p>
	  <p>Once the plugin has been uploaded and activated, you will see a special area below where you write your posts, which will provide you with a list of posts related to the post you're currently writing. I'm sure you've been writing an post before and had the big mission of tracking down the link to a previous post you wrote on a similiar topic. I know I've been there and it's a big mission - Remind Me solves this!</p>
	  <p>Remind me will provide you with related articles based on Titles, Post Content, Tags and Categories, thus returning really good related articles. Linking from one post to another post of similiar content is not only hugely beneficial for SEO, but also a fantastic way to keep your visiter interested and increase your page views.</p>
	  <p>You can read the instructions on how to use the plugin by <a href="http://imod.co.za/remind-me/" target="_blank">clicking here</a>. </p></td>
	</tr>
	</table>	
	<br />
	<table align="left" width="80%">
	<tr>
	<td align="right"><a href="http://www.twitter.com/christopherm" target="_blank">Follow on Twitter</a> | <a href="http://imod.co.za/remind-me/" target="_blank">Plugin Website</a> | 
	<a href="http://del.icio.us/post?url=http://imod.co.za/remind-me/&title=Remind Me Deep Linking SEO Plugin for Wordpress" target="_blank"><img src="<?php echo $globals["tmplUrl"] ?>/img/delicious.png" border="0" /></a>
	<a href="http://digg.com/submit?phase=2&url=http://imod.co.za/remind-me/&title=Remind Me Deep Linking SEO Plugin for Wordpress" target="_blank"><img src="<?php echo $globals["tmplUrl"] ?>/img/digg.png" border="0" /></a>
	<a href="http://www.facebook.com/sharer.php?u=http://imod.co.za/remind-me/&t=Remind Me Deep Linking SEO Plugin for Wordpress" target="_blank"><img src="<?php echo $globals["tmplUrl"] ?>/img/facebook.png" border="0" /></a>
	<a href="http://reddit.com/submit?url=http://imod.co.za/remind-me/&title=Remind Me Deep Linking SEO Plugin for Wordpress" target="_blank"><img src="<?php echo $globals["tmplUrl"] ?>/img/reddit.png" border="0" /></a>
	<a href="http://sphinn.com/submit.php?url=http://imod.co.za/remind-me/&title=Remind Me Deep Linking SEO Plugin for Wordpress" target="_blank"><img src="<?php echo $globals["tmplUrl"] ?>/img/sphinn.gif" border="0" /></a>
	<a href="http://twitter.com/home?status=Remind%20Me%20Deep%20Linking%20%23SEO%20Plugin%20for%20Wordpress%20http%3A%2F%2Fimod.co.za%2Fremind-me%2F" target="_blank"><img src="<?php echo $globals["tmplUrl"] ?>/img/twitter.gif" border="0" /></a>
	</td>
	</tr>
	</table>
	<p>&nbsp;</p>
	<h2>Remind Me Configuration</h2>
	<table class="form-table">		
		<tr>
			<th scope="row">Listing ordering:</th>
		  <td>
				<input type="text" name="items[defaultOrder]" value="<?php echo $items["defaultOrder"]; ?>" size="40" /> 
			  (Specify how the listings will be ordered by default - <strong>advanced users only</strong>)			</td>
		</tr>		
		<tr>
			<th scope="row">Max listings returned:</th>
		  <td>
				<input type="text" name="items[perPage]" value="<?php echo $items["perPage"]; ?>" size="40" />			 
				 (Specify the maximum number of listings to display in your write panel results)			</td>
		</tr>		
	</table>
		<p class="submit"><input type="submit" name="save" value="Save Changes!" /></p>
	</form>
<h2>More Information</h2>
<iframe width="90%" height="200px" src="http://imod.co.za/control/"></iframe>
</div>
<?php endif ?>