<?php

namespace App\Controller;

use App\Entity\Images;
use App\Form\ImagesType;
use App\Repository\ImagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Route("/images")
 */
class ImagesController extends AbstractController
{
    /**
     * @Route("/", name="images_index", methods={"GET"})
     */
    public function index(ImagesRepository $imagesRepository): Response
    {
        return $this->render('images/index.html.twig', [
            'images' => $imagesRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="images_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $image = new Images();
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            return $this->redirectToRoute('images_index');
        }

        return $this->render('images/new.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="images_show", methods={"GET"})
     */
    public function show(Images $image, UploaderHelper $helper): Response
    {
        $toto = $helper->asset($image, 'imageFile');
        return $this->render('images/show.html.twig', [
            'image' => $image,
            'imageUrl' => $toto
        ]);
    }

    /**
     * @Route("/api/{id}", name="image_api", methods={"GET"})
     */
    public function getFromApi(Images $image, UploaderHelper $helper){
        $obj = new \stdClass();

        $obj->imageName = $image->getImage();
        $obj->imageUrl = $helper->asset($image, 'imageFile');


        return new JsonResponse($obj);
    }

    /**
     * @Route("/{id}/edit", name="images_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Images $image): Response
    {
        $form = $this->createForm(ImagesType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('images_index');
        }

        return $this->render('images/edit.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="images_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Images $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('images_index');
    }
}
