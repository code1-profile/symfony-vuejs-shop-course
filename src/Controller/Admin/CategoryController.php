<?php

namespace App\Controller\Admin;

use App\Repository\CategoryRepository;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/category", name="admin_category_")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/list", name="list")
     */
    public function list(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findBy([], ['id' => 'DESC']);

        return $this->render('admin/category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @Route("/add", name="add")
     */
    public function edit(Request $request, CategoryFormHandler $categoryFormHandler, Category $category = null): Response
    {
      //
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete(Category $category, CategoryManager $categoryManager): Response
    {

    }
}