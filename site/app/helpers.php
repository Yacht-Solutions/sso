<?php

function err404()
{
    header('HTTP/1.0 404 Not Found');
    echo json_encode([
        'success' => FALSE,
        'err_type' => 404,
    ]);
    exit;
}

function p($any)
{
    echo '<pre>';
    print_r($any);
    echo '</pre>';
}

function d($any)
{
    p($any);
    exit;
}

function array_map_assoc(array $array, callable $callback): array
{
    $return = [];
    foreach($array as $k => $v)
    {
        $return[] = $callback($k, $v);
    }
    return $return;
}

class Url
{
    private ?string $scheme = NULL;
    private ?string $user = NULL;
    private ?string $pass = NULL;
    private ?string $host = NULL;
    private ?int $port = NULL;
    private ?string $path = NULL;
    private array $query = [];
    private ?string $fragment = NULL;

    function __construct(?string $url = NULL)
    {
        if(is_null($url))
        {
    		$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        foreach(parse_url($url) as $k => $v)
        {
            switch($k)
            {
                case 'query':
                    foreach(explode('&', $v) as $q)
                    {
                        list($a, $b) = explode('=', $q);
                        $this->query[$a] = $b;
                    }
                    break;

                default:
                    $this->$k = $v;
                    break;
            }
        }
    }

    function setQuery(string $k, string $v): self
    {
        $this->query[$k] = $v;
        return $this;
    }

    function deleteQuery(string $k): self
    {
        if(array_key_exists($k, $this->query))
        {
            unset($this->query[$k]);
        }
        return $this;
    }

    function redirect(): void
    {
        header('Location: ' . $this);
        exit;
    }

    function get()
    {
        $url = [];
    
        if($this->scheme)
        {
            $url[] = $this->scheme . '://';
        }
    
        if($this->user)
        {
            $url[] = $this->user;
        }
    
        if($this->pass)
        {
            $url[] = ':' . $this->pass;
        }
    
        if($this->user || $this->pass)
        {
            $url[] = '@';
        }
    
        if($this->host)
        {
            $url[] = $this->host;
        }
    
        if($this->port)
        {
            $url[] = ':' . $this->port;
        }
    
        $url[] = $this->path;
    
        if($this->query)
        {
            $url[] = '?';
            $url[] = implode('&', array_map_assoc($this->query, function($a, $b) {return $a . '=' . $b;}));
        }
    
        if($this->fragment)
        {
            $url[] = '#' . $this->fragment;
        }
    
        return implode('', $url);
    }

    function __toString()
    {
        return $this->get();
    }
}