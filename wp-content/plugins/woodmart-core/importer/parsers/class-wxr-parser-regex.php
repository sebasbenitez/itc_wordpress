<?php
/**
 * WordPress eXtended RSS file parser implementations
 *
 * @package woodmart
 * @subpackage Importer
 */

namespace WOODMART_CORE\Importer\Parser;

use WP_Error;

/**
 * WXR Parser that uses regular expressions. Fallback for installs without an XML parser.
 */
class WXR_Parser_Regex {
	/**
	 * Parsed authors keyed by author login.
	 *
	 * @var array
	 */
	public $authors = array();

	/**
	 * Parsed posts from the WXR file.
	 *
	 * @var array
	 */
	public $posts = array();

	/**
	 * Parsed categories.
	 *
	 * @var array
	 */
	public $categories = array();

	/**
	 * Parsed tags.
	 *
	 * @var array
	 */
	public $tags = array();

	/**
	 * Parsed custom taxonomy terms.
	 *
	 * @var array
	 */
	public $terms = array();

	/**
	 * Base site URL from WXR file.
	 *
	 * @var string
	 */
	public $base_url = '';

	/**
	 * Base blog URL from WXR file.
	 *
	 * @var string
	 */
	public $base_blog_url = '';

	/**
	 * Whether gzip stream functions are available.
	 *
	 * @var bool
	 */
	public $has_gzip = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->has_gzip = is_callable( 'gzopen' );
	}

	/**
	 * Parse a WXR file and return an array of parsed data.
	 *
	 * @param string $file Path to the WXR file to parse.
	 *
	 * @return array|WP_Error Parsed data from the WXR file, or a WP_Error on failure.
	 */
	public function parse( $file ) {
		$wxr_version       = false;
		$in_multiline      = false;
		$multiline_content = '';
		$multiline_tags    = array(
			'item'        => array( 'posts', array( $this, 'process_post' ) ),
			'wp:category' => array( 'categories', array( $this, 'process_category' ) ),
			'wp:tag'      => array( 'tags', array( $this, 'process_tag' ) ),
			'wp:term'     => array( 'terms', array( $this, 'process_term' ) ),
		);

		$fp = $this->fopen( $file, 'r' );
		if ( $fp ) {
			while ( ! $this->feof( $fp ) ) {
				$importline = rtrim( $this->fgets( $fp ) );

				if ( ! $wxr_version && preg_match( '|<wp:wxr_version>(\d+\.\d+)</wp:wxr_version>|', $importline, $version ) ) {
					$wxr_version = $version[1];
				}

				if ( false !== strpos( $importline, '<wp:base_site_url>' ) ) {
					preg_match( '|<wp:base_site_url>(.*?)</wp:base_site_url>|is', $importline, $url );
					$this->base_url = $url[1];
					continue;
				}

				if ( false !== strpos( $importline, '<wp:base_blog_url>' ) ) {
					preg_match( '|<wp:base_blog_url>(.*?)</wp:base_blog_url>|is', $importline, $blog_url );
					$this->base_blog_url = $blog_url[1];
					continue;
				} else {
					$this->base_blog_url = $this->base_url;
				}

				if ( false !== strpos( $importline, '<wp:author>' ) ) {
					preg_match( '|<wp:author>(.*?)</wp:author>|is', $importline, $author );
					$a                                   = $this->process_author( $author[1] );
					$this->authors[ $a['author_login'] ] = $a;
					continue;
				}

				foreach ( $multiline_tags as $tag => $handler ) {
					$open_tag_position  = strpos( $importline, "<$tag>" );
					$close_tag_position = strpos( $importline, "</$tag>" );

					// Handle multi-line tags on a singular line
					if ( preg_match( '|<' . $tag . '>(.*?)</' . $tag . '>|is', $importline, $matches ) ) {
						$this->{$handler[0]}[] = call_user_func( $handler[1], $matches[1] );
					} elseif ( false !== $open_tag_position ) {
						// Take note of any content after the opening tag
						$multiline_content = trim( substr( $importline, $open_tag_position + strlen( $tag ) + 2 ) );

						// We don't want to have this line added to `$is_multiline` below.
						$importline   = '';
						$in_multiline = $tag;
					} elseif ( false !== $close_tag_position ) {
						$in_multiline       = false;
						$multiline_content .= trim( substr( $importline, 0, $close_tag_position ) );

						$this->{$handler[0]}[] = call_user_func( $handler[1], $multiline_content );
					}
				}

				if ( $in_multiline && $importline ) {
					$multiline_content .= $importline . "\n";
				}
			}

			$this->fclose( $fp );
		}

		if ( ! $wxr_version ) {
			return new WP_Error( 'WXR_parse_error', __( 'This does not appear to be a WXR file, missing/invalid WXR version number', 'wordpress-importer' ) );
		}

		return array(
			'authors'       => $this->authors,
			'posts'         => $this->posts,
			'categories'    => $this->categories,
			'tags'          => $this->tags,
			'terms'         => $this->terms,
			'base_url'      => $this->base_url,
			'base_blog_url' => $this->base_blog_url,
			'version'       => $wxr_version,
		);
	}

	/**
	 * Extract the content of a tag from a string, handling CDATA if present.
	 *
	 * @param string $str The string to search within.
	 * @param string $tag The tag name to extract.
	 *
	 * @return string The content of the tag, or an empty string if the tag is not found.
	 */
	public function get_tag( $str, $tag ) {
		preg_match( "|<$tag.*?>(.*?)</$tag>|is", $str, $return );

		if ( isset( $return[1] ) ) {
			if ( substr( $return[1], 0, 9 ) === '<![CDATA[' ) {
				if ( strpos( $return[1], ']]]]><![CDATA[>' ) !== false ) {
					preg_match_all( '|<!\[CDATA\[(.*?)\]\]>|s', $return[1], $matches );
					$return = '';
					foreach ( $matches[1] as $match ) {
						$return .= $match;
					}
				} else {
					$return = preg_replace( '|^<!\[CDATA\[(.*)\]\]>$|s', '$1', $return[1] );
				}
			} else {
				$return = $return[1];
			}
		} else {
			$return = '';
		}
		return $return;
	}

	/**
	 * Process a category element and return an array of its data, including any term meta.
	 *
	 * @param string $c The category element as a string.
	 *
	 * @return array An associative array containing the category data and any term meta.
	 */
	public function process_category( $c ) {
		$term = array(
			'term_id'              => $this->get_tag( $c, 'wp:term_id' ),
			'cat_name'             => $this->get_tag( $c, 'wp:cat_name' ),
			'category_nicename'    => $this->get_tag( $c, 'wp:category_nicename' ),
			'category_parent'      => $this->get_tag( $c, 'wp:category_parent' ),
			'category_description' => $this->get_tag( $c, 'wp:category_description' ),
		);

		$term_meta = $this->process_meta( $c, 'wp:termmeta' );

		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	/**
	 * Process a tag element and return an array of its data, including any term meta.
	 *
	 * @param string $t The tag element as a string.
	 *
	 * @return array An associative array containing the tag data and any term meta.
	 */
	public function process_tag( $t ) {
		$term = array(
			'term_id'         => $this->get_tag( $t, 'wp:term_id' ),
			'tag_name'        => $this->get_tag( $t, 'wp:tag_name' ),
			'tag_slug'        => $this->get_tag( $t, 'wp:tag_slug' ),
			'tag_description' => $this->get_tag( $t, 'wp:tag_description' ),
		);

		$term_meta = $this->process_meta( $t, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	/**
	 * Process a term element and return an array of its data, including any term meta.
	 *
	 * @param string $t The term element as a string.
	 *
	 * @return array An associative array containing the term data and any term meta.
	 */
	public function process_term( $t ) {
		$term = array(
			'term_id'          => $this->get_tag( $t, 'wp:term_id' ),
			'term_taxonomy'    => $this->get_tag( $t, 'wp:term_taxonomy' ),
			'slug'             => $this->get_tag( $t, 'wp:term_slug' ),
			'term_parent'      => $this->get_tag( $t, 'wp:term_parent' ),
			'term_name'        => $this->get_tag( $t, 'wp:term_name' ),
			'term_description' => $this->get_tag( $t, 'wp:term_description' ),
		);

		$term_meta = $this->process_meta( $t, 'wp:termmeta' );
		if ( ! empty( $term_meta ) ) {
			$term['termmeta'] = $term_meta;
		}

		return $term;
	}

	/**
	 * Process meta elements and return an array of meta key/value pairs.
	 *
	 * @param string $str The string to search within.
	 * @param string $tag The meta tag to look for (e.g., 'wp:postmeta', 'wp:termmeta').
	 *
	 * @return array An array of associative arrays, each containing a 'key' and 'value' for the meta data.
	 */
	public function process_meta( $str, $tag ) {
		$parsed_meta = array();

		preg_match_all( "|<$tag>(.+?)</$tag>|is", $str, $meta );

		if ( ! isset( $meta[1] ) ) {
			return $parsed_meta;
		}

		foreach ( $meta[1] as $m ) {
			$parsed_meta[] = array(
				'key'   => $this->get_tag( $m, 'wp:meta_key' ),
				'value' => $this->get_tag( $m, 'wp:meta_value' ),
			);
		}

		return $parsed_meta;
	}

	/**
	 * Process an author element and return an array of its data.
	 *
	 * @param string $a The author element as a string.
	 *
	 * @return array An associative array containing the author data.
	 */
	public function process_author( $a ) {
		return array(
			'author_id'           => $this->get_tag( $a, 'wp:author_id' ),
			'author_login'        => $this->get_tag( $a, 'wp:author_login' ),
			'author_email'        => $this->get_tag( $a, 'wp:author_email' ),
			'author_display_name' => $this->get_tag( $a, 'wp:author_display_name' ),
			'author_first_name'   => $this->get_tag( $a, 'wp:author_first_name' ),
			'author_last_name'    => $this->get_tag( $a, 'wp:author_last_name' ),
		);
	}

	/**
	 * Process a post element and return an array of its data.
	 *
	 * @param string $post The post element as a string.
	 *
	 * @return array An associative array containing the post data.
	 */
	public function process_post( $post ) {
		$post_id        = $this->get_tag( $post, 'wp:post_id' );
		$post_title     = $this->get_tag( $post, 'title' );
		$post_date      = $this->get_tag( $post, 'wp:post_date' );
		$post_date_gmt  = $this->get_tag( $post, 'wp:post_date_gmt' );
		$comment_status = $this->get_tag( $post, 'wp:comment_status' );
		$ping_status    = $this->get_tag( $post, 'wp:ping_status' );
		$status         = $this->get_tag( $post, 'wp:status' );
		$post_name      = $this->get_tag( $post, 'wp:post_name' );
		$post_parent    = $this->get_tag( $post, 'wp:post_parent' );
		$menu_order     = $this->get_tag( $post, 'wp:menu_order' );
		$post_type      = $this->get_tag( $post, 'wp:post_type' );
		$post_password  = $this->get_tag( $post, 'wp:post_password' );
		$is_sticky      = $this->get_tag( $post, 'wp:is_sticky' );
		$guid           = $this->get_tag( $post, 'guid' );
		$post_author    = $this->get_tag( $post, 'dc:creator' );

		$post_excerpt = $this->get_tag( $post, 'excerpt:encoded' );
		$post_excerpt = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, 'normalize_tag' ), $post_excerpt );
		$post_excerpt = str_replace( '<br>', '<br />', $post_excerpt );
		$post_excerpt = str_replace( '<hr>', '<hr />', $post_excerpt );

		$post_content = $this->get_tag( $post, 'content:encoded' );
		$post_content = preg_replace_callback( '|<(/?[A-Z]+)|', array( &$this, 'normalize_tag' ), $post_content );
		$post_content = str_replace( '<br>', '<br />', $post_content );
		$post_content = str_replace( '<hr>', '<hr />', $post_content );

		$postdata = compact(
			'post_id',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_excerpt',
			'post_title',
			'status',
			'post_name',
			'comment_status',
			'ping_status',
			'guid',
			'post_parent',
			'menu_order',
			'post_type',
			'post_password',
			'is_sticky'
		);

		$attachment_url = $this->get_tag( $post, 'wp:attachment_url' );
		if ( $attachment_url ) {
			$postdata['attachment_url'] = $attachment_url;
		}

		preg_match_all( '|<category domain="([^"]+?)" nicename="([^"]+?)">(.+?)</category>|is', $post, $terms, PREG_SET_ORDER );
		foreach ( $terms as $t ) {
			$post_terms[] = array(
				'slug'   => $t[2],
				'domain' => $t[1],
				'name'   => str_replace( array( '<![CDATA[', ']]>' ), '', $t[3] ),
			);
		}
		if ( ! empty( $post_terms ) ) {
			$postdata['terms'] = $post_terms;
		}

		preg_match_all( '|<wp:comment>(.+?)</wp:comment>|is', $post, $comments );
		$comments = $comments[1];
		if ( $comments ) {
			foreach ( $comments as $comment ) {
				$post_comments[] = array(
					'comment_id'           => $this->get_tag( $comment, 'wp:comment_id' ),
					'comment_author'       => $this->get_tag( $comment, 'wp:comment_author' ),
					'comment_author_email' => $this->get_tag( $comment, 'wp:comment_author_email' ),
					'comment_author_IP'    => $this->get_tag( $comment, 'wp:comment_author_IP' ),
					'comment_author_url'   => $this->get_tag( $comment, 'wp:comment_author_url' ),
					'comment_date'         => $this->get_tag( $comment, 'wp:comment_date' ),
					'comment_date_gmt'     => $this->get_tag( $comment, 'wp:comment_date_gmt' ),
					'comment_content'      => $this->get_tag( $comment, 'wp:comment_content' ),
					'comment_approved'     => $this->get_tag( $comment, 'wp:comment_approved' ),
					'comment_type'         => $this->get_tag( $comment, 'wp:comment_type' ),
					'comment_parent'       => $this->get_tag( $comment, 'wp:comment_parent' ),
					'comment_user_id'      => $this->get_tag( $comment, 'wp:comment_user_id' ),
					'commentmeta'          => $this->process_meta( $comment, 'wp:commentmeta' ),
				);
			}
		}
		if ( ! empty( $post_comments ) ) {
			$postdata['comments'] = $post_comments;
		}

		$post_meta = $this->process_meta( $post, 'wp:postmeta' );
		if ( ! empty( $post_meta ) ) {
			$postdata['postmeta'] = $post_meta;
		}

		return $postdata;
	}

	/**
	 * Normalize tag names in a string by converting them to lowercase.
	 *
	 * @param array $matches The regex matches containing the tag name to normalize.
	 *
	 * @return string The normalized tag with lowercase tag name.
	 */
	public function normalize_tag( $matches ) {
		return '<' . strtolower( $matches[1] );
	}

	/**
	 * Open a file for reading, using gzip functions if available and the file is gzipped.
	 *
	 * @param string $filename The path to the file to open.
	 * @param string $mode     The mode in which to open the file (default 'r').
	 *
	 * @return resource|false A file pointer resource on success, or false on failure.
	 */
	public function fopen( $filename, $mode = 'r' ) {
		if ( $this->has_gzip ) {
			return gzopen( $filename, $mode );
		}
		return fopen( $filename, $mode ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	}

	/**
	 * Check if the end of the file has been reached, using gzip functions if available.
	 *
	 * @param resource $fp The file pointer resource to check.
	 *
	 * @return bool True if the end of the file has been reached, false otherwise.
	 */
	public function feof( $fp ) {
		if ( $this->has_gzip ) {
			return gzeof( $fp );
		}
		return feof( $fp );
	}

	/**
	 * Read a line from the file, using gzip functions if available.
	 *
	 * @param resource $fp  The file pointer resource to read from.
	 * @param int      $len The maximum number of bytes to read (default 8192).
	 *
	 * @return string|false The line read from the file, or false on failure.
	 */
	public function fgets( $fp, $len = 8192 ) {
		if ( $this->has_gzip ) {
			return gzgets( $fp, $len );
		}
		return fgets( $fp, $len );
	}

	/**
	 * Close a file, using gzip functions if available.
	 *
	 * @param resource $fp The file pointer resource to close.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function fclose( $fp ) {
		if ( $this->has_gzip ) {
			return gzclose( $fp );
		}

		return fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
	}
}
