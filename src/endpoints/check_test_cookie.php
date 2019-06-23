<?php

namespace RedRockSubscriptions;

class Memerful_WP_Endpoint_Check_Test_Cookie implements Memerful_WP_Endpoint {
    public function verify_request($request_method) {
        return $request_method === 'GET';
    }

    public function process(array $request_params, array $server_params) {
        if (isset($_COOKIE['memberful_cookie_test'])) {
            Reporter::report("Cookies test passed! Everything should work as expected.", "updated");
        } else {
            Reporter::report("Cookies test failed! Memberful WP will be not able to sign in users to WordPress. Please contact Memberful support.", "error");
        }

        wp_redirect(memberful_wp_plugin_cookies_test_url());
    }
}
