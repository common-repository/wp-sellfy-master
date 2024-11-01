<?php
/*
Plugin Name: WP Sellfy Master
Plugin URI: http://konradprzydzial.pl/
Description: A simple plugin that makes it easy to webpage connect with the Sellfy.com
Version: 1.1.1b
Author: Konrad Przydział
Author URI: http://konradprzydzial.pl
License: GPL2

    Copyright 2012  Konrad Przydział  (email : konrad.przydzial@outlook.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
*	Widget with Sellfy offer
*/

class Sellfy_Widget extends WP_Widget {
    function Sellfy_Widget() {
        parent::WP_Widget( 'sellfy-widget', $name = 'Sellfy Widget', array( 'description' => __( 'Show your Sellfy.com offers', 'text_domain' ) ));
    }

 
	function form($instance) {
		if ( $instance ) {
			$title = esc_attr( $instance[ 'title' ] );
		}
		else {
			$title = __('My Sellfy Product');
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('auto'); ?>"><?php _e('Auto Loading of title:'); ?></label>
			<input type="checkbox" id="<?php echo $this->get_field_id('auto'); ?>" name="<?php echo $this->get_field_name('auto'); ?>"value="yes" <?php if($instance['auto'] == "yes") echo "checked"; ?>>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('url'); ?>"><?php _e('Offer URL:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('url'); ?>" name="<?php echo $this->get_field_name('url'); ?>" type="text" value="<?php echo $instance['url']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('type'); ?>"><?php _e('Button type:'); ?></label>
			<select id="<?php echo $this->get_field_id('type'); ?>" name="<?php echo $this->get_field_name('type'); ?>">
				<option value="small" <?php if($instance['type'] == 'small') echo 'selected="selected"';?>>Small button</option>
				<option value="large" <?php if($instance['type'] == 'large') echo 'selected="selected"';?>>Large button</option>
			</select>
		</p>
		<?php
	}


    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['url'] = strip_tags($new_instance['url']);
		$instance['type'] = strip_tags($new_instance['type']);
		$instance['auto'] = strip_tags($new_instance['auto']);
		return $instance;
    }

    function widget($args, $instance) {
		extract( $args );

	$title = apply_filters( 'widget_title', $instance['title'] );

	echo $before_widget;
	
	if($instance['auto'] == "yes") {
		$page = file_get_contents($instance['url']);
		preg_match_all("/<h1>(.*?)<\/h1>/",$page,$title);
		$autotitle = $title[1][0];
		echo $before_title . $autotitle . $after_title;	
	} else {
	if ( $title )
		echo $before_title . $title . $after_title;
	}
	
		$token = str_replace('http://sellfy.com/p/', '', $instance['url']);
		if ($instance['type'] == 'small') {
	echo '<a href="'.$instance['url'].'" id="'.$token.'" class="sellfy-buy-button sellfy-small">buy</a>';
	} else {
		echo '<a href="'.$instance['url'].'" id="'.$token.'" class="sellfy-buy-button sellfy-large">buy</a>';
	}
	echo $after_widget;
    }

}

function sellfy_widget_register_widget() {
	register_widget( 'Sellfy_Widget' );
}

add_action( 'widgets_init', 'sellfy_widget_register_widget' );

/*
*	Shortcode for posts and pages: [sellfy type="type here"]url_to_offer[/sellfy]
*/

function sellfy_tag( $atts , $content = null) {
	extract( shortcode_atts( array(
		'type' => 'small'
	), $atts ) );
	
	$token = str_replace('http://sellfy.com/p/', '', $content);
	
	return '<a href="'.$content.'" id="'.$token.'" class="sellfy-buy-button sellfy-'.$atts['type'].'">buy</a>';
}

add_shortcode( 'sellfy', 'sellfy_tag' );

/*
*	One script for all buttons
*/

function add_sellfy_head() {
	echo '<script type="text/javascript" src="http://sellfy.com/js/api_buttons.js"></script>';
}

add_action('wp_head', 'add_sellfy_head');

?>