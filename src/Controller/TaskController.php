<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;

class TaskController extends AbstractController
{
    /**
     * @Route("api/get_task_list", name="getList", methods={"GET"})
     */
    public function getList(SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        // Получаем список невыполненных задач status=0
        $task = $repository->findBy([
            'status' => 0
        ]);
        $json = $serializer->serialize($task, 'json');
        $response = new Response(
            $json,
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
                'charset' => 'utf8'
            ] 
        );
        return $response;
    }

    /**
     * @Route("api/new_task", name="create", methods={"POST"})
     */
    public function createTask(ValidatorInterface $validator): Response
    {   
        $request = Request::createFromGlobals();
        $description = $request->request->get('description');
        $task = new Task();
        $task->setDescription($description);
        $task->setStatus(0);
        
        $errors = $validator->validate($task);

        if (count($errors) > 0) 
        {
            $errorsString = (string) $errors;
            return new Response (
                $errorsString, 
                Response::HTTP_NOT_FOUND,
                [
                    'content-type' => 'application/json',
                    'charset' => 'utf8'
                ] 
            );
        }
        
        $entityManager = $this->getDoctrine()->getManager();
        
        $entityManager->persist($task);
        $entityManager->flush();
        return new Response(
            '{"answer": "Task '.$task->getId().' created!"}', 
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
                'charset' => 'utf8'
            ]
        );
    }
    
    /**
     * @Route("api/complete_task/{id}", name="completeTask", requirements={"id"="\d+"}, methods={"PUT"})
     */
    public function completeTask($id, ValidatorInterface $validator)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return new Response (
                '{"answer": "Task '.$id.' not found."}', 
                Response::HTTP_NOT_FOUND,
                [
                    'content-type' => 'application/json',
                    'charset' => 'utf8'
                ] 
            );
        }
        $task->setStatus(1);
        $entityManager->flush();
        
        return new Response(
            '{"answer": "Task '.$id.' comleted!"}', 
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
                'charset' => 'utf8'
            ]
        );
    }

    /**
     * @Route("api/delete_task/{id}", name="deleteTask", requirements={"id"="\d+"}, methods={"DELETE"})
     */
    public function deleteTask($id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);

        if (!$task) {
            return new Response (
                '{"answer": "Task '.$id.' not found."}', 
                Response::HTTP_NOT_FOUND,
                [
                    'content-type' => 'application/json',
                    'charset' => 'utf8'
                ] 
            );
        }
        $entityManager->remove($task);
        $entityManager->flush();
        
        return new Response(
            '{"answer": "Task '.$id.' deleted!"}', 
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
                'charset' => 'utf8'
            ]
        );
    }

    /**
     * @Route("api/get_archive_task_list", name="getArchiveList", methods={"GET"})
     */
    public function getArchiveList(SerializerInterface $serializer): Response
    {
        $repository = $this->getDoctrine()->getRepository(Task::class);
        // Получаем список выполненных задач status=1
        $task = $repository->findBy([
            'status' => 1
        ]);
        $json = $serializer->serialize($task, 'json');
        $response = new Response(
            $json,
            Response::HTTP_OK,
            [
                'content-type' => 'application/json',
                'charset' => 'utf8'
            ] 
        );
        return $response;
    }
}
