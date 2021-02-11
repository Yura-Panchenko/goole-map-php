<?php

class GMapTool {
	protected $args = array();
	protected $markers = array();
	protected $maps = array();

	function __construct($args = null) {
		if (is_array($args)) {
			if (!isset($args['center']) && isset($args['position'])) {
				$args['center'] = $args['position'];
			}

			$this->addMarker($args);
			$this->display($args);
		}
	}

	public function addMarker($args = null) {

		$marker = wp_parse_args($args, array(
			'position' => null,
			'icon' => null,
			'popup' => null,
			'title' => null,
		));

		if (
			is_array($marker['position']) &&
			isset($marker['position']['lat']) &&
			isset($marker['position']['lng'])) {

				$this->markers[] = $marker;
		}
	}

	public function display($args = null) {

		$args = wp_parse_args($args, array(
			'id' => null,
			'class' => 'gmap-holder',
			'width' => '100%',
			'height' => '100%',
			'zoom' => 14,
			'type' => 'roadmap',
			'center' => null,
			'styles' => null,
			'wrapper' => '<div id="%1$s" class="%2$s" style="width: %3$s; height: %4$s;"></div>',
			'default_icon' => null,
		));

		$args['type'] = in_array(strtoupper($args['type']), array('HYBRID', 'ROADMAP', 'SATELLITE', 'TERRAIN')) ? strtoupper($args['type']) : 'ROADMAP';

		if (
			$args['id'] &&
			!isset($this->maps[$args['id']]) &&
			is_array($args['center']) &&
			isset($args['center']['lat']) &&
			isset ($args['center']['lng'])) {

				$this->maps[$args['id']] = $args;
				echo sprintf($args['wrapper'], $args['id'], $args['class'], $args['width'], $args['height']);
		}

		wp_enqueue_script('gmap-api', 'http://maps.googleapis.com/maps/api/js?sensor=false&key=' . get_field( 'gm_api_key', 'options' ) , array(), null, true);
		add_action('wp_footer', array($this, 'footer_js'), 9999);
	}


	public function footer_js() {
	?>
		<script>
		<?php foreach ($this->maps as $map) : ?>
			google.maps.event.addDomListener(window, 'load', function(){
				var map = new google.maps.Map(document.getElementById('<?php echo $map['id']; ?>'), {
					zoom: <?php echo $map['zoom']; ?>,
					mapTypeId: google.maps.MapTypeId.<?php echo $map['type']; ?>,
					center: new google.maps.LatLng(<?php echo $map['center']['lat']; ?>, <?php echo $map['center']['lng']; ?>),
					<?php if ($map['styles']) : ?>
					styles: <?php echo $map['styles']; ?>,
					<?php endif; ?>
				});

				<?php foreach ($this->markers as $marker) : ?>

				(function(){
					<?php $marker['icon'] = $marker['icon'] ? $marker['icon'] : $map['default_icon']; ?>
					var marker = new google.maps.Marker({
						position: new google.maps.LatLng(<?php echo $marker['position']['lat']; ?>, <?php echo $marker['position']['lng']; ?>),
						map: map,

						<?php if ($marker['title']) : ?>
						title: "<?php echo $marker['title']; ?>",
						<?php endif; ?>

						<?php if($marker['icon']) : ?>
						icon: "<?php echo $marker['icon']; ?>",
						<?php endif; ?>
					});

					<?php if($marker['popup']) : ?>
					var popup = new google.maps.InfoWindow({
						content: '<div class="gmap-popup-content" style="line-height:1.35;overflow:hidden;white-space:nowrap;"><?php echo $marker['popup'] ?></div>',
					});

					google.maps.event.addListener(marker, 'click', function() {
						popup.open(map, marker);
					});
					<?php endif; ?>
				}());

				<?php endforeach; ?>
			});
		<?php endforeach; ?>
		</script>
	<?php
	}
}
