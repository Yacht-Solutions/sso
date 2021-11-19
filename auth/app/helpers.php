<?php

use Firebase\JWT\JWT as FJWT;

global $db;
$db = new PDO('sqlite:' . __DIR__ . '/database.sqlite');

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
    print_r($any);
}

function d($any)
{
    p($any);
    exit;
}

function array_map_assoc(array $array, callable $callback): array
{
    $return = [];
    foreach ($array as $k => $v)
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
        if (is_null($url))
        {
    		$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        }

        foreach (parse_url($url) as $k => $v)
        {
            switch ($k)
            {
                case 'query':
                    foreach (explode('&', $v) as $q)
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
        if (array_key_exists($k, $this->query))
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

        if ($this->scheme)
        {
            $url[] = $this->scheme . '://';
        }

        if ($this->user)
        {
            $url[] = $this->user;
        }

        if ($this->pass)
        {
            $url[] = ':' . $this->pass;
        }

        if ($this->user || $this->pass)
        {
            $url[] = '@';
        }

        if ($this->host)
        {
            $url[] = $this->host;
        }

        if ($this->port)
        {
            $url[] = ':' . $this->port;
        }

        $url[] = $this->path;

        if ($this->query)
        {
            $url[] = '?';
            $url[] = implode('&', array_map_assoc($this->query, function($a, $b) {return $a . '=' . $b;}));
        }

        if ($this->fragment)
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

class JWT
{
    private ?int $tokenId = NULL;
    private ?int $userId = NULL;

    static private string $key = '7WVQWzdclux2zF3ZCYZL';
    static private PDO $db;

    function __construct(?string $data = NULL)
    {
        if (!isset(self::$db))
        {
            global $db;
            self::$db = &$db;
        }

        # Si on ne fournit pas de $data, c'est qu'on veut récupérer le JWT enregistré en cookie ou bien en créer un nouveau
        # À utiliser dans des requêtes client <=> authServer

        if (is_null($data))
        {
            # On récupère le JWT en cookie

            if (isset($_COOKIE['jwt']))
            {
                $this->tokenId = self::decode($_COOKIE['jwt']);
            }

            # Si ça n'a pas marché, on en crée un nouveau et on l'enregistre en BDD et en cookie

            if (is_null($this->tokenId))
            {
                $sth = self::$db->prepare('INSERT INTO `token` (`userId`) VALUES (NULL)');
                $sth->execute();
                $this->tokenId = self::$db->lastInsertId();
                $this->saveCookie();
            }
        }

        # Sinon on récupère un JWT à partir d'un échange server <=> authServer

        else
        {
            $this->tokenId = self::decode($data);
        }
    }

    /**
     * Un JWT est valide s'il a bien un tokenId et si ce tokenId existe bien en BDD.
     *
     * @return boolean
     */
    public function isValid(): bool
    {
        if (is_null($this->tokenId))
        {
            return FALSE;
        }

        $sth = self::$db->prepare('SELECT * FROM `token` WHERE `id` = :id');
        $sth->bindParam('id', $this->tokenId);
        $sth->execute();

        $results = $sth->fetchAll(PDO::FETCH_CLASS);
        if(1 === count($results))
        {
            $this->userId = $results[0]->userId;
            return TRUE;
        }
        else
        {
            return FALSE;
        }
    }

    public function getTokenId(): ?int
    {
        return $this->tokenId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * Génère le JWT à partir de son payload
     *
     * @return string
     */
    public function encode(): string
    {
        return FJWT::encode(['tokenId' => $this->tokenId], self::$key, 'HS256');
    }

    /**
     * Retourne le tokenId d'un JWT s'il est valide et s'il existe, NULL sinon
     *
     * @param string $jwt
     * @return integer|null
     */
    static public function decode(string $jwt): ?int
    {
        $timestamp = time();

        $tks = explode('.', $jwt);

        if (count($tks) != 3) {
            return NULL;
        }

        list($headb64, $bodyb64, $cryptob64) = $tks;

        if (NULL === ($header = FJWT::jsonDecode(FJWT::urlsafeB64Decode($headb64)))) {
            return NULL;
        }

        if (NULL === $payload = FJWT::jsonDecode(FJWT::urlsafeB64Decode($bodyb64))) {
            return NULL;
        }

        if (!isset($payload->tokenId)) {
            return NULL;
        }

        if (FALSE === ($sig = FJWT::urlsafeB64Decode($cryptob64))) {
            return NULL;
        }

        if (empty($header->alg)) {
            return NULL;
        }

        if (empty(FJWT::$supported_algs[$header->alg])) {
            return NULL;
        }

        if (!FJWT::constantTimeEquals('HS256', $header->alg)) {
            return NULL;
        }

        if (!hash_equals(hash_hmac('SHA256', "$headb64.$bodyb64", self::$key, TRUE), $sig)) {
            return NULL;
        }

        if (isset($payload->nbf) && $payload->nbf > ($timestamp + FJWT::$leeway)) {
            return NULL;
        }

        if (isset($payload->iat) && $payload->iat > ($timestamp + FJWT::$leeway)) {
            return NULL;
        }

        if (isset($payload->exp) && ($timestamp - FJWT::$leeway) >= $payload->exp) {
            return NULL;
        }

        return $payload->tokenId;
    }

    /**
     * Enregistre le JWT dans un cookie
     *
     * @return void
     */
    public function saveCookie(): void
    {
        setcookie('jwt', $this->encode(), time() + 3600);
    }

    /**
     * Associe en base de donnéese le userId au JWT
     *
     * @param integer $userId
     * @return void
     */
    public function updateUser(int $userId): void
    {
        $sth = self::$db->prepare('UPDATE `token` SET `userId` = :userId WHERE `id` = :id');
        $sth->bindParam('id', $this->tokenId);
        $sth->bindParam('userId', $userId);
        $sth->execute();
    }

    /**
     * Dissocie en base de données le userId du JWT
     *
     * @return void
     */
    public function logout(): void
    {
        if($this->tokenId)
        {
            $sth = self::$db->prepare('UPDATE `token` SET `userId` = NULL WHERE `id` = :id');
            $sth->bindParam('id', $this->tokenId);
            $sth->execute();
        }
    }
}
