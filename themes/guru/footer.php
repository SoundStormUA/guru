<footer>
	<div class="left"><img src="<?php echo get_stylesheet_directory_uri();?>/img/footer-map.png" alt="#"/></div>
	<div class="right">
		<?php echo contact_fields();?>
	</div>
</footer>
</div>
<?php wp_footer(); ?>
<!-- Dialog window -->
	<div id="somedialog" class="dialog">
		<div class="dialog__overlay"></div>
		<div class="dialog__content">
			<h2>Заявку відправлено!</h2><div><button class="action" data-dialog-close>OK</button></div>
		</div>
	</div>
<!-- Dialog window END-->
</body>
</html>