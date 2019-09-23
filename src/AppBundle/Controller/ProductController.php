<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    /**
     * @Route("/product/display",name="web_product_display")
     */
    public function showProduct()
    {
        $pro = $this->getDoctrine()
            ->getRepository('AppBundle:Product')
            ->findAll();
        return $this->render('Product/display.html.twig', ['data'=>$pro]);
    }
    /**
     * @Route("/product/new", name="web_product_new")
     */
    public function newAction(Request $request)
    {
        $pro = new Product();
        $form = $this->createFormBuilder($pro)
            ->add('name', TextType::class)
            ->add('color',TextType::class)
            ->add('price',TextType::class)
            ->add('save',SubmitType::class, array('label'=>'Add'))
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $pro = $form->getData();
            $doct = $this->getDoctrine()->getManager();
            $doct->persist($pro);
            $doct->flush();
            return $this->redirectToRoute('web_product_display');
        }
        else {
            return $this->render('Product/new.html.twig', array('form' => $form->createView()));
        }
    }

    /**
     * @Route("/product/update/{id}", name="web_product_update")
     */
    public function updateAction($id, Request $request) {
        $doct = $this->getDoctrine()->getManager();
        $pro = $doct->getRepository('AppBundle:Product')->find($id);

        if (!$pro)
        {
            throw $this->createNotFoundException('No product found for id '.$id);
        }
        $form = $this->createFormBuilder($pro)
            ->add('name', TextType::class)
            ->add('color', TextType::class)
            ->add('price', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Update'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pro = $form->getData();
            $doct = $this->getDoctrine()->getManager();

            // tells Doctrine you want to save the Product
            $doct->persist($pro);

            //executes the queries (i.e. the INSERT query)
            $doct->flush();
            return $this->redirectToRoute('web_product_display');
        } else {
            return $this->render('Product/new.html.twig', array('form' => $form->createView()));
        }
    }
    /**
     * @Route("/product/delete/{id}", name="web_product_delete")
     */
    public function deleteAction($id)
    {
        $doct = $this->getDoctrine()->getManager();
        $pro = $doct->getRepository('AppBundle:Product')->find($id);

        if (!$pro)
        {
            throw $this->createNotFoundException('No product found with id '.$id);
        }
        $doct->remove($pro);
        $doct->flush();
        return $this->redirectToRoute('web_product_display');
    }
}
