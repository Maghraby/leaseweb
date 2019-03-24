<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ServerService;
use App\Controller\BaseController;
use Symfony\Component\Security\Core\Security;
use App\Serializer\ServerSerializer;

/**
 * @Route("/api")
 * @return JsonResponse
 */
class ServersController extends BaseController
{

    /**
     * @var ServerService
     */
    private $serverService;

    /**
     * ServersController constructor.
     * @param ServerService $serverService
     */
    public function __construct(ServerService $serverService)
    {
        $this->serverService = $serverService;
    }

    /**
     * @Route("/servers", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->query->getInt('page', 1);
        $perPage = $request->query->getInt('per_page', 10);
        $servers = $this->serverService->findAll($page, $perPage);

        if (count($servers) == 0) {
            return JsonResponse::create([], JsonResponse::HTTP_NO_CONTENT);
        }

        $serializedServers = [];

        foreach ($servers as $server) {
            $ServerSerializer = new ServerSerializer($server);
            $serializedServers[] = $ServerSerializer->serialize();
        }

        return JsonResponse::create($serializedServers, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/servers", methods={"POST"})
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function create(Request $request)
    {
        $keysValues = [
            'name' => 'name',
            'assetId' => 'asset_id',
            'brand' => 'brand',
            'price' => 'price',
            'rams' => 'rams',
        ];

        $data = $this->getRequestData($request, $keysValues);

        try {
            $server = $this->serverService->create($data);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }

        $serverSerializer = new ServerSerializer($server);

        return JsonResponse::create($serverSerializer->serialize(), JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/servers/{id}", methods={"GET"})
     * @param $id
     * @return Response|JsonResponse
     */
    public function find($id)
    {
        try {
            $server = $this->serverService->find($id);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }

        $serverSerializer = new ServerSerializer($server);

        return JsonResponse::create($serverSerializer->serialize(), JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/servers/{id}", methods={"DELETE"})
     * @param $id
     * @return Response|JsonResponse
     */
    public function delete($id)
    {
        try {
            $this->serverService->delete($id);
        } catch (\Exception $exception) {
            return $this->errorResponse($exception);
        }

        return JsonResponse::create([], JsonResponse::HTTP_OK);
    }
}
