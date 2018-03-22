<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoriesController extends Controller
{   

    private $categoryRepository;


    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @Route("/categories", name="categories")
     */


    public function index()
    {	
        $categories = $this->categoryRepository->findAll();
    	
    	return $this->render('categories/list.html.twig', [
    		'categories' => $categories,
    	]);
    }

    /**
     * @Route("/categories/create", name="categories/create")
     */

    public function create(Request $request)
    {	
    	$category = new Category();
    	$form = $this->createFormBuilder($category)
        ->add('Id', TextType::class)
    	->add('Value', TextType::class,[
            'label' => 'Name'
        ])
    	->add('Add', SubmitType::class, [
    		'attr' => [
    			'class' => 'btn btn-success btn-block btn-lg'
    		],
    	])
    	->getForm();
    	$form->handleRequest($request);
    	if ($form->isSubmitted() && $form->isValid()) {
    		$category = $form->getData();

            $this->categoryRepository->save($category);

    		return $this->redirectToRoute('categories');
    	}

    	return $this->render('categories/create.html.twig', [
    		'form' => $form->createView(),
    	]);
    }

}
