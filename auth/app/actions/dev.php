<?php

use Firebase\JWT\JWT as FJWT;
use Firebase\JWT\Key;

$key = "7WVQWzdclux2zF3ZCYZL";
$payload = array(
    "iss" => "http://example.org",
    "aud" => "http://example.com",
    "iat" => 1356999524,
    "nbf" => 1357000000
);

/**
 * IMPORTANT:
 * You must specify supported algorithms for your application. See
 * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
 * for a list of spec-compliant algorithms.
 */
$jwt = FJWT::encode($payload, $key, 'HS256');
$decoded = FJWT::decode($jwt, new Key($key, 'HS256'));

p($jwt);
p($decoded);

$jwt = new JWT($jwt);
p($jwt);
p($jwt->encode());
