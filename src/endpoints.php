<?php
/**
 * Defines a set of external endpoints that can be used to communicate
 * directly with the plugin
 */

require MEMBERFUL_DIR . '/src/endpoints/auth.php';
require MEMBERFUL_DIR . '/src/endpoints/check_test_cookie.php';
require MEMBERFUL_DIR . '/src/endpoints/debug.php';
require MEMBERFUL_DIR . '/src/endpoints/set_test_cookie.php';
require MEMBERFUL_DIR . '/src/endpoints/webhook.php';

add_action('wp_loaded', 'memberful_wp_endpoint_filter');

/**
 * Listens to all requests and checks to see if the user is trying to access one of
 * the endpoints.
 *
 * If the user is, then the request is dispatched to that endpoint
 */
function memberful_wp_endpoint_filter() {
    if ($endpoint = memberful_wp_endpoint_for_request()) {
        if (! $endpoint->verify_request($_SERVER['REQUEST_METHOD']))
            die('Invalid request');

        header('Cache-Control: private');
        $endpoint->process($_REQUEST, $_SERVER);
        exit();
    }
}

/**
 * Extracts the endpoint (if any) that the user is trying to access
 *
 * @return Memerful_WP_Endpoint
 */
function memberful_wp_endpoint_for_request() {
    $endpoint = NULL;

    if (! empty($_GET['memberful_endpoint'])) {
        switch(strtolower($_GET['memberful_endpoint'])) {
        case 'auth':
            $endpoint = new Memerful_WP_Endpoint_Auth;
            break;
        case 'set_test_cookie':
            $endpoint = new Memerful_WP_Endpoint_Set_Test_Cookie;
            break;
        case 'check_test_cookie':
            $endpoint = new Memerful_WP_Endpoint_Check_Test_Cookie;
            break;
        case 'webhook':
            $endpoint = new Memerful_WP_Endpoint_Webhook;
            break;
        case 'debug':
            $endpoint = new Memerful_WP_Endpoint_Debug;
            break;
        }
    }

    return $endpoint;
}

interface Memerful_WP_Endpoint {
    /**
     * Allow the endpoint to process the request
     */
    public function process(array $request_params, array $server_params);

    /**
     * Checks if the request method is acceptable for this endpoint
     *
     * If false is returned the request should be cancelled
     *
     * @return boolean True if request method is acceptable, else false
     */
    public function verify_request($request_method);
}
