<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\UnseiType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Unsei;
use Doctrine\ORM\EntityManager;

/**
 * @Route("/unsei")
 */
class UnseiController extends Controller
{
    /**
     * @Route("/", name="unsei_index")
     * @Method("GET")
     * 
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $unseis = $repository->findAll();
        
        return $this->render('unsei/index.html.twig', ['unseis' => $unseis]);
    }
    
    /**
     *
     * @Route("/new", name="unsei_new")
     * @Method({"GET", "POST"}) 
     *
     * @param Request $request
     * @return Response
     */
    public function newAction(Request $request)
    {
        $unsei = new Unsei();
        
//         $form = $this->createFormBuilder($unsei)
//         ->add('name', TextType::class)
//         ->getForm();

        $form = $this->createForm(UnseiType::class, $unsei);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EntityManager $em */
            $em = $this->getDoctrine()->getManager();
            $em->persist($unsei);
            $em->flush();
            
            $this->addFlash('notice', "{$unsei->getName()}を追加しました");
            
            return $this->redirectToRoute('unsei_index');
        }
        
        return $this->render('unsei/new.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
    /**
     *
     * @Route("/{id}/edit", name="unsei_edit")
     * @Method({"GET", "PUT"})
//      * @ParamConverter("unsei", class="AppBundle:Unsei")
     *
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request, Unsei $unsei)
    {
//         $repository = $this->getDoctrine()->getRepository(Unsei::class);
//         $unsei = $repository->find($id);
        
//         if (!$unsei) {
//             throw $this->createNotFoundException('No unsei found for id '.$id);
//         }
        
        $form = $this->createForm(UnseiType::class, $unsei, [
            'method' => 'PUT',
        ]);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            
            $this->addFlash('notice', "{$unsei->getName()}に更新しました");
            
            return $this->redirectToRoute('unsei_index');
        }
        
        return $this->render('unsei/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    /**
     *
     * @Route("/{id}", name="unsei_delete")
     * @Method("DELETE") 
     *
     * @param Request $request
     * @return Response
     */
    public function deleteAction(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $unsei = $repository->find($id);
        
        if (!$unsei) {
            throw $this->createNotFoundException('No unsei found for id '.$id);
        }
        
        if ($this->isCsrfTokenValid('unsei', $request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($unsei);
            $em->flush();
            
            $this->addFlash('notice', "{$unsei->getName()}を削除しました");
        }
        
        return $this->redirectToRoute('unsei_index');
    }
    
    /**
     * @Route("/validate/{name}", defaults={"name" = ""})
     */
    public function validateAction($name)
    {
        $unsei = new Unsei();
        $unsei->setName($name);
        
        $validator = $this->get('validator');
        $errors = $validator->validate($unsei);
        
        if(count($errors) > 0) {
            return $this->render('unsei/validate.html.twig', [
                'errors' => $errors,
            ]);
        }
        
        return new Response("「{$name}」は正しい運勢です！");
    }
}

