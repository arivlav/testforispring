<?php

namespace App\Controller;

use App\Entity\Task;
use App\Service\TaskStatus;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


/**
  * Class PostController
  * @package App\Controller
  * @Route("/V1", name="task_api")
  */
class TaskController extends AbstractController
{
    /**
    * @param TaskRepository $taskRepository
    * @return JsonResponse
    * @Route("/tasks", name="tasks", methods={"GET"})
    */
    public function getTasks(TaskRepository $taskRepository, TaskStatus $taskStatus): JsonResponse
    {
        $data = $taskRepository->findByStatus($taskStatus::IN_PROGRESS);
        return $this->response($data);
    }

    /**
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse
     * @Route("/tasks", name="tasks_create", methods={"POST"})
     */
    public function createTask(TaskRepository $taskRepository, Request $request, ValidatorInterface $validator): JsonResponse
    {      
        $description = $request->request->get('description');
        $task = new Task();
        $task->setDescription($description);
        $task->setStatus(0);
        $errors = $validator->validate($task);
        if (count($errors) > 0) 
        {
            return $this->response($errors, JsonResponse::HTTP_NOT_FOUND);
        }
        
        $taskRepository->createUpdateTask($task);
        $data = [
            "answer" => "Task ".$task->getId()." created!",
        ];
        return $this->response($data);
    }
    
    /**
    * @param TaskRepository $taskRepository
    * @param $id
    * @return JsonResponse
    * @Route("/tasks/{id}", name="tasks_complete", requirements={"id"="\d+"}, methods={"PUT"})
    */
    public function completeTask($id, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->find($id);
        if (!$task) {
            $data = [
                "answer" => "Task ".$id." not found.",
            ];
            return $this->response($data, Response::HTTP_NOT_FOUND);
        }
        $task->setStatus(1);
        $taskRepository->createUpdateTask($task);
        $data = [
            "answer" => "Task ".$id." comleted!",
        ];
        return $this->response($data);
    }

    /**
    * @param TaskRepository $taskRepository
    * @param $id
    * @return JsonResponse
    * @Route("/tasks/{id}", name="tasks_delete", requirements={"id"="\d+"}, methods={"DELETE"})
    */
    public function deleteTask($id, TaskRepository $taskRepository): JsonResponse
    {
        $task = $taskRepository->find($id);

        if (!$task) {
            $data = [
                "answer" => "Task ".$id." not found.",
            ];
            return $this->response($data, Response::HTTP_NOT_FOUND);
        }

        $taskRepository->deleteTask($task);
        $data = [
            "answer" => "Task ".$id." deleted!",
        ];
        return $this->response($data);
    }

    /**
    * @param TaskRepository $taskRepository
    * @return JsonResponse
    * @Route("/tasks/archive", name="tasks_archive", methods={"GET"})
    */
    public function getArchiveList(TaskRepository $taskRepository, TaskStatus $taskStatus): JsonResponse
    {
        $data = $taskRepository->findByStatus($taskStatus::COMPLETED);
        return $this->response($data);
    }
    
    public function response($payload, $httpcode = JsonResponse::HTTP_OK)
    {
        return new JsonResponse($payload, $httpcode);
    }
}
