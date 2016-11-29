<?php

namespace IdNet\Middleware;


use Firebase\JWT\JWT;
use Interop\Http\Factory\ResponseFactoryInterface;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class JwtMiddleware implements ServerMiddlewareInterface
{
    /** @var string */
    protected $key;

    /** @var string[] */
    protected $algorithms;

    /** @var string[] */
    protected $publicPaths;

    /** @var ResponseFactoryInterface */
    protected $responseFactory;

    /** @var string */
    protected $attribute;

    public function __construct($options = [], ResponseFactoryInterface $responseFactory)
    {
        $this->key = $options['key'] ?? '';
        $this->algorithms = $options['algorithms'] ?? ['HS512'];
        $this->publicPaths = $options['public'] ?? [];
        $this->attribute = $options['attribute'] ?? 'jwt-token';
        $this->responseFactory = $responseFactory;
    }

    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        if (!$this->shouldAuthenticate($request)) {
            return $delegate->process($request);
        }

        $token = $this->getTokenFromRequest($request);
        if (empty($token)) {
            return $this->responseFactory->createResponse(401);
        }

        $decoded = $this->decodeToken($token);
        if ($decoded === false) {
            return $this->responseFactory->createResponse(401);
        }

        // TODO: more descriptive 401 errors
        // TODO: callback for further token processing

        $request = $request->withAttribute($this->attribute, $decoded);

        return $delegate->process($request);
    }

    protected function decodeToken($token)
    {
        try {
            return JWT::decode($token, $this->key, $this->algorithms);
        }
        catch (\Exception $exception) {
            return false;
        }
    }

    protected function shouldAuthenticate(ServerRequestInterface $request)
    {
        return !in_array($request->getUri()->getPath(),
            $this->publicPaths);
    }

    protected function getTokenFromRequest(ServerRequestInterface $request)
    {
        $headers = $request->getHeader('Authorization');
        if (preg_match('/Bearer\s+(.*)$/i', $headers[0] ?? "", $matches))
        {
            return $matches[1];
        }
        return '';
    }
}
