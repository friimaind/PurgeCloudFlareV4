<?php
/**
 * @name PurgeSingleFile
 * @description This is used to clear individual URL's from CloudFlare when they are saved
 * @PluginEvents OnDocFormSave
 * @author Massimiliano Monaro <massimiliano.monaro@gmail.com>
 */

/*
 * API Credentials
 */

$email = $modx->getOption('cloudflare.email_address');
$token = $modx->getOption('cloudflare.api_key');

/*
 * Get Zone ID
 */
 
$context = $modx->getContext($resource->getOne('Context')->key);

// Current page
$page_url = $modx->makeUrl($resource->get('id'), '', '', 'full');

// Parent
$parent_id = $resource->get('parent');
if ($parent_id != 0) {
    $parent = $modx->getObject('modResource', $parent_id);
    $parent_url = $modx->makeUrl($parent->get('id'), '', '', 'full');
} else {
    $parent_url = NULL;
}

// Homepage
$site_start_id = $context->getOption('site_start');
if ($site_start_id != 0) {
    $homepage = $modx->getObject('modResource', $site_start_id);
    $homepage_url = $modx->makeUrl($homepage->get('id'), '', '', 'full');
}

$skip = $context->getOption('cf_skip') || 0;
$http_host = str_replace("www.", "", $context->getOption('cloudflare.http_host'));

if ($skip != 1 && $page_url && $email && $token) {
    $headers = array();
    $headers[] = 'X-Auth-Email: '. $email;
    $headers[] = 'X-Auth-Key: '.$token;
    $headers[] = 'Content-Type: application/json';
    
    $ch = curl_init('https://api.cloudflare.com/client/v4/zones?name=' . $http_host . '&status=active&page=1&per_page=20&order=status&direction=desc&match=all');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $result = json_decode(curl_exec($ch), true);

    curl_close($ch);

    if ($result['success'] == 1) {
        $zone_id = $result['result'][0]['id'];
        $data = array("files" => array_filter(array($page_url, $parent_url, $homepage_url)));

        $ch = curl_init('https://api.cloudflare.com/client/v4/zones/' . $zone_id . '/purge_cache');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $result = json_decode(curl_exec($ch), true);

        if ($result['success'] == 1) {
            $modx->log(modx::LOG_LEVEL_INFO, 'File cleared from CloudFlare cache: ' . $page_url);
        } else {
            $modx->log(modx::LOG_LEVEL_ERROR, 'Cloudflare: ' . print_r($result['errors']));
        }

        curl_close($ch);
    } else {
        $modx->log(modx::LOG_LEVEL_ERROR, 'Cloudflare: ' . print_r($result['errors']));
    }
}
