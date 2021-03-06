		</div>
		
		<div class="clear"></div>
		
	</div>
	
	<?php if($device == 'mobile'): ?>	
		<div id="bbb">
			<?php display_img('bbb',$device,'png') ?>
		</div>
	<?php endif; ?>
	
	<div class="clear"></div>
	
	<div class="push"></div>
	
	</div>
	
	<footer>
		
		<div class="wrap">
				
			<div id="pl">
				<a href="http://www.premierleague.com/"><?php display_img('pl',$device,'png') ?></a>
			</div>
			
			<div id="clubs">
				<div id="badges">
				<?php
					
					for($i=1;$i<21;$i++){
						echo '<div class="slide"><a href="http://'.$_clublinks[$i-1].'">';
						display_img('club-badges/badge-'.sprintf("%02s",$i),$device,'png');
						echo '</a></div>';
					}
					
				?>
				</div>
				<div id="controls">
					<div id="slider-prev"></div><div id="slider-next"></div>
				</div>
			</div>
			
			<div class="clear"></div>
			
			<div id="links">
				
				<p id="infolinks"><a href="/terms-and-conditions/">Terms and Conditions</a> | <a href="/privacy-policy/">Privacy Policy</a></p>
			
				<p id="copyright">Copyright <?php date_default_timezone_set('Europe/London'); echo date("Y"); ?> Barclays and Premier League<?php if($device == 'desktop') echo ' | '; ?></p>
					
			</div>
					
		</div>
	
	</footer>
<script type="text/javascript">
var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-48828028-1']);
_gaq.push(['_trackPageview']);
(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; 

ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';

var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
	</body>
</html>