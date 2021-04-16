<?php

namespace App\Controller\ApiController;

use App\Service\UserService;
use App\Service\UtilService;
use OAuth2\OAuth2;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\OAuthServerBundle\Controller\TokenController as BaseController;

/**
 * Class SecurityController
 * @package App\Controller\ApiController
 */
class SecurityController extends BaseController
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var UtilService
     */
    private $utilService;

    /**
     * SecurityController constructor.
     * @param OAuth2 $server
     * @param UserService $userService
     * @param UtilService $utilService
     */
    public function __construct(OAuth2 $server, UserService $userService, UtilService $utilService)
    {
        $this->userService = $userService;
        $this->utilService = $utilService;
        parent::__construct($server);
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function tokenAction(Request $request)
    {
        $requestData = json_decode($request->getContent(), true);
        if (empty($requestData)) {
            $requestData = $request->request->all();
        }
        try {
            $response = $this->server->grantAccessToken(new Request($requestData));
        } catch (\Exception $exception) {
            return $this->utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Invalid Credentials");
        }

        $data = ['token' => json_decode($response->getContent(), true)];
        if ($requestData['grant_type'] === OAuth2::GRANT_TYPE_USER_CREDENTIALS) {
            $user = $this->userService->getUserByUserName($requestData['username']);
            $data['user'] = $user->toArray();
        }
        if ($user->getIsDeleted()) {
            return $this->utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Your account is deactivated, please contact Support for further details, thank you.");
        }

        return $this->utilService->makeResponse(Response::HTTP_OK, "Login Successful", $data, UtilService::SUCCESS_RESPONSE_TYPE);
    }
}
