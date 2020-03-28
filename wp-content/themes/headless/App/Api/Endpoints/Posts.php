<?php

include_once __DIR__ . "/../Interfaces/IApi.php";
include_once __DIR__ . "/../../Builders/Response/ResponseBuilder.php";
include_once __DIR__ . "/../../Helpers/DataHelper.php";

/**
 * All related posts endpoints
 * 
 * @package Posts
 * @since 1.0
 */
class Posts implements IApi
{
    public $className = __CLASS__;

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_endpoint']);
    }

    public function register_endpoint()
    {
        register_rest_route('custom/v1', '/post_meta', array(
            'methods' => 'POST',
            'callback' => [$this, 'get_posts_meta']
        ));
        
        register_rest_route('custom/v1', '/post_highlights', array(
            'methods' => 'POST',
            'callback' => [$this, 'post_highlights']
        ));

        // comments
        register_rest_route('custom/v1', '/post_comments/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => [$this, 'post_comments']
        ));
        register_rest_route('custom/v1', '/post_comments', array(
            'methods' => 'POST',
            'callback' => [$this, 'insert_comment']
        ));

        
    }

    /**
     * Get posts meta
     * 
     * @since 1.0
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function get_posts_meta(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $id = $request->get_param('id');
            if (!$id) throw new \Exception("Id param is required", 401);

            if (is_array($id)) {
                $postsMeta = [];
                foreach ($id as $postId) {
                    $postsMeta[] = array_merge(
                        ['postId' => $postId],
                        get_post_meta($postId)
                    );
                }
                return ResponseBuilder::success($postsMeta);
            } else {
                return ResponseBuilder::success(get_post_meta($id));
            }
        } catch (\Exception $ex) {
            return ResponseBuilder::error(
                'OAn error occurred, please try again later.',
                $ex->getMessage(),
                500,
                $ex
            );
        }
    }

    // Final
    /**
     * Get posts order by most viewed
     * 
     * @since 1.0
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */


    /**
     * Get posts order by most viewed
     * 
     * @since 1.0
     * 
     * @param \WP_REST_Request $request
     * @return \WP_REST_Response
     */
    public function post_highlights(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $limit = $request->get_param('limit');
            $categoriesId = $request->get_param('categories');

            if (!$limit) throw new \Exception("Limit param is required", 401);

            $args = [
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => $limit,
                'orderby' => 'rand'
            ];

            if ($categoriesId) $args['category__and'] = $categoriesId;

            $query = new WP_Query($args);

            $ret = [];
            $count = 0;

            while ($query->have_posts()) {
                $query->the_post();
                $post = get_post();

                $idPost = $post->ID;

                $categories = get_the_category($idPost);

                $ret[$count]["id"] = $idPost;
                $ret[$count]["_embedded"]["wp:featuredmedia"][0]['source_url'] = wp_get_attachment_url(get_post_thumbnail_id($idPost));
                $ret[$count]["title"]['rendered'] = $post->post_title;
                $ret[$count]["_embedded"]["author"][0]["name"] = get_the_author_meta('display_name', $post->post_author);
                $ret[$count]["_embedded"]["wp:term"][0][0]["name"] = DataHelper::getFatherCategory($categories);
                $ret[$count]["slug"] = $post->post_name;
                $ret[$count]["categories"] = wp_get_post_categories($idPost);
                $ret[$count]["date"] = $post->post_date;
                $ret[$count]['content']['rendered'] = $post->post_content;
                $ret[$count]['excerpt']['rendered'] = get_the_excerpt($post);
                $count++;
            }

            if (count($ret) > 0) {
                return ResponseBuilder::success($ret);
            } else {
                return ResponseBuilder::success(['error' => 'You have no posts.']);
            }
        } catch (\Exception $ex) {
            return ResponseBuilder::error(
                'An error occurred, please try again later.',
                $ex->getMessage(),
                500,
                $ex
            );
        }
    }

    public function post_comments(\WP_REST_Request $request): \WP_REST_Response
    {
        try {
            $limit = 10;//$request->get_param('limit');
            $postId = $request['id'];

            if (!$limit) throw new \Exception("Limit param is required", 401);

            $args = $postId ? [
                'post_id' => $postId,
                'order'       => 'ASC'
            ] : [];

            $comments = get_comments($args);
            
            $ret = [];
            foreach ( $comments as $comment ) :
                $parentId = $comment->comment_parent;

                $commentId = $comment->comment_ID;

                $tempArray = [];
                $tempArray['id'] = $commentId;
                $tempArray['author'] = $comment->comment_author;
                $tempArray['authorEmail'] = $comment->comment_author_email;
                $tempArray['date'] = $comment->comment_date;
                $tempArray['content'] = $comment->comment_content;
                $tempArray['parentId'] = $parentId;
                $tempArray['avatarUrl'] = get_avatar_url($comment->comment_author_email);
                
                if ($parentId == 0) {
                    $tempArray['children'] = [];
                    $ret[$commentId] = $tempArray;
                } 
                else {
                    if ($ret[$parentId]) {
                        $ret[$parentId]['children'][] = $tempArray;
                    }
                }
 
            endforeach;

            $arrayFinal = [];
            foreach ( $ret as $item ) :
                $arrayFinal[] = $item;
            endforeach;

            return ResponseBuilder::success($arrayFinal);

        } catch (\Exception $ex) {
            return ResponseBuilder::error(
                'An error occurred, please try again later.',
                $ex->getMessage(),
                500,
                $ex
            );
        }
    }


    public function insert_comment(\WP_REST_Request $request): \WP_REST_Response
    {

        $data = $request->get_param('data');

        $comment_data = array( 
            'comment_post_ID' => $data['postId'], 
            'author' => $data['author'], 
            'email' => $data['email'], 
            'url' => '', 
            'comment' => $data['comment'], 
            'comment_parent' => $data['commentParent']
        ); 
  
        $result = wp_handle_comment_submission($comment_data); 

        return ResponseBuilder::success($result);

    }
    
    // end 
}
