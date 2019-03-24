<?php

namespace App\Service;

use App\Exception\ResourceValidationException;
use App\Exception\ResourceNotFoundException;
use App\Exception\CanNotPerformThisActionException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Repository\RamRepository;
use App\Entity\Ram;

/**
 * @package App\Service
 */
class RamService
{
    /**
     * @var RamRepository
     */
    private $ramRepository;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * RamService constructor.
     * @param RamRepository $ramRepository
     * @param ValidatorInterface $validator
     */
    public function __construct(
        RamRepository $ramRepository,
        ValidatorInterface $validator
    ) {
        $this->ramRepository = $ramRepository;
        $this->validator = $validator;
    }

    /**
     * @param $server
     * @param int $page
     * @param int $limit
     * @return Ram[]
     */
    public function findAll($server, $page = 1, $limit = 10)
    {
        $limit = ($limit > 20) ? 20 : $limit;
        $offset = (($page * $limit) - $limit);

        return $this->ramRepository->findBy(['server' => $server], null, $limit, $offset);
    }

    /**
     * @param $server
     * @param $data
     * @param bool $flush
     * @return Ram
     * @throws ResourceValidationException
     */
    public function create($server, $data, $flush = true): Ram
    {
        $type = isset($data['type']) ? $data['type'] : '';
        $size = isset($data['size']) ? $data['size'] : 0;

        $ram = new Ram();
        $ram->setType($type);
        $ram->setSize($size);
        $ram->setServer($server);

        $errors = $this->validator->validate($ram);

        if (count($errors) > 0) {
            $exp = new ResourceValidationException('Ram validation Exception.');
            $exp->setFields($errors);
            $exp->setStatusCode(400);

            throw $exp;
        }

        if ($flush == true) {
            $this->ramRepository->persistAndFlush($ram);
        } else {
            $this->ramRepository->persist($ram);
        }

        return $ram;
    }

    /**
     * @param $server
     * @param $id
     * @throws CanNotPerformThisActionException
     * @throws ResourceNotFoundException
     */
    public function delete($server, $id)
    {
        $ram = $this->findOneBy($server, $id);

        $server = $ram->getServer();

        if ($server->getRams()->count() == 1) {
            $exp = new CanNotPerformThisActionException("Sorry, you can't remove this ram, Server has only one ram");
            $exp->setStatusCode(422);

            throw $exp;
        }

        $this->ramRepository->remove($ram);
    }

    /**
     * @param $server
     * @param $id
     * @return Ram
     * @throws ResourceNotFoundException
     */
    public function findOneBy($server, $id): Ram
    {
        $ram = $this->ramRepository->findOneBy(["server" => $server, "id" => $id]);

        if (!$ram) {
            $exp = new ResourceNotFoundException('ram is not found');
            $exp->setStatusCode(404);

            throw $exp;
        }

        return $ram;
    }
}
