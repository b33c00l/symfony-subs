<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Entity\Subscribtion;
use App\Repository\SubscribtionRepository;

use App\Entity\Category;
use App\Repository\CategoryRepository;


class SubscribersController extends Controller
{	
    private $subscribtionRepository;
    private $categoryRepository;



    public function __construct(SubscribtionRepository $subscribtionRepository, CategoryRepository $categoryRepository)
    {
        $this->subscribtionRepository = $subscribtionRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/subscribers", name="subscribers")
     */

    public function index()
    {	
        $subscribers = $this->subscribtionRepository->findAll();
    	
    	return $this->render('subscribers/list.html.twig', [
    		'subscribers' => $subscribers,
    	]);
    }

    /**
     * @Route("/", name="home")
     */

    public function create(Request $request, AuthorizationCheckerInterface $authChecker)
    {	
        $subscribe = new Subscribtion;
    	$form = $this->createFormBuilder($subscribe)
    	->add('User', TextType::class)
    	->add('Email', EmailType::class)
    	->add('Categories', ChoiceType::class,[
    		'multiple' => true,
    		'choices' => $this->categoryRepository->findAll(),
            'choice_label' => 'value',
            'choice_value' => 'id',
    	])
    	->add('Subscribe', SubmitType::class, [
    		'attr' => [
    			'class' => 'btn btn-success btn-block btn-lg'
    		],
    	])
    	->getForm();

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		$subscribe = $form->getData();

            $subscribe->setDate(new \DateTime());

            $this->subscribtionRepository->save($subscribe);

            if (!$authChecker->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('home');
            } else {
                return $this->redirectToRoute('subscribers');
            }
    	}

    	return $this->render('subscribers/create.html.twig', [
    		'form' => $form->createView(),
    	]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */

    public function delete($id)
    {   
        $subscriber = $this->subscribtionRepository->find($id);

        $subscribers = $this->subscribtionRepository->delete($subscriber);

    	return $this->redirectToRoute('subscribers');

    }

    /**
     * @Route("/update/{id}", name="update")
     */

    public function update(Request $request, $id)
    {
    	$subscriber = $this->subscribtionRepository->find($id);

    	$form = $this->createFormBuilder($subscriber)
    	->add('user', TextType::class)
    	->add('email', EmailType::class)
    	->add('Edit', SubmitType::class, [
    		'attr' => [
    			'class' => 'btn btn-success btn-block btn-lg'
    		],
    	])

    	->getForm();

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
    		
    		$subscriber = $form->getData();

            $this->subscribtionRepository->save($subscriber);

    		return $this->redirectToRoute('subscribers');

    	}

    	return $this->render('subscribers/update.html.twig', [
    		'form' => $form->createView(),
    	]);

    }

}
