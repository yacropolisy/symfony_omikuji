<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Unsei;
use AppBundle\Repository\UnseiRepository;
use Doctrine\ORM\EntityManager;
// use Symfony\Component\Routing\Annotation\Route;

class OmikujiController extends Controller
{
    /**
     * @Route("/omikuji/{yourname}", defaults={"yourname" = "YOU"}, name="omikuji")
     * 
     * @param Request $request
     * @return Response
     */
    public function omikujiAction(Request $request, $yourname)
    {
//         $omikuji = ['大吉', '中吉', '小吉', '末吉','凶'];
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        $omikuji = $repository->findAll();
        
        $number = rand(0, count($omikuji) - 1);
        // replace this example code with whatever you need
//         return new Response(
//                 "<html><body>{$yourname}さんの運勢は $omikuji[$number]です。</body></html>"
//         ); 
        return $this->render('omikuji/omikuji.html.twig', [
            'name' => $yourname,
            'unsei' => $omikuji[$number],
        ]);
        
    }
    
    /**
     * @Route("/find")
     */
    public function findAction()
    {
        /**
         * @var UnseiRepository $repository
         */
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        
        $unseis = $repository->findAll();
        dump($unseis);
        
        $unsei = $repository->find(1);
        dump($unsei);
        
        $unsei = $repository->findOneBy([
            'name' => '大吉',
        ]);
        dump($unsei);
        
        $unsei = $repository->findBy([
            'name' => '大吉',
        ]);
        dump($unsei);
        $unsei = $repository->findOneById(1);
        dump($unsei);
        
        $unsei = $repository->findOneByName('中吉');
        dump($unsei);
        
        $unsei = $repository->findByName('中吉');
        dump($unsei);
        
        die;
        return new Response("Dummy");
        
    }
    
    /**
     * @Route("/crud")
     */
    public function crudAction()
    {
        /**
         *  @var EntityManager $em
         */
        $em = $this->getDoctrine()->getManager();
        
        //
        // Create
        //
        $unsei = new Unsei();
        $unsei->setName("大凶");
        dump($unsei);
        
        $em->persist($unsei);
        $em->flush();
        dump($unsei);
        
        //
        // Read
        //
        $repository = $em->getRepository(Unsei::class);
        
        /** @var Unsei $unsei */
        $unsei = $repository->findOneByName('大凶');
        dump($unsei);
        
        //
        // Update
        //
        $unsei->setName("大大吉");
        $em->flush();
        dump($unsei);
        
        $unsei = $repository->find($unsei->getId());
        dump($unsei);
        
        //
        // Delete
        //
        $em->remove($unsei);
        $em->flush();
        
        $unseis = $repository->findAll();
        dump($unseis);
        foreach ($unseis as $unsei) {
            dump($unsei->getName());
        }
        
        die;
        
        return new Response("Dummy");
    }

        
    /**
     * @Route("/dql")
     */
    public function dql()
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT u
        FROM AppBundle:Unsei u
        WHERE u.name = :name'
            )->setParameter('name', '大吉');
            
            $unsei = $query->getResult();
            dump($unsei);
            
            die;
            
            return new Response("Dummy");
    }
    
    /**
     * @Route("/qb")
     */
    public function queryBuilder()
    {
        /** @var UnseiRepository $repository **/
        $repository = $this->getDoctrine()->getRepository(Unsei::class);
        
        $query = $repository->createQueryBuilder('u')
        ->where('u.name = :name')
        ->setParameter('name', '大吉')
        ->getQuery();
        
        $unsei = $query->getResult();
        dump($unsei);
        
        die;
        
        return new Response("Dummy");
    }
    
}
