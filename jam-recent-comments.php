<?php
/*
Plugin Name: JAM Recent Comments
Plugin URI: http://joshmccarty.com 
Description: Adds a widget that displays recent comments including a gravatar and a comment excerpt.
Version: 0.1
Author: Josh McCarty
Author URI: http://joshmccarty.com
License: GPL v2
*/

/*  Copyright YEAR  PLUGIN_AUTHOR_NAME  (email : PLUGIN AUTHOR EMAIL)

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

class JAM_Recent_Comments extends WP_Widget {
    
    /**
     * Register widget with WordPress 
     */
    public function __construct() {
        parent::__construct(
            'jam_recent_comments', //Base ID
            'JAM Recent Comments', //Name
            array( 'description' => __( 'Displays recent comments including a gravatar and excerpt', 'jam-recent-comments-widget' ), ) //Args
        );
    }
    
    /**
     *Front-end display of widget
     * 
     * @see WP_Widget::widget()
     * 
     * @param array $args   Widget arguments
     * @param array $instance   Saved values from database. 
     */
    public function widget( $args, $instance ) {
        extract( $args );
        $title = apply_filters( 'widget-title', $instance['title'] );
        $count = $instance['count'];
        
        echo $before_widget;
        if ( ! empty( $title ) )
            echo $before_title . $title . $after_title; ?>
        <div id="<?php echo $args['widget_id']; ?>" class="jam-recent-comments">
        <?php
        $comment_args = array(
            'status' => 'approve', // Only get approved comments
            'number' => $count, // Get the 3 most recent comments
            'type' => '' // Don't get trackbacks or pingbacks
        );
        $comments = get_comments( $comment_args );
        foreach( $comments as $comment ) :
            $jam_recent_comment_post_title = get_the_title( $comment -> comment_post_ID );
            $jam_recent_comment_post_permalink = get_permalink( $comment -> comment_post_ID );
            ?><article>
                <div class="jam-recent-comment-author vcard">
                    <?php echo get_avatar( $comment, 40 ); ?>
                    <cite class="fn">
                        <?php echo( $comment -> comment_author ); ?>
                    </cite>
                    <span class="jam-recent-comment-says">says:</span>
                    <span class="jam-recent-comment-meta"><time pubdate="pubdate" datetime="<?php echo( $comment -> comment_date_gmt ); ?>"><?php comment_date( 'M j, Y', $comment -> comment_ID ); ?></time></span>
                </div>
                <div class="jam-recent-comment-post-title">
                    <a href="<?php echo $jam_recent_comment_post_permalink; ?>" title="<?php _e( 'Read &ldquo;', 'jam-recent-comments-widget' ); echo $jam_recent_comment_post_title; _e( '&rdquo;', 'jam-recent-comments-widget' ); ?>"><?php echo $jam_recent_comment_post_title; ?></a>
                </div>
                <div class="jam-recent-comment-content">
                    <?php comment_excerpt( $comment -> comment_ID ); ?>
                </div>
            </article><?php
        endforeach;
        ?></div><?php
        echo $after_widget;
    }
    
    /**
     * Sanitize widget form values as they are saved
     * 
     * @see WP_Widget::update()
     * 
     * @param type $new_instance    Values just sent to be saved.
     * @param type $old_instance    Previously saved values from database.
     * 
     * @return array    Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['count'] = absint( $new_instance['count'] );
        
        return $instance;
    }
    
    /**
     * @see WP_Widget::form()
     * 
     * @param array $instance   Previously saved values from database
     */
    public function form( $instance ) {
        if ( isset( $instance['title'] ) ) {
            $title = $instance['title'];
        }
        else {
            $title = __( 'Recent Comments', 'jam-recent-comments-widget' );
        }
        if ( isset( $instance['count'] ) ) {
            $count = $instance['count'];
        }
        else {
            $count = 3;
        }
        ?>
        <p>
            <label for="<?php echo $this -> get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'jam-recent-comments-widget' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <p>
            <label for="<?php echo $this -> get_field_id( 'count' ); ?>"><?php _e( 'Number of Comments:', 'jam-recent-comments-widget' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" value="<?php echo esc_attr( $count ); ?>" />
        </p>
        <?php
    }
} // class JAM_Recent_Comments

// register JAM_Recent_Comments widget
add_action( 'widgets_init', create_function( '', 'register_widget( "jam_recent_comments" );' ) );

// Enable internationalization
load_plugin_textdomain( 'jam-recent-comments-widget', false, basename( dirname( __FILE__ ) ) . '/languages' );

// Embed CSS for the widget
function jam_recent_comments_styles() {
    ?><style type="text/css">
.jam-recent-comments article {
    margin: 1em 0;
    overflow: hidden;
    position: relative;
}
.jam-recent-comments img {
    display: block;
    float: left;
    margin-right: 5px;
}
.jam-recent-comment-says {
    border: 0;
    clip: rect(0 0 0 0);
    height: 1px;
    margin: -1px;
    overflow: hidden;
    padding: 0;
    position: absolute;
    width: 1px;
}
.jam-recent-comment-meta {
    display: block;
    float: right;
    text-align: right;
}
.jam-recent-comment-post-title {
    clear: both;
}
.jam-recent-comment-content {
    font-style: italic;
}
.jam-recent-comment-content:before {
    content: '\201C';
}
.jam-recent-comment-content:after {
    content: '\201D';
}
</style><?php
}
add_action( 'wp_head', 'jam_recent_comments_styles' );
?>