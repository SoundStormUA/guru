<footer>
	<div class="left"><img src="wp-content/themes/guru/img/footer-map.png" alt="#"/></div>
	<div class="right">
		<?php echo contact_fields();?>
	</div>
</footer>
</div>
<?php wp_footer(); ?>
<script>
function setHeaders(){
	var headers = {<?php echo header_content();?>};
	if(location.hash && location.hash != '#home' && location.hash != '#shedule') {
		var key = location.hash;
		key = key.substring(1,key.length);
		jQuery('#slide-course h1').text(headers[key]);
	} else {
		jQuery('#slide-course h1').text('IT School');
	}
}
setHeaders();
</script>
</body>
</html>