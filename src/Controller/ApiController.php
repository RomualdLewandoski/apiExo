<?php

namespace App\Controller;

use App\Entity\Todo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/listAll", name="list_all")
     */
    public function listAll(): JsonResponse
    {
        $obj = new \stdClass();
        $em = $this->getDoctrine()->getManager();
        $todoRepo = $em->getRepository(Todo::class);
        $todoLists = $todoRepo->findAll();
        //$obj->todos = $todoLists;
        $arr = [];
        foreach ($todoLists as $todo) {
            $temp = new \stdClass();
            $temp->id = $todo->getId();
            $temp->action = $todo->getAction();
            $temp->isDone = $todo->getIsDone();
            array_push($arr, $temp);
        }
        $obj->todos = $arr;
        /*
         * {
         *  todos: [
         *      {
         *          id: 1,
         *          action: "Allumez le pc",
         *          isDone: false
         *      },{
         *          id : 2 ....
         *      }
         *     ]
         */
        return new JsonResponse($obj);
    }

    /**
     * @Route("/api/finish/{id}", name="finish_todo")
     */
    public function finishTodo(Todo $todo)
    {
        $obj = new \stdClass();

        if ($todo == null) {
            $obj->status = 404;
        } else {
            $obj->status = 200;
            if ($todo->getIsDone()) {
                $todo->setIsDone(false);
            } else {
                $todo->setIsDone(true);
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return new JsonResponse($obj);
    }

    /**
     * @Route("/api/new/", name="new_todo", methods={"POST"})
     * @param Request $request
     */
    public function addTodo(Request $request)
    {
        $obj = new \stdClass();
        $action = $request->request->get('action');
        if ($action == null || $action == "") {
            $obj->status = 500;
            $obj->error = "Champs manquants";
        } else {
            $todo = new Todo();
            $todo->setAction($action);
            $todo->setIsDone(false);
            $em = $this->getDoctrine()->getManager();
            $em->persist($todo);
            $em->flush();
            $obj->status = 200;
            $obj->id = $todo->getId();
        }

        return new JsonResponse($obj);
    }


    /**
     * @Route("/api/delete/{id}", name="delete_todo")
     * @param Todo $todo
     */
    public function removeTodo(Todo $todo)
    {
        $obj = new \stdClass();

        if ($todo == null) {
            $obj->status = 404;
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->remove($todo);
            $em->flush();
            $obj->status = 200;
        }

        return new JsonResponse($obj);
    }
}
