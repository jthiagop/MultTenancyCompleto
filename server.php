<?php

$publicPath = getcwd();

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

// Usa is_file() em vez de file_exists() para que diretórios com mesmo nome
// que rotas do Laravel (ex: /app/financeiro) não causem 404.
if ($uri !== '/' && is_file($publicPath.$uri)) {
    return false;
}

require_once $publicPath.'/index.php';
