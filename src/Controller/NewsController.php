<?php

namespace App\Controller;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/news")
 */
class NewsController extends AbstractController
{
    /**
     * @Route("/", name="news_index", methods={"GET"})
     */
    public function index(NewsRepository $newsRepository): Response
    {
        return $this->render('news/index.html.twig', [
            'news' => $newsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="news_new", methods={"GET","POST"})
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $file = $form['image']->getData();
            $fileName = $fileUploader->upload($file);
            $news->setImage($fileName);

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('news_index');
        }

        return $this->render('news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="news_show", methods={"GET"})
     */
    public function show(News $news): Response
    {
        return $this->render('news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="news_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, News $news, FileUploader $fileUploader): Response
    {

        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();


            $fileName = $fileUploader->upload($file);
            $news->setImage($fileName);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('news_index', [
                'id' => $news->getId(),
            ]);
        }

        return $this->render('news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/fast-edit", name="news_edit_fast", methods={"GET", "POST"})
     */
    public function fastEdit(Request $request, News $news): Response
    {

        $form = $this->createForm(NewsType::class, $news, ['edit_type' => 'fast'] );
        $form->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid()) {

                $this->getDoctrine()->getManager()->flush();

            }

        return $this->render('news/_formFast.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);



    }

    /**
     * @Route("/{id}/update", name="news_update", methods={"GET", "POST"})
     */
    public function update(Request $request, News $news) : Response
    {


            try {
                $params = $request->request->all();
                $param  = $params['news'];

                    if (array_key_exists('enabled', $param)){
                        $news->setEnabled(true);
                    } else {
                        $news->setEnabled(false);
                    }

                  $news->setPublishedAt(\DateTime::createFromFormat('Y-m-d',$param['publishedAt']));
                    if (strlen($param['title']) < 3) {

                        $form = $this->createForm(NewsType::class, $news, ['edit_type' => 'fast'] );
                        $form->get('title')->addError(new FormError('Tytuł nie moze byc pusty'));
                        return $this->render('news/_formFast.html.twig', [
                            'news' => $news,
                            'form' => $form->createView(),
                        ]);

                    }
                    $news->setTitle($param['title']);
                    $this->getDoctrine()->getManager()->persist($news);
                    $this->getDoctrine()->getManager()->flush();

                return $this->render('news/list_row.html.twig', ['news' => $news]);

            } catch (\Exception $exception) {
                return new JsonResponse($param);
            }


    }

    /**
     * @Route("/{id}/fast-delete", name="news_delete")
     */
    public function delete(Request $request, News $news): Response
    {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($news);
            $entityManager->flush();


        return new JsonResponse($request->request->all());
    }
}
