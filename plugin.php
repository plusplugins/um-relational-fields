<?php
/**
 * Plugin Name: UM Relational Fields
 * Plugin URI:  https://plusplugins.com
 * Description: Add relationships between users, post types and taxonomies.
 * Author:      PlusPlugins
 * Version:     0.9
 * Author URI:  https://plusplugins.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PP_Fields {
	public $notice_messge = '';
	public $plugin_inactive = false;

	function __construct() {
		$this->plugin_inactive = false;

		add_action( 'init', array( $this, 'plugin_check' ), 10 );
		add_action( 'admin_notices', array( $this, 'add_notice' ), 20 );
		add_filter( 'redux/options/um_options/sections', array( $this, 'add_field_relationships_tab' ), 9005 );
		add_action( 'init', array( $this, 'init' ), 100 );
	}

	function plugin_check() {
		if ( ! class_exists( 'UM_API' ) ) {
			$this->notice_messge   = __( 'The <strong>UM Relational Fields</strong> extension requires the Ultimate Member plugin to be activated to work properly. You can download it <a href="https://wordpress.org/plugins/ultimate-member">here</a>', 'pp-contact' );
			$this->plugin_inactive = true;
		} else if ( ! version_compare( ultimatemember_version, PP_CONTACT_REQUIRES, '>=' ) ) {
			$this->notice_messge   = __( 'The <strong>UM Relational Fields</strong> extension requires a <a href="https://wordpress.org/plugins/ultimate-member">newer version</a> of Ultimate Member to work properly.', 'pp-contact' );
			$this->plugin_inactive = true;
		}
	}

	function add_notice() {
		if ( ! is_admin() || empty( $this->notice_messge ) ) {
			return;
		}

		echo '<div class="error"><p>' . $this->notice_messge . '</p></div>';
	}

	function add_field_relationships_tab( $sections ) {
		ob_start();
		?>

		<h2>Enter a relationship in the following format</h2>
		<pre>meta_key|type|slug|link</pre>
		<h4>meta_key</h4>
		<p>This is the meta key of the field generated in the UM Form Builder.<br>
			<strong>Important:</strong> The field type should be either <code>dropdown</code> or
			<code>multiselect</code>, and "relationship" should be added in the first line of the options field of the
			Form Builder.</p>
		<h4>type</h4>
		<p>This is type of item you are relating to - either <code>user</code>, <code>tax</code> or <code>post</code>.
		</p>
		<h4>slug</h4>
		<p>If your <code>type</code> is <code>user</code>, this value is the user role slug that will filter the options
			in the select area. Multiple user roles should be defined as a comma-seperated list eg.
			<code>admin,member</code>. Leave empty to add all user roles.<br>
			If your <code>type</code> is <code>tax</code>, this value is the taxonomy slug that will filter the options
			in the select area. A single taxonomy slug must be entered.<br>
			If your <code>type</code> is <code>post</code>, this value is the comma seperated list of post types slugs
			that filter the options in the select area.</p>
		<h4>link</h4>
		<p>Set this value to <code>true</code> if you want to hyperlink the users or posts in display mode on the
			profile, or <code>false</code> to disable hyperlinks. If your type is <code>user</code>, you can also use
			<code>avatar</code> here to display an avatar.</p>
		<h4>Examples</h4>
		<p><code>my_coach|user|coach|true</code></p>
		<p><code>fav_meal|post|recipes|true</code></p>
		<p><code>recommended_content|post|post,page|true</code></p>
		<p><code>friends|user||false</code></p>
		<p><code>category|tax|category|true</code></p>
		<h4>Questions?</h4>
		<p>Shoot us an email: <strong>info@plusplugins.com</strong></p>

		<?php
		$desc = ob_get_contents();
		ob_end_clean();

		$fields = array();

		$fields[] = array(
			'id'       => 'field_relationships',
			'type'     => 'multi_text',
			'default'  => array(),
			'add_text' => __( 'Add New Relationship', 'ultimatemember' ),
			'title'    => 'Field Relationships',
			'desc'     => $desc,
		);

		$sections[] = array(

			'icon'       => 'um-faicon-exchange',
			'title'      => __( 'Field Relationships', 'pp-fields' ),
			'fields'     => $fields,
			'subsection' => false,

		);

		return $sections;
	}

	function init() {
		if ( $this->plugin_inactive ) {
			return;
		}

		global $ultimatemember;

		$relationships = um_get_option( 'field_relationships' );

		if ( empty( $relationships ) ) {
			return;
		}

		foreach ( $relationships as $relationship ) {
			$args = array_map( 'trim', explode( "|", trim( $relationship ) ) );

			$key  = isset( $args[0] ) ? $args[0] : '';
			$type = isset( $args[1] ) ? $args[1] : '';
			$slug = isset( $args[2] ) ? $args[2] : '';
			$link = isset( $args[3] ) ? $args[3] : '';

			if ( ! empty( $key ) ) {
				add_action( 'um_before_form', function ( $args ) use ( $type, $key, $slug, $link ) {
					$this->profile_form( $type, $key, $slug, $link );
				}, 10, 1 );

				$this->search_form( $type, $key, $slug, $link );
				$this->filtered_value( $type, $key, $slug, $link );
			}
		}
	}

	function items( $type, $slug ) {
		$items = array();

		if ( $type == 'user' ) {
			$args = array();

			if ( ! empty( $slug ) ) {
				$roles = explode( ",", $slug );

				$args['meta_key']     = 'role';
				$args['meta_value']   = $roles;
				$args['meta_compare'] = 'IN';
			}

			$items = get_users( $args );
		}

		if ( $type == 'tax' ) {
			$items = get_terms( array( $slug ), array( 'hide_empty' => 0 ) );
		}

		if ( $type == 'post' ) {
			$post_types = array();

			if ( empty( $slug ) ) {
				$post_types[] = 'post';
			} else {
				$post_types = explode( ",", $slug );
			}

			$items = get_posts( array( "post_type" => $post_types, 'posts_per_page' => 999 ) );
		}

		return $items;
	}

	function options( $items, $type ) {
		$options = array();

		if ( $type == 'tax' ) {
			foreach ( $items as $item ) {
				$options[ $item->term_id ] = apply_filters( 'pp-fields-tax-option-field', $item->name, $item );
			}
		}

		if ( $type == 'user' ) {
			foreach ( $items as $item ) {
				$options[ $item->ID ] = apply_filters( 'pp-fields-user-option-field', $item->display_name, $item );
			}
		}

		if ( $type == 'post' ) {
			foreach ( $items as $item ) {
				$options[ $item->ID ] = apply_filters( 'pp-fields-post-option-field', $item->post_title, $item );
			}
		}

		asort( $options );

		return $options;
	}

	function search_form( $type, $key, $slug, $link ) {
		add_filter( "um_search_field_{$key}", function ( $attrs ) use ( $type, $key, $slug ) {
			$items   = $this->items( $type, $slug );
			$options = $this->options( $items, $type );

			$attrs['options'] = $options;
			$attrs['custom']  = true;

			return $attrs;
		}, 100, 1 );
	}

	function profile_form( $type, $key, $slug, $link ) {
		$profile_id = um_profile_id();

		add_filter( "um_{$key}_form_edit_field", function ( $output, $mode ) use ( $type, $key, $slug, $profile_id ) {
			$ids     = get_user_meta( $profile_id, $key, true );
			$replace = '<option value=""></option>';
			$items   = $this->items( $type, $slug );
			$options = $this->options( $items, $type );

			foreach ( $options as $k => $v ) {
				$selected = '';

				if ( is_array( $ids ) ) {
					if ( $type == 'tax' ) {
						if ( in_array( $k, $ids ) ) {
							$selected = 'selected';
						}
					} elseif ( in_array( $k, $ids ) ) {
						$selected = 'selected';
					}
				} else {
					if ( $type == 'tax' ) {
						if ( $k == $ids ) {
							$selected = 'selected';
						}
					} elseif ( $k == $ids ) {
						$selected = 'selected';
					}
				}

				$replace .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
			}

			$output = preg_replace( '%<option .*</option>%', $replace, $output );

			return $output;
		}, 10, 2 );

		add_filter( "um_{$key}_form_show_field", function ( $output, $mode ) use ( $type, $key, $slug, $link, $profile_id ) {
			$ids = get_user_meta( $profile_id, $key, true );

			if ( ! is_array( $ids ) ) {
				$ids = array( $ids );
			}

			$replace = '<div class="um-field-value">';
			$items   = array();

			if ( $type == 'user' ) {
				$items = get_users( array( 'include' => $ids ) );
			}

			if ( $type == 'tax' ) {
				$items = get_terms( array( $slug ), array( 'include' => $ids, 'hide_empty' => 0 ) );
			}

			if ( $type == 'post' ) {
				$post_types = explode( ",", $slug );
				$items      = get_posts( array(
					'include'        => $ids,
					'post_type'      => $post_types,
					'posts_per_page' => 999
				) );
			}

			$replace = '<div class="um-field-value">';

			$names = array();

			foreach ( $items as $item ) {
				if ( $type == 'user' ) {
					um_fetch_user( $item->ID );

					$result = $item->display_name;

					if ( $link == 'true' ) {
						$result = '<a href="' . um_user_profile_url() . '">' . $item->display_name . '</a>';
					} elseif ( $link == 'avatar' ) {
						$result = '<a style="display:inline-block" href="' . um_user_profile_url() . '" class="um-tip-s" title="' . um_user( 'display_name' ) . '">' . get_avatar( $item->ID, 40 ) . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-user-display-field', $result, $item );
				}

				if ( $type == 'tax' ) {
					$result = $item->name;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_term_link( $item ) . '">' . $item->name . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-tax-value-field', $result, $item );
				}

				if ( $type == 'post' ) {
					$result = $item->post_title;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_permalink( $item ) . '">' . $item->post_title . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-post-value-field', $result, $item );
				}
			}

			// return to profile user data
			um_fetch_user( $profile_id );

			if ( $link == "avatar" ) {
				$replace .= implode( " ", $names );
			} else {
				$replace .= implode( ", ", $names );
			}

			$replace .= '</div>';

			$find = '%<div class="um-field-value">([0-9,\s]+)*</div>%';

			$output = preg_replace( $find, $replace, $output );

			return $output;
		}, 10, 2 );
	}

	/**
	 * Show relation on Profile Card in Members Directory
	 *
	 * @param $type
	 * @param $key
	 * @param $slug
	 * @param $link
	 */
	function filtered_value( $type, $key, $slug, $link ) {

		add_filter( "um_profile_field_filter_hook__{$key}", function ( $value, $data ) use ( $type, $key, $slug, $link ) {

			$profile_id = um_user( 'ID' );

			$ids = get_user_meta( $profile_id, $key, true );

			if ( ! is_array( $ids ) ) {
				$ids = array( $ids );
			}

			$items = array();

			if ( $type == 'user' ) {
				$items = get_users( array( 'include' => $ids ) );
			}

			if ( $type == 'tax' ) {
				$items = get_terms( array( $slug ), array( 'include' => $ids, 'hide_empty' => 0 ) );
			}

			if ( $type == 'post' ) {
				$post_types = explode( ",", $slug );
				$items      = get_posts( array(
					'include'        => $ids,
					'post_type'      => $post_types,
					'posts_per_page' => 999
				) );
			}

			$modified_value = '<div class="um-field-value">';

			$names = array();

			foreach ( $items as $item ) {
				if ( $type == 'user' ) {
					um_fetch_user( $item->ID );

					$result = $item->display_name;

					if ( $link == 'true' ) {
						$result = '<a href="' . um_user_profile_url() . '">' . $item->display_name . '</a>';
					} elseif ( $link == 'avatar' ) {
						$result = '<a style="display:inline-block" href="' . um_user_profile_url() . '" class="um-tip-s" title="' . um_user( 'display_name' ) . '">' . get_avatar( $item->ID, 40 ) . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-user-display-field', $result, $item );
				}

				if ( $type == 'tax' ) {
					$result = $item->name;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_term_link( $item ) . '">' . $item->name . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-tax-value-field', $result, $item );
				}

				if ( $type == 'post' ) {
					$result = $item->post_title;

					if ( $link == 'true' ) {
						$result = '<a href="' . get_permalink( $item ) . '">' . $item->post_title . '</a>';
					}

					$names[] = apply_filters( 'pp-fields-post-value-field', $result, $item );
				}
			}

			// return to profile user data
			um_fetch_user( $profile_id );

			if ( $link == "avatar" ) {
				$modified_value .= implode( " ", $names );
			} else {
				$modified_value .= implode( ", ", $names );
			}

			$modified_value .= '</div>';

			return $modified_value;

		}, 10, 2 );
	}
}

new PP_Fields();
