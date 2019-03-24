<?php

namespace App\Service;

use App\Exception\ResourceValidationException;
use App\Exception\ResourceNotFoundException;
use App\Exception\CanNotPerformThisActionException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolation;
use App\Repository\ServerRepository;
use App\Service\RamService;
use App\Entity\Server;

/**
 * @package App\Service
 */
class ServerService
{
    /**
     * @var ServerRepository
     */
    private $serverRepository;

    /**
     * @var \App\Service\RamService
     */
    private $ramService;

    /**
     * @var ValidatorInterface
     */
    private $validator;


    /**
     * ServerService constructor.
     * @param ServerRepository $serverRepository
     * @param \App\Service\RamService $ramService
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ServerRepository $serverRepository,
        RamService $ramService,
        ValidatorInterface $validator
    ) {
        $this->serverRepository = $serverRepository;
        $this->ramService = $ramService;
        $this->validator = $validator;
    }

    /**
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function findAll($page = 1, $limit = 10)
    {
        $limit = ($limit > 20) ? 20 : $limit;
        $offset = (($page * $limit) - $limit);

        return $this->serverRepository->findBy([], null, $limit, $offset);
    }

    /**
     * @param $data
     * @return Server
     * @throws ResourceValidationException
     */
    public function create($data): Server
    {
        $server = new Server();
        $server->setName((string)$data['name']);
        $server->setBrand((string)$data['brand']);
        $server->setAssetId((integer)$data['assetId']);
        $server->setPrice((float)$data['price']);

        $serverErrors = $this->validator->validate($server);

        if ($data['rams'] == NULL || count($data['rams']) == 0) {
            $error = new ConstraintViolation('Should have at least one', '', [], '', 'rams', '');
            $serverErrors->add($error);
        }

        $this->serverRepository->persist($server);

        $ramsErrors = [];
        $rams = [];

        foreach ((array)$data['rams'] as $key => $ram) {
            try {
                $rams[] = $this->ramService->create($server, $ram, false);
            } catch (\Exception $exception) {
                $ramsErrors[$key] = $exception->getFields();
            }
        }

        if (count($serverErrors) > 0 || count($ramsErrors) > 0) {
            $exp = new ResourceValidationException('Server validation Exception.');
            $fields = $this->prepareErrors($serverErrors, $ramsErrors);
            $exp->setFields($fields);
            $exp->setStatusCode(400);

            throw $exp;
        }

        $server->addRams($rams);
        $this->serverRepository->flush();

        return $server;
    }

    /**
     * @param $id
     * @throws ResourceNotFoundException
     */
    public function delete($id)
    {
        $server = $this->find($id);
        $this->serverRepository->remove($server);
    }

    /**
     * @param $id
     * @return Server
     * @throws ResourceNotFoundException
     */
    public function find($id): Server
    {
        $server = $this->serverRepository->find($id);

        if (!$server) {
            $exp = new ResourceNotFoundException('Server is not found');
            $exp->setStatusCode(404);

            throw $exp;
        }

        return $server;
    }


    /**
     * @param $serverErrors
     * @param $ramsErrors
     * @return array
     */
    private function prepareErrors($serverErrors, $ramsErrors)
    {
        $fields = [];

        foreach ($serverErrors as $key => $error) {
            $fields[] = [
                'name' => $error->getPropertyPath(),
                'message' => $error->getMessage(),
                'extra' => [],
            ];
        }

        if (count($ramsErrors) == 0) {
            return $fields;
        }

        $field = [
            'name' => 'rams',
            'message' => 'validation error',
        ];

        foreach ($ramsErrors as $key => $errors) {
            foreach ($errors as $key2 => $error) {
                $field['extra'][$key][$key2] = [
                    'name' => $error->getPropertyPath(),
                    'message' => $error->getMessage(),
                ];
            }
        }


        $fields[] = $field;

        return $fields;
    }
}
