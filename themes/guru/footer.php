<footer>
	<div class="left"><img src="wp-content/themes/guru/img/footer-map.png" alt="#"/></div>
	<div class="right">
		<div class="social-footer">
			<a href="#" class="social-ico fb"></a>
			<a href="#" class="social-ico vk"></a>
			<a href="#" class="social-ico g"></a>
			<a href="#" class="social-ico in"></a>
			<a href="#" class="social-ico skype"></a>
		</div>
		<div class="info">
			<div class="location">
				<p class="marker">Location:</p>
				<p>47, Shandora Petefi sq.</p>
				<p>Uzhhorod, 88000, Ukraine</p>
			</div>
			<div class="contactInfo">
				<p>Contact info:</p>
				<a href="mailto:itschool@thinkmobiles.com" class="mail-footer">itschool@thinkmobiles.com</a>
				<p>+38 066 527 26 52</p>
			</div>
		</div>
</footer>
    </div>
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