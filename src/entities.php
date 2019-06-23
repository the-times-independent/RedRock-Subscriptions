<?php

function memberful_download($slug) {
    $downloads = memberful_downloads();
    $id       = memberful_wp_extract_id_from_slug($slug);

    return empty($downloads[$id]) ? NULL : $downloads[$id];
}

function memberful_subscription_plan($slug) {
    $plans = memberful_subscription_plans();
    $id    = memberful_wp_extract_id_from_slug($slug);

    return empty($plans[$id]) ? NULL : $plans[$id];
}

function memberful_downloads() {
    return get_option('memberful_downloads', array());
}

function memberful_subscription_plans() {
    return get_option('memberful_subscriptions', array());
}

function memberful_wp_sync_downloads() {
    $url = memberful_admin_downloads_url(MEMBERFUL_JSON);

    return memberful_wp_update_entities('memberful_downloads', $url);
}

function memberful_wp_sync_subscription_plans() {
    $url = memberful_admin_subscription_plans_url(MEMBERFUL_JSON);

    return memberful_wp_update_entities('memberful_subscriptions', $url);
}

function memberful_wp_update_entities($type, $url) {
    $entities = memberful_wp_fetch_entities($url);

    if (is_wp_error($entities)) {
        return $entities;
    }

    return update_option($type, $entities);
}

function memberful_wp_fetch_entities($url) {
    $full_url = add_query_arg('auth_token', get_option('memberful_api_key'), $url);

    $response = wp_remote_get($full_url, array('sslverify' => Plugin::defaultInstance()->shouldSSLVerify()));

    $response_code = (int) wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if (is_wp_error($response)) {
        return new WP_Error('memberful_sync_request_error', "We couldn't connect to Memberful, please email info@memberful.com ({$response->get_error_message()}, {$url})");
    }

    if ($response['response']['code'] != 200 OR ! isset($response['body'])) {
        error_log(print_r($response, true));
        return new WP_Error('memberful_sync_fail', "Couldn't retrieve list of entities from Memberful.");
    }

    $raw_entity = json_decode($response['body']);
    $entities   = array();

    foreach ($raw_entity as $entity) {
        $entities[$entity->id] = memberful_wp_format_entity($entity);
    }

    return $entities;
}

function memberful_wp_format_entity($entity) {
    $payload = array(
        'id'          => $entity->id,
        'name'        => $entity->name,
        'slug'        => $entity->slug,
        'for_sale'    => $entity->for_sale,
        'price'       => $entity->price,
 );

    if (isset($entity->description)) {
        $payload['description'] = isset($entity->description) ? $entity->description : '';
    }

    return $payload;
}
