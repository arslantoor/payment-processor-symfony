<?php

namespace App\Controller\ApiController;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UtilService;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(path="/api/v1/users")
 * Class UserController
 * @package App\Controller\ApiController
 */
class UserController extends AbstractController
{

    /**
     * @Route(path="/delete-user/{id}", name="delete-user", methods={"DELETE"})
     * @SWG\Post(
     *     path="/delete-user/{id}",
     *     tags={"User"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Response(response=200, description="Request processed successfully."),
     *     @SWG\Response(response=202, description="Request accepted."),
     *     @SWG\Response(response=400, description="Bad request."),
     *     @SWG\Response(response=401, description="Unauthorized request."),
     *     @SWG\Response(response=404, description="Not found."),
     *     @SWG\Response(response=414, description="Request URI is too long."),
     *     @SWG\Response(response=422, description="Unprocessable request."),
     *     @SWG\Response(response=500, description="Internal server error.")
     * )
     * @param int $id
     * @param UtilService $utilService
     * @param UserService $userService
     * @return JsonResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function deleteAction(
        int $id,
        UtilService $utilService,
        UserService $userService
    ): JsonResponse
    {
        $user = $userService->findOneByOrNull($id);

        if (!$user instanceof User) {
            return $utilService->makeResponse(
                Response::HTTP_NOT_FOUND,
                "Error! User not found.",
                [],
                UtilService::ERROR_RESPONSE_TYPE
            );
        }

        $user = $userService->deleteUser($user);

        return $utilService->makeResponse(
            Response::HTTP_OK,
            "Successfully user deleted.",
            $user->toArray(),
            UtilService::SUCCESS_RESPONSE_TYPE
        );
    }

    /**
     * @Route(path="/create-user", name="user-create", methods={"POST"})
     * @SWG\Post(
     *     path="/create-user",
     *     tags={"User"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="body", in="body", required=true, format="application/json",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="first_name", type="string"),
     *             @SWG\Property(property="last_name", type="string"),
     *             @SWG\Property(property="email", type="string"),
     *             @SWG\Property(property="password", type="string"),
     *         )
     *     ),
     *     @SWG\Response(response=200, description="Request processed successfully."),
     *     @SWG\Response(response=202, description="Request accepted."),
     *     @SWG\Response(response=400, description="Bad request."),
     *     @SWG\Response(response=401, description="Unauthorized request."),
     *     @SWG\Response(response=404, description="Not found."),
     *     @SWG\Response(response=414, description="Request URI is too long."),
     *     @SWG\Response(response=422, description="Unprocessable request."),
     *     @SWG\Response(response=500, description="Internal server error.")
     * )
     * @param Request $request
     * @param UtilService $utilService
     * @param UserService $userService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createAction(
        Request $request,
        UtilService $utilService,
        UserService $userService
    ): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (empty($requestData)) {
            $requestData = $request->request->all();
        }

        $validRequest = UtilService::checkRequiredFieldsByRequestedData($requestData, User::CREATE_COMPANY_API_REQUIRED_FIELDS);
        if (!$validRequest) {
            return $utilService->makeResponse(Response::HTTP_BAD_REQUEST, "Some required parameters are missing.");
        }

        $userByEmail = $userService->getUserByUserName($requestData['email']);
        if ($userByEmail instanceof User) {
            return $utilService->makeResponse(
                Response::HTTP_BAD_REQUEST,
                "Error!User already exists",
                ['email' => $requestData['email']],
                UtilService::ERROR_RESPONSE_TYPE
            );
        }

        $user = $userService->createUser($requestData);

        return $utilService->makeResponse(
            Response::HTTP_OK,
            "User Successfully Created",
            $user->toArray(),
            UtilService::SUCCESS_RESPONSE_TYPE
        );
    }

    /**
     * @Route(path="/update-user/{id}", name="update-user", methods={"POST"})
     * @SWG\Post(
     *     path="/update-user/{id}",
     *     tags={"User"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="body", in="body", required=true, format="application/json",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(property="first_name", type="string"),
     *             @SWG\Property(property="last_name", type="string"),
     *             @SWG\Property(property="email", type="string"),
     *             @SWG\Property(property="password", type="string"),
     *         )
     *     ),
     *     @SWG\Response(response=200, description="Request processed successfully."),
     *     @SWG\Response(response=202, description="Request accepted."),
     *     @SWG\Response(response=400, description="Bad request."),
     *     @SWG\Response(response=401, description="Unauthorized request."),
     *     @SWG\Response(response=404, description="Not found."),
     *     @SWG\Response(response=414, description="Request URI is too long."),
     *     @SWG\Response(response=422, description="Unprocessable request."),
     *     @SWG\Response(response=500, description="Internal server error.")
     * )
     * @param int $id
     * @param Request $request
     * @param UtilService $utilService
     * @param UserService $userService
     * @return JsonResponse
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function updateAction(
        int $id,
        Request $request,
        UtilService $utilService,
        UserService $userService
    ): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        if (empty($requestData)) {
            $requestData = $request->request->all();
        }

        $user = $userService->findOneByOrNull($id);

        if (!$user instanceof User) {
            return $utilService->makeResponse(
                Response::HTTP_NOT_FOUND,
                "Error! User not found.",
                UtilService::ERROR_RESPONSE_TYPE
            );
        }

        if (isset($requestData['email'])) {
            $userByEmail = $userService->getUserByUserName($requestData['email']);
            if ($userByEmail instanceof User && $userByEmail->getId() !== (int)$id) {
                return $utilService->makeResponse(
                    Response::HTTP_BAD_REQUEST,
                    "Error! email already exists.",
                    ['email' => $userByEmail->getEmail()],
                    UtilService::ERROR_RESPONSE_TYPE
                );
            }
        }

        $user = $userService->updateUser($user, $requestData);

        return $utilService->makeResponse(
            Response::HTTP_OK,
            "User Successfully Updated",
            $user->toArray(),
            UtilService::SUCCESS_RESPONSE_TYPE
        );
    }
}
