<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RamService;
use App\Service\ServerService;
use App\Controller\BaseController;
use App\Serializer\RamSerializer;

/**
 * @Route("/api")
 * @return JsonResponse
 */
class RamsController extends BaseController
{

    /**
     * @var RamService
     */
    private $ramService;

    /**
     * @var ServerService
     */
    private $serverService;

    /**
     * RamsController constructor.
     * @param RamService $ramService
     * @param ServerService $serverService
     */
    public function __construct(RamService $ramService, ServerService $serverService)
    {
        $this->ramService = $ramService;
        $this->serverService = $serverService;
    }

    /**
     * @Route("/servers/{id}/rams", methods={"GET"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function index($id, Request $request)
    {
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', 10);
        try {
            $server = $this->serverService->find($id);
            $rams = $this->ramService->findAll($server, $page, $perPage);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }

        if (count($rams) == 0) {
            return JsonResponse::create([], JsonResponse::HTTP_NO_CONTENT);
        }

        $serializedRams = [];

        foreach ($rams as $server) {
            $ramSerializer = new RamSerializer($server);
            $serializedRams[] = $ramSerializer->serialize();
        }

        return JsonResponse::create($serializedRams, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/servers/{id}/rams", methods={"POST"})
     * @param $id
     * @param Request $request
     * @return JsonResponse
     */
    public function create($id, Request $request)
    {
        $keysValues = [
            'type' => 'type',
            'size' => 'size',
        ];

        $data = $this->getRequestData($request, $keysValues);

        try {
            $server = $this->serverService->find($id);
            $ram = $this->ramService->create($server, $data);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }

        $ramSerializer = new RamSerializer($ram);

        return JsonResponse::create($ramSerializer->serialize(), JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/servers/{server_id}/rams/{id}", methods={"GET"})
     * @param $server_id
     * @param $id
     * @return JsonResponse
     */
    public function find($server_id, $id)
    {
        try {
            $server = $this->serverService->find($server_id);
            $ram = $this->ramService->findOneBy($server, $id);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }

        $ramSerializer = new RamSerializer($ram);

        return JsonResponse::create($ramSerializer->serialize(), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/servers/{server_id}/rams/{id}", methods={"DELETE"})
     * @param $server_id
     * @param $id
     * @return JsonResponse
     */
    public function delete($server_id, $id)
    {
        try {
            $server = $this->serverService->find($server_id);
            $this->ramService->delete($server, $id);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }

        return JsonResponse::create([], JsonResponse::HTTP_OK);
    }
}
